<?php
declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\BaseModule;
use App\Core\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiTestAcademicModule extends BaseModule
{
    protected string $id = 'academic-structure';
    protected array $name = ['ar' => 'الهيكل الأكاديمي', 'en' => 'Academic Structure'];
    protected array $description = ['ar' => 'إدارة الكليات والأقسام والبرامج', 'en' => 'Manage colleges, departments, and programs'];
    protected string $version = '1.0.0';
    protected array $dependencies = [];
    protected array $ownedTables = ['colleges', 'departments', 'programs'];
}

class ApiTestAdmissionsModule extends BaseModule
{
    protected string $id = 'admissions';
    protected array $name = ['ar' => 'القبول والتسجيل', 'en' => 'Admissions'];
    protected array $description = ['ar' => 'إدارة طلبات الالتحاق', 'en' => 'Manage student applications'];
    protected string $version = '1.0.0';
    protected array $dependencies = ['academic-structure'];
    protected array $ownedTables = ['admission_cycles', 'applications'];
}

class ApiTestStudentPortalModule extends BaseModule
{
    protected string $id = 'student-portal';
    protected array $name = ['ar' => 'بوابة الطلاب والنتائج', 'en' => 'Student Portal'];
    protected array $description = ['ar' => 'استعلام نتائج الطلاب الأكاديمية', 'en' => 'Student academic results inquiry'];
    protected string $version = '1.0.0';
    protected array $dependencies = ['academic-structure'];
    protected array $ownedTables = ['student_records', 'course_results'];
}

class ModuleManagementApiTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['modules.default_enabled' => []]);

        $this->moduleManager = $this->app->make(ModuleManager::class);
        $this->moduleManager->reset();

        $this->moduleManager->register(new ApiTestAcademicModule());
        $this->moduleManager->register(new ApiTestAdmissionsModule());
        $this->moduleManager->register(new ApiTestStudentPortalModule());
    }

    public function test_index_lists_all_registered_modules_with_metadata(): void
    {
        $response = $this->getJson('/api/v1/modules');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name' => ['ar', 'en'],
                        'display_name',
                        'description' => ['ar', 'en'],
                        'display_description',
                        'version',
                        'dependencies',
                        'owned_tables',
                        'is_enabled',
                        'can_enable',
                        'can_disable',
                    ],
                ],
                'meta' => [
                    'total',
                    'enabled_count',
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
        $this->assertEquals(0, $response->json('meta.enabled_count'));
    }

    public function test_dependencies_endpoint_returns_module_dependency_graph_and_status(): void
    {
        $response = $this->getJson('/api/v1/modules/admissions/dependencies');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 'admissions',
                    'is_enabled' => false,
                    'dependencies' => ['academic-structure'],
                    'can_enable' => false,
                    'missing_dependencies' => ['academic-structure'],
                    'can_disable' => true,
                ],
            ]);

        // Dependents test for academic-structure
        $academicResp = $this->getJson('/api/v1/modules/academic-structure/dependencies');
        $academicResp->assertStatus(200);
        $dependents = $academicResp->json('data.dependents');
        $dependentIds = array_column($dependents, 'id');
        $this->assertContains('admissions', $dependentIds);
        $this->assertContains('student-portal', $dependentIds);
    }

    public function test_dependencies_endpoint_returns_404_for_unknown_module(): void
    {
        $response = $this->getJson('/api/v1/modules/non-existent/dependencies');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'module_not_found',
            ]);
    }

    public function test_toggle_endpoint_enables_valid_module(): void
    {
        $response = $this->patchJson('/api/v1/modules/academic-structure/toggle');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Module [academic-structure] has been enabled successfully.',
                'data' => [
                    'id' => 'academic-structure',
                    'is_enabled' => true,
                ],
            ]);

        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));
    }

    public function test_toggle_endpoint_rejects_enabling_module_with_unsatisfied_dependencies(): void
    {
        // academic-structure is not enabled yet, so enabling admissions must fail with 409
        $response = $this->patchJson('/api/v1/modules/admissions/toggle');

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'dependency_conflict',
            ]);

        $this->assertFalse($this->moduleManager->isEnabled('admissions'));
    }

    public function test_toggle_endpoint_rejects_disabling_module_with_active_dependents(): void
    {
        // Enable base and dependent
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        // Trying to toggle academic-structure (to disabled) should return 409
        $response = $this->patchJson('/api/v1/modules/academic-structure/toggle');

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'dependency_conflict',
            ]);

        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));
    }

    public function test_toggle_endpoint_supports_explicit_state_payload(): void
    {
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 'academic-structure',
                    'is_enabled' => true,
                ],
            ]);

        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));

        // Calling toggle with explicit enabled: true again should return 200 with already enabled
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Module [academic-structure] is already enabled.',
            ]);

        // Explicitly disable
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => false])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 'academic-structure',
                    'is_enabled' => false,
                ],
            ]);

        $this->assertFalse($this->moduleManager->isEnabled('academic-structure'));
    }

    public function test_toggle_endpoint_returns_404_for_unknown_module(): void
    {
        $response = $this->patchJson('/api/v1/modules/unknown-module/toggle');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'module_not_found',
            ]);
    }
}