<?php

namespace Tests\Feature\Vendor;

use App\Core\Security\EntitlementManager;
use App\Core\Security\VendorKeyProvider;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorControlPlaneSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected VendorKeyProvider $keyProvider;
    protected EntitlementManager $entitlementManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->keyProvider = new VendorKeyProvider();
        $this->entitlementManager = new EntitlementManager($this->keyProvider);
        $this->entitlementManager->reset();
    }

    public function test_tampered_payload_fields_are_cryptographically_rejected(): void
    {
        // 1. First apply a legitimate starter package (only academic-structure)
        $initialPackage = $this->keyProvider->signPayload([
            'client_id' => 'egyitech_portal',
            'tier' => 'academic_starter',
            'licensed_modules' => ['academic-structure'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);
        $this->entitlementManager->applySignedPackage($initialPackage);

        // 2. Attacker attempts to apply tampered package with unauthorized 'results' module
        $payload = [
            'client_id' => 'egyitech_portal',
            'tier' => 'academic_starter',
            'licensed_modules' => ['academic-structure'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);

        // Attacker injects extra modules without regenerating signature
        $package['payload']['licensed_modules'][] = 'results';
        $package['payload']['licensed_modules'][] = 'admissions';

        $response = $this->postJson('/api/v1/vendor/entitlement/apply', $package);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error_code' => 'SIGNATURE_VERIFICATION_FAILED',
            ]);

        $this->assertFalse($this->entitlementManager->isModuleEntitled('results'));
        $this->assertFalse($this->entitlementManager->isModuleEntitled('admissions'));
        $this->assertTrue($this->entitlementManager->isModuleEntitled('academic-structure'));
    }

    public function test_expired_subscription_entitlement_fails_verification_and_blocks_routes(): void
    {
        $payload = [
            'client_id' => 'egyitech_portal',
            'tier' => 'enterprise',
            'licensed_modules' => ['academic-structure', 'admissions'],
            'valid_until' => now()->subDay()->toISOString(), // Expired
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);

        $response = $this->postJson('/api/v1/vendor/entitlement/apply', $package);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error_code' => 'ENTITLEMENT_EXPIRED',
            ]);
    }

    public function test_audit_trail_integrity_is_preserved_across_vendor_actions(): void
    {
        $payload = [
            'client_id' => 'egyitech_portal',
            'tier' => 'enterprise_full',
            'licensed_modules' => ['academic-structure', 'admissions', 'academic-services', 'cms', 'events', 'documents', 'results'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);
        $this->postJson('/api/v1/vendor/entitlement/apply', $package)->assertStatus(200);

        // Verify audit logs have valid integrity hashes
        $logs = AuditLog::where('action', 'vendor.entitlement.applied')->get();
        $this->assertNotEmpty($logs);
        foreach ($logs as $log) {
            $this->assertNotEmpty($log->integrity_hash);
        }
    }
}
