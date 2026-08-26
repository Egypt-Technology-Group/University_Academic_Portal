---
noteId: "f927b6f0a19311f1ba16875512ddc7d8"
tags: []

---

﻿# Task 1: Refactor AcademicStructure Module (Thin Controllers, Form Requests, Services)

## Objective
Refactor `backend/app/Modules/AcademicStructure/` so all controllers (`AcademicStructureController`, `AdminAcademicStructureController`) are thin delegates. All validation moves into dedicated FormRequests, all file uploads, slugging, and persistence orchestration move into `AcademicStructureService`.

## Files to Create/Modify
- Create:
  - `backend/app/Modules/AcademicStructure/Requests/StoreCollegeRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/UpdateCollegeRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/StoreDepartmentRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/UpdateDepartmentRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/StoreProgramRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/UpdateProgramRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/StoreFacultyRequest.php`
  - `backend/app/Modules/AcademicStructure/Requests/UpdateFacultyRequest.php`
  - `backend/app/Modules/AcademicStructure/Services/AcademicStructureService.php`
- Modify:
  - `backend/app/Modules/AcademicStructure/Controllers/AcademicStructureController.php`
  - `backend/app/Modules/AcademicStructure/Controllers/AdminAcademicStructureController.php`

## Requirements
1. FormRequests validate required fields, multilingual arrays or strings, unique slugs, file mime types (PDF/images), and return validated typed arrays.
2. `AcademicStructureService` provides methods:
   - `getColleges()`, `getCollege(string $slug)`
   - `getDepartments(array $filters)`, `getPrograms(array $filters)`, `getProgram(string $slug)`
   - `getFaculty(array $filters)`
   - `createCollege(array $data, $bannerFile = null): College`
   - `updateCollege(College $college, array $data, $bannerFile = null): College`
   - `deleteCollege(College $college): void`
   - `createDepartment(array $data): Department`
   - `updateDepartment(Department $dept, array $data): Department`
   - `deleteDepartment(Department $dept): void`
   - `createProgram(array $data, $studyPlanFile = null): Program`
   - `updateProgram(Program $prog, array $data, $studyPlanFile = null): Program`
   - `deleteProgram(Program $prog): void`
   - `createFaculty(array $data, $cvFile = null): FacultyProfile`
   - `updateFaculty(FacultyProfile $fac, array $data, $cvFile = null): FacultyProfile`
   - `deleteFaculty(FacultyProfile $fac): void`
3. Controllers:
   - Inject `AcademicStructureService`.
   - Methods: authorize, call service, return `CollegeResource`, `ProgramResource`, `FacultyResource`, or JsonResponse.
   - Zero inline `$request->validate()`, zero direct `Storage::put()`, zero DB transactions in controller.
4. Run `php artisan test` to verify all tests pass without regression.

## Output Contract
Report file: `.superpowers/sdd/2026-08-27-backend-clean-architecture-refactoring/task-1-report.md`
