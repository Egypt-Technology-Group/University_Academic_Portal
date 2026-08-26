<?php
declare(strict_types=1);

use App\Modules\Cms\Controllers\AdminCmsController;
use App\Modules\Cms\Controllers\CmsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:cms')->group(function () {
    // Public CMS endpoints
    Route::get('/news', [CmsController::class, 'news']);
    Route::get('/news/{slug}', [CmsController::class, 'getNews']);
    Route::get('/announcements', [CmsController::class, 'announcements']);

    // Admin CMS Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/news', [AdminCmsController::class, 'storeNews']);
        Route::match(['put', 'patch'], '/news/{id}', [AdminCmsController::class, 'updateNews']);
        Route::delete('/news/{id}', [AdminCmsController::class, 'deleteNews']);

        Route::post('/announcements', [AdminCmsController::class, 'storeAnnouncement']);
        Route::match(['put', 'patch'], '/announcements/{id}', [AdminCmsController::class, 'updateAnnouncement']);
        Route::delete('/announcements/{id}', [AdminCmsController::class, 'deleteAnnouncement']);
    });
});
