<?php
declare(strict_types=1);

namespace App\Modules\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Resources\AnnouncementResource;
use App\Modules\Cms\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CmsController extends Controller
{
    /**
     * Paginated news with category, featured, and search filters.
     */
    public function news(Request $request): AnonymousResourceCollection
    {
        $query = NewsArticle::with('category')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('category')) {
            $cat = $request->input('category');
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('slug', $cat)->orWhere('id', $cat);
            });
        }

        if ($request->boolean('is_featured') || $request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('search') || $request->filled('q')) {
            $search = (string) $request->input('search', $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('excerpt->en', 'like', "%{$search}%")
                    ->orWhere('excerpt->ar', 'like', "%{$search}%")
                    ->orWhere('body->en', 'like', "%{$search}%")
                    ->orWhere('body->ar', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $articles = $query->paginate($perPage);

        return NewsResource::collection($articles);
    }

    /**
     * Get single news article, increment its view count, and return related articles.
     */
    public function getNews(string $slug): NewsResource
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

        return (new NewsResource($article))->additional([
            'related_articles' => NewsResource::collection($related),
        ]);
    }

    /**
     * List active announcements with urgent and audience filters.
     */
    public function announcements(Request $request): AnonymousResourceCollection
    {
        $query = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'pinned' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc');

        if ($request->boolean('urgent') || $request->input('priority') === 'urgent') {
            $query->where('priority', 'urgent');
        }

        if ($request->filled('audience') || $request->filled('target_audience')) {
            $audience = $request->input('audience', $request->input('target_audience'));
            $query->where(function ($q) use ($audience) {
                $q->where('target_audience', $audience)->orWhere('target_audience', 'all');
            });
        }

        $announcements = $query->get();

        return AnnouncementResource::collection($announcements);
    }
}
