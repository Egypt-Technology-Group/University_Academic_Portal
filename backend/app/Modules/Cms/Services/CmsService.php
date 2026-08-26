<?php
declare(strict_types=1);

namespace App\Modules\Cms\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Models\NewsCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CmsService
{
    /**
     * Get published news articles with category, featured, and search filters.
     */
    public function getNews(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = NewsArticle::with('category')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['category'])) {
            $cat = $filters['category'];
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('slug', $cat)->orWhere('id', $cat);
            });
        }

        $isFeatured = !empty($filters['is_featured']) || !empty($filters['featured']);
        if ($isFeatured) {
            $query->where('is_featured', true);
        }

        if (!empty($filters['search']) || !empty($filters['q'])) {
            $search = (string) ($filters['search'] ?? $filters['q']);
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('excerpt->en', 'like', "%{$search}%")
                    ->orWhere('excerpt->ar', 'like', "%{$search}%")
                    ->orWhere('body->en', 'like', "%{$search}%")
                    ->orWhere('body->ar', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 10);
        $returnAll = !empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN);

        return $returnAll ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get a single news article by slug, increment view count, and get related articles.
     */
    public function getNewsArticle(string $slug): NewsArticle
    {
        $article = NewsArticle::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('views_count');

        $related = NewsArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $article->setRelation('related_articles', $related);

        return $article;
    }

    /**
     * Get active announcements with optional urgent and audience filters.
     */
    public function getAnnouncements(array $filters = []): Collection
    {
        $query = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'pinned' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc');

        $isUrgent = !empty($filters['urgent']) || (!empty($filters['priority']) && $filters['priority'] === 'urgent');
        if ($isUrgent) {
            $query->where('priority', 'urgent');
        }

        $audience = $filters['audience'] ?? $filters['target_audience'] ?? null;
        if ($audience) {
            $query->where(function ($q) use ($audience) {
                $q->where('target_audience', $audience)->orWhere('target_audience', 'all');
            });
        }

        return $query->get();
    }

    /**
     * Create a new news article.
     */
    public function createNewsArticle(array $data, ?UploadedFile $imageFile = null, ?User $author = null): NewsArticle
    {
        $slug = Str::slug($data['title_en'] ?? 'news') . '-' . rand(100, 999);

        $featuredImage = $data['featured_image'] ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';
        if ($imageFile) {
            $path = $imageFile->store('news_images', 'public');
            $featuredImage = '/storage/' . $path;
        }

        $article = NewsArticle::create([
            'category_id' => $data['category_id'],
            'title' => [
                'ar' => $data['title_ar'],
                'en' => $data['title_en'],
            ],
            'slug' => $slug,
            'excerpt' => [
                'ar' => $data['excerpt_ar'] ?? '',
                'en' => $data['excerpt_en'] ?? '',
            ],
            'body' => [
                'ar' => $data['body_ar'],
                'en' => $data['body_en'],
            ],
            'featured_image' => $featuredImage,
            'is_featured' => $data['is_featured'] ?? false,
            'published_at' => $data['published_at'] ?? now(),
            'views_count' => 0,
        ]);

        AuditLog::record(
            action: 'create',
            auditable: $article,
            oldValues: null,
            newValues: ['title_ar' => $data['title_ar'], 'title_en' => $data['title_en'], 'category_id' => $data['category_id']],
            module: 'cms',
            descriptionAr: "نشر خبر صحفي جديد: {$data['title_ar']}",
            descriptionEn: "Published new news article: {$data['title_en']}",
            severity: 'info',
            status: 'success'
        );

        return $article;
    }

    /**
     * Update an existing news article.
     */
    public function updateNewsArticle(NewsArticle $article, array $data, ?UploadedFile $imageFile = null): NewsArticle
    {
        $oldValues = $article->only(['title', 'excerpt', 'category_id', 'is_featured']);

        if (isset($data['category_id'])) {
            $article->category_id = $data['category_id'];
        }
        if (isset($data['title_ar'])) {
            $article->setTranslation('title', 'ar', $data['title_ar']);
        }
        if (isset($data['title_en'])) {
            $article->setTranslation('title', 'en', $data['title_en']);
        }
        if (isset($data['excerpt_ar'])) {
            $article->setTranslation('excerpt', 'ar', $data['excerpt_ar']);
        }
        if (isset($data['excerpt_en'])) {
            $article->setTranslation('excerpt', 'en', $data['excerpt_en']);
        }
        if (isset($data['body_ar'])) {
            $article->setTranslation('body', 'ar', $data['body_ar']);
        }
        if (isset($data['body_en'])) {
            $article->setTranslation('body', 'en', $data['body_en']);
        }
        if ($imageFile) {
            $path = $imageFile->store('news_images', 'public');
            $article->featured_image = '/storage/' . $path;
        } elseif (isset($data['featured_image'])) {
            $article->featured_image = $data['featured_image'];
        }
        if (isset($data['is_featured'])) {
            $article->is_featured = (bool) $data['is_featured'];
        }
        if (isset($data['published_at'])) {
            $article->published_at = $data['published_at'];
        }

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

        return $article;
    }

    /**
     * Delete a news article.
     */
    public function deleteNewsArticle(NewsArticle $article): void
    {
        $title = $article->title;
        $id = $article->id;

        $article->delete();

        AuditLog::record(
            action: 'delete',
            auditable: NewsArticle::class,
            oldValues: ['id' => $id, 'title' => $title],
            newValues: null,
            module: 'cms',
            descriptionAr: "حذف الخبر الصحفي: {$title}",
            descriptionEn: "Deleted news article: {$title}",
            severity: 'warning',
            status: 'success'
        );
    }

    /**
     * Create a new announcement.
     */
    public function createAnnouncement(array $data): Announcement
    {
        return Announcement::create([
            'title' => [
                'ar' => $data['title_ar'],
                'en' => $data['title_en'],
            ],
            'content' => [
                'ar' => $data['content_ar'],
                'en' => $data['content_en'],
            ],
            'target_audience' => $data['target_audience'],
            'priority' => $data['priority'],
            'is_active' => $data['is_active'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    /**
     * Update an existing announcement.
     */
    public function updateAnnouncement(Announcement $announcement, array $data): Announcement
    {
        if (isset($data['title_ar'])) {
            $announcement->setTranslation('title', 'ar', $data['title_ar']);
        }
        if (isset($data['title_en'])) {
            $announcement->setTranslation('title', 'en', $data['title_en']);
        }
        if (isset($data['content_ar'])) {
            $announcement->setTranslation('content', 'ar', $data['content_ar']);
        }
        if (isset($data['content_en'])) {
            $announcement->setTranslation('content', 'en', $data['content_en']);
        }
        if (isset($data['target_audience'])) {
            $announcement->target_audience = $data['target_audience'];
        }
        if (isset($data['priority'])) {
            $announcement->priority = $data['priority'];
        }
        if (isset($data['is_active'])) {
            $announcement->is_active = (bool) $data['is_active'];
        }
        if (isset($data['expires_at'])) {
            $announcement->expires_at = $data['expires_at'];
        }

        $announcement->save();

        return $announcement;
    }

    /**
     * Delete an announcement.
     */
    public function deleteAnnouncement(Announcement $announcement): void
    {
        $announcement->delete();
    }
}
