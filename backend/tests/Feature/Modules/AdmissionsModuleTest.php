<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdmissionsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $moduleManager;
    protected User $adminUser;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->seed(DatabaseSeeder::class);

        $this->moduleManager = $this->app->make(ModuleManager::class);

        // Ensure both modules are enabled by default for tests
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_admissions_module_metadata(): void
    {
        $module = $this->moduleManager->get('admissions');

        $this->assertNotNull($module);
        $this->assertEquals('admissions', $module->getId());
        $this->assertEquals('القبول والتسجيل', $module->getName('ar'));
        $this->assertEquals('Admissions & Registration', $module->getName('en'));
        $this->assertEquals(['academic-structure'], $module->getDependencies());
        $this->assertEquals([
            'admission_cycles',
            'applications',
            'application_documents',
        ], $module->getOwnedTables());
    }

    public function test_public_admissions_routes_are_accessible_when_module_is_enabled(): void
    {
        $app = Application::first();

        $this->getJson('/api/v1/admissions/active-cycle')->assertStatus(200);

        $this->getJson("/api/v1/admissions/track?application_number={$app->application_number}&national_id={$app->national_id}")
            ->assertStatus(200);
    }

    public function test_public_admissions_routes_return_404_when_module_is_disabled(): void
    {
        $this->moduleManager->disable('admissions');

        $app = Application::first();

        $this->getJson('/api/v1/admissions/active-cycle')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'admissions',
            ]);

        $this->getJson("/api/v1/admissions/track?application_number={$app->application_number}&national_id={$app->national_id}")
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'admissions',
            ]);

        $this->postJson('/api/v1/admissions/apply', [])
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'admissions',
            ]);
    }

    public function test_admin_admissions_routes_are_accessible_when_enabled(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/applications');

        $response->assertStatus(200);
    }

    public function test_admin_admissions_routes_return_404_when_disabled(): void
    {
        $this->moduleManager->disable('admissions');

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/applications');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'admissions',
            ]);
    }

    public function test_cannot_enable_admissions_when_academic_structure_is_disabled(): void
    {
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        if ($this->moduleManager->isEnabled('academic-services')) {
            $this->moduleManager->disable('academic-services');
        }
        $this->moduleManager->disable('admissions');
        $this->moduleManager->disable('academic-structure');

        $canEnable = $this->moduleManager->canEnable('admissions');

        $this->assertFalse($canEnable['can_enable']);
        $this->assertContains('academic-structure', $canEnable['missing_dependencies']);

        // Via API toggle endpoint
        $response = $this->patchJson('/api/v1/modules/admissions/toggle', [
            'enabled' => true,
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'dependency_conflict',
            ]);

        $this->assertFalse($this->moduleManager->isEnabled('admissions'));
    }
}