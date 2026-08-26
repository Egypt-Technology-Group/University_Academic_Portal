---
noteId: "3a54fd50a17511f1ba16875512ddc7d8"
tags: []

---

﻿# Task 2: Complete Refactoring for Admissions Module

## Objective
Relocate ALL Admissions domain code from global directories into `backend/app/Modules/Admissions/` and `frontend/src/modules/admissions/`.

## Backend Actions
1. Move models:
   - `backend/app/Models/AdmissionCycle.php` -> `backend/app/Modules/Admissions/Models/AdmissionCycle.php`
   - `backend/app/Models/Application.php` -> `backend/app/Modules/Admissions/Models/Application.php`
   - `backend/app/Models/ApplicationDocument.php` -> `backend/app/Modules/Admissions/Models/ApplicationDocument.php`
   (Update namespaces to `App\Modules\Admissions\Models`)
2. Move resources:
   - `backend/app/Http/Resources/AdmissionCycleResource.php` -> `backend/app/Modules/Admissions/Resources/AdmissionCycleResource.php`
   - `backend/app/Http/Resources/ApplicationResource.php` -> `backend/app/Modules/Admissions/Resources/ApplicationResource.php`
   - `backend/app/Http/Resources/ApplicationDocumentResource.php` -> `backend/app/Modules/Admissions/Resources/ApplicationDocumentResource.php`
3. Move requests:
   - `backend/app/Http/Requests/SubmitApplicationRequest.php` -> `backend/app/Modules/Admissions/Requests/SubmitApplicationRequest.php`
4. Create/Move module controllers:
   - `backend/app/Modules/Admissions/Controllers/AdmissionsController.php`
   - `backend/app/Modules/Admissions/Controllers/AdminAdmissionsController.php`
5. Update `backend/app/Modules/Admissions/Routes/api.php` to point to the module controllers.
6. Remove old global Admissions files and update seeders / relations.

## Frontend Actions
1. Move views:
   - `frontend/src/views/AdmissionsView.vue` -> `frontend/src/modules/admissions/views/AdmissionsView.vue`
   - `frontend/src/views/ApplicationTrackView.vue` -> `frontend/src/modules/admissions/views/ApplicationTrackView.vue`
   - `frontend/src/views/admin/AdminAdmissionsView.vue` -> `frontend/src/modules/admissions/views/AdminAdmissionsView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/admissions/services/admissionsApi.js`
3. Update `frontend/src/modules/admissions/routes.js` and `index.js`.
4. Update imports across all related views.

## Verification
- Run `php artisan test` in `backend/`
- Run `npm run build` in `frontend/`

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-2-report.md`
