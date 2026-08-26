<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Core\ModuleManager;
use App\Core\Security\EntitlementManager;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorEntitlementController extends Controller
{
    public function __construct(
        protected EntitlementManager $entitlementManager,
        protected ModuleManager $moduleManager
    ) {
    }

    /**
     * Get the current cryptographic entitlement and subscription status.
     */
    public function status(): JsonResponse
    {
        $active = $this->entitlementManager->getActiveEntitlement();

        return response()->json([
            'success' => true,
            'data' => [
                'has_active_license' => $active !== null,
                'entitlement' => $active ? [
                    'client_id' => $active['client_id'] ?? 'unknown',
                    'client_name' => $active['client_name'] ?? null,
                    'tier' => $active['tier'] ?? 'standard',
                    'licensed_modules' => $active['licensed_modules'] ?? [],
                    'issued_at' => $active['issued_at'] ?? null,
                    'valid_until' => $active['valid_until'] ?? null,
                ] : null,
                'registered_modules' => array_keys($this->moduleManager->all()),
                'enabled_modules' => $this->moduleManager->getEnabledIds(),
            ],
        ]);
    }

    /**
     * Verify a vendor-signed entitlement package without applying it.
     */
    public function verify(Request $request): JsonResponse
    {
        $package = $request->all();
        $verification = $this->entitlementManager->verifyPackage($package);

        if (!$verification['valid']) {
            return response()->json([
                'success' => false,
                'error_code' => $verification['error_code'] ?? 'INVALID_PACKAGE',
                'message' => $verification['message'] ?? 'Signature verification failed.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $verification['data'],
        ]);
    }

    /**
     * Apply a cryptographically signed vendor entitlement package.
     */
    public function apply(Request $request): JsonResponse
    {
        $package = $request->all();
        $verification = $this->entitlementManager->verifyPackage($package);

        if (!$verification['valid']) {
            // Immutable Security Audit Log
            AuditLog::record(
                action: 'security.unauthorized_entitlement_attempt',
                auditable: 'vendor_entitlement',
                oldValues: null,
                newValues: [
                    'error_code' => $verification['error_code'] ?? 'SIGNATURE_VERIFICATION_FAILED',
                    'payload_summary' => is_array($package['payload'] ?? null) ? array_keys($package['payload']) : null,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                module: 'system',
                descriptionAr: 'محاولة غير مصرح بها لتطبيق ترخيص موديولات بدون توقيع رقمي سليم من المزود',
                descriptionEn: 'Unauthorized attempt to apply unsigned/invalid module license',
                severity: 'critical',
                status: 'failure'
            );

            return response()->json([
                'success' => false,
                'error_code' => $verification['error_code'] ?? 'SIGNATURE_VERIFICATION_FAILED',
                'message' => $verification['message'] ?? 'The cryptographic vendor signature is invalid.',
            ], 403);
        }

        // Apply package
        $applied = $this->entitlementManager->applySignedPackage($package);
        if (!$applied) {
            return response()->json([
                'success' => false,
                'error_code' => 'APPLY_FAILED',
                'message' => 'Failed to apply entitlement package.',
            ], 500);
        }

        // Synchronize module enabled state with the newly licensed modules
        $licensedModules = $verification['data']['licensed_modules'] ?? [];
        foreach ($this->moduleManager->all() as $id => $module) {
            $normalizedId = str_replace('_', '-', strtolower($id));
            $normalizedLicensed = array_map(fn($m) => str_replace('_', '-', strtolower($m)), $licensedModules);

            if (!in_array($normalizedId, $normalizedLicensed, true)) {
                // Safely disable unentitled module
                try {
                    $this->moduleManager->disable($id);
                } catch (\Throwable $e) {
                    // Ignore dependency blocks during license downgrade
                }
            }
        }

        // Immutable Audit Log
        AuditLog::record(
            action: 'vendor.entitlement.applied',
            auditable: 'vendor_entitlement',
            oldValues: null,
            newValues: [
                'tier' => $verification['data']['tier'] ?? 'custom',
                'licensed_modules' => $licensedModules,
                'valid_until' => $verification['data']['valid_until'] ?? null,
                'ip' => $request->ip(),
            ],
            module: 'system',
            descriptionAr: 'تم تطبيق واعتماد ترخيص موديولات رسمي وموقع رقمياً من المزود',
            descriptionEn: 'Cryptographically signed vendor module entitlement applied successfully',
            severity: 'info',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Vendor subscription entitlement verified and applied successfully.',
            'data' => $verification['data'],
        ]);
    }
}
