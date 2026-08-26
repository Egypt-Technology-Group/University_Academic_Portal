<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AcademicStructureModuleTest extends TestCase
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
        $this->installTestLicense(['academic-structure', 'admissions']);

        // Ensure both modules are enabled by default for tests
        $this->moduleManager->enable('academic-structure');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_academic_structure_module_metadata(): void
    {
        $module = $this->moduleManager->get('academic-structure');

        $this->assertNotNull($module);
        $this->assertEquals('academic-structure', $module->getId());
        $this->assertEquals('الهيكل الأكاديمي', $module->getName('ar'));
        $this->assertEquals('Academic Structure', $module->getName('en'));
        $this->assertEmpty($module->getDependencies());
        $this->assertEquals([
            'colleges',
            'departments',
            'programs',
            'faculty_profiles',
        ], $module->getOwnedTables());
    }

    public function test_public_academic_structure_routes_are_accessible_when_module_is_enabled(): void
    {
        $college = College::first();
        $program = Program::first();

        $this->getJson('/api/v1/colleges')->assertStatus(200);
        $this->getJson("/api/v1/colleges/{$college->slug}")->assertStatus(200);
        $this->getJson('/api/v1/departments')->assertStatus(200);
        $this->getJson('/api/v1/programs')->assertStatus(200);
        $this->getJson("/api/v1/programs/{$program->slug}")->assertStatus(200);
        $this->getJson('/api/v1/faculty')->assertStatus(200);
    }

    public function test_public_academic_structure_routes_return_404_when_module_is_disabled(): void
    {
        // First disable dependents if any (e.g. results, academic-services, admissions)
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        if ($this->moduleManager->isEnabled('academic-services')) {
            $this->moduleManager->disable('academic-services');
        }
        if ($this->moduleManager->isEnabled('admissions')) {
            $this->moduleManager->disable('admissions');
        }
        $this->moduleManager->disable('academic-structure');

        $college = College::first();
        $program = Program::first();

        $this->getJson('/api/v1/colleges')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);

        $this->getJson("/api/v1/colleges/{$college->slug}")
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);

        $this->getJson('/api/v1/departments')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);

        $this->getJson('/api/v1/programs')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);

        $this->getJson("/api/v1/programs/{$program->slug}")
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);

        $this->getJson('/api/v1/faculty')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);
    }

    public function test_admin_academic_structure_routes_are_accessible_when_enabled(): void
    {
        $college = College::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/colleges/{$college->id}", [
                'name_ar' => 'كلية الهندسة المحدثة',
            ]);

        $response->assertStatus(200);
    }

    public function test_admin_academic_structure_routes_return_404_when_disabled(): void
    {
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        if ($this->moduleManager->isEnabled('academic-services')) {
            $this->moduleManager->disable('academic-services');
        }
        if ($this->moduleManager->isEnabled('admissions')) {
            $this->moduleManager->disable('admissions');
        }
        $this->moduleManager->disable('academic-structure');

        $college = College::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/colleges/{$college->id}", [
                'name_ar' => 'كلية الهندسة المحدثة',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);
    }

    public function test_cannot_disable_academic_structure_when_admissions_is_enabled(): void
    {
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        $canDisable = $this->moduleManager->canDisable('academic-structure');

        $this->assertFalse($canDisable['can_disable']);
        $this->assertContains('admissions', $canDisable['blocking_dependents']);

        // Via API toggle endpoint
        $response = $this->patchJson('/api/v1/modules/academic-structure/toggle', [
            'enabled' => false,
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'dependency_conflict',
            ]);

        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));
    }

    public function test_can_disable_academic_structure_after_disabling_admissions(): void
    {
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        // First disable dependents (results, academic-services, admissions)
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        if ($this->moduleManager->isEnabled('academic-services')) {
            $this->moduleManager->disable('academic-services');
        }
        $this->moduleManager->disable('admissions');
        $this->assertFalse($this->moduleManager->isEnabled('admissions'));

        // Now disabling academic-structure should succeed
        $canDisable = $this->moduleManager->canDisable('academic-structure');
        $this->assertTrue($canDisable['can_disable']);

        $disabled = $this->moduleManager->disable('academic-structure');
        $this->assertTrue($disabled);
        $this->assertFalse($this->moduleManager->isEnabled('academic-structure'));
    }
}