---
noteId: "b2eee070a16e11f1ba16875512ddc7d8"
tags: []

---

﻿# Task 3: Backend Domain Modularization (Phase 1: Academic Structure & Admissions)

## Objective
Encapsulate the first two core business domains into autonomous module classes implementing `ModuleInterface`:
1. `academic-structure`: Owns `colleges`, `departments`, `programs`, `faculty_profiles`. Zero domain dependencies.
2. `admissions`: Owns `admission_cycles`, `applications`, `application_documents`. Explicitly declares dependency on `academic-structure`.

## Files to Create/Modify
- Create: `backend/app/Modules/AcademicStructure/AcademicStructureModule.php`
- Create: `backend/app/Modules/AcademicStructure/Routes/api.php`
- Create: `backend/app/Modules/Admissions/AdmissionsModule.php`
- Create: `backend/app/Modules/Admissions/Routes/api.php`
- Modify: `backend/app/Core/Providers/ModuleServiceProvider.php` (register `AcademicStructureModule` and `AdmissionsModule`)
- Modify: `backend/routes/api.php` (move relevant routes into module route files or bridge them)
- Create: `backend/tests/Feature/Modules/AcademicStructureModuleTest.php`
- Create: `backend/tests/Feature/Modules/AdmissionsModuleTest.php`

## Requirements
1. `AcademicStructureModule`:
   - ID: `'academic-structure'`
   - Owned tables: `['colleges', 'departments', 'programs', 'faculty_profiles']`
   - Dependencies: `[]`
   - Route path: `app/Modules/AcademicStructure/Routes/api.php`
   - Wraps routes with `module.enabled:academic-structure`
2. `AdmissionsModule`:
   - ID: `'admissions'`
   - Owned tables: `['admission_cycles', 'applications', 'application_documents']`
   - Dependencies: `['academic-structure']`
   - Route path: `app/Modules/Admissions/Routes/api.php`
   - Wraps routes with `module.enabled:admissions`
3. Tests:
   - Verify public & admin routes for colleges/programs/faculty are accessible when `academic-structure` is enabled and return 404 when disabled.
   - Verify admissions routes are accessible when enabled and return 404 when disabled.
   - Verify trying to disable `academic-structure` while `admissions` is enabled is blocked via `ModuleManager::canDisable('academic-structure')` returning `false`.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-3-report.md`
