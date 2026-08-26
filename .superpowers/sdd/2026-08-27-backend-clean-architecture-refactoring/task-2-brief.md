---
noteId: "8c773390a19411f1ba16875512ddc7d8"
tags: []

---

﻿# Task 2: Refactor Admissions Module (Thin Controllers, Form Requests, Services, Events)

## Objective
Refactor `backend/app/Modules/Admissions/` so all controllers (`AdmissionsController`, `AdminAdmissionsController`) are thin delegates. All validation moves into dedicated FormRequests, all file uploads, application numbering, workflow state transitions, and missing doc dispatches move into `AdmissionsService`.

## Files to Create/Modify
- Create:
  - `backend/app/Modules/Admissions/Requests/UpdateApplicationDecisionRequest.php`
  - `backend/app/Modules/Admissions/Requests/RequestMissingDocumentsRequest.php`
  - `backend/app/Modules/Admissions/Requests/StoreAdmissionCycleRequest.php`
  - `backend/app/Modules/Admissions/Requests/UpdateAdmissionCycleRequest.php`
  - `backend/app/Modules/Admissions/Services/AdmissionsService.php`
- Modify:
  - `backend/app/Modules/Admissions/Requests/SubmitApplicationRequest.php` (clean up and align)
  - `backend/app/Modules/Admissions/Controllers/AdmissionsController.php`
  - `backend/app/Modules/Admissions/Controllers/AdminAdmissionsController.php`

## Requirements
1. FormRequests validate required fields, decision statuses (`under_review`, `accepted`, `rejected`), document files, cycles dates, and return validated typed arrays.
2. `AdmissionsService` provides methods:
   - `getActiveCycles()`, `getAllCycles()`, `getCyclesWithStats()`
   - `createCycle(array $data): AdmissionCycle`, `updateCycle(AdmissionCycle $cycle, array $data): AdmissionCycle`, `deleteCycle(AdmissionCycle $cycle): void`
   - `submitApplication(array $data, array $uploadedFiles = []): Application` (generates application number, maps documents, saves uploads)
   - `trackApplication(string $appNumber, string $nationalId): ?Application`
   - `getApplications(array $filters)`
   - `updateApplicationDecision(Application $app, string $status, ?string $notes = null, ?User $reviewer = null): Application`
   - `requestMissingDocuments(Application $app, array $documentTypes, ?string $instructions = null): Application`
3. Controllers:
   - Inject `AdmissionsService`.
   - Methods: authorize, call service, return `ApplicationResource`, `AdmissionCycleResource`, or JsonResponse.
   - Zero inline `$request->validate()`, zero direct `Storage::put()`, zero DB transactions in controller.
4. Run `php artisan test` to verify all tests pass without regression.

## Output Contract
Report file: `.superpowers/sdd/2026-08-27-backend-clean-architecture-refactoring/task-2-report.md`
