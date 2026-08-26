<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AcademicServicesController;
use App\Http\Controllers\Api\Admin\AdminCrudController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AuditLogController;
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
            Route::post('/applications/{id}/documents/{documentId}/verify', [AdminDashboardController::class, 'verifyDocument']);
            Route::post('/applications/{id}/request-missing-docs', [AdminDashboardController::class, 'requestMissingDocuments']);

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

            // Academic Structure CRUD Management (Colleges, Departments, Programs)
            Route::post('/colleges', [AdminCrudController::class, 'storeCollege']);
            Route::match(['put', 'patch'], '/colleges/{id}', [AdminCrudController::class, 'updateCollege']);
            Route::delete('/colleges/{id}', [AdminCrudController::class, 'deleteCollege']);

            Route::post('/departments', [AdminCrudController::class, 'storeDepartment']);
            Route::match(['put', 'patch'], '/departments/{id}', [AdminCrudController::class, 'updateDepartment']);
            Route::delete('/departments/{id}', [AdminCrudController::class, 'deleteDepartment']);

            Route::post('/programs', [AdminCrudController::class, 'storeProgram']);
            Route::match(['put', 'patch'], '/programs/{id}', [AdminCrudController::class, 'updateProgram']);
            Route::delete('/programs/{id}', [AdminCrudController::class, 'deleteProgram']);

            // Faculty & Researchers CRUD Management
            Route::post('/faculty', [AdminCrudController::class, 'storeFaculty']);
            Route::match(['put', 'patch'], '/faculty/{id}', [AdminCrudController::class, 'updateFaculty']);
            Route::delete('/faculty/{id}', [AdminCrudController::class, 'deleteFaculty']);

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

    // Public Academic endpoints
    Route::get('/colleges', [AcademicController::class, 'indexColleges']);
    Route::get('/colleges/{slug}', [AcademicController::class, 'getCollege']);
    Route::get('/departments', [AcademicController::class, 'indexDepartments']);
    Route::get('/programs', [AcademicController::class, 'indexPrograms']);
    Route::get('/programs/{slug}', [AcademicController::class, 'getProgram']);
    Route::get('/faculty', [AcademicController::class, 'indexFaculty']);

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
