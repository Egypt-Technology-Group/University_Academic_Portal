<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\AdmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:admissions')->group(function () {
    // Public Admission endpoints
    Route::middleware('throttle:admissions')->group(function () {
        Route::get('/admissions/active-cycle', [AdmissionController::class, 'activeCycle']);
        Route::post('/admissions/apply', [AdmissionController::class, 'submitApplication']);
        Route::get('/admissions/track', [AdmissionController::class, 'trackApplication']);
        Route::post('/admissions/track', [AdmissionController::class, 'trackApplication']);
    });

    // Admin Admission Management Endpoints
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::get('/applications', [AdminDashboardController::class, 'applications']);
        Route::match(['patch', 'put'], '/applications/{id}/status', [AdminDashboardController::class, 'updateApplicationStatus']);
        Route::post('/applications/{id}/documents/{documentId}/verify', [AdminDashboardController::class, 'verifyDocument']);
        Route::post('/applications/{id}/request-missing-docs', [AdminDashboardController::class, 'requestMissingDocuments']);
    });
});
