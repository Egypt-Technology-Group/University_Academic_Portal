<?php

namespace Tests\Feature\Core;

use App\Core\Exceptions\ModuleDependencyException;
use App\Core\ModuleManager;
use App\Core\Security\EntitlementManager;
use App\Core\Security\VendorKeyProvider;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntitledModuleManagerTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $moduleManager;
    protected EntitlementManager $entitlementManager;
    protected VendorKeyProvider $keyProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->keyProvider = new VendorKeyProvider();
        $this->entitlementManager = new EntitlementManager($this->keyProvider);
        $this->entitlementManager->reset();
        $this->moduleManager = app(ModuleManager::class);
        $this->moduleManager->resetEnabledState();
    }

    public function test_cannot_enable_module_not_covered_by_signed_license(): void
    {
        // Deploy a limited signed license: only academic-structure and cms
        $package = $this->keyProvider->signPayload([
            'client_id' => 'egyitech_test',
            'tier' => 'starter',
            'licensed_modules' => ['academic-structure', 'cms'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);
        $this->entitlementManager->applySignedPackage($package);
        $this->moduleManager->resetEnabledState();

        // academic-structure is entitled -> should succeed
        $this->assertTrue($this->moduleManager->enable('academic-structure'));

        // results is NOT entitled -> canEnable should return false and enable must throw exception
        $validation = $this->moduleManager->canEnable('results');
        $this->assertFalse($validation['can_enable']);
        $this->assertStringContainsString('not licensed or entitled', $validation['reason']);

        $this->expectException(ModuleDependencyException::class);
        $this->moduleManager->enable('results');
    }

    public function test_tampered_database_records_are_ignored_if_not_entitled(): void
    {
        // Deploy a limited license (only academic-structure)
        $package = $this->keyProvider->signPayload([
            'client_id' => 'egyitech_test',
            'tier' => 'starter',
            'licensed_modules' => ['academic-structure'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);
        $this->entitlementManager->applySignedPackage($package);

        // Attacker directly modifies enabled_modules in database SiteSetting to include 'results'
        SiteSetting::set('enabled_modules', ['academic-structure', 'results', 'admissions'], 'system', false);

        $this->moduleManager->flushMemoryCache();

        // results is in DB enabled_modules, BUT not entitled by cryptographic license
        $this->assertFalse($this->moduleManager->isEnabled('results'));
        $this->assertFalse($this->moduleManager->isEnabled('admissions'));
        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));
    }

    public function test_middleware_blocks_routes_for_unentitled_modules(): void
    {
        // Deploy license with admissions and academic-structure, excluding results
        $package = $this->keyProvider->signPayload([
            'client_id' => 'egyitech_test',
            'tier' => 'standard',
            'licensed_modules' => ['academic-structure', 'admissions'],
            'valid_until' => now()->addYear()->toISOString(),
            'nonce' => bin2hex(random_bytes(16)),
        ]);
        $this->entitlementManager->applySignedPackage($package);
        $this->moduleManager->resetEnabledState();

        // Enable academic-structure
        $this->moduleManager->enable('academic-structure');

        // Accessing academic-structure endpoint succeeds
        $this->getJson('/api/v1/colleges')->assertStatus(200);

        // Accessing results endpoint returns 404 (route isolated and concealed)
        $this->getJson('/api/v1/results/inquiry')->assertStatus(404);
    }

    public function test_without_license_all_modules_are_disabled_and_cannot_be_enabled(): void
    {
        $this->entitlementManager->reset();
        $this->moduleManager->resetEnabledState();

        $modules = ['academic-structure', 'admissions', 'academic-services', 'cms', 'events', 'documents', 'results'];
        foreach ($modules as $mod) {
            $this->assertFalse(
                $this->moduleManager->isEnabled($mod),
                "Module [{$mod}] must be disabled without license."
            );

            $validation = $this->moduleManager->canEnable($mod);
            $this->assertFalse(
                $validation['can_enable'],
                "Module [{$mod}] must NOT be eligible for enable without license."
            );
            $this->assertEquals('UNENTITLED_MODULE', $validation['error_code']);
        }

        $this->assertEmpty($this->moduleManager->getEnabledIds());
    }
}
