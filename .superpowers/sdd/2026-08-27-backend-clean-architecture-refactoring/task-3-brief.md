---
noteId: "d608f8e0a19411f1ba16875512ddc7d8"
tags: []

---

﻿# Task 3: Refactor AcademicServices Module (Thin Controllers, Form Requests, Services)

## Objective
Refactor `backend/app/Modules/AcademicServices/` so all controllers (`AcademicServicesController`, `AdminAcademicServicesController`) are thin delegates. All validation moves into dedicated FormRequests, all file uploads, QR verification hashes, request processing, and statement issuance move into dedicated services (`AcademicServicesService`).

## Files to Create/Modify
- Create:
  - `backend/app/Modules/AcademicServices/Requests/SubmitStudentServiceRequest.php`
  - `backend/app/Modules/AcademicServices/Requests/UpdateStudentServiceRequest.php`
  - `backend/app/Modules/AcademicServices/Requests/StoreExamScheduleRequest.php`
  - `backend/app/Modules/AcademicServices/Requests/UpdateExamScheduleRequest.php`
  - `backend/app/Modules/AcademicServices/Requests/IssueOfficialStatementRequest.php`
  - `backend/app/Modules/AcademicServices/Services/AcademicServicesService.php`
- Modify:
  - `backend/app/Modules/AcademicServices/Controllers/AcademicServicesController.php`
  - `backend/app/Modules/AcademicServices/Controllers/AdminAcademicServicesController.php`

## Requirements
1. FormRequests validate required fields, service types (`enrollment_cert`, `transcript`, `grade_appeal`, `tuition_deferral`), exam timings, and document upload files.
2. `AcademicServicesService` provides methods:
   - `getRequests(array $filters)`
   - `submitRequest(array $data): StudentServiceRequest` (generates request number)
   - `updateRequestStatus(StudentServiceRequest $req, array $data, ?User $admin = null): StudentServiceRequest`
   - `deleteRequest(StudentServiceRequest $req): void`
   - `getExamSchedules(array $filters)`
   - `createExamSchedule(array $data, $timetableFile = null): ExamSchedule`
   - `updateExamSchedule(ExamSchedule $exam, array $data, $timetableFile = null): ExamSchedule`
   - `deleteExamSchedule(ExamSchedule $exam): void`
   - `getOfficialStatements(array $filters)`
   - `issueOfficialStatement(array $data, $documentFile = null, ?User $issuer = null): OfficialStatement` (generates QR verification code and digital hash)
3. Controllers:
   - Inject `AcademicServicesService`.
   - Methods: authorize, call service, return JSON responses or resources.
   - Zero inline `$request->validate()`, zero direct `Storage::put()`, zero DB transactions in controller.
4. Run `php artisan test` to verify all tests pass without regression.

## Output Contract
Report file: `.superpowers/sdd/2026-08-27-backend-clean-architecture-refactoring/task-3-report.md`
