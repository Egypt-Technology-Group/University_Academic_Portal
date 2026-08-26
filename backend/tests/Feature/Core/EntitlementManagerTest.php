<?php

namespace Tests\Feature\Core;

use App\Core\Security\EntitlementManager;
use App\Core\Security\VendorKeyProvider;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntitlementManagerTest extends TestCase
{
    use RefreshDatabase;

    protected EntitlementManager $entitlementManager;
    protected VendorKeyProvider $keyProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->keyProvider = new VendorKeyProvider();
        $this->entitlementManager = new EntitlementManager($this->keyProvider);
        $this->entitlementManager->reset();
    }

    public function test_vendor_can_generate_valid_signed_entitlement_package(): void
    {
        $payload = [
            'client_id' => 'egyitech_university_main',
            'client_name' => 'Egyptian Technology University',
            'tier' => 'enterprise',
            'licensed_modules' => ['academic_structure', 'admissions', 'academic_services', 'cms', 'events', 'documents', 'results'],
            'issued_at' => now()->toISOString(),
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);

        $this->assertArrayHasKey('payload', $package);
        $this->assertArrayHasKey('signature', $package);
        $this->assertArrayHasKey('algorithm', $package);

        $verification = $this->entitlementManager->verifyPackage($package);
        $this->assertTrue($verification['valid']);
        $this->assertEquals('enterprise', $verification['data']['tier']);
    }

    public function test_tampered_payload_is_strictly_rejected(): void
    {
        $payload = [
            'client_id' => 'egyitech_university_main',
            'tier' => 'standard',
            'licensed_modules' => ['academic_structure', 'cms'],
            'issued_at' => now()->toISOString(),
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);

        // Tamper with licensed_modules directly in the payload
        $package['payload']['licensed_modules'][] = 'results';
        $package['payload']['tier'] = 'enterprise';

        $verification = $this->entitlementManager->verifyPackage($package);
        $this->assertFalse($verification['valid']);
        $this->assertEquals('SIGNATURE_VERIFICATION_FAILED', $verification['error_code']);
    }

    public function test_expired_entitlement_is_rejected(): void
    {
        $payload = [
            'client_id' => 'egyitech_university_main',
            'tier' => 'enterprise',
            'licensed_modules' => ['academic_structure', 'admissions'],
            'issued_at' => now()->subMonths(6)->toISOString(),
            'valid_until' => now()->subDay()->toISOString(), // Expired yesterday
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);
        $verification = $this->entitlementManager->verifyPackage($package);

        $this->assertFalse($verification['valid']);
        $this->assertEquals('ENTITLEMENT_EXPIRED', $verification['error_code']);
    }

    public function test_apply_signed_license_persists_and_reports_active_entitlements(): void
    {
        $payload = [
            'client_id' => 'egyitech_university_main',
            'tier' => 'academic_suite',
            'licensed_modules' => ['academic_structure', 'admissions', 'academic_services'],
            'issued_at' => now()->toISOString(),
            'valid_until' => now()->addMonths(6)->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);
        $applied = $this->entitlementManager->applySignedPackage($package);

        $this->assertTrue($applied);
        $this->assertTrue($this->entitlementManager->isModuleEntitled('academic_structure'));
        $this->assertTrue($this->entitlementManager->isModuleEntitled('admissions'));
        $this->assertTrue($this->entitlementManager->isModuleEntitled('academic_services'));
        $this->assertFalse($this->entitlementManager->isModuleEntitled('results'));
        $this->assertFalse($this->entitlementManager->isModuleEntitled('cms'));

        $active = $this->entitlementManager->getActiveEntitlement();
        $this->assertNotNull($active);
        $this->assertEquals('academic_suite', $active['tier']);
    }

    public function test_direct_database_tampering_without_valid_signature_fails_entitlement_check(): void
    {
        // Attacker writes fake entitlement directly to database
        SiteSetting::updateOrCreate(
            ['key' => 'vendor_entitlement_package'],
            [
                'value' => json_encode([
                    'payload' => [
                        'tier' => 'hacked_tier',
                        'licensed_modules' => ['results', 'documents'],
                        'valid_until' => now()->addYears(10)->toISOString(),
                    ],
                    'signature' => 'invalid_fake_signature_hash',
                    'algorithm' => 'HMAC-SHA256',
                ]),
                'group' => 'system',
                'type' => 'json',
                'is_public' => false,
            ]
        );

        $this->entitlementManager->resetCache();

        // Must reject fake license
        $this->assertFalse($this->entitlementManager->isModuleEntitled('results'));
        $this->assertFalse($this->entitlementManager->isModuleEntitled('documents'));
    }
}
