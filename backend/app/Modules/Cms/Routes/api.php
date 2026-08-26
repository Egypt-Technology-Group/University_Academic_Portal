<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\ContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:cms')->group(function () {
    // Public CMS endpoints
    Route::get('/news', [ContentController::class, 'news']);
    Route::get('/news/{slug}', [ContentController::class, 'getNews']);
    Route::get('/announcements', [ContentController::class, 'announcements']);

    // Admin CMS Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/news', [AdminCrudController::class, 'storeNews']);
        Route::match(['put', 'patch'], '/news/{id}', [AdminCrudController::class, 'updateNews']);
        Route::delete('/news/{id}', [AdminCrudController::class, 'deleteNews']);

        Route::post('/announcements', [AdminCrudController::class, 'storeAnnouncement']);
        Route::match(['put', 'patch'], '/announcements/{id}', [AdminCrudController::class, 'updateAnnouncement']);
        Route::delete('/announcements/{id}', [AdminCrudController::class, 'deleteAnnouncement']);
    });
});
