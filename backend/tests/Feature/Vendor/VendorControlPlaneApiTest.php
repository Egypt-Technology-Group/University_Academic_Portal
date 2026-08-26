<?php

namespace Tests\Feature\Vendor;

use App\Core\Security\EntitlementManager;
use App\Core\Security\VendorKeyProvider;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorControlPlaneApiTest extends TestCase
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

    public function test_vendor_status_endpoint_reports_license_metadata(): void
    {
        $response = $this->getJson('/api/v1/vendor/entitlement/status');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'has_active_license',
                    'entitlement',
                ],
            ]);
    }

    public function test_vendor_can_apply_valid_signed_entitlement_package(): void
    {
        $payload = [
            'client_id' => 'egyitech_portal_prod',
            'tier' => 'enterprise_premium',
            'licensed_modules' => ['academic-structure', 'admissions', 'academic-services', 'cms'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ];

        $package = $this->keyProvider->signPayload($payload);

        $response = $this->postJson('/api/v1/vendor/entitlement/apply', $package);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Vendor subscription entitlement verified and applied successfully.',
                'data' => [
                    'tier' => 'enterprise_premium',
                ],
            ]);

        $this->assertTrue($this->entitlementManager->isModuleEntitled('admissions'));
        $this->assertFalse($this->entitlementManager->isModuleEntitled('results'));

        // Verify audit log entry was created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'vendor.entitlement.applied',
        ]);
    }

    public function test_rejects_unauthorized_tampered_entitlement_package_with_audit_log(): void
    {
        $package = [
            'payload' => [
                'client_id' => 'hacked_client',
                'tier' => 'hacked_tier',
                'licensed_modules' => ['results', 'documents'],
            ],
            'signature' => 'invalid_signature_string',
            'algorithm' => 'HMAC-SHA256',
        ];

        $response = $this->postJson('/api/v1/vendor/entitlement/apply', $package);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error_code' => 'SIGNATURE_VERIFICATION_FAILED',
            ]);

        // Audit trail records security attempt
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security.unauthorized_entitlement_attempt',
        ]);
    }

    public function test_client_admin_cannot_toggle_unlicensed_modules_via_admin_api(): void
    {
        // Deploy standard license (only academic-structure)
        $package = $this->keyProvider->signPayload([
            'client_id' => 'egyitech_test',
            'tier' => 'starter',
            'licensed_modules' => ['academic-structure'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);
        $this->entitlementManager->applySignedPackage($package);

        // Login as super-admin
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Client admin attempts to toggle 'results' (unentitled) -> MUST fail with 409 conflict
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/admin/modules/results/toggle', [
                'enabled' => true,
            ]);

        $response->assertStatus(409);
    }
}
