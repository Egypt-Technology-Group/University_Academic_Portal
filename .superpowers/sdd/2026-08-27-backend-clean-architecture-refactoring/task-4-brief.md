---
noteId: "26ba2b10a19511f1ba16875512ddc7d8"
tags: []

---

﻿# Task 4: Refactor Cms & Events Modules (Thin Controllers, Form Requests, Services)

## Objective
Refactor `backend/app/Modules/Cms/` and `backend/app/Modules/Events/` so all controllers (`CmsController`, `AdminCmsController`, `EventsController`, `AdminEventsController`) are thin delegates. All validation moves into dedicated FormRequests, all file uploads, slugging, registration logic, and persistence orchestration move into dedicated services (`CmsService`, `EventsService`).

## Files to Create/Modify
- Create (CMS):
  - `backend/app/Modules/Cms/Requests/StoreNewsArticleRequest.php`
  - `backend/app/Modules/Cms/Requests/UpdateNewsArticleRequest.php`
  - `backend/app/Modules/Cms/Requests/StoreAnnouncementRequest.php`
  - `backend/app/Modules/Cms/Requests/UpdateAnnouncementRequest.php`
  - `backend/app/Modules/Cms/Services/CmsService.php`
- Modify (CMS):
  - `backend/app/Modules/Cms/Controllers/CmsController.php`
  - `backend/app/Modules/Cms/Controllers/AdminCmsController.php`

- Create (Events):
  - `backend/app/Modules/Events/Requests/RegisterEventAttendeeRequest.php`
  - `backend/app/Modules/Events/Requests/StoreEventRequest.php`
  - `backend/app/Modules/Events/Requests/UpdateEventRequest.php`
  - `backend/app/Modules/Events/Services/EventsService.php`
- Modify (Events):
  - `backend/app/Modules/Events/Controllers/EventsController.php`
  - `backend/app/Modules/Events/Controllers/AdminEventsController.php`

## Requirements
1. FormRequests validate required fields, multilingual content arrays, featured image / cover image uploads, and event registration details.
2. `CmsService` provides methods:
   - `getNews(array $filters)`, `getNewsArticle(string $slug)`
   - `getAnnouncements(array $filters)`
   - `createNewsArticle(array $data, $imageFile = null, ?User $author = null): NewsArticle`
   - `updateNewsArticle(NewsArticle $article, array $data, $imageFile = null): NewsArticle`
   - `deleteNewsArticle(NewsArticle $article): void`
   - `createAnnouncement(array $data): Announcement`
   - `updateAnnouncement(Announcement $announcement, array $data): Announcement`
   - `deleteAnnouncement(Announcement $announcement): void`
3. `EventsService` provides methods:
   - `getEvents(array $filters)`, `getEvent(string $slug)`
   - `registerAttendee(Event $event, array $data): EventAttendee`
   - `createEvent(array $data, $coverImageFile = null, ?User $organizer = null): Event`
   - `updateEvent(Event $event, array $data, $coverImageFile = null): Event`
   - `deleteEvent(Event $event): void`
4. Controllers:
   - Inject services and delegate immediately.
   - Zero inline validation, zero direct storage calls, zero DB operations in controllers.
5. Run `php artisan test` to verify all tests pass without regression.

## Output Contract
Report file: `.superpowers/sdd/2026-08-27-backend-clean-architecture-refactoring/task-4-report.md`
