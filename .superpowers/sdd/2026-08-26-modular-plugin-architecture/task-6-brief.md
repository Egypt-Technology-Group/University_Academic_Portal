---
noteId: "c6a9ec70a17011f1ba16875512ddc7d8"
tags: []

---

﻿# Task 6: Frontend Module Packaging & Dynamic Layout Menu Injection

## Objective
Package each frontend business domain into self-contained module directories under `frontend/src/modules/` containing route metadata, navigation descriptors, and component declarations. Update `AdminLayout.vue` and `Navbar.vue` to dynamically query enabled navigation items from `moduleRegistry` instead of hardcoding menu lists.

## Files to Create/Modify
- Create: `frontend/src/modules/academic-structure/index.js`
- Create: `frontend/src/modules/admissions/index.js`
- Create: `frontend/src/modules/academic-services/index.js`
- Create: `frontend/src/modules/cms/index.js`
- Create: `frontend/src/modules/events/index.js`
- Create: `frontend/src/modules/documents/index.js`
- Create: `frontend/src/modules/results/index.js`
- Modify: `frontend/src/main.js` (boot & register all frontend modules)
- Modify: `frontend/src/components/layout/AdminLayout.vue` (dynamically render sidebar items from `modulesStore.enabledNavItems`)
- Modify: `frontend/src/components/layout/Navbar.vue` (dynamically render public nav items from `modulesStore.enabledNavItems`)

## Requirements
1. Each module package exports a standard manifest registering its metadata, dependencies, public nav items, and admin nav items.
2. `AdminLayout.vue`:
   - Keep core fixed items: Dashboard, Audit Trail, Settings, and Module Center.
   - Domain menu items (Colleges/Faculty, Admissions Queue, Academic Services, CMS, Events, Documents) must be rendered dynamically according to active modules.
3. `Navbar.vue`:
   - Keep core brand link and public language toggle.
   - Public nav links (Programs, Admissions, Services, News, Events, Regulations, Results) must be filtered dynamically based on enabled modules.
4. Verify build passes with `npm run build`.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-6-report.md`
