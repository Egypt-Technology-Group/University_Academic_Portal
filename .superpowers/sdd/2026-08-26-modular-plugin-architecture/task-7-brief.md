---
noteId: "2e2180c0a17111f1ba16875512ddc7d8"
tags: []

---

﻿# Task 7: Admin Module Center UI & System Orchestration

## Objective
Build the administrative Module Management Center view (`AdminModulesView.vue`) allowing university administrators to view all registered modules, inspect their metadata, owned database tables, and dependency trees, and safely toggle them ON/OFF with live confirmation dialogs and HTTP 409 conflict notifications. Mount the route and sidebar entry in the Admin portal.

## Files to Create/Modify
- Create: `frontend/src/views/admin/AdminModulesView.vue`
- Modify: `frontend/src/router/index.js` (add `/admin/modules` route)
- Modify: `frontend/src/components/layout/AdminLayout.vue` (ensure Module Center link is displayed in system navigation)
- Modify: `frontend/src/i18n/ar.json` & `frontend/src/i18n/en.json` (add module center strings)

## Requirements
1. `AdminModulesView.vue`:
   - Header with total modules count, active modules count, and system status indicator.
   - Module cards displaying:
     - Title (Arabic/English) & Version
     - Category badge & Status badge (Active / Suspended)
     - Description (Arabic/English)
     - Owned database tables pill tags (e.g. `colleges`, `programs`)
     - Dependencies list (e.g. `Requires: academic-structure`)
     - Toggle switch with loading indicator
   - Safety confirmations:
     - When toggling OFF a module that has active dependents, prompt an alert or show warning that it is blocked.
     - When toggle is successful, trigger `toast.success` and refresh `modulesStore`.
     - When toggle fails or returns 409 Conflict, trigger `toast.error` with the exact conflict explanation.
2. Verify production build with `npm run build`.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-7-report.md`
