<?php
declare(strict_types=1);

use App\Modules\AcademicServices\Controllers\AcademicServicesController;
use App\Modules\AcademicServices\Controllers\AdminAcademicServicesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:academic-services')->group(function () {
    // Public Academic & Student Services endpoints
    Route::get('/exam-schedules', [AcademicServicesController::class, 'indexExamSchedules']);
    Route::post('/student-services/apply', [AcademicServicesController::class, 'submitRequest']);
    Route::get('/student-services/requests', [AcademicServicesController::class, 'indexRequests']);
    Route::get('/verify-statement', [AcademicServicesController::class, 'verifyStatement']);
    Route::post('/verify-statement', [AcademicServicesController::class, 'verifyStatement']);

    // Admin Academic & Student Services Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::get('/student-requests', [AdminAcademicServicesController::class, 'indexRequests']);
        Route::post('/student-requests', [AdminAcademicServicesController::class, 'submitRequest']);
        Route::patch('/student-requests/{id}/status', [AdminAcademicServicesController::class, 'updateRequestStatus']);
        Route::delete('/student-requests/{id}', [AdminAcademicServicesController::class, 'deleteRequest']);
        Route::get('/official-statements', [AdminAcademicServicesController::class, 'indexStatements']);
        Route::post('/official-statements/issue', [AdminAcademicServicesController::class, 'issueStatement']);
        Route::get('/exam-schedules', [AdminAcademicServicesController::class, 'indexExamSchedules']);
        Route::post('/exam-schedules', [AdminAcademicServicesController::class, 'storeExamSchedule']);
        Route::match(['put', 'patch'], '/exam-schedules/{id}', [AdminAcademicServicesController::class, 'updateExamSchedule']);
        Route::delete('/exam-schedules/{id}', [AdminAcademicServicesController::class, 'deleteExamSchedule']);
    });
});
