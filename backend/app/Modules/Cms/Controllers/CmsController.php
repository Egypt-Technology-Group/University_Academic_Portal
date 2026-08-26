<?php
declare(strict_types=1);

namespace App\Modules\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cms\Resources\AnnouncementResource;
use App\Modules\Cms\Resources\NewsResource;
use App\Modules\Cms\Services\CmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CmsController extends Controller
{
    public function __construct(
        protected readonly CmsService $cmsService
    ) {}

    /**
     * Paginated news with category, featured, and search filters.
     */
    public function news(Request $request): AnonymousResourceCollection
    {
        $articles = $this->cmsService->getNews($request->all());

        return NewsResource::collection($articles);
    }

    /**
     * Get single news article, increment its view count, and return related articles.
     */
    public function getNews(string $slug): NewsResource
    {
        $article = $this->cmsService->getNewsArticle($slug);
        $related = $article->relationLoaded('related_articles') ? $article->getRelation('related_articles') : [];

        return (new NewsResource($article))->additional([
            'related_articles' => NewsResource::collection($related),
        ]);
    }

    /**
     * List active announcements with urgent and audience filters.
     */
    public function announcements(Request $request): AnonymousResourceCollection
    {
        $announcements = $this->cmsService->getAnnouncements($request->all());

        return AnnouncementResource::collection($announcements);
    }
}
