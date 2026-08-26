---
noteId: "6169d190a17011f1ba16875512ddc7d8"
tags: []

---

﻿# Task 5: Frontend Micro-Module Registry & Dynamic Routing

## Objective
Build the client-side micro-module engine in Vue 3. Implement a centralized `moduleRegistry` to manage frontend module registration, dynamic routing via `router.addRoute()`, dependency inspection, and a Pinia store (`useModulesStore`) synchronizing enabled module states with backend feature flags.

## Files to Create/Modify
- Create: `frontend/src/core/modules/moduleRegistry.js`
- Create: `frontend/src/core/modules/types.js`
- Create: `frontend/src/stores/modules.js`
- Modify: `frontend/src/router/index.js`
- Create: `frontend/src/views/ModuleDisabledView.vue`
- Create: `frontend/src/services/modulesApi.js`

## Requirements
1. `moduleRegistry`:
   - Methods: `register(moduleDefinition)`, `get(id)`, `getAll()`, `getEnabled(enabledIds)`, `getNavItems(enabledIds, section)`, `validateDependencies(id, enabledIds)`.
   - Supports module definition schema:
     ```js
     {
       id: 'admissions',
       name: { ar: 'القبول والتسجيل', en: 'Admissions & Enrollment' },
       description: { ar: '...', en: '...' },
       version: '1.0.0',
       dependencies: ['academic-structure'],
       ownedTables: ['admission_cycles', 'applications', 'application_documents'],
       publicRoutes: [...],
       adminRoutes: [...],
       navItems: {
         public: [...],
         admin: [...]
       }
     }
     ```
2. `useModulesStore` (Pinia):
   - Fetches active modules from `GET /api/v1/modules`.
   - Tracks `modules`, `enabledIds`, `loading`, `error`.
   - `toggleModule(id)`: Calls `PATCH /api/v1/modules/{id}/toggle`, handles dependency conflict (409) with informative error messages.
   - `isModuleEnabled(id)`: returns boolean.
3. `router/index.js`:
   - Configures navigation guard: checks if target route belongs to a module; if that module is disabled, redirects to `/module-disabled?module={id}` or 404.
4. `ModuleDisabledView.vue`:
   - Clean, bilingual fallback screen indicating that the requested academic module is temporarily disabled or offline for maintenance.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-5-report.md`
