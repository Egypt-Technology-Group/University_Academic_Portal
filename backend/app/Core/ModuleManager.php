<?php

namespace App\Core;

use App\Core\Contracts\ModuleInterface;
use App\Core\Exceptions\ModuleDependencyException;
use App\Models\SiteSetting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ModuleManager
{
    /**
     * Registered modules keyed by module ID.
     *
     * @var array<string, ModuleInterface>
     */
    protected array $modules = [];

    /**
     * Cached list of enabled module IDs in memory.
     *
     * @var string[]|null
     */
    protected ?array $enabledModuleIds = null;

    /**
     * Set of modules that have already been booted.
     *
     * @var array<string, bool>
     */
    protected array $bootedModules = [];

    public function __construct(
        protected DependencyValidator $validator,
        protected ?Application $app = null
    ) {
    }

    /**
     * Register a module in the manager.
     */
    public function register(ModuleInterface $module): static
    {
        $id = $module->getId();
        $this->modules[$id] = $module;

        // Synchronize enabled state on the module instance if supported
        $enabledIds = $this->getEnabledIds();
        $isEnabled = in_array($id, $enabledIds, true);

        if (method_exists($module, 'setEnabled')) {
            $module->setEnabled($isEnabled);
        }

        return $this;
    }

    /**
     * Check if a module is registered by ID.
     */
    public function has(string $id): bool
    {
        return isset($this->modules[$id]);
    }

    /**
     * Get a registered module instance by ID.
     */
    public function get(string $id): ?ModuleInterface
    {
        return $this->modules[$id] ?? null;
    }

    /**
     * Check if a module is currently enabled by ID.
     */
    public function isEnabled(string $id): bool
    {
        return in_array($id, $this->getEnabledIds(), true);
    }

    /**
     * Get all registered modules.
     *
     * @return array<string, ModuleInterface>
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Get all currently enabled module instances in topological order.
     *
     * @return ModuleInterface[]
     */
    public function getEnabled(): array
    {
        $enabledIds = $this->getEnabledIds();
        $enabledModules = [];

        foreach ($enabledIds as $id) {
            if (isset($this->modules[$id])) {
                $enabledModules[$id] = $this->modules[$id];
            }
        }

        try {
            $order = $this->validator->getTopologicalOrder($enabledModules);
            $ordered = [];
            foreach ($order as $id) {
                if (isset($enabledModules[$id])) {
                    $ordered[] = $enabledModules[$id];
                }
            }
            return $ordered;
        } catch (\Throwable $e) {
            return array_values($enabledModules);
        }
    }

    /**
     * Get list of enabled module IDs.
     *
     * @return string[]
     */
    public function getEnabledIds(): array
    {
        if ($this->enabledModuleIds !== null) {
            return $this->enabledModuleIds;
        }

        $this->enabledModuleIds = $this->loadEnabledState();
        return $this->enabledModuleIds;
    }

    /**
     * Validate whether a module can be safely enabled.
     *
     * @return array{can_enable: bool, missing_dependencies: array<string>, reason: ?string}
     */
    public function canEnable(string $id): array
    {
        return $this->validator->canEnable($id, $this->modules, $this->getEnabledIds());
    }

    /**
     * Validate whether a module can be safely disabled.
     *
     * @return array{can_disable: bool, blocking_dependents: array<string>, reason: ?string}
     */
    public function canDisable(string $id): array
    {
        return $this->validator->canDisable($id, $this->modules, $this->getEnabledIds());
    }

    /**
     * Enable a module by ID.
     *
     * @throws ModuleDependencyException If dependencies are not satisfied
     */
    public function enable(string $id): bool
    {
        if (!$this->has($id)) {
            return false;
        }

        $validation = $this->canEnable($id);
        if (!$validation['can_enable']) {
            throw new ModuleDependencyException(
                $validation['reason'] ?? "Cannot enable module '{$id}'.",
                $validation
            );
        }

        $enabledIds = $this->getEnabledIds();
        if (!in_array($id, $enabledIds, true)) {
            $enabledIds[] = $id;
            $this->enabledModuleIds = array_values(array_unique($enabledIds));
            $this->persistEnabledState($this->enabledModuleIds);
        }

        $module = $this->get($id);
        if ($module) {
            if (method_exists($module, 'setEnabled')) {
                $module->setEnabled(true);
            }

            // Boot if auto-boot enabled and not yet booted
            if (config('modules.auto_boot', true) && !isset($this->bootedModules[$id])) {
                $this->bootModule($module);
            }
        }

        return true;
    }

    /**
     * Disable a module by ID.
     *
     * @throws ModuleDependencyException If disabling would break active dependents
     */
    public function disable(string $id): bool
    {
        if (!$this->has($id)) {
            return false;
        }

        $validation = $this->canDisable($id);
        if (!$validation['can_disable']) {
            throw new ModuleDependencyException(
                $validation['reason'] ?? "Cannot disable module '{$id}'.",
                $validation
            );
        }

        $enabledIds = $this->getEnabledIds();
        $this->enabledModuleIds = array_values(array_filter($enabledIds, fn($item) => $item !== $id));
        $this->persistEnabledState($this->enabledModuleIds);

        $module = $this->get($id);
        if ($module && method_exists($module, 'setEnabled')) {
            $module->setEnabled(false);
        }

        return true;
    }

    /**
     * Boot all enabled modules.
     */
    public function bootEnabledModules(): void
    {
        $enabled = $this->getEnabled();

        foreach ($enabled as $module) {
            $this->bootModule($module);
        }
    }

    /**
     * Boot a single module and load its routes if provided.
     */
    protected function bootModule(ModuleInterface $module): void
    {
        $id = $module->getId();
        if (isset($this->bootedModules[$id])) {
            return;
        }

        $module->boot();
        $this->bootedModules[$id] = true;
    }

    /**
     * Load the enabled module IDs from persistence (Cache / SiteSettings / config).
     *
     * @return string[]
     */
    protected function loadEnabledState(): array
    {
        $cacheKey = config('modules.cache_key', 'app_modules_enabled');
        $defaultEnabled = config('modules.default_enabled', []);

        // 1. Try Cache
        try {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {
            // Cache not reachable, fallback to DB / config
        }

        // 2. Try SiteSetting model in DB if table exists
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                $setting = SiteSetting::get('enabled_modules');
                if (is_array($setting)) {
                    // Also store to cache for faster subsequent reads
                    try {
                        Cache::forever($cacheKey, $setting);
                    } catch (\Throwable $e) {
                    }
                    return $setting;
                }
            }
        } catch (\Throwable $e) {
            // DB table might not exist yet during migration/testing
        }

        return $defaultEnabled;
    }

    /**
     * Persist the enabled module IDs list to Cache and SiteSettings.
     *
     * @param string[] $enabledIds
     */
    protected function persistEnabledState(array $enabledIds): void
    {
        $cacheKey = config('modules.cache_key', 'app_modules_enabled');

        // 1. Save to cache
        try {
            Cache::forever($cacheKey, $enabledIds);
        } catch (\Throwable $e) {
            Log::warning("Could not persist module state to cache: {$e->getMessage()}");
        }

        // 2. Save to SiteSetting if available
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                SiteSetting::set('enabled_modules', $enabledIds, 'system', false);
            }
        } catch (\Throwable $e) {
            // DB not ready or not migrated yet
        }
    }

    /**
     * Reset in-memory state, cache, and DB persistence (useful in tests).
     */
    public function reset(): void
    {
        $this->modules = [];
        $this->enabledModuleIds = null;
        $this->bootedModules = [];
        try {
            Cache::forget(config('modules.cache_key', 'app_modules_enabled'));
        } catch (\Throwable $e) {
        }
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                SiteSetting::where('key', 'enabled_modules')->delete();
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Get the validator instance.
     */
    public function getValidator(): DependencyValidator
    {
        return $this->validator;
    }
}
