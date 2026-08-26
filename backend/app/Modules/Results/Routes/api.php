<?php
declare(strict_types=1);

use App\Http\Controllers\Api\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:results')->group(function () {
    // Public Student Results & Registration Inquiry endpoints
    Route::middleware('throttle:student-portal')->group(function () {
        Route::get('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/results', [StudentPortalController::class, 'inquireResult']);
        Route::post('/student-portal/simulate-registration', [StudentPortalController::class, 'simulateRegistration']);
    });
});
