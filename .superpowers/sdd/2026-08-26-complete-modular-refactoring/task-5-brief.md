---
noteId: "69683320a17811f1ba16875512ddc7d8"
tags: []

---

﻿# Task 5: Complete Refactoring for Documents & Results Modules

## Objective
Relocate ALL Documents and Results domain code from global directories into:
1. `backend/app/Modules/Documents/` and `frontend/src/modules/documents/`
2. `backend/app/Modules/Results/` and `frontend/src/modules/results/`

## Backend Actions (Documents)
1. Move models:
   - `backend/app/Models/DownloadDocument.php` -> `backend/app/Modules/Documents/Models/DownloadDocument.php`
   (Update namespace to `App\Modules\Documents\Models`)
2. Move resources:
   - `backend/app/Http/Resources/DocumentResource.php` -> `backend/app/Modules/Documents/Resources/DocumentResource.php`
3. Create/Move module controllers:
   - `backend/app/Modules/Documents/Controllers/DocumentsController.php`
   - `backend/app/Modules/Documents/Controllers/AdminDocumentsController.php`
4. Update `backend/app/Modules/Documents/Routes/api.php` to point to the module controllers.
5. Remove old global Documents files and update seeders / relations.

## Backend Actions (Results)
1. Move models:
   - `backend/app/Models/CourseResult.php` -> `backend/app/Modules/Results/Models/CourseResult.php`
   - `backend/app/Models/AcademicTerm.php` -> `backend/app/Modules/Results/Models/AcademicTerm.php`
   (Update namespaces to `App\Modules\Results\Models`)
2. Move resources:
   - `backend/app/Http/Resources/CourseResultResource.php` -> `backend/app/Modules/Results/Resources/CourseResultResource.php`
3. Create/Move module controllers:
   - `backend/app/Modules/Results/Controllers/ResultsController.php`
4. Update `backend/app/Modules/Results/Routes/api.php` to point to the module controllers.
5. Remove old global Results files and update seeders / relations.

## Frontend Actions (Documents)
1. Move views:
   - `frontend/src/views/DocumentsView.vue` -> `frontend/src/modules/documents/views/DocumentsView.vue`
   - `frontend/src/views/admin/AdminDocumentsView.vue` -> `frontend/src/modules/documents/views/AdminDocumentsView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/documents/services/documentsApi.js`
3. Update `frontend/src/modules/documents/routes.js` and `index.js`.

## Frontend Actions (Results)
1. Move views:
   - `frontend/src/views/StudentResultsView.vue` -> `frontend/src/modules/results/views/StudentResultsView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/results/services/resultsApi.js`
3. Update `frontend/src/modules/results/routes.js` and `index.js`.

## Verification
- Run `php artisan test` in `backend/`
- Run `npm run build` in `frontend/`

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-5-report.md`
