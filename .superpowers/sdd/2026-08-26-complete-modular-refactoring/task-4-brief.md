---
noteId: "63a8e7f0a17711f1ba16875512ddc7d8"
tags: []

---

﻿# Task 4: Complete Refactoring for Cms & Events Modules

## Objective
Relocate ALL CMS and Events domain code from global directories into:
1. `backend/app/Modules/Cms/` and `frontend/src/modules/cms/`
2. `backend/app/Modules/Events/` and `frontend/src/modules/events/`

## Backend Actions (CMS)
1. Move models:
   - `backend/app/Models/NewsCategory.php` -> `backend/app/Modules/Cms/Models/NewsCategory.php`
   - `backend/app/Models/NewsArticle.php` -> `backend/app/Modules/Cms/Models/NewsArticle.php`
   - `backend/app/Models/Announcement.php` -> `backend/app/Modules/Cms/Models/Announcement.php`
   (Update namespaces to `App\Modules\Cms\Models`)
2. Move resources:
   - `backend/app/Http/Resources/NewsCategoryResource.php` -> `backend/app/Modules/Cms/Resources/NewsCategoryResource.php`
   - `backend/app/Http/Resources/NewsResource.php` -> `backend/app/Modules/Cms/Resources/NewsResource.php`
   - `backend/app/Http/Resources/AnnouncementResource.php` -> `backend/app/Modules/Cms/Resources/AnnouncementResource.php`
3. Create/Move module controllers:
   - `backend/app/Modules/Cms/Controllers/CmsController.php`
   - `backend/app/Modules/Cms/Controllers/AdminCmsController.php`
4. Update `backend/app/Modules/Cms/Routes/api.php` to point to the module controllers.
5. Remove old global CMS files and update seeders / relations.

## Backend Actions (Events)
1. Move models:
   - `backend/app/Models/Event.php` -> `backend/app/Modules/Events/Models/Event.php`
   - `backend/app/Models/EventAttendee.php` -> `backend/app/Modules/Events/Models/EventAttendee.php`
   (Update namespaces to `App\Modules\Events\Models`)
2. Move resources:
   - `backend/app/Http/Resources/EventResource.php` -> `backend/app/Modules/Events/Resources/EventResource.php`
3. Create/Move module controllers:
   - `backend/app/Modules/Events/Controllers/EventsController.php`
   - `backend/app/Modules/Events/Controllers/AdminEventsController.php`
4. Update `backend/app/Modules/Events/Routes/api.php` to point to the module controllers.
5. Remove old global Events files and update seeders / relations.

## Frontend Actions (CMS)
1. Move views:
   - `frontend/src/views/NewsView.vue` -> `frontend/src/modules/cms/views/NewsView.vue`
   - `frontend/src/views/NewsDetailView.vue` -> `frontend/src/modules/cms/views/NewsDetailView.vue`
   - `frontend/src/views/admin/AdminCmsView.vue` -> `frontend/src/modules/cms/views/AdminCmsView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/cms/services/cmsApi.js`
3. Update `frontend/src/modules/cms/routes.js` and `index.js`.

## Frontend Actions (Events)
1. Move views:
   - `frontend/src/views/EventsView.vue` -> `frontend/src/modules/events/views/EventsView.vue`
   - `frontend/src/views/admin/AdminEventsView.vue` -> `frontend/src/modules/events/views/AdminEventsView.vue`
2. Create dedicated API client:
   - `frontend/src/modules/events/services/eventsApi.js`
3. Update `frontend/src/modules/events/routes.js` and `index.js`.

## Verification
- Run `php artisan test` in `backend/`
- Run `npm run build` in `frontend/`

## Output Contract
Report file: `.superpowers/sdd/2026-08-26-complete-modular-refactoring/task-4-report.md`
