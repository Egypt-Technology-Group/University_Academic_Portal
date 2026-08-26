<?php
declare(strict_types=1);

use App\Modules\Events\Controllers\AdminEventsController;
use App\Modules\Events\Controllers\EventsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:events')->group(function () {
    // Public Events endpoints
    Route::get('/events', [EventsController::class, 'events']);
    Route::post('/events/{id}/register', [EventsController::class, 'registerForEvent']);

    // Admin Events Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/events', [AdminEventsController::class, 'storeEvent']);
        Route::match(['put', 'patch'], '/events/{id}', [AdminEventsController::class, 'updateEvent']);
        Route::delete('/events/{id}', [AdminEventsController::class, 'deleteEvent']);
    });
});
