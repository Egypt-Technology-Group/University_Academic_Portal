---
noteId: "728bb8f0a16e11f1ba16875512ddc7d8"
tags: []

---

﻿# Task 2: Backend Feature Flag & Module Lifecycle Policy with Dependency Guard

## Objective
Implement route protection middleware and administrative REST API endpoints for live module management, state introspection, and dependency-guarded toggling.

## Files to Create/Modify
- `backend/app/Core/Middleware/EnsureModuleEnabled.php`
- `backend/app/Http/Controllers/Api/ModuleManagementController.php`
- `backend/routes/api.php`
- `backend/tests/Feature/Core/ModuleMiddlewareTest.php`
- `backend/tests/Feature/Core/ModuleManagementApiTest.php`

## Requirements
1. `EnsureModuleEnabled` middleware:
   - Alias: `module.enabled:{module_id}`
   - Checks if `ModuleManager::isEnabled($moduleId)` is true.
   - If disabled, returns JSON HTTP 404 (or 503) with message: `Module [module_id] is currently disabled.`
2. `ModuleManagementController`:
   - `GET /api/v1/modules`: Returns list of all modules, their metadata, dependencies, owned tables, and active status.
   - `GET /api/v1/modules/{id}/dependencies`: Returns dependency tree, whether it can be disabled/enabled, and blocking dependents.
   - `PATCH /api/v1/modules/{id}/toggle`: Toggles module enabled status. If action violates dependencies (e.g. disabling a module needed by an active module, or enabling without required dependencies), returns HTTP 409 Conflict with clear error reason.
3. Tests:
   - Test route protection with `EnsureModuleEnabled` for both enabled and disabled modules.
   - Test module listing API.
   - Test toggle endpoint rejecting unsafe deactivation with HTTP 409 and accepting safe toggles with HTTP 200.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-2-report.md`
