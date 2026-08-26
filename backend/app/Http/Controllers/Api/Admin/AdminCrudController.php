<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\NewsResource;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCrudController extends Controller
{
    // News Articles
    public function storeNews(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:news_categories,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'excerpt_ar' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'body_ar' => 'required|string',
            'body_en' => 'required|string',
            'featured_image' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $slug = Str::slug($validated['title_en']).'-'.rand(100, 999);

        $article = NewsArticle::create([
            'category_id' => $validated['category_id'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'slug' => $slug,
            'excerpt' => ['ar' => $validated['excerpt_ar'] ?? '', 'en' => $validated['excerpt_en'] ?? ''],
            'body' => ['ar' => $validated['body_ar'], 'en' => $validated['body_en']],
            'featured_image' => $validated['featured_image'] ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
            'is_featured' => $validated['is_featured'] ?? false,
            'published_at' => now(),
            'views_count' => 0,
        ]);

        AuditLog::record(
            action: 'create',
            auditable: $article,
            oldValues: null,
            newValues: ['title_ar' => $validated['title_ar'], 'title_en' => $validated['title_en'], 'category_id' => $validated['category_id']],
            module: 'cms',
            descriptionAr: "نشر خبر صحفي جديد: {$validated['title_ar']}",
            descriptionEn: "Published new news article: {$validated['title_en']}",
            severity: 'info',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'News article published successfully.',
            'data' => new NewsResource($article),
        ], 201);
    }

    public function updateNews(Request $request, int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $oldValues = $article->only(['title', 'excerpt', 'category_id', 'is_featured']);

        $validated = $request->validate([
            'category_id' => 'sometimes|nullable|integer',
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'excerpt_ar' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'body_ar' => 'sometimes|required|string',
            'body_en' => 'sometimes|required|string',
            'featured_image' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        if (isset($validated['category_id'])) $article->category_id = $validated['category_id'];
        if (isset($validated['title_ar'])) $article->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $article->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['excerpt_ar'])) $article->setTranslation('excerpt', 'ar', $validated['excerpt_ar']);
        if (isset($validated['excerpt_en'])) $article->setTranslation('excerpt', 'en', $validated['excerpt_en']);
        if (isset($validated['body_ar'])) $article->setTranslation('body', 'ar', $validated['body_ar']);
        if (isset($validated['body_en'])) $article->setTranslation('body', 'en', $validated['body_en']);
        if (isset($validated['featured_image'])) $article->featured_image = $validated['featured_image'];
        if (isset($validated['is_featured'])) $article->is_featured = (bool) $validated['is_featured'];

        $article->save();

        AuditLog::record(
            action: 'update',
            auditable: $article,
            oldValues: $oldValues,
            newValues: $article->only(['title', 'excerpt', 'category_id', 'is_featured']),
            module: 'cms',
            descriptionAr: "تحديث وتعديل بيانات الخبر: {$article->title}",
            descriptionEn: "Updated news article: {$article->title}",
            severity: 'info',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully.',
            'data' => new NewsResource($article),
        ]);
    }

    public function deleteNews(int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $title = $article->title;
        $article->delete();

        AuditLog::record(
            action: 'delete',
            auditable: 'App\Models\NewsArticle',
            oldValues: ['id' => $id, 'title' => $title],
            newValues: null,
            module: 'cms',
            descriptionAr: "حذف الخبر الصحفي: {$title}",
            descriptionEn: "Deleted news article: {$title}",
            severity: 'warning',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'News article removed successfully.',
        ]);
    }

    // Announcements
    public function storeAnnouncement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'target_audience' => 'required|in:all,students,faculty,public',
            'priority' => 'required|in:normal,urgent,pinned',
        ]);

        $announcement = Announcement::create([
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'content' => ['ar' => $validated['content_ar'], 'en' => $validated['content_en']],
            'target_audience' => $validated['target_audience'],
            'priority' => $validated['priority'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function updateAnnouncement(Request $request, int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'content_ar' => 'sometimes|required|string',
            'content_en' => 'sometimes|required|string',
            'target_audience' => 'sometimes|required|in:all,students,faculty,public',
            'priority' => 'sometimes|required|in:normal,urgent,pinned',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['title_ar'])) $announcement->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $announcement->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['content_ar'])) $announcement->setTranslation('content', 'ar', $validated['content_ar']);
        if (isset($validated['content_en'])) $announcement->setTranslation('content', 'en', $validated['content_en']);
        if (isset($validated['target_audience'])) $announcement->target_audience = $validated['target_audience'];
        if (isset($validated['priority'])) $announcement->priority = $validated['priority'];
        if (isset($validated['is_active'])) $announcement->is_active = (bool) $validated['is_active'];

        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => new AnnouncementResource($announcement),
        ]);
    }

    public function deleteAnnouncement(int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }

    // Events
    public function storeEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'location_ar' => 'required|string',
            'location_en' => 'required|string',
            'organizer_ar' => 'required|string',
            'organizer_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'cover_image' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['title_en']).'-'.rand(100, 999);

        $event = Event::create([
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'slug' => $slug,
            'location' => ['ar' => $validated['location_ar'], 'en' => $validated['location_en']],
            'organizer' => ['ar' => $validated['organizer_ar'], 'en' => $validated['organizer_en']],
            'description' => ['ar' => $validated['description_ar'], 'en' => $validated['description_en']],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'cover_image' => $validated['cover_image'] ?? 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'data' => new EventResource($event),
        ], 201);
    }

    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'location_ar' => 'sometimes|required|string',
            'location_en' => 'sometimes|required|string',
            'organizer_ar' => 'sometimes|required|string',
            'organizer_en' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string',
            'description_en' => 'sometimes|required|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'nullable|date',
            'cover_image' => 'nullable|string',
        ]);

        if (isset($validated['title_ar'])) $event->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $event->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['location_ar'])) $event->setTranslation('location', 'ar', $validated['location_ar']);
        if (isset($validated['location_en'])) $event->setTranslation('location', 'en', $validated['location_en']);
        if (isset($validated['organizer_ar'])) $event->setTranslation('organizer', 'ar', $validated['organizer_ar']);
        if (isset($validated['organizer_en'])) $event->setTranslation('organizer', 'en', $validated['organizer_en']);
        if (isset($validated['description_ar'])) $event->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $event->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['start_time'])) $event->start_time = $validated['start_time'];
        if (isset($validated['end_time'])) $event->end_time = $validated['end_time'];
        if (isset($validated['cover_image'])) $event->cover_image = $validated['cover_image'];

        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'data' => new EventResource($event),
        ]);
    }

    public function deleteEvent(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event cancelled and removed.',
        ]);
    }

    // Documents & Regulations Repository
    public function storeDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200', // 50MB max
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        $filePath = $validated['file_path'] ?? '/downloads/regulation_sample.pdf';
        $fileSize = $validated['file_size'] ?? '2.4 MB';
        $fileType = $validated['file_type'] ?? 'PDF';

        // Process real uploaded file from device
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $filePath = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $fileType = strtoupper($uploadedFile->getClientOriginalExtension());
        }

        $doc = DownloadDocument::create([
            'category' => $validated['category'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'description' => [
                'ar' => $validated['description_ar'] ?? '',
                'en' => $validated['description_en'] ?? '',
            ],
            'version' => $validated['version'] ?? '1.0',
            'status' => $validated['status'] ?? 'published',
            'target_audience' => $validated['target_audience'] ?? 'all',
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_archived' => false,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'download_count' => 0,
            'effective_date' => $validated['effective_date'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded and published to document & regulations repository.',
            'data' => new DocumentResource($doc),
        ], 201);
    }

    public function updateDocument(Request $request, int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);

        $validated = $request->validate([
            'category' => 'nullable|string',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200',
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $doc->file_path = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $doc->file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $doc->file_type = strtoupper($uploadedFile->getClientOriginalExtension());
        } elseif (isset($validated['file_path'])) {
            $doc->file_path = $validated['file_path'];
            if (isset($validated['file_size'])) $doc->file_size = $validated['file_size'];
            if (isset($validated['file_type'])) $doc->file_type = $validated['file_type'];
        }

        if (isset($validated['category'])) $doc->category = $validated['category'];
        if (isset($validated['title_ar'])) $doc->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $doc->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['description_ar'])) $doc->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $doc->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['version'])) $doc->version = $validated['version'];
        if (isset($validated['status'])) $doc->status = $validated['status'];
        if (isset($validated['target_audience'])) $doc->target_audience = $validated['target_audience'];
        if (isset($validated['is_featured'])) $doc->is_featured = (bool) $validated['is_featured'];
        if (isset($validated['is_archived'])) $doc->is_archived = (bool) $validated['is_archived'];
        if (isset($validated['effective_date'])) $doc->effective_date = $validated['effective_date'];

        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Document & regulations updated successfully.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function toggleArchiveDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->is_archived = !$doc->is_archived;
        $doc->status = $doc->is_archived ? 'archived' : 'published';
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => $doc->is_archived ? 'Document archived.' : 'Document unarchived and restored.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function deleteDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document permanently deleted.',
        ]);
    }
}
