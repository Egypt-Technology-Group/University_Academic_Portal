---
noteId: "f2621060a17311f1ba16875512ddc7d8"
tags: []

---

﻿# Task 1: Complete Refactoring for AcademicStructure Module

## Objective
Relocate ALL AcademicStructure domain code from global directories into `backend/app/Modules/AcademicStructure/` and `frontend/src/modules/academic-structure/`.

## Backend Actions
1. Move models:
   - `backend/app/Models/College.php` -> `backend/app/Modules/AcademicStructure/Models/College.php`
   - `backend/app/Models/Department.php` -> `backend/app/Modules/AcademicStructure/Models/Department.php`
   - `backend/app/Models/Program.php` -> `backend/app/Modules/AcademicStructure/Models/Program.php`
   - `backend/app/Models/FacultyProfile.php` -> `backend/app/Modules/AcademicStructure/Models/FacultyProfile.php`
   (Update namespaces to `App\Modules\AcademicStructure\Models`)
2. Move resources:
   - `backend/app/Http/Resources/CollegeResource.php` -> `backend/app/Modules/AcademicStructure/Resources/CollegeResource.php`
   - `backend/app/Http/Resources/DepartmentResource.php` -> `backend/app/Modules/AcademicStructure/Resources/DepartmentResource.php`
   - `backend/app/Http/Resources/ProgramResource.php` -> `backend/app/Modules/AcademicStructure/Resources/ProgramResource.php`
   - `backend/app/Http/Resources/FacultyResource.php` -> `backend/app/Modules/AcademicStructure/Resources/FacultyResource.php`
3. Create module controllers:
   - `backend/app/Modules/AcademicStructure/Controllers/AcademicStructureController.php`
   - `backend/app/Modules/AcademicStructure/Controllers/AdminAcademicStructureController.php`
4. Update `backend/app/Modules/AcademicStructure/Routes/api.php` to point to the module controllers.
5. Update relations and seeders referencing these models.

## Frontend Actions
1. Move views:
   - `frontend/src/views/CollegesView.vue` -> `frontend/src/modules/academic-structure/views/CollegesView.vue`
   - `frontend/src/views/CollegeDetailView.vue` -> `frontend/src/modules/academic-structure/views/CollegeDetailView.vue`
   - `frontend/src/views/ProgramsView.vue` -> `frontend/src/modules/academic-structure/views/ProgramsView.vue`
   - `frontend/src/views/ProgramDetailView.vue` -> `frontend/src/modules/academic-structure/views/ProgramDetailView.vue`
   - `frontend/src/views/FacultyDirectoryView.vue` -> `frontend/src/modules/academic-structure/views/FacultyDirectoryView.vue`
   - `frontend/src/views/admin/AdminAcademicStructureView.vue` -> `frontend/src/modules/academic-structure/views/AdminAcademicStructureView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/academic-structure/services/academicStructureApi.js`
3. Update `frontend/src/modules/academic-structure/routes.js` and `index.js`.
4. Update imports across all related views.

## Verification
- Run `php artisan test` in `backend/`
- Run `npm run build` in `frontend/`

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-1-report.md`
