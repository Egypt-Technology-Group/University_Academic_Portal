<?php

namespace Tests\Feature\Core;

use App\Core\BaseModule;
use App\Core\Contracts\ModuleInterface;
use App\Core\DependencyValidator;
use App\Core\Exceptions\ModuleDependencyException;
use App\Core\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TestAcademicModule extends BaseModule
{
    protected string $id = 'academic-structure';
    protected array $name = ['ar' => 'الهيكل الأكاديمي', 'en' => 'Academic Structure'];
    protected array $description = ['ar' => 'إدارة الكليات والأقسام والبرامج', 'en' => 'Manage colleges, departments, and programs'];
    protected string $version = '1.0.0';
    protected array $dependencies = [];
    protected array $ownedTables = ['colleges', 'departments', 'programs'];
    public bool $booted = false;

    public function boot(): void
    {
        parent::boot();
        $this->booted = true;
    }
}

class TestAdmissionsModule extends BaseModule
{
    protected string $id = 'admissions';
    protected array $name = ['ar' => 'القبول والتسجيل', 'en' => 'Admissions'];
    protected array $description = ['ar' => 'إدارة طلبات الالتحاق والتقديم', 'en' => 'Manage student applications and admission cycles'];
    protected string $version = '1.0.0';
    protected array $dependencies = ['academic-structure'];
    protected array $ownedTables = ['admission_cycles', 'applications', 'application_documents'];
    public bool $booted = false;

    public function boot(): void
    {
        parent::boot();
        $this->booted = true;
    }
}

class TestStudentPortalModule extends BaseModule
{
    protected string $id = 'student-portal';
    protected array $name = ['ar' => 'بوابة الطلاب والنتائج', 'en' => 'Student Portal'];
    protected array $description = ['ar' => 'استعلام نتائج الطلاب الأكاديمية', 'en' => 'Student academic results and records inquiry'];
    protected string $version = '1.0.0';
    protected array $dependencies = ['academic-structure'];
    protected array $ownedTables = ['student_records', 'course_results'];
    public bool $booted = false;

    public function boot(): void
    {
        parent::boot();
        $this->booted = true;
    }
}

class TestScholarshipsModule extends BaseModule
{
    protected string $id = 'scholarships';
    protected array $name = ['ar' => 'المنح الدراسية', 'en' => 'Scholarships'];
    protected array $description = ['ar' => 'إدارة المنح والتخفيضات', 'en' => 'Manage student scholarships'];
    protected string $version = '1.0.0';
    protected array $dependencies = ['admissions', 'student-portal'];
    protected array $ownedTables = ['scholarships'];
    public bool $booted = false;

    public function boot(): void
    {
        parent::boot();
        $this->booted = true;
    }
}

class TestCyclicModuleA extends BaseModule
{
    protected string $id = 'cyclic-a';
    protected array $dependencies = ['cyclic-b'];
}

class TestCyclicModuleB extends BaseModule
{
    protected string $id = 'cyclic-b';
    protected array $dependencies = ['cyclic-a'];
}

class ModuleManagerTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $manager;
    protected DependencyValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['modules.default_enabled' => []]);

        $this->validator = new DependencyValidator();
        $this->manager = new ModuleManager($this->validator, $this->app);
        $this->installTestLicense(['academic-structure', 'admissions', 'student-portal', 'analytics', 'scholarships']);
    }

    public function test_module_can_be_registered_and_retrieved(): void
    {
        $module = new TestAcademicModule();
        $this->manager->register($module);

        $this->assertTrue($this->manager->has('academic-structure'));
        $this->assertSame($module, $this->manager->get('academic-structure'));
        $this->assertArrayHasKey('academic-structure', $this->manager->all());
        $this->assertEquals('الهيكل الأكاديمي', $module->getName('ar'));
        $this->assertEquals('Academic Structure', $module->getName('en'));
        $this->assertEquals(['colleges', 'departments', 'programs'], $module->getOwnedTables());
    }

    public function test_cannot_enable_module_when_dependencies_are_missing(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);

        $check = $this->manager->canEnable('admissions');
        $this->assertFalse($check['can_enable']);
        $this->assertContains('academic-structure', $check['missing_dependencies']);
        $this->assertNotNull($check['reason']);

        $this->expectException(ModuleDependencyException::class);
        $this->manager->enable('admissions');
    }

    public function test_can_enable_module_when_dependencies_are_satisfied(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);

        // First enable academic-structure (has no dependencies)
        $this->assertTrue($this->manager->enable('academic-structure'));
        $this->assertTrue($academic->isEnabled());

        // Now admissions can be enabled
        $check = $this->manager->canEnable('admissions');
        $this->assertTrue($check['can_enable']);
        $this->assertEmpty($check['missing_dependencies']);

        $this->assertTrue($this->manager->enable('admissions'));
        $this->assertTrue($admissions->isEnabled());

        $enabledModules = $this->manager->getEnabled();
        $this->assertCount(2, $enabledModules);
    }

    public function test_dependency_validator_blocks_disabling_module_with_active_dependents(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);

        $this->manager->enable('academic-structure');
        $this->manager->enable('admissions');

        // Trying to disable academic-structure should be blocked because admissions depends on it
        $check = $this->manager->canDisable('academic-structure');
        $this->assertFalse($check['can_disable']);
        $this->assertContains('admissions', $check['blocking_dependents']);
        $this->assertNotNull($check['reason']);

        $this->expectException(ModuleDependencyException::class);
        $this->manager->disable('academic-structure');
    }

    public function test_can_disable_module_after_dependents_are_disabled(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);

        $this->manager->enable('academic-structure');
        $this->manager->enable('admissions');

        // Disable dependent first
        $this->assertTrue($this->manager->disable('admissions'));
        $this->assertFalse($admissions->isEnabled());

        // Now disabling academic-structure is allowed
        $check = $this->manager->canDisable('academic-structure');
        $this->assertTrue($check['can_disable']);
        $this->assertEmpty($check['blocking_dependents']);

        $this->assertTrue($this->manager->disable('academic-structure'));
        $this->assertFalse($academic->isEnabled());
        $this->assertEmpty($this->manager->getEnabled());
    }

    public function test_dependency_validator_detects_circular_dependencies(): void
    {
        $cyclicA = new TestCyclicModuleA();
        $cyclicB = new TestCyclicModuleB();

        $this->assertTrue($this->validator->hasCycles([$cyclicA, $cyclicB]));
        $cycles = $this->validator->detectCycles([$cyclicA, $cyclicB]);
        $this->assertNotEmpty($cycles);

        $this->manager->register($cyclicA);
        $this->manager->register($cyclicB);

        $checkA = $this->manager->canEnable('cyclic-a');
        $this->assertFalse($checkA['can_enable']);
    }

    public function test_topological_sort_orders_dependencies_before_dependents(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();
        $studentPortal = new TestStudentPortalModule();
        $scholarships = new TestScholarshipsModule();

        $this->manager->register($scholarships);
        $this->manager->register($admissions);
        $this->manager->register($studentPortal);
        $this->manager->register($academic);

        $this->manager->enable('academic-structure');
        $this->manager->enable('admissions');
        $this->manager->enable('student-portal');
        $this->manager->enable('scholarships');

        $enabled = $this->manager->getEnabled();
        $enabledIds = array_map(fn($m) => $m->getId(), $enabled);

        // academic-structure must be before admissions and student-portal
        $academicIdx = array_search('academic-structure', $enabledIds, true);
        $admissionsIdx = array_search('admissions', $enabledIds, true);
        $studentPortalIdx = array_search('student-portal', $enabledIds, true);
        $scholarshipsIdx = array_search('scholarships', $enabledIds, true);

        $this->assertLessThan($admissionsIdx, $academicIdx);
        $this->assertLessThan($studentPortalIdx, $academicIdx);
        $this->assertLessThan($scholarshipsIdx, $admissionsIdx);
        $this->assertLessThan($scholarshipsIdx, $studentPortalIdx);
    }

    public function test_boot_executes_on_enabled_modules(): void
    {
        // Configure academic-structure as enabled in default state
        config(['modules.default_enabled' => ['academic-structure']]);

        $manager = new ModuleManager($this->validator, $this->app);
        $academic = new TestAcademicModule();
        $manager->register($academic);

        $this->assertTrue($academic->isEnabled());
        $this->assertFalse($academic->booted);

        $manager->bootEnabledModules();

        $this->assertTrue($academic->booted);
    }

    public function test_enabling_module_automatically_boots_it_when_auto_boot_is_true(): void
    {
        $academic = new TestAcademicModule();
        $this->manager->register($academic);

        $this->assertFalse($academic->booted);

        $this->manager->enable('academic-structure');

        $this->assertTrue($academic->booted);
    }

    public function test_module_state_persists_across_manager_instances(): void
    {
        $academic = new TestAcademicModule();
        $this->manager->register($academic);
        $this->manager->enable('academic-structure');

        // Create a new fresh ModuleManager instance
        $newManager = new ModuleManager($this->validator, $this->app);
        $newAcademic = new TestAcademicModule();
        $newManager->register($newAcademic);

        $this->assertTrue($newAcademic->isEnabled());
        $this->assertContains('academic-structure', $newManager->getEnabledIds());
    }

    public function test_service_provider_registers_singleton(): void
    {
        $manager1 = $this->app->make(ModuleManager::class);
        $manager2 = $this->app->make('module.manager');

        $this->assertInstanceOf(ModuleManager::class, $manager1);
        $this->assertSame($manager1, $manager2);
    }

    public function test_persistence_reloads_from_database_when_cache_is_cold(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);
        $this->manager->enable('academic-structure');
        $this->manager->enable('admissions');

        // Verify state is stored in DB
        $this->assertDatabaseHas('site_settings', [
            'key' => 'enabled_modules',
        ]);
        $dbData = \App\Models\SiteSetting::get('enabled_modules');
        $this->assertContains('academic-structure', $dbData);
        $this->assertContains('admissions', $dbData);

        // Cold cache simulation
        Cache::flush();

        $coldManager = new ModuleManager($this->validator, $this->app);
        $coldAcademic = new TestAcademicModule();
        $coldAdmissions = new TestAdmissionsModule();
        $coldManager->register($coldAcademic);
        $coldManager->register($coldAdmissions);

        $this->assertTrue($coldAcademic->isEnabled());
        $this->assertTrue($coldAdmissions->isEnabled());
        $this->assertEquals(['academic-structure', 'admissions'], $coldManager->getEnabledIds());
    }

    public function test_disable_updates_both_cache_and_database_persistence(): void
    {
        $academic = new TestAcademicModule();
        $admissions = new TestAdmissionsModule();

        $this->manager->register($academic);
        $this->manager->register($admissions);
        $this->manager->enable('academic-structure');
        $this->manager->enable('admissions');

        // Disable admissions
        $this->manager->disable('admissions');

        $dbData = \App\Models\SiteSetting::get('enabled_modules');
        $this->assertContains('academic-structure', $dbData);
        $this->assertNotContains('admissions', $dbData);

        // Cold cache simulation
        Cache::flush();
        $freshManager = new ModuleManager($this->validator, $this->app);
        $freshAcademic = new TestAcademicModule();
        $freshAdmissions = new TestAdmissionsModule();
        $freshManager->register($freshAcademic);
        $freshManager->register($freshAdmissions);

        $this->assertTrue($freshAcademic->isEnabled());
        $this->assertFalse($freshAdmissions->isEnabled());
        $this->assertEquals(['academic-structure'], $freshManager->getEnabledIds());
    }
}
