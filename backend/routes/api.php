<?php

use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ModuleManagementController;
use App\Http\Controllers\Api\SiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Dynamic Site Settings
    Route::get('/settings', [SiteSettingsController::class, 'getPublicSettings']);

    // Module Lifecycle & Dependency Introspection Endpoints
    Route::get('/modules/manifest', [ModuleManagementController::class, 'manifest']);
    Route::get('/modules', [ModuleManagementController::class, 'index']);
    Route::get('/modules/{id}/dependencies', [ModuleManagementController::class, 'dependencies']);
    Route::patch('/modules/{id}/toggle', [ModuleManagementController::class, 'toggle']);

    // Dedicated Vendor-Only Control Plane (Protected by Rate Limiting & Cryptographic Verification)
    Route::prefix('vendor')->middleware('throttle:30,1')->group(function () {
        Route::get('/entitlement/status', [\App\Http\Controllers\Api\Vendor\VendorEntitlementController::class, 'status']);
        Route::post('/entitlement/verify', [\App\Http\Controllers\Api\Vendor\VendorEntitlementController::class, 'verify']);
        Route::post('/entitlement/apply', [\App\Http\Controllers\Api\Vendor\VendorEntitlementController::class, 'apply']);
    });

    // Auth endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Admin Management Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [\App\Http\Controllers\Api\Admin\AdminDashboardController::class, 'stats']);

            // Module Control for Admins
            Route::post('/modules/{id}/toggle', [ModuleManagementController::class, 'toggle']);
            Route::patch('/modules/{id}/toggle', [ModuleManagementController::class, 'toggle']);

            // Site Customization & Dynamic Settings
            Route::get('/settings', [SiteSettingsController::class, 'index']);
            Route::post('/settings', [SiteSettingsController::class, 'update']);
            Route::patch('/settings/{key}', [SiteSettingsController::class, 'updateSingle']);
            Route::post('/settings/reset', [SiteSettingsController::class, 'resetToDefaults']);

            // Enterprise Audit Trail & Compliance Log Endpoints
            Route::get('/audit-logs', [AuditLogController::class, 'index']);
            Route::get('/audit-logs/integrity', [AuditLogController::class, 'verifyIntegrity']);
            Route::get('/audit-logs/export', [AuditLogController::class, 'export']);
            Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
        });
    });
});
