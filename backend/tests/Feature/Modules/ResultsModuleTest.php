<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\Results\Models\CourseResult;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ResultsModuleTest extends TestCase
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
        $this->installTestLicense(['academic-structure', 'academic-services', 'results']);
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('academic-services');
        $this->moduleManager->enable('results');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_results_module_metadata(): void
    {
        $module = $this->moduleManager->get('results');

        $this->assertNotNull($module);
        $this->assertEquals('results', $module->getId());
        $this->assertEquals('النتائج والتقديرات الأكاديمية', $module->getName('ar'));
        $this->assertEquals(['academic-structure', 'academic-services'], $module->getDependencies());
        $this->assertEquals([
            'course_results',
            'academic_terms',
        ], $module->getOwnedTables());
    }

    public function test_public_results_inquiry_accessible_when_enabled(): void
    {
        $response = $this->postJson('/api/v1/student-portal/results', [
            'student_id_number' => '20241001',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'student' => [
                    'id',
                    'student_id_number',
                    'student_name',
                    'email',
                    'program',
                    'current_level',
                    'status',
                ],
                'cumulative_gpa',
                'course_results',
                'transcript_metadata',
            ]);
    }

    public function test_public_results_simulate_registration_when_enabled(): void
    {
        $response = $this->postJson('/api/v1/student-portal/simulate-registration', [
            'student_id_number' => '20241001',
            'selected_courses' => [
                ['code' => 'CSE201', 'credits' => 3],
                ['code' => 'CSE202', 'credits' => 3],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_eligible', true);
    }

    public function test_public_results_routes_return_404_when_disabled(): void
    {
        $this->moduleManager->disable('results');

        $this->postJson('/api/v1/student-portal/results', [
            'student_id_number' => '20241001',
        ])
        ->assertStatus(404)
        ->assertJson([
            'error' => 'module_disabled',
            'module_id' => 'results',
        ]);

        $this->postJson('/api/v1/student-portal/simulate-registration', [
            'student_id_number' => '20241001',
            'selected_courses' => [
                ['code' => 'CSE201', 'credits' => 3],
            ],
        ])
        ->assertStatus(404)
        ->assertJson([
            'error' => 'module_disabled',
            'module_id' => 'results',
        ]);
    }
}
