---
noteId: "72b279d0a17911f1ba16875512ddc7d8"
tags: []

---

﻿# Task 6: Core Cleanup, Router Synchronization & Final End-to-End Verification

## Objective
Finalize the modular refactoring:
1. Audit `backend/app/Models` and `backend/app/Http/Controllers/Api`:
   - Keep ONLY core shared models: `User.php`, `SiteSetting.php`, `AuditLog.php`.
   - Keep ONLY core controllers: `AuthController.php`, `SiteSettingsController.php`, `ModuleManagementController.php`, `Admin/AdminDashboardController.php`, `Admin/AuditLogController.php`.
   - Remove obsolete monolithic controllers (`AdminCrudController.php`, `AcademicController.php`, `AdmissionController.php`, `AcademicServicesController.php`, `ContentController.php`, `StudentPortalController.php`).
2. Audit `frontend/src/views`:
   - Keep ONLY core views: `HomeView.vue`, `NotFoundView.vue`, `ModuleDisabledView.vue`, `admin/AdminDashboardView.vue`, `admin/AdminSettingsView.vue`, `admin/AdminModulesView.vue`, `admin/AdminAuditTrailView.vue`, `admin/AdminLoginView.vue`.
   - Remove duplicate root views from `frontend/src/views/` that have been moved to `frontend/src/modules/`.
3. Audit `frontend/src/services/api.js`:
   - Refactor `api.js` to serve cleanly as the unified API gateway aggregating the 7 module API services without duplicate logic.
4. Run full backend test suite (`php artisan test`) and frontend production build (`npm run build`).

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-6-report.md`
