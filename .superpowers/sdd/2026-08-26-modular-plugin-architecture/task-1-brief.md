---
noteId: "1e452b00a16e11f1ba16875512ddc7d8"
tags: []

---

﻿# Task 1: Backend Modular Core Engine & Dependency Graph Validator

## Objective
Establish the foundational backend Module Engine in Laravel. Define standard module interfaces, dynamic module loading, dependency graph resolution, cycle detection, and safety validation (blocking unsafe deactivations when dependent modules are enabled).

## Files to Create/Modify
- `backend/app/Core/Contracts/ModuleInterface.php`
- `backend/app/Core/DependencyValidator.php`
- `backend/app/Core/ModuleManager.php`
- `backend/app/Core/Providers/ModuleServiceProvider.php`
- `backend/config/modules.php`
- `backend/tests/Feature/Core/ModuleManagerTest.php`

## Requirements
1. `ModuleInterface` contract:
   - `getId(): string` (e.g. `'academic-structure'`, `'admissions'`)
   - `getName(string $locale = 'ar'): string`
   - `getDescription(string $locale = 'ar'): string`
   - `getVersion(): string`
   - `getDependencies(): array` (array of module IDs this module depends on)
   - `getOwnedTables(): array` (list of tables owned by this module)
   - `getRoutes(): ?string` (path to route file, or null)
   - `isEnabled(): bool`
   - `boot(): void`
2. `DependencyValidator`:
   - Validates dependency existence and detects circular dependencies.
   - `canDisable(string $moduleId, array $allModules, array $enabledModuleIds): array` -> returns `['can_disable' => bool, 'blocking_dependents' => array, 'reason' => ?string]`
   - `canEnable(string $moduleId, array $allModules, array $enabledModuleIds): array` -> returns `['can_enable' => bool, 'missing_dependencies' => array, 'reason' => ?string]`
3. `ModuleManager`:
   - Manages module registration, state persistence (via cache/config/database settings), and lifecycle.
   - Methods: `register(ModuleInterface $module)`, `get(string $id): ?ModuleInterface`, `all(): array`, `getEnabled(): array`, `enable(string $id): bool`, `disable(string $id): bool`, `canDisable(string $id): array`.
4. `ModuleServiceProvider`:
   - Discovers and registers all modules into `ModuleManager` singleton during Laravel boot.
5. `ModuleManagerTest`:
   - Feature tests verifying module registration, enabling/disabling, dependency validation, and blocking deactivations of dependencies.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-1-report.md`
