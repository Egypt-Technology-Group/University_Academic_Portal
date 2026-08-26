<?php

namespace App\Core\Providers;

use App\Core\Contracts\ModuleInterface;
use App\Core\DependencyValidator;
use App\Core\ModuleManager;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            config_path('modules.php'),
            'modules'
        );

        $this->app->singleton(DependencyValidator::class, function () {
            return new DependencyValidator();
        });

        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager(
                $app->make(DependencyValidator::class),
                $app
            );
        });

        $this->app->alias(ModuleManager::class, 'module.manager');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../config/modules.php' => config_path('modules.php'),
            ], 'modules-config');
        }

        /** @var ModuleManager $moduleManager */
        $moduleManager = $this->app->make(ModuleManager::class);

        // Register configured modules
        $configuredModules = config('modules.modules', []);
        foreach ($configuredModules as $moduleClass) {
            if (is_string($moduleClass) && class_exists($moduleClass)) {
                $instance = $this->app->make($moduleClass);
                if ($instance instanceof ModuleInterface) {
                    $moduleManager->register($instance);
                }
            } elseif ($moduleClass instanceof ModuleInterface) {
                $moduleManager->register($moduleClass);
            }
        }

        // Boot enabled modules if configured
        if (config('modules.auto_boot', true)) {
            $moduleManager->bootEnabledModules();
        }
    }
}
