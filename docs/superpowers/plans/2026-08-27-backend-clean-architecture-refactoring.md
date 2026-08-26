---
noteId: "f1ceea90a19311f1ba16875512ddc7d8"
tags: []

---

﻿# Backend Clean Architecture & Thin Controller Refactoring Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor every Backend module in Laravel to adhere strictly to Clean Architecture, SOLID principles, and Thin Controllers:
1. **Controllers**: Delegate immediately to Services/Actions; handle only authorization, request-to-service mapping, and response/resource rendering.
2. **Form Requests**: Encapsulate all input validation, custom messages, and typed input retrieval.
3. **Services / Actions**: Own all business logic, orchestration, model state transitions, file storage, and side-effects.
4. **Resources / Transformers**: Format API responses consistently using JsonResources with multilingual handling.
5. **Events & Listeners / Audit Logs**: Dispatched automatically upon key state mutations (e.g. application submission, decision updates, content publishing).
6. **Models**: Pure domain entities with relationships, casts, and query scopes — zero business workflows.

---

## Tasks Breakdown by Module

### Task 1: Refactor `AcademicStructure` Module (Services, Requests, Actions)
**Components:**
- Create Form Requests:
  - `App\Modules\AcademicStructure\Requests\StoreCollegeRequest`
  - `App\Modules\AcademicStructure\Requests\UpdateCollegeRequest`
  - `App\Modules\AcademicStructure\Requests\StoreDepartmentRequest`
  - `App\Modules\AcademicStructure\Requests\UpdateDepartmentRequest`
  - `App\Modules\AcademicStructure\Requests\StoreProgramRequest`
  - `App\Modules\AcademicStructure\Requests\UpdateProgramRequest`
  - `App\Modules\AcademicStructure\Requests\StoreFacultyRequest`
  - `App\Modules\AcademicStructure\Requests\UpdateFacultyRequest`
- Create Service / Actions:
  - `App\Modules\AcademicStructure\Services\AcademicStructureService` (handles slug generation, file uploads for study plans/CVs, CRUD orchestration)
- Refactor Controllers to be ultra-thin:
  - `AcademicStructureController.php`
  - `AdminAcademicStructureController.php`

- [ ] **Step 1: Create Form Requests for College, Department, Program, Faculty**
- [ ] **Step 2: Implement `AcademicStructureService`**
- [ ] **Step 3: Refactor Controllers to inject and call `AcademicStructureService`**
- [ ] **Step 4: Run `php artisan test` to verify zero regression**

---

### Task 2: Refactor `Admissions` Module (Services, Requests, DTOs, Events)
**Components:**
- Create Form Requests:
  - `App\Modules\Admissions\Requests\SubmitApplicationRequest` (already exists, ensure clean)
  - `App\Modules\Admissions\Requests\UpdateApplicationDecisionRequest`
  - `App\Modules\Admissions\Requests\RequestMissingDocumentsRequest`
  - `App\Modules\Admissions\Requests\StoreAdmissionCycleRequest`
  - `App\Modules\Admissions\Requests\UpdateAdmissionCycleRequest`
- Create Service / Actions:
  - `App\Modules\Admissions\Services\ApplicationSubmissionService` (handles application numbering, document uploads, cycle matching)
  - `App\Modules\Admissions\Services\ApplicationReviewService` (handles decision state transitions, notes, and notifications)
- Refactor Controllers:
  - `AdmissionsController.php`
  - `AdminAdmissionsController.php`

- [ ] **Step 1: Create Form Requests for Admissions workflows**
- [ ] **Step 2: Implement `ApplicationSubmissionService` and `ApplicationReviewService`**
- [ ] **Step 3: Refactor `AdmissionsController` and `AdminAdmissionsController`**
- [ ] **Step 4: Run `php artisan test` to verify zero regression**

---

### Task 3: Refactor `AcademicServices` Module (Services, Requests, Actions)
**Components:**
- Create Form Requests:
  - `App\Modules\AcademicServices\Requests\SubmitStudentServiceRequest`
  - `App\Modules\AcademicServices\Requests\StoreExamScheduleRequest`
  - `App\Modules\AcademicServices\Requests\UpdateExamScheduleRequest`
  - `App\Modules\AcademicServices\Requests\IssueOfficialStatementRequest`
- Create Service / Actions:
  - `App\Modules\AcademicServices\Services\StudentServiceManagementService` (handles request submission, number generation, status updates)
  - `App\Modules\AcademicServices\Services\OfficialStatementService` (handles QR verification hash generation, digital signatures, statement issuance)
  - `App\Modules\AcademicServices\Services\ExamScheduleService`
- Refactor Controllers:
  - `AcademicServicesController.php`
  - `AdminAcademicServicesController.php`

- [ ] **Step 1: Create Form Requests for Academic Services**
- [ ] **Step 2: Implement Services (`StudentServiceManagementService`, `OfficialStatementService`, `ExamScheduleService`)**
- [ ] **Step 3: Refactor Controllers**
- [ ] **Step 4: Run `php artisan test` to verify zero regression**

---

### Task 4: Refactor `Cms` & `Events` Modules (Services, Requests, Actions)
**Components:**
- CMS Requests & Services:
  - `App\Modules\Cms\Requests\StoreNewsArticleRequest`
  - `App\Modules\Cms\Requests\UpdateNewsArticleRequest`
  - `App\Modules\Cms\Requests\StoreAnnouncementRequest`
  - `App\Modules\Cms\Requests\UpdateAnnouncementRequest`
  - `App\Modules\Cms\Services\CmsContentService` (handles slugging, featured image storage, publish state)
- Events Requests & Services:
  - `App\Modules\Events\Requests\RegisterEventAttendeeRequest`
  - `App\Modules\Events\Requests\StoreEventRequest`
  - `App\Modules\Events\Requests\UpdateEventRequest`
  - `App\Modules\Events\Services\EventRegistrationService` (handles capacity check, registration confirmation)
  - `App\Modules\Events\Services\EventManagementService` (handles event creation/update, cover image handling)
- Refactor Controllers:
  - `CmsController.php`, `AdminCmsController.php`
  - `EventsController.php`, `AdminEventsController.php`

- [ ] **Step 1: Create Requests and Services for CMS**
- [ ] **Step 2: Create Requests and Services for Events**
- [ ] **Step 3: Refactor Controllers to thin proxies**
- [ ] **Step 4: Run `php artisan test` to verify zero regression**

---

### Task 5: Refactor `Documents` & `Results` Modules (Services, Requests, Actions)
**Components:**
- Documents Requests & Services:
  - `App\Modules\Documents\Requests\StoreDownloadDocumentRequest`
  - `App\Modules\Documents\Requests\UpdateDownloadDocumentRequest`
  - `App\Modules\Documents\Services\DocumentRepositoryService` (handles file uploads, download counter increment, versioning)
- Results Requests & Services:
  - `App\Modules\Results\Requests\InquireResultsRequest`
  - `App\Modules\Results\Requests\SimulateRegistrationRequest`
  - `App\Modules\Results\Services\StudentResultInquiryService` (handles student national ID & code lookup, GPA calculation, term grouping)
- Refactor Controllers:
  - `DocumentsController.php`, `AdminDocumentsController.php`
  - `ResultsController.php`

- [ ] **Step 1: Create Requests and Services for Documents**
- [ ] **Step 2: Create Requests and Services for Results**
- [ ] **Step 3: Refactor Controllers to thin proxies**
- [ ] **Step 4: Run `php artisan test` to verify zero regression**

---

### Task 6: Final Architecture Audit & Full Test Suite Verification
**Objectives:**
- Audit all controllers across every module to guarantee that no inline validation (`$request->validate()`), direct model query orchestration, file manipulation, or business workflow logic remains.
- Run complete test suite: `php artisan test` (103+ tests).
- Run frontend build: `npm run build`.

- [ ] **Step 1: Final code audit across all controller methods**
- [ ] **Step 2: Execute backend tests and frontend build**
