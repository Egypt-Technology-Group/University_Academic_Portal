<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\CollegeResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\ProgramResource;
use App\Models\Announcement;
use App\Models\College;
use App\Models\Department;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\Program;
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

        return response()->json([
            'success' => true,
            'message' => 'News article published successfully.',
            'data' => new NewsResource($article),
        ], 201);
    }

    public function deleteNews(int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $article->delete();

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
            'cover_image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'data' => new EventResource($event),
        ], 201);
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

    // Documents
    public function storeDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|in:bylaws,schedules,forms,guides',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
        ]);

        $doc = DownloadDocument::create([
            'category' => $validated['category'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'file_path' => $validated['file_path'] ?? '/downloads/sample_doc.pdf',
            'file_size' => $validated['file_size'] ?? '2.4 MB',
            'file_type' => $validated['file_type'] ?? 'PDF',
            'download_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document published to document repository.',
            'data' => new DocumentResource($doc),
        ], 201);
    }

    public function deleteDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted.',
        ]);
    }
}
