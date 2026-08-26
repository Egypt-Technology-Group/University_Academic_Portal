<?php
declare(strict_types=1);

namespace App\Modules\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Models\NewsCategory;
use App\Modules\Cms\Resources\AnnouncementResource;
use App\Modules\Cms\Resources\NewsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCmsController extends Controller
{
    // News Articles
    public function storeNews(Request $request): JsonResponse
    {
        if (!$request->has('category_id') && $request->filled('category')) {
            $cat = NewsCategory::where('slug', $request->input('category'))->orWhere('id', $request->input('category'))->first();
            if ($cat) {
                $request->merge(['category_id' => $cat->id]);
            } else {
                $firstCat = NewsCategory::first();
                if ($firstCat) {
                    $request->merge(['category_id' => $firstCat->id]);
                }
            }
        }

        if ($request->filled('summary_ar') && !$request->filled('excerpt_ar')) {
            $request->merge(['excerpt_ar' => $request->input('summary_ar')]);
        }
        if ($request->filled('summary_en') && !$request->filled('excerpt_en')) {
            $request->merge(['excerpt_en' => $request->input('summary_en')]);
        }
        if ($request->filled('content_ar') && !$request->filled('body_ar')) {
            $request->merge(['body_ar' => $request->input('content_ar')]);
        }
        if ($request->filled('content_en') && !$request->filled('body_en')) {
            $request->merge(['body_en' => $request->input('content_en')]);
        }

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

        if (!$request->has('category_id') && $request->filled('category')) {
            $cat = NewsCategory::where('slug', $request->input('category'))->orWhere('id', $request->input('category'))->first();
            if ($cat) {
                $request->merge(['category_id' => $cat->id]);
            }
        }

        if ($request->filled('summary_ar') && !$request->filled('excerpt_ar')) {
            $request->merge(['excerpt_ar' => $request->input('summary_ar')]);
        }
        if ($request->filled('summary_en') && !$request->filled('excerpt_en')) {
            $request->merge(['excerpt_en' => $request->input('summary_en')]);
        }
        if ($request->filled('content_ar') && !$request->filled('body_ar')) {
            $request->merge(['body_ar' => $request->input('content_ar')]);
        }
        if ($request->filled('content_en') && !$request->filled('body_en')) {
            $request->merge(['body_en' => $request->input('content_en')]);
        }

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
            auditable: 'App\Modules\Cms\Models\NewsArticle',
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
        if ($request->filled('is_urgent') && !$request->has('priority')) {
            $request->merge(['priority' => $request->boolean('is_urgent') ? 'urgent' : 'normal']);
        }

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'target_audience' => 'required|in:all,students,faculty,applicants,public,staff',
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

        if ($request->filled('is_urgent') && !$request->has('priority')) {
            $request->merge(['priority' => $request->boolean('is_urgent') ? 'urgent' : 'normal']);
        }

        $validated = $request->validate([
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'content_ar' => 'sometimes|required|string',
            'content_en' => 'sometimes|required|string',
            'target_audience' => 'sometimes|required|in:all,students,faculty,applicants,public,staff',
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
}
