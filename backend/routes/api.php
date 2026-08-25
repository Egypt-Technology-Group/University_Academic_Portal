<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\AdmissionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\SiteSettingsController;
use App\Http\Controllers\Api\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Dynamic Site Settings
    Route::get('/settings', [SiteSettingsController::class, 'getPublicSettings']);

    // Auth endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Admin Management Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [AdminDashboardController::class, 'stats']);
            Route::get('/applications', [AdminDashboardController::class, 'applications']);
            Route::match(['patch', 'put'], '/applications/{id}/status', [AdminDashboardController::class, 'updateApplicationStatus']);

            // Site Customization & Dynamic Settings
            Route::get('/settings', [SiteSettingsController::class, 'index']);
            Route::post('/settings', [SiteSettingsController::class, 'update']);
            Route::patch('/settings/{key}', [SiteSettingsController::class, 'updateSingle']);
            Route::post('/settings/reset', [SiteSettingsController::class, 'resetToDefaults']);

            // CMS Management
            Route::post('/news', [AdminCrudController::class, 'storeNews']);
            Route::delete('/news/{id}', [AdminCrudController::class, 'deleteNews']);
            Route::post('/announcements', [AdminCrudController::class, 'storeAnnouncement']);
            Route::delete('/announcements/{id}', [AdminCrudController::class, 'deleteAnnouncement']);
            Route::post('/events', [AdminCrudController::class, 'storeEvent']);
            Route::delete('/events/{id}', [AdminCrudController::class, 'deleteEvent']);
            Route::post('/documents', [AdminCrudController::class, 'storeDocument']);
            Route::delete('/documents/{id}', [AdminCrudController::class, 'deleteDocument']);
        });
    });

    // Public Academic endpoints
    Route::get('/colleges', [AcademicController::class, 'indexColleges']);
    Route::get('/colleges/{slug}', [AcademicController::class, 'getCollege']);
    Route::get('/programs', [AcademicController::class, 'indexPrograms']);
    Route::get('/programs/{slug}', [AcademicController::class, 'getProgram']);
    Route::get('/faculty', [AcademicController::class, 'indexFaculty']);

    // Public Content endpoints
    Route::get('/news', [ContentController::class, 'news']);
    Route::get('/news/{slug}', [ContentController::class, 'getNews']);
    Route::get('/events', [ContentController::class, 'events']);
    Route::get('/announcements', [ContentController::class, 'announcements']);
    Route::get('/documents', [ContentController::class, 'documents']);
    Route::post('/documents/{id}/download', [ContentController::class, 'incrementDocumentDownload']);

    // Public Admission endpoints
    Route::middleware('throttle:admissions')->group(function () {
        Route::get('/admissions/active-cycle', [AdmissionController::class, 'activeCycle']);
        Route::post('/admissions/apply', [AdmissionController::class, 'submitApplication']);
        Route::get('/admissions/track', [AdmissionController::class, 'trackApplication']);
        Route::post('/admissions/track', [AdmissionController::class, 'trackApplication']);
    });

    // Public Student Portal endpoints
    Route::middleware('throttle:student-portal')->group(function () {
        Route::get('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/simulate-registration', [StudentPortalController::class, 'simulateRegistration']);
    });
});
