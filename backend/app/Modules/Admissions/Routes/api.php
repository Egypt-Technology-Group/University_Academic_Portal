<?php
declare(strict_types=1);

use App\Modules\Admissions\Controllers\AdminAdmissionsController;
use App\Modules\Admissions\Controllers\AdmissionsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:admissions')->group(function () {
    // Public Admission endpoints
    Route::middleware('throttle:admissions')->group(function () {
        Route::get('/admissions/active-cycle', [AdmissionsController::class, 'activeCycle']);
        Route::post('/admissions/apply', [AdmissionsController::class, 'submitApplication']);
        Route::get('/admissions/track', [AdmissionsController::class, 'trackApplication']);
        Route::post('/admissions/track', [AdmissionsController::class, 'trackApplication']);
    });

    // Admin Admission Management Endpoints
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::get('/applications', [AdminAdmissionsController::class, 'applications']);
        Route::match(['patch', 'put'], '/applications/{id}/status', [AdminAdmissionsController::class, 'updateApplicationStatus']);
        Route::post('/applications/{id}/documents/{documentId}/verify', [AdminAdmissionsController::class, 'verifyDocument']);
        Route::post('/applications/{id}/request-missing-docs', [AdminAdmissionsController::class, 'requestMissingDocuments']);
    });
});