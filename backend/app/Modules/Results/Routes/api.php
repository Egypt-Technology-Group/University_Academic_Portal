<?php
declare(strict_types=1);

use App\Modules\Results\Controllers\ResultsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:results')->group(function () {
    // Public Student Results & Registration Inquiry endpoints
    Route::middleware('throttle:student-portal')->group(function () {
        Route::get('/student-portal/results', [ResultsController::class, 'inquireResult']);
        Route::post('/student-portal/results', [ResultsController::class, 'inquireResult']);
        Route::post('/student-portal/simulate-registration', [ResultsController::class, 'simulateRegistration']);
    });
});

