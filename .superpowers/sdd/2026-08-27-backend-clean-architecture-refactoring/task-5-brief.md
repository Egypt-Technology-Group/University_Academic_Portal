---
noteId: "6c740d60a19511f1ba16875512ddc7d8"
tags: []

---

﻿# Task 5: Refactor Documents & Results Modules (Thin Controllers, Form Requests, Services)

## Objective
Refactor `backend/app/Modules/Documents/` and `backend/app/Modules/Results/` so all controllers (`DocumentsController`, `AdminDocumentsController`, `ResultsController`) are thin delegates. All validation moves into dedicated FormRequests, all file uploads, versioning, download counter tracking, student record lookup, and term GPA calculations move into dedicated services (`DocumentsService`, `ResultsService`).

## Files to Create/Modify
- Create (Documents):
  - `backend/app/Modules/Documents/Requests/StoreDownloadDocumentRequest.php`
  - `backend/app/Modules/Documents/Requests/UpdateDownloadDocumentRequest.php`
  - `backend/app/Modules/Documents/Services/DocumentsService.php`
- Modify (Documents):
  - `backend/app/Modules/Documents/Controllers/DocumentsController.php`
  - `backend/app/Modules/Documents/Controllers/AdminDocumentsController.php`

- Create (Results):
  - `backend/app/Modules/Results/Requests/InquireResultsRequest.php`
  - `backend/app/Modules/Results/Requests/SimulateRegistrationRequest.php`
  - `backend/app/Modules/Results/Services/ResultsService.php`
- Modify (Results):
  - `backend/app/Modules/Results/Controllers/ResultsController.php`

## Requirements
1. FormRequests validate required fields, file uploads (PDF/Docs), student ID numbers, national ID numbers, and course registration codes.
2. `DocumentsService` provides methods:
   - `getDocuments(array $filters)`
   - `incrementDownload(DownloadDocument $document): DownloadDocument`
   - `createDocument(array $data, $file = null, ?User $uploader = null): DownloadDocument`
   - `updateDocument(DownloadDocument $doc, array $data, $file = null): DownloadDocument`
   - `deleteDocument(DownloadDocument $doc): void`
3. `ResultsService` provides methods:
   - `inquireResults(string $studentIdNumber, string $nationalId, ?int $termId = null): array` (formats GPA, student profile, course results)
   - `simulateRegistration(StudentRecord $student, array $courseCodes): array`
4. Controllers:
   - Inject services and delegate immediately.
   - Zero inline validation, zero direct storage calls, zero DB operations in controllers.
5. Run `php artisan test` to verify all tests pass without regression.

## Output Contract
Report file: `.superpowers/sdd/2026-08-27-backend-clean-architecture-refactoring/task-5-report.md`
