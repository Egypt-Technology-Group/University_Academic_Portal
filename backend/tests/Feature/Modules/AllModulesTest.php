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

class AllModulesTest extends TestCase
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

        // Ensure all modules are enabled by default for tests
        $allModules = [
            'academic-structure',
            'admissions',
            'academic-services',
            'cms',
            'events',
            'documents',
            'results',
        ];
        $this->installTestLicense($allModules);
        foreach ($allModules as $mod) {
            $this->moduleManager->enable($mod);
        }

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    /**
     * Test 1: Verify all 7 domain modules metadata and registration.
     */
    public function test_all_modules_registered_with_correct_metadata(): void
    {
        $modules = $this->moduleManager->all();
        $this->assertCount(7, $modules);

        // Academic Services
        $academicServices = $this->moduleManager->get('academic-services');
        $this->assertNotNull($academicServices);
        $this->assertEquals(['academic-structure'], $academicServices->getDependencies());
        $this->assertEquals([
            'student_records',
            'student_service_requests',
            'exam_schedules',
            'official_statements',
        ], $academicServices->getOwnedTables());

        // CMS
        $cms = $this->moduleManager->get('cms');
        $this->assertNotNull($cms);
        $this->assertEquals([], $cms->getDependencies());
        $this->assertEquals([
            'news_categories',
            'news_articles',
            'announcements',
        ], $cms->getOwnedTables());

        // Events
        $events = $this->moduleManager->get('events');
        $this->assertNotNull($events);
        $this->assertEquals([], $events->getDependencies());
        $this->assertEquals([
            'events',
            'event_attendees',
        ], $events->getOwnedTables());

        // Documents
        $documents = $this->moduleManager->get('documents');
        $this->assertNotNull($documents);
        $this->assertEquals([], $documents->getDependencies());
        $this->assertEquals([
            'download_documents',
        ], $documents->getOwnedTables());

        // Results
        $results = $this->moduleManager->get('results');
        $this->assertNotNull($results);
        $this->assertEquals(['academic-structure', 'academic-services'], $results->getDependencies());
        $this->assertEquals([
            'course_results',
            'academic_terms',
        ], $results->getOwnedTables());
    }

    /**
     * Test 2: Academic Services Module route isolation.
     */
    public function test_academic_services_routes_isolation(): void
    {
        // When enabled
        $this->getJson('/api/v1/exam-schedules')->assertStatus(200);
        $this->getJson('/api/v1/student-services/requests')->assertStatus(200);

        // Disable results first (it depends on academic-services)
        $this->moduleManager->disable('results');
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

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/student-requests')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'academic-services',
            ]);
    }

    /**
     * Test 3: CMS Module route isolation.
     */
    public function test_cms_routes_isolation(): void
    {
        // When enabled
        $this->getJson('/api/v1/news')->assertStatus(200);
        $this->getJson('/api/v1/announcements')->assertStatus(200);

        $this->moduleManager->disable('cms');

        $this->getJson('/api/v1/news')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'cms',
            ]);

        $this->getJson('/api/v1/announcements')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'cms',
            ]);

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/news', [
                'category_id' => 1,
                'title_ar' => 'خبر تجريبي جديد',
                'title_en' => 'New Experimental News',
                'body_ar' => 'محتوى الخبر',
                'body_en' => 'News content',
            ])
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'cms',
            ]);
    }

    /**
     * Test 4: Events Module route isolation.
     */
    public function test_events_routes_isolation(): void
    {
        // When enabled
        $this->getJson('/api/v1/events')->assertStatus(200);

        $this->moduleManager->disable('events');

        $this->getJson('/api/v1/events')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'events',
            ]);

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/events', [
                'title_ar' => 'فعالية تجريبية',
                'title_en' => 'Test Event',
                'location_ar' => 'المقر الرئيسي',
                'location_en' => 'Main Campus',
                'organizer_ar' => 'إدارة الكلية',
                'organizer_en' => 'College Admin',
                'description_ar' => 'وصف الفعالية',
                'description_en' => 'Event description',
                'start_time' => now()->addDays(2)->toIso8601String(),
            ])
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'events',
            ]);
    }

    /**
     * Test 5: Documents Module route isolation.
     */
    public function test_documents_routes_isolation(): void
    {
        // When enabled
        $this->getJson('/api/v1/documents')->assertStatus(200);

        $this->moduleManager->disable('documents');

        $this->getJson('/api/v1/documents')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'documents',
            ]);

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/documents', [
                'category' => 'bylaws',
                'title_ar' => 'لائحة جديدة',
                'title_en' => 'New Bylaw',
            ])
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'documents',
            ]);
    }

    /**
     * Test 6: Results Module route isolation.
     */
    public function test_results_routes_isolation(): void
    {
        // When enabled
        $this->getJson('/api/v1/student-portal/results?student_id_number=20241001')
            ->assertStatus(200);

        $this->moduleManager->disable('results');

        $this->getJson('/api/v1/student-portal/results?student_id_number=20241001')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'results',
            ]);
    }

    /**
     * Test 7: Multi-level dependency chain validation.
     * results depends on academic-structure AND academic-services.
     * academic-services depends on academic-structure.
     */
    public function test_results_multi_level_dependency_enforcement(): void
    {
        // Disabling academic-structure must fail when results is enabled
        $this->assertTrue($this->moduleManager->isEnabled('results'));
        $this->assertTrue($this->moduleManager->isEnabled('academic-services'));
        $this->assertTrue($this->moduleManager->isEnabled('academic-structure'));

        $canDisableStructure = $this->moduleManager->canDisable('academic-structure');
        $this->assertFalse($canDisableStructure['can_disable']);
        $this->assertContains('results', $canDisableStructure['blocking_dependents']);
        $this->assertContains('academic-services', $canDisableStructure['blocking_dependents']);

        // Disabling academic-services must fail when results is enabled
        $canDisableServices = $this->moduleManager->canDisable('academic-services');
        $this->assertFalse($canDisableServices['can_disable']);
        $this->assertContains('results', $canDisableServices['blocking_dependents']);

        // Cannot enable results if academic-services is disabled
        $this->moduleManager->disable('results');
        $this->moduleManager->disable('academic-services');

        $canEnableResults = $this->moduleManager->canEnable('results');
        $this->assertFalse($canEnableResults['can_enable']);
        $this->assertContains('academic-services', $canEnableResults['missing_dependencies']);

        // Enable academic-services -> now results can be enabled
        $this->moduleManager->enable('academic-services');
        $canEnableResults = $this->moduleManager->canEnable('results');
        $this->assertTrue($canEnableResults['can_enable']);

        $this->moduleManager->enable('results');
        $this->assertTrue($this->moduleManager->isEnabled('results'));
    }
}