<?php

namespace App\Core\Contracts;

interface ModuleInterface
{
    /**
     * Get the unique machine identifier of the module (e.g. 'academic-structure', 'admissions').
     */
    public function getId(): string;

    /**
     * Get the human-readable display name of the module, localized.
     */
    public function getName(string $locale = 'ar'): string;

    /**
     * Get the human-readable description of the module, localized.
     */
    public function getDescription(string $locale = 'ar'): string;

    /**
     * Get the module semantic version.
     */
    public function getVersion(): string;

    /**
     * Get array of module IDs that this module depends on.
     *
     * @return string[]
     */
    public function getDependencies(): array;

    /**
     * Get list of database tables owned by this module.
     *
     * @return string[]
     */
    public function getOwnedTables(): array;

    /**
     * Get the absolute path to the module's route file, or null if it has none.
     */
    public function getRoutes(): ?string;

    /**
     * Check if the module is currently enabled.
     */
    public function isEnabled(): bool;

    /**
     * Boot the module (register routes, event listeners, services, etc.).
     */
    public function boot(): void;
}
