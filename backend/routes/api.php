<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AdmissionController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Academic endpoints
    Route::get('/colleges', [AcademicController::class, 'indexColleges']);
    Route::get('/colleges/{slug}', [AcademicController::class, 'getCollege']);
    Route::get('/programs', [AcademicController::class, 'indexPrograms']);
    Route::get('/programs/{slug}', [AcademicController::class, 'getProgram']);
    Route::get('/faculty', [AcademicController::class, 'indexFaculty']);

    // Content endpoints
    Route::get('/news', [ContentController::class, 'news']);
    Route::get('/news/{slug}', [ContentController::class, 'getNews']);
    Route::get('/events', [ContentController::class, 'events']);
    Route::get('/announcements', [ContentController::class, 'announcements']);
    Route::get('/documents', [ContentController::class, 'documents']);
    Route::post('/documents/{id}/download', [ContentController::class, 'incrementDocumentDownload']);

    // Admission endpoints
    Route::get('/admissions/active-cycle', [AdmissionController::class, 'activeCycle']);
    Route::post('/admissions/apply', [AdmissionController::class, 'submitApplication']);
    Route::get('/admissions/track', [AdmissionController::class, 'trackApplication']);
    Route::post('/admissions/track', [AdmissionController::class, 'trackApplication']);

    // Student Portal endpoints
    Route::get('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
    Route::post('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
});
