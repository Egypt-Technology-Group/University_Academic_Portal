<?php
declare(strict_types=1);

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\Admin\AdminCrudController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:academic-structure')->group(function () {
    // Public Academic Structure endpoints
    Route::get('/colleges', [AcademicController::class, 'indexColleges']);
    Route::get('/colleges/{slug}', [AcademicController::class, 'getCollege']);
    Route::get('/departments', [AcademicController::class, 'indexDepartments']);
    Route::get('/programs', [AcademicController::class, 'indexPrograms']);
    Route::get('/programs/{slug}', [AcademicController::class, 'getProgram']);
    Route::get('/faculty', [AcademicController::class, 'indexFaculty']);

    // Admin Academic Structure CRUD Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/colleges', [AdminCrudController::class, 'storeCollege']);
        Route::match(['put', 'patch'], '/colleges/{id}', [AdminCrudController::class, 'updateCollege']);
        Route::delete('/colleges/{id}', [AdminCrudController::class, 'deleteCollege']);

        Route::post('/departments', [AdminCrudController::class, 'storeDepartment']);
        Route::match(['put', 'patch'], '/departments/{id}', [AdminCrudController::class, 'updateDepartment']);
        Route::delete('/departments/{id}', [AdminCrudController::class, 'deleteDepartment']);

        Route::post('/programs', [AdminCrudController::class, 'storeProgram']);
        Route::match(['put', 'patch'], '/programs/{id}', [AdminCrudController::class, 'updateProgram']);
        Route::delete('/programs/{id}', [AdminCrudController::class, 'deleteProgram']);

        Route::post('/faculty', [AdminCrudController::class, 'storeFaculty']);
        Route::match(['put', 'patch'], '/faculty/{id}', [AdminCrudController::class, 'updateFaculty']);
        Route::delete('/faculty/{id}', [AdminCrudController::class, 'deleteFaculty']);
    });
});
