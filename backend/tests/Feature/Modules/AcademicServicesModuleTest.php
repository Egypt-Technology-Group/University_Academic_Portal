<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\OfficialStatement;
use App\Modules\AcademicServices\Models\StudentRecord;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AcademicServicesModuleTest extends TestCase
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
        $this->moduleManager->enable('academic-services');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_academic_services_module_metadata(): void
    {
        $module = $this->moduleManager->get('academic-services');

        $this->assertNotNull($module);
        $this->assertEquals('academic-services', $module->getId());
        $this->assertEquals('الخدمات الأكاديمية والطلابية', $module->getName('ar'));
        $this->assertEquals('Academic & Student Services', $module->getName('en'));
        $this->assertEquals(['academic-structure'], $module->getDependencies());
        $this->assertEquals([
            'student_records',
            'student_service_requests',
            'exam_schedules',
            'official_statements',
        ], $module->getOwnedTables());
    }

    public function test_public_academic_services_routes_are_accessible_when_module_is_enabled(): void
    {
        $this->getJson('/api/v1/exam-schedules')->assertStatus(200);
        $this->getJson('/api/v1/student-services/requests')->assertStatus(200);

        $stmt = OfficialStatement::first();
        if ($stmt) {
            $this->getJson("/api/v1/verify-statement?code={$stmt->certificate_code}")
                ->assertStatus(200);
        }
    }

    public function test_public_academic_services_routes_return_404_when_module_is_disabled(): void
    {
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        $this->moduleManager->disable('academic-services');

        $this->getJson('/api/v1/exam-schedules')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-services',
            ]);

        $this->getJson('/api/v1/student-services/requests')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-services',
            ]);

        $this->postJson('/api/v1/student-services/apply', [])
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-services',
            ]);
    }

    public function test_admin_academic_services_routes_are_accessible_when_enabled(): void
    {
        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/student-requests')
            ->assertStatus(200);

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/official-statements')
            ->assertStatus(200);

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/exam-schedules')
            ->assertStatus(200);
    }

    public function test_admin_academic_services_routes_return_404_when_disabled(): void
    {
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        $this->moduleManager->disable('academic-services');

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/student-requests')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-services',
            ]);
    }

    public function test_cannot_enable_academic_services_when_academic_structure_is_disabled(): void
    {
        if ($this->moduleManager->isEnabled('results')) {
            $this->moduleManager->disable('results');
        }
        if ($this->moduleManager->isEnabled('admissions')) {
            $this->moduleManager->disable('admissions');
        }
        $this->moduleManager->disable('academic-services');
        $this->moduleManager->disable('academic-structure');

        $canEnable = $this->moduleManager->canEnable('academic-services');

        $this->assertFalse($canEnable['can_enable']);
        $this->assertContains('academic-structure', $canEnable['missing_dependencies']);

        // Via API toggle endpoint
        $response = $this->patchJson('/api/v1/modules/academic-services/toggle', [
            'enabled' => true,
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'dependency_conflict',
            ]);

        $this->assertFalse($this->moduleManager->isEnabled('academic-services'));
    }
}
