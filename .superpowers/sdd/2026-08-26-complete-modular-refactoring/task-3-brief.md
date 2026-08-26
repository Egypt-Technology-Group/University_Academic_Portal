---
noteId: "64534e30a17611f1ba16875512ddc7d8"
tags: []

---

﻿# Task 3: Complete Refactoring for AcademicServices Module

## Objective
Relocate ALL AcademicServices domain code from global directories into `backend/app/Modules/AcademicServices/` and `frontend/src/modules/academic-services/`.

## Backend Actions
1. Move models:
   - `backend/app/Models/StudentRecord.php` -> `backend/app/Modules/AcademicServices/Models/StudentRecord.php`
   - `backend/app/Models/StudentServiceRequest.php` -> `backend/app/Modules/AcademicServices/Models/StudentServiceRequest.php`
   - `backend/app/Models/ExamSchedule.php` -> `backend/app/Modules/AcademicServices/Models/ExamSchedule.php`
   - `backend/app/Models/OfficialStatement.php` -> `backend/app/Modules/AcademicServices/Models/OfficialStatement.php`
   (Update namespaces to `App\Modules\AcademicServices\Models`)
2. Move resources:
   - `backend/app/Http/Resources/StudentRecordResource.php` -> `backend/app/Modules/AcademicServices/Resources/StudentRecordResource.php`
3. Create/Move module controllers:
   - `backend/app/Modules/AcademicServices/Controllers/AcademicServicesController.php`
   - `backend/app/Modules/AcademicServices/Controllers/AdminAcademicServicesController.php`
4. Update `backend/app/Modules/AcademicServices/Routes/api.php` to point to the module controllers.
5. Remove old global AcademicServices files and update relations.

## Frontend Actions
1. Move views:
   - `frontend/src/views/admin/AdminAcademicServicesView.vue` -> `frontend/src/modules/academic-services/views/AdminAcademicServicesView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/academic-services/services/academicServicesApi.js`
3. Update `frontend/src/modules/academic-services/routes.js` and `index.js`.
4. Update imports across all related views.

## Verification
- Run `php artisan test` in `backend/`
- Run `npm run build` in `frontend/`

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-3-report.md`
