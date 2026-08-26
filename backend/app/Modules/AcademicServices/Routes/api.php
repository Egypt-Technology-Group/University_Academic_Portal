<?php
declare(strict_types=1);

use App\Http\Controllers\Api\AcademicServicesController;
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
        Route::get('/student-requests', [AcademicServicesController::class, 'indexRequests']);
        Route::post('/student-requests', [AcademicServicesController::class, 'submitRequest']);
        Route::patch('/student-requests/{id}/status', [AcademicServicesController::class, 'updateRequestStatus']);
        Route::delete('/student-requests/{id}', [AcademicServicesController::class, 'deleteRequest']);
        Route::get('/official-statements', [AcademicServicesController::class, 'indexStatements']);
        Route::post('/official-statements/issue', [AcademicServicesController::class, 'issueStatement']);
        Route::get('/exam-schedules', [AcademicServicesController::class, 'indexExamSchedules']);
        Route::post('/exam-schedules', [AcademicServicesController::class, 'storeExamSchedule']);
        Route::match(['put', 'patch'], '/exam-schedules/{id}', [AcademicServicesController::class, 'updateExamSchedule']);
        Route::delete('/exam-schedules/{id}', [AcademicServicesController::class, 'deleteExamSchedule']);
    });
});
