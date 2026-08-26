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

        $this->installTestLicense(['academic-structure', 'admissions', 'student-portal']);

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

    public function test_module_enable_persists_across_subsequent_get_requests_and_refreshes(): void
    {
        // 1. Initially all modules disabled
        $this->assertFalse($this->moduleManager->isEnabled('academic-structure'));
        $initialResp = $this->getJson('/api/v1/modules');
        $initialResp->assertStatus(200);
        $academicData = collect($initialResp->json('data'))->firstWhere('id', 'academic-structure');
        $this->assertFalse($academicData['is_enabled']);

        // 2. Enable academic-structure via PATCH toggle
        $enableResp = $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true]);
        $enableResp->assertStatus(200)
            ->assertJson([
                'message' => 'Module [academic-structure] has been enabled successfully.',
                'data' => [
                    'id' => 'academic-structure',
                    'is_enabled' => true,
                ],
            ]);

        // 3. Immediately perform subsequent GET /api/v1/modules (simulates frontend refresh/re-fetch)
        $refreshResp = $this->getJson('/api/v1/modules');
        $refreshResp->assertStatus(200);
        $refreshedAcademic = collect($refreshResp->json('data'))->firstWhere('id', 'academic-structure');
        $this->assertTrue($refreshedAcademic['is_enabled'], 'Module must remain enabled after subsequent GET request / refresh');
        $this->assertEquals(1, $refreshResp->json('meta.enabled_count'));

        // 4. Verify DB SiteSetting contains academic-structure
        $this->assertDatabaseHas('site_settings', [
            'key' => 'enabled_modules',
        ]);
        $storedSetting = \App\Models\SiteSetting::get('enabled_modules');
        $this->assertIsArray($storedSetting);
        $this->assertContains('academic-structure', $storedSetting);
    }

    public function test_module_enable_persists_when_cache_is_flushed_via_database_layer(): void
    {
        // 1. Enable base module and dependent module
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])->assertStatus(200);
        $this->patchJson('/api/v1/modules/admissions/toggle', ['enabled' => true])->assertStatus(200);

        // 2. Wipe memory cache completely
        Cache::flush();

        // 3. Re-instantiate ModuleManager from container (simulating brand new HTTP request lifecycle)
        $freshManager = new ModuleManager(new \App\Core\DependencyValidator(), $this->app);
        $freshAcademic = new ApiTestAcademicModule();
        $freshAdmissions = new ApiTestAdmissionsModule();
        $freshManager->register($freshAcademic);
        $freshManager->register($freshAdmissions);

        $this->assertTrue($freshAcademic->isEnabled(), 'Module state must reload as enabled from Database SiteSetting when Cache is cold');
        $this->assertTrue($freshAdmissions->isEnabled(), 'Dependent module must reload as enabled from Database SiteSetting');
        $this->assertContains('academic-structure', $freshManager->getEnabledIds());
        $this->assertContains('admissions', $freshManager->getEnabledIds());

        // 4. Overwrite singleton in container and check API response
        $this->app->instance(ModuleManager::class, $freshManager);
        $getResp = $this->getJson('/api/v1/modules');
        $getResp->assertStatus(200);
        $modules = collect($getResp->json('data'));
        $this->assertTrue($modules->firstWhere('id', 'academic-structure')['is_enabled']);
        $this->assertTrue($modules->firstWhere('id', 'admissions')['is_enabled']);
        $this->assertEquals(2, $getResp->json('meta.enabled_count'));
    }

    public function test_module_disable_persists_across_subsequent_requests_and_refreshes(): void
    {
        // 1. Enable both modules
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])->assertStatus(200);
        $this->patchJson('/api/v1/modules/admissions/toggle', ['enabled' => true])->assertStatus(200);

        // 2. Disable dependent module
        $disableResp = $this->patchJson('/api/v1/modules/admissions/toggle', ['enabled' => false]);
        $disableResp->assertStatus(200)
            ->assertJson([
                'message' => 'Module [admissions] has been disabled successfully.',
                'data' => [
                    'id' => 'admissions',
                    'is_enabled' => false,
                ],
            ]);

        // 3. Subsequent GET request
        $getResp = $this->getJson('/api/v1/modules');
        $getResp->assertStatus(200);
        $modules = collect($getResp->json('data'));
        $this->assertTrue($modules->firstWhere('id', 'academic-structure')['is_enabled']);
        $this->assertFalse($modules->firstWhere('id', 'admissions')['is_enabled']);
        $this->assertEquals(1, $getResp->json('meta.enabled_count'));

        // 4. Verify DB SiteSetting
        $storedSetting = \App\Models\SiteSetting::get('enabled_modules');
        $this->assertContains('academic-structure', $storedSetting);
        $this->assertNotContains('admissions', $storedSetting);
    }

    public function test_rejected_disable_attempt_does_not_corrupt_enabled_persistence(): void
    {
        // 1. Enable academic-structure and admissions
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])->assertStatus(200);
        $this->patchJson('/api/v1/modules/admissions/toggle', ['enabled' => true])->assertStatus(200);

        // 2. Attempt to disable academic-structure (blocked by active dependent admissions)
        $failResp = $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => false]);
        $failResp->assertStatus(409)
            ->assertJson(['error' => 'dependency_conflict']);

        // 3. Subsequent GET request must confirm academic-structure remains enabled
        $getResp = $this->getJson('/api/v1/modules');
        $getResp->assertStatus(200);
        $modules = collect($getResp->json('data'));
        $this->assertTrue($modules->firstWhere('id', 'academic-structure')['is_enabled']);
        $this->assertTrue($modules->firstWhere('id', 'admissions')['is_enabled']);
    }

    public function test_multi_module_dependency_chain_persistence_and_lifecycle(): void
    {
        // Chain: student-portal depends on academic-structure
        // Try enabling student-portal without academic-structure -> 409
        $this->patchJson('/api/v1/modules/student-portal/toggle', ['enabled' => true])
            ->assertStatus(409);

        // Enable academic-structure
        $this->patchJson('/api/v1/modules/academic-structure/toggle', ['enabled' => true])
            ->assertStatus(200);

        // Now enabling student-portal succeeds
        $this->patchJson('/api/v1/modules/student-portal/toggle', ['enabled' => true])
            ->assertStatus(200);

        // Subsequent GET request
        $getResp = $this->getJson('/api/v1/modules');
        $modules = collect($getResp->json('data'));
        $this->assertTrue($modules->firstWhere('id', 'academic-structure')['is_enabled']);
        $this->assertTrue($modules->firstWhere('id', 'student-portal')['is_enabled']);
        $this->assertFalse($modules->firstWhere('id', 'admissions')['is_enabled']);
        $this->assertEquals(2, $getResp->json('meta.enabled_count'));
    }
}