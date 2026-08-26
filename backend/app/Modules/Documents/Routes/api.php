<?php
declare(strict_types=1);

use App\Modules\Documents\Controllers\AdminDocumentsController;
use App\Modules\Documents\Controllers\DocumentsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:documents')->group(function () {
    // Public Documents endpoints
    Route::get('/documents', [DocumentsController::class, 'documents']);
    Route::post('/documents/{id}/download', [DocumentsController::class, 'incrementDocumentDownload']);

    // Admin Documents Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/documents', [AdminDocumentsController::class, 'storeDocument']);
        Route::match(['put', 'patch'], '/documents/{id}', [AdminDocumentsController::class, 'updateDocument']);
        Route::post('/documents/{id}/toggle-archive', [AdminDocumentsController::class, 'toggleArchiveDocument']);
        Route::delete('/documents/{id}', [AdminDocumentsController::class, 'deleteDocument']);
    });
});

