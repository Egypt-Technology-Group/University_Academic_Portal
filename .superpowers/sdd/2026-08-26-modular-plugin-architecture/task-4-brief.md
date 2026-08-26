---
noteId: "3cf1fe10a16f11f1ba16875512ddc7d8"
tags: []

---

﻿# Task 4: Backend Domain Modularization (Phase 2: Academic Services, CMS, Events, Documents, Results)

## Objective
Encapsulate the remaining 5 business domain modules implementing `ModuleInterface` with explicit dependency metadata and table ownership:
1. `academic-services`: Owns `student_records`, `student_service_requests`, `exam_schedules`, `official_statements`. Dependencies: `['academic-structure']`.
2. `cms`: Owns `news_categories`, `news_articles`, `announcements`. Dependencies: `[]`.
3. `events`: Owns `events`, `event_attendees`. Dependencies: `[]`.
4. `documents`: Owns `download_documents`. Dependencies: `[]`.
5. `results`: Owns `course_results`, `academic_terms`. Dependencies: `['academic-structure', 'academic-services']`.

## Files to Create/Modify
- Create: `backend/app/Modules/AcademicServices/AcademicServicesModule.php`
- Create: `backend/app/Modules/AcademicServices/Routes/api.php`
- Create: `backend/app/Modules/Cms/CmsModule.php`
- Create: `backend/app/Modules/Cms/Routes/api.php`
- Create: `backend/app/Modules/Events/EventsModule.php`
- Create: `backend/app/Modules/Events/Routes/api.php`
- Create: `backend/app/Modules/Documents/DocumentsModule.php`
- Create: `backend/app/Modules/Documents/Routes/api.php`
- Create: `backend/app/Modules/Results/ResultsModule.php`
- Create: `backend/app/Modules/Results/Routes/api.php`
- Modify: `backend/app/Core/Providers/ModuleServiceProvider.php` (register all 7 domain modules)
- Modify: `backend/routes/api.php` (delegate route registration to module route files)
- Create: `backend/tests/Feature/Modules/AllModulesTest.php`

## Requirements
1. Every module implements `ModuleInterface` with ID, metadata translations, owned tables list, dependencies array, and route definitions.
2. Every module's routes are wrapped with `module.enabled:{moduleId}`.
3. Verify `results` depends on `academic-structure` and `academic-services`.
4. Run full test suite to ensure 100% regression-free operation.

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-modular-plugin-architecture/task-4-report.md`
