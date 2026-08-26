<?php

namespace Tests;

use App\Core\Security\EntitlementManager;
use App\Core\Security\VendorKeyProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Install a valid cryptographically signed vendor license for test suites.
     */
    public function installTestLicense(?array $modules = null, bool $autoEnable = false): array
    {
        $keyProvider = new VendorKeyProvider();
        $entitlementManager = app(EntitlementManager::class);

        $modules = $modules ?? [
            'academic-structure',
            'admissions',
            'academic-services',
            'cms',
            'events',
            'documents',
            'results',
        ];

        $package = $keyProvider->signPayload([
            'client_id' => 'test_client_portal',
            'client_name' => 'Test University Portal',
            'tier' => 'enterprise',
            'licensed_modules' => $modules,
            'issued_at' => now()->toISOString(),
            'valid_until' => now()->addYears(2)->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);

        $entitlementManager->applySignedPackage($package);

        if ($autoEnable) {
            $moduleManager = app(\App\Core\ModuleManager::class);
            foreach ($modules as $mod) {
                try {
                    $moduleManager->enable($mod);
                } catch (\Throwable $e) {
                }
            }
        }

        return $package;
    }
}
