---
noteId: "e91bf2f0a17311f1ba16875512ddc7d8"
tags: []

---

﻿# Complete Modular Domain Refactoring Plan (Backend & Frontend)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Strictly refactor the backend and frontend into 100% self-contained, domain-encapsulated plug-in modules. Move ALL domain-specific Controllers, Models, Resources, Form Requests, Services, API routes, migrations, and tests inside `backend/app/Modules/{Module}`, and ALL domain-specific Views, API logic, routes, and components inside `frontend/src/modules/{module}`. Keep ONLY genuine cross-cutting primitives (Auth/RBAC, User, AuditLog, SiteSettings, Base Layouts, Dialog/Toast, UI components, utilities) in core.

## Target Structure

### Backend Modules Structure:
```
backend/app/Modules/{Module}/
├── Controllers/         # Public & Admin Controllers for this domain
├── Models/              # Domain Eloquent Models
├── Resources/           # Domain API JsonResources
├── Requests/            # Domain FormRequests & Validation
├── Services/            # Domain Business Services
├── Routes/              # api.php (Public + Admin routes)
├── Tests/               # Unit & Feature tests
└── {Module}Module.php   # Module manifest & lifecycle hooks
```

### Frontend Modules Structure:
```
frontend/src/modules/{module}/
├── views/               # Domain Views (Public & Admin views)
├── components/          # Domain-specific UI subcomponents
├── services/            # Domain API service methods
├── routes.js            # Public & Admin route definitions
└── index.js             # Module manifest & navigation export
```

---

## Tasks

### Task 1: Refactor `AcademicStructure` Module (Backend & Frontend)
**Backend Moves:**
- Models: `College.php`, `Department.php`, `Program.php`, `FacultyProfile.php` -> `backend/app/Modules/AcademicStructure/Models/`
- Resources: `CollegeResource.php`, `DepartmentResource.php`, `ProgramResource.php`, `FacultyResource.php` -> `backend/app/Modules/AcademicStructure/Resources/`
- Controllers: `AcademicStructureController.php` -> `backend/app/Modules/AcademicStructure/Controllers/`
- Tests: `backend/app/Modules/AcademicStructure/Tests/`

**Frontend Moves:**
- Views: `CollegesView.vue`, `CollegeDetailView.vue`, `ProgramsView.vue`, `ProgramDetailView.vue`, `FacultyDirectoryView.vue`, `AdminAcademicStructureView.vue` -> `frontend/src/modules/academic-structure/views/`
- Services: `frontend/src/modules/academic-structure/services/academicStructureApi.js`
- Routes: `frontend/src/modules/academic-structure/routes.js`

- [ ] **Step 1: Move Backend models, resources, controllers into `app/Modules/AcademicStructure/` and update namespaces**
- [ ] **Step 2: Move Frontend views and API methods into `modules/academic-structure/` and update imports**
- [ ] **Step 3: Run backend and frontend test/build verification**

---

### Task 2: Refactor `Admissions` Module (Backend & Frontend)
**Backend Moves:**
- Models: `AdmissionCycle.php`, `Application.php`, `ApplicationDocument.php` -> `backend/app/Modules/Admissions/Models/`
- Resources: `AdmissionCycleResource.php`, `ApplicationResource.php`, `ApplicationDocumentResource.php` -> `backend/app/Modules/Admissions/Resources/`
- Requests: `SubmitApplicationRequest.php` -> `backend/app/Modules/Admissions/Requests/`
- Controllers: `AdmissionsController.php` -> `backend/app/Modules/Admissions/Controllers/`
- Tests: `backend/app/Modules/Admissions/Tests/`

**Frontend Moves:**
- Views: `AdmissionsView.vue`, `ApplicationTrackView.vue`, `AdminAdmissionsView.vue` -> `frontend/src/modules/admissions/views/`
- Services: `frontend/src/modules/admissions/services/admissionsApi.js`
- Routes: `frontend/src/modules/admissions/routes.js`

- [ ] **Step 1: Move Backend models, resources, requests, and controllers into `app/Modules/Admissions/` and update namespaces**
- [ ] **Step 2: Move Frontend views and API methods into `modules/admissions/` and update imports**
- [ ] **Step 3: Run backend and frontend test/build verification**

---

### Task 3: Refactor `AcademicServices` Module (Backend & Frontend)
**Backend Moves:**
- Models: `StudentRecord.php`, `StudentServiceRequest.php`, `ExamSchedule.php`, `OfficialStatement.php` -> `backend/app/Modules/AcademicServices/Models/`
- Resources: `StudentRecordResource.php` -> `backend/app/Modules/AcademicServices/Resources/`
- Controllers: `AcademicServicesController.php` -> `backend/app/Modules/AcademicServices/Controllers/`
- Tests: `backend/app/Modules/AcademicServices/Tests/`

**Frontend Moves:**
- Views: `AdminAcademicServicesView.vue` -> `frontend/src/modules/academic-services/views/`
- Services: `frontend/src/modules/academic-services/services/academicServicesApi.js`
- Routes: `frontend/src/modules/academic-services/routes.js`

- [ ] **Step 1: Move Backend models, resources, and controllers into `app/Modules/AcademicServices/`**
- [ ] **Step 2: Move Frontend views and API methods into `modules/academic-services/`**
- [ ] **Step 3: Run backend and frontend test/build verification**

---

### Task 4: Refactor `Cms` & `Events` Modules (Backend & Frontend)
**Backend Moves:**
- CMS Models: `NewsCategory.php`, `NewsArticle.php`, `Announcement.php` -> `backend/app/Modules/Cms/Models/`
- CMS Resources: `NewsCategoryResource.php`, `NewsResource.php`, `AnnouncementResource.php` -> `backend/app/Modules/Cms/Resources/`
- CMS Controllers: `CmsController.php` -> `backend/app/Modules/Cms/Controllers/`
- Events Models: `Event.php`, `EventAttendee.php` -> `backend/app/Modules/Events/Models/`
- Events Resources: `EventResource.php` -> `backend/app/Modules/Events/Resources/`
- Events Controllers: `EventsController.php` -> `backend/app/Modules/Events/Controllers/`

**Frontend Moves:**
- CMS Views: `NewsView.vue`, `NewsDetailView.vue`, `AdminCmsView.vue` -> `frontend/src/modules/cms/views/`
- CMS Services: `frontend/src/modules/cms/services/cmsApi.js`
- Events Views: `EventsView.vue`, `AdminEventsView.vue` -> `frontend/src/modules/events/views/`
- Events Services: `frontend/src/modules/events/services/eventsApi.js`

- [ ] **Step 1: Modularize CMS backend & frontend**
- [ ] **Step 2: Modularize Events backend & frontend**
- [ ] **Step 3: Run backend and frontend test/build verification**

---

### Task 5: Refactor `Documents` & `Results` Modules (Backend & Frontend)
**Backend Moves:**
- Documents Models: `DownloadDocument.php` -> `backend/app/Modules/Documents/Models/`
- Documents Resources: `DocumentResource.php` -> `backend/app/Modules/Documents/Resources/`
- Documents Controllers: `DocumentsController.php` -> `backend/app/Modules/Documents/Controllers/`
- Results Models: `CourseResult.php`, `AcademicTerm.php` -> `backend/app/Modules/Results/Models/`
- Results Resources: `CourseResultResource.php` -> `backend/app/Modules/Results/Resources/`
- Results Controllers: `ResultsController.php` -> `backend/app/Modules/Results/Controllers/`

**Frontend Moves:**
- Documents Views: `DocumentsView.vue`, `AdminDocumentsView.vue` -> `frontend/src/modules/documents/views/`
- Documents Services: `frontend/src/modules/documents/services/documentsApi.js`
- Results Views: `StudentResultsView.vue` -> `frontend/src/modules/results/views/`
- Results Services: `frontend/src/modules/results/services/resultsApi.js`

- [ ] **Step 1: Modularize Documents backend & frontend**
- [ ] **Step 2: Modularize Results backend & frontend**
- [ ] **Step 3: Run backend and frontend test/build verification**

---

### Task 6: Core Cleanup, Router Synchronization & Final End-to-End Verification
**Objectives:**
- Audit `backend/app/Models` and `backend/app/Http/Controllers`: ensure only `User.php`, `SiteSetting.php`, `AuditLog.php`, `AuthController.php`, `SiteSettingsController.php`, `ModuleManagementController.php`, `AdminDashboardController.php`, `AuditLogController.php` remain in core.
- Audit `frontend/src/views`: ensure only core views (`HomeView.vue`, `NotFoundView.vue`, `ModuleDisabledView.vue`, `AdminDashboardView.vue`, `AdminSettingsView.vue`, `AdminModulesView.vue`, `AdminAuditTrailView.vue`, `AdminLoginView.vue`) remain in core views folder.
- Clean up `frontend/src/services/api.js` to serve as the unified API gateway bridging module services.
- Execute full test suite `php artisan test` and `npm run build`.

- [ ] **Step 1: Clean up global directories and audit residual domain code**
- [ ] **Step 2: Run backend tests and frontend build**
- [ ] **Step 3: Verify all public and admin routes end-to-end**
