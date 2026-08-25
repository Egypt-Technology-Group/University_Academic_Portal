<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\NewsResource;
use App\Models\Announcement;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\NewsArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContentController extends Controller
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
     * List events (upcoming or past).
     */
    public function events(Request $request): AnonymousResourceCollection
    {
        $query = Event::query();

        if ($request->input('filter') === 'past' || $request->boolean('past')) {
            $query->where('end_time', '<', now())->orderBy('start_time', 'desc');
        } elseif ($request->input('filter') === 'all' || $request->boolean('all')) {
            $query->orderBy('start_time', 'asc');
        } else {
            // Default: upcoming events
            $query->where('end_time', '>=', now())->orderBy('start_time', 'asc');
        }

        $perPage = (int) $request->input('per_page', 15);
        $events = $request->boolean('paginate') ? $query->paginate($perPage) : $query->get();

        return EventResource::collection($events);
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

    /**
     * Downloadable documents grouped or filtered by category.
     */
    public function documents(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $query = DownloadDocument::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('grouped')) {
            $groupedDocs = $query->get()->groupBy('category');
            $data = [];
            foreach ($groupedDocs as $category => $items) {
                $data[$category] = DocumentResource::collection($items);
            }
            return response()->json(['data' => $data]);
        }

        $docs = $query->get();

        return DocumentResource::collection($docs);
    }

    /**
     * Increment download counter and return file URL.
     */
    public function incrementDocumentDownload(int $id): JsonResponse
    {
        $document = DownloadDocument::findOrFail($id);
        $document->increment('download_count');

        return response()->json([
            'success' => true,
            'id' => $document->id,
            'download_count' => $document->download_count,
            'file_url' => asset('storage/' . $document->file_path),
            'file_path' => $document->file_path,
            'document' => new DocumentResource($document),
        ]);
    }
}
