<?php

use App\Http\Controllers\Api\AcademicServicesController;
use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ModuleManagementController;
use App\Http\Controllers\Api\SiteSettingsController;
use App\Http\Controllers\Api\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Dynamic Site Settings
    Route::get('/settings', [SiteSettingsController::class, 'getPublicSettings']);

    // Module Lifecycle & Dependency Introspection Endpoints
    Route::get('/modules', [ModuleManagementController::class, 'index']);
    Route::get('/modules/{id}/dependencies', [ModuleManagementController::class, 'dependencies']);
    Route::patch('/modules/{id}/toggle', [ModuleManagementController::class, 'toggle']);

    // Auth endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Admin Management Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [\App\Http\Controllers\Api\Admin\AdminDashboardController::class, 'stats']);

            // Site Customization & Dynamic Settings
            Route::get('/settings', [SiteSettingsController::class, 'index']);
            Route::post('/settings', [SiteSettingsController::class, 'update']);
            Route::patch('/settings/{key}', [SiteSettingsController::class, 'updateSingle']);
            Route::post('/settings/reset', [SiteSettingsController::class, 'resetToDefaults']);

            // CMS Management
            Route::post('/news', [AdminCrudController::class, 'storeNews']);
            Route::match(['put', 'patch'], '/news/{id}', [AdminCrudController::class, 'updateNews']);
            Route::delete('/news/{id}', [AdminCrudController::class, 'deleteNews']);
            Route::post('/announcements', [AdminCrudController::class, 'storeAnnouncement']);
            Route::match(['put', 'patch'], '/announcements/{id}', [AdminCrudController::class, 'updateAnnouncement']);
            Route::delete('/announcements/{id}', [AdminCrudController::class, 'deleteAnnouncement']);
            Route::post('/events', [AdminCrudController::class, 'storeEvent']);
            Route::match(['put', 'patch'], '/events/{id}', [AdminCrudController::class, 'updateEvent']);
            Route::delete('/events/{id}', [AdminCrudController::class, 'deleteEvent']);
            Route::post('/documents', [AdminCrudController::class, 'storeDocument']);
            Route::match(['put', 'patch'], '/documents/{id}', [AdminCrudController::class, 'updateDocument']);

            // Academic & Student Services Management (Admin)
            Route::get('/student-requests', [AcademicServicesController::class, 'indexRequests']);
            Route::post('/student-requests', [AcademicServicesController::class, 'submitRequest']);
            Route::patch('/student-requests/{id}/status', [AcademicServicesController::class, 'updateRequestStatus']);
            Route::get('/official-statements', [AcademicServicesController::class, 'indexStatements']);
            Route::post('/official-statements/issue', [AcademicServicesController::class, 'issueStatement']);
            Route::get('/exam-schedules', [AcademicServicesController::class, 'indexExamSchedules']);
            Route::post('/exam-schedules', [AcademicServicesController::class, 'storeExamSchedule']);
            Route::match(['put', 'patch'], '/exam-schedules/{id}', [AcademicServicesController::class, 'updateExamSchedule']);
            Route::delete('/exam-schedules/{id}', [AcademicServicesController::class, 'deleteExamSchedule']);

            // Enterprise Audit Trail & Compliance Log Endpoints
            Route::get('/audit-logs', [AuditLogController::class, 'index']);
            Route::get('/audit-logs/integrity', [AuditLogController::class, 'verifyIntegrity']);
            Route::get('/audit-logs/export', [AuditLogController::class, 'export']);
            Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
        });
    });

    // Public Content endpoints
    Route::get('/news', [ContentController::class, 'news']);
    Route::get('/news/{slug}', [ContentController::class, 'getNews']);
    Route::get('/events', [ContentController::class, 'events']);
    Route::post('/events/{id}/register', [ContentController::class, 'registerForEvent']);
    Route::get('/announcements', [ContentController::class, 'announcements']);
    Route::get('/documents', [ContentController::class, 'documents']);
    Route::post('/documents/{id}/download', [ContentController::class, 'incrementDocumentDownload']);

    // Public Academic & Student Services endpoints
    Route::get('/exam-schedules', [AcademicServicesController::class, 'indexExamSchedules']);
    Route::post('/student-services/apply', [AcademicServicesController::class, 'submitRequest']);
    Route::get('/student-services/requests', [AcademicServicesController::class, 'indexRequests']);
    Route::get('/verify-statement', [AcademicServicesController::class, 'verifyStatement']);
    Route::post('/verify-statement', [AcademicServicesController::class, 'verifyStatement']);

    // Public Student Portal endpoints
    Route::middleware('throttle:student-portal')->group(function () {
        Route::get('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/simulate-registration', [StudentPortalController::class, 'simulateRegistration']);
    });
});
