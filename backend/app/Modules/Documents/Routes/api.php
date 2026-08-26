<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\ContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:documents')->group(function () {
    // Public Documents endpoints
    Route::get('/documents', [ContentController::class, 'documents']);
    Route::post('/documents/{id}/download', [ContentController::class, 'incrementDocumentDownload']);

    // Admin Documents Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/documents', [AdminCrudController::class, 'storeDocument']);
        Route::match(['put', 'patch'], '/documents/{id}', [AdminCrudController::class, 'updateDocument']);
        Route::post('/documents/{id}/toggle-archive', [AdminCrudController::class, 'toggleArchiveDocument']);
        Route::delete('/documents/{id}', [AdminCrudController::class, 'deleteDocument']);
    });
});
