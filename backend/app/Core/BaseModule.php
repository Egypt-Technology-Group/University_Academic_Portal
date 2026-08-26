<?php

namespace App\Core;

use App\Core\Contracts\ModuleInterface;
use Illuminate\Support\Facades\Route;

abstract class BaseModule implements ModuleInterface
{
    protected string $id;
    protected array $name = [];
    protected array $description = [];
    protected string $version = '1.0.0';
    protected array $dependencies = [];
    protected array $ownedTables = [];
    protected ?string $routesPath = null;
    protected bool $enabled = false;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(string $locale = 'ar'): string
    {
        return $this->name[$locale] ?? $this->name['en'] ?? $this->name['ar'] ?? $this->id;
    }

    public function getDescription(string $locale = 'ar'): string
    {
        return $this->description[$locale] ?? $this->description['en'] ?? $this->description['ar'] ?? '';
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getOwnedTables(): array
    {
        return $this->ownedTables;
    }

    public function getRoutes(): ?string
    {
        return $this->routesPath;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function boot(): void
    {
        if ($this->routesPath && file_exists($this->routesPath)) {
            Route::middleware('api')
                ->prefix('api')
                ->group($this->routesPath);
        }
    }
}
