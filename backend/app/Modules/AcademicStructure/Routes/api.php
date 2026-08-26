<?php
declare(strict_types=1);

use App\Modules\AcademicStructure\Controllers\AcademicStructureController;
use App\Modules\AcademicStructure\Controllers\AdminAcademicStructureController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('module.enabled:academic-structure')->group(function () {
    // Public Academic Structure endpoints
    Route::get('/colleges', [AcademicStructureController::class, 'indexColleges']);
    Route::get('/colleges/{slug}', [AcademicStructureController::class, 'getCollege']);
    Route::get('/departments', [AcademicStructureController::class, 'indexDepartments']);
    Route::get('/programs', [AcademicStructureController::class, 'indexPrograms']);
    Route::get('/programs/{slug}', [AcademicStructureController::class, 'getProgram']);
    Route::get('/faculty', [AcademicStructureController::class, 'indexFaculty']);

    // Admin Academic Structure CRUD Management
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::post('/colleges', [AdminAcademicStructureController::class, 'storeCollege']);
        Route::match(['put', 'patch'], '/colleges/{id}', [AdminAcademicStructureController::class, 'updateCollege']);
        Route::delete('/colleges/{id}', [AdminAcademicStructureController::class, 'deleteCollege']);

        Route::post('/departments', [AdminAcademicStructureController::class, 'storeDepartment']);
        Route::match(['put', 'patch'], '/departments/{id}', [AdminAcademicStructureController::class, 'updateDepartment']);
        Route::delete('/departments/{id}', [AdminAcademicStructureController::class, 'deleteDepartment']);

        Route::post('/programs', [AdminAcademicStructureController::class, 'storeProgram']);
        Route::match(['put', 'patch'], '/programs/{id}', [AdminAcademicStructureController::class, 'updateProgram']);
        Route::delete('/programs/{id}', [AdminAcademicStructureController::class, 'deleteProgram']);

        Route::post('/faculty', [AdminAcademicStructureController::class, 'storeFaculty']);
        Route::match(['put', 'patch'], '/faculty/{id}', [AdminAcademicStructureController::class, 'updateFaculty']);
        Route::delete('/faculty/{id}', [AdminAcademicStructureController::class, 'deleteFaculty']);
    });
});
