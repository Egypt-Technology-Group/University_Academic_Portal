<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\ContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:events')->group(function () {
    // Public Events endpoints
    Route::get('/events', [ContentController::class, 'events']);
    Route::post('/events/{id}/register', [ContentController::class, 'registerForEvent']);

    // Admin Events Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/events', [AdminCrudController::class, 'storeEvent']);
        Route::match(['put', 'patch'], '/events/{id}', [AdminCrudController::class, 'updateEvent']);
        Route::delete('/events/{id}', [AdminCrudController::class, 'deleteEvent']);
    });
});
