<?php
declare(strict_types=1);

namespace App\Modules\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Requests\StoreAnnouncementRequest;
use App\Modules\Cms\Requests\StoreNewsArticleRequest;
use App\Modules\Cms\Requests\UpdateAnnouncementRequest;
use App\Modules\Cms\Requests\UpdateNewsArticleRequest;
use App\Modules\Cms\Resources\AnnouncementResource;
use App\Modules\Cms\Resources\NewsResource;
use App\Modules\Cms\Services\CmsService;
use Illuminate\Http\JsonResponse;

class AdminCmsController extends Controller
{
    public function __construct(
        protected readonly CmsService $cmsService
    ) {}

    // News Articles
    public function storeNews(StoreNewsArticleRequest $request): JsonResponse
    {
        $imageFile = $request->file('featured_image_file') ?? $request->file('image');
        $article = $this->cmsService->createNewsArticle(
            $request->validated(),
            $imageFile,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'News article published successfully.',
            'data' => new NewsResource($article),
        ], 201);
    }

    public function updateNews(UpdateNewsArticleRequest $request, int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $imageFile = $request->file('featured_image_file') ?? $request->file('image');

        $updated = $this->cmsService->updateNewsArticle(
            $article,
            $request->validated(),
            $imageFile
        );

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully.',
            'data' => new NewsResource($updated),
        ]);
    }

    public function deleteNews(int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $this->cmsService->deleteNewsArticle($article);

        return response()->json([
            'success' => true,
            'message' => 'News article removed successfully.',
        ]);
    }

    // Announcements
    public function storeAnnouncement(StoreAnnouncementRequest $request): JsonResponse
    {
        $announcement = $this->cmsService->createAnnouncement($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function updateAnnouncement(UpdateAnnouncementRequest $request, int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $updated = $this->cmsService->updateAnnouncement($announcement, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => new AnnouncementResource($updated),
        ]);
    }

    public function deleteAnnouncement(int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $this->cmsService->deleteAnnouncement($announcement);

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }
}
