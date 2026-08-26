<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\OfficialStatement;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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

    public function test_student_service_request_lifecycle_and_crud(): void
    {
        // 1. Submit request via public endpoint
        $submitResponse = $this->postJson('/api/v1/student-services/apply', [
            'student_id_number' => 'STU-99001',
            'student_name' => 'Sara Mahmoud',
            'service_type' => 'enrollment_cert',
            'purpose_ar' => 'تقديم للسفارة',
            'purpose_en' => 'Embassy Submission',
            'fee_amount' => 100.00,
        ]);

        $submitResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $requestId = $submitResponse->json('data.id');
        $this->assertNotNull($requestId);
        $this->assertDatabaseHas('student_service_requests', [
            'id' => $requestId,
            'student_id_number' => 'STU-99001',
            'status' => 'pending',
        ]);

        // 2. Admin updates request status
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/student-requests/{$requestId}/status", [
                'status' => 'approved',
                'admin_notes' => 'Verified and stamped.',
                'handled_by' => 'Admin Officer',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('student_service_requests', [
            'id' => $requestId,
            'status' => 'approved',
            'admin_notes' => 'Verified and stamped.',
        ]);

        // 3. Admin deletes request
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/student-requests/{$requestId}");

        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('student_service_requests', ['id' => $requestId]);
    }

    public function test_official_statement_issuance_and_verification(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('statement.pdf', 500, 'application/pdf');

        $issueResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/official-statements/issue', [
                'student_id_number' => 'STU-88221',
                'student_name' => 'Omar Farooq',
                'national_id' => '29901011234567',
                'statement_type' => 'transcript',
                'workflow_mode' => 'both',
                'title_ar' => 'شهادة تخرج مؤقتة',
                'title_en' => 'Provisional Graduation Certificate',
                'recipient_entity_ar' => 'نقابة المهندسين',
                'recipient_entity_en' => 'Engineers Syndicate',
                'signatory_name' => 'Prof. Dr. Dean',
                'signatory_title' => 'Dean of Faculty',
                'valid_months' => 12,
                'document' => $file,
            ]);

        $issueResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $certCode = $issueResponse->json('data.certificate_code');
        $hash = $issueResponse->json('data.verification_hash');
        $this->assertNotNull($certCode);

        // Verification query
        $verifyResponse = $this->getJson("/api/v1/verify-statement?code={$certCode}&hash=" . substr($hash, 0, 8));
        $verifyResponse->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'is_revoked' => false,
            ]);
    }

    public function test_exam_schedule_crud(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('exam_timetable.pdf', 300, 'application/pdf');

        // 1. Create exam schedule
        $storeResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/exam-schedules', [
                'course_code' => 'CS301',
                'course_name_ar' => 'هندسة البرمجيات',
                'course_name_en' => 'Software Engineering',
                'exam_type' => 'final',
                'workflow_mode' => 'both',
                'exam_date' => '2026-06-15',
                'start_time' => '10:00',
                'end_time' => '13:00',
                'hall_location_ar' => 'مدرج أ',
                'hall_location_en' => 'Hall A',
                'seating_capacity' => 120,
                'timetable_document' => $file,
            ]);

        $storeResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $examId = $storeResponse->json('data.id');
        $this->assertNotNull($examId);

        // 2. Update exam schedule
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->putJson("/api/v1/admin/exam-schedules/{$examId}", [
                'seating_capacity' => 150,
                'hall_location_en' => 'Main Hall 1',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('exam_schedules', [
            'id' => $examId,
            'seating_capacity' => 150,
        ]);

        // 3. Delete exam schedule
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/exam-schedules/{$examId}");

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('exam_schedules', ['id' => $examId]);
    }
}
