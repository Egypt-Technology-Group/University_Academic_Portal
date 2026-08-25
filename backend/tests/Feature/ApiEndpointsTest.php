<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\AdmissionCycle;
use App\Models\Announcement;
use App\Models\Application;
use App\Models\College;
use App\Models\Department;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\FacultyProfile;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\Program;
use App\Models\StudentRecord;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test Academic Endpoints: Colleges
     */
    public function test_colleges_index_returns_active_colleges_with_counts(): void
    {
        $response = $this->getJson('/api/v1/colleges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'dean_name',
                        'about',
                        'vision',
                        'mission',
                        'banner_image',
                        'is_active',
                        'departments_count',
                        'programs_count',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(4, count($response->json('data')));
    }

    public function test_colleges_locale_awareness(): void
    {
        $responseEn = $this->withHeaders(['Accept-Language' => 'en'])->getJson('/api/v1/colleges');
        $responseEn->assertStatus(200);
        $namesEn = collect($responseEn->json('data'))->pluck('name')->all();
        $this->assertContains('Faculty of Engineering & Technology', $namesEn);

        $responseAr = $this->withHeaders(['Accept-Language' => 'ar'])->getJson('/api/v1/colleges');
        $responseAr->assertStatus(200);
        $namesAr = collect($responseAr->json('data'))->pluck('name')->all();
        $this->assertContains('كلية الهندسة والتكنولوجيا', $namesAr);

        $responseAll = $this->getJson('/api/v1/colleges?all_locales=1');
        $responseAll->assertStatus(200);
        $firstCollege = $responseAll->json('data.0');
        $this->assertIsArray($firstCollege['name']);
        $this->assertArrayHasKey('en', $firstCollege['name']);
        $this->assertArrayHasKey('ar', $firstCollege['name']);
    }

    public function test_get_college_by_slug(): void
    {
        $college = College::where('slug', 'faculty-of-engineering-and-technology')->first();
        $response = $this->getJson("/api/v1/colleges/{$college->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'dean_name',
                    'departments' => [
                        '*' => [
                            'id',
                            'college_id',
                            'name',
                            'slug',
                            'programs',
                        ],
                    ],
                    'faculty_profiles',
                ],
            ]);

        $response404 = $this->getJson('/api/v1/colleges/non-existent-college-slug');
        $response404->assertStatus(404);
    }

    /**
     * Test Academic Endpoints: Programs
     */
    public function test_programs_index_with_filters(): void
    {
        $response = $this->getJson('/api/v1/programs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'department_id',
                        'department_name',
                        'college_name',
                        'name',
                        'slug',
                        'degree_level',
                        'duration_years',
                        'credit_hours',
                        'curriculum',
                        'career_opportunities',
                        'tuition_fees',
                        'admission_requirements',
                        'is_active',
                    ],
                ],
            ]);

        // Filter by degree_level
        $bachelorResponse = $this->getJson('/api/v1/programs?degree_level=bachelor');
        $bachelorResponse->assertStatus(200);
        $this->assertNotEmpty($bachelorResponse->json('data'));

        // Filter by college_id
        $engineering = College::where('slug', 'faculty-of-engineering-and-technology')->first();
        $collegeFilteredResponse = $this->getJson("/api/v1/programs?college_id={$engineering->id}");
        $collegeFilteredResponse->assertStatus(200);
        $this->assertNotEmpty($collegeFilteredResponse->json('data'));

        // Search query
        $searchResponse = $this->getJson('/api/v1/programs?search=Artificial');
        $searchResponse->assertStatus(200);
        $this->assertNotEmpty($searchResponse->json('data'));
    }

    public function test_get_program_by_slug(): void
    {
        $program = Program::where('slug', 'bsc-artificial-intelligence-machine-learning')->first();
        $response = $this->getJson("/api/v1/programs/{$program->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'degree_level',
                    'duration_years',
                    'credit_hours',
                    'curriculum',
                    'career_opportunities',
                    'tuition_fees',
                    'admission_requirements',
                ],
            ]);

        $this->assertEquals($program->id, $response->json('data.id'));

        $response404 = $this->getJson('/api/v1/programs/non-existent-program');
        $response404->assertStatus(404);
    }

    /**
     * Test Academic Endpoints: Faculty
     */
    public function test_faculty_index_with_filters(): void
    {
        $response = $this->getJson('/api/v1/faculty');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'academic_title',
                        'bio',
                        'research_interests',
                        'email',
                        'phone',
                        'office_location',
                        'avatar',
                        'cv_path',
                        'is_featured',
                    ],
                ],
            ]);

        // Filter featured
        $featuredResponse = $this->getJson('/api/v1/faculty?is_featured=1');
        $featuredResponse->assertStatus(200);
        foreach ($featuredResponse->json('data') as $faculty) {
            $this->assertTrue($faculty['is_featured']);
        }

        // Search
        $searchResponse = $this->getJson('/api/v1/faculty?search=Mansour');
        $searchResponse->assertStatus(200);
        $this->assertNotEmpty($searchResponse->json('data'));
    }

    /**
     * Test Content Endpoints: News
     */
    public function test_news_index_with_filters(): void
    {
        $response = $this->getJson('/api/v1/news');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'excerpt',
                        'body',
                        'featured_image',
                        'is_featured',
                        'published_at',
                        'views_count',
                        'category',
                    ],
                ],
            ]);

        // Category filter
        $category = NewsCategory::first();
        $catResponse = $this->getJson("/api/v1/news?category={$category->slug}");
        $catResponse->assertStatus(200);

        // Featured filter
        $featuredResponse = $this->getJson('/api/v1/news?is_featured=1');
        $featuredResponse->assertStatus(200);
    }

    public function test_get_news_by_slug_increments_views(): void
    {
        $article = NewsArticle::first();
        $initialViews = $article->views_count;

        $response = $this->getJson("/api/v1/news/{$article->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'body',
                    'category',
                ],
                'related_articles',
            ]);

        $this->assertEquals($initialViews + 1, $article->fresh()->views_count);

        $response404 = $this->getJson('/api/v1/news/non-existent-news-slug');
        $response404->assertStatus(404);
    }

    /**
     * Test Content Endpoints: Events
     */
    public function test_events_index(): void
    {
        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'location',
                        'organizer',
                        'description',
                        'cover_image',
                        'start_time',
                        'end_time',
                    ],
                ],
            ]);
    }

    /**
     * Test Content Endpoints: Announcements
     */
    public function test_announcements_index_with_filters(): void
    {
        $response = $this->getJson('/api/v1/announcements');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'target_audience',
                        'priority',
                        'is_active',
                        'expires_at',
                        'created_at',
                    ],
                ],
            ]);

        // Urgent filter
        $urgentResponse = $this->getJson('/api/v1/announcements?urgent=1');
        $urgentResponse->assertStatus(200);
        foreach ($urgentResponse->json('data') as $item) {
            $this->assertEquals('urgent', $item['priority']);
        }

        // Audience filter
        $audienceResponse = $this->getJson('/api/v1/announcements?audience=students');
        $audienceResponse->assertStatus(200);
        $this->assertNotEmpty($audienceResponse->json('data'));
    }

    /**
     * Test Content Endpoints: Documents & Download counter
     */
    public function test_documents_index_and_grouped(): void
    {
        $response = $this->getJson('/api/v1/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'category',
                        'title',
                        'file_path',
                        'file_size',
                        'file_type',
                        'download_count',
                    ],
                ],
            ]);

        // Category filter
        $bylawsResponse = $this->getJson('/api/v1/documents?category=bylaws');
        $bylawsResponse->assertStatus(200);
        foreach ($bylawsResponse->json('data') as $doc) {
            $this->assertEquals('bylaws', $doc['category']);
        }

        // Grouped
        $groupedResponse = $this->getJson('/api/v1/documents?grouped=1');
        $groupedResponse->assertStatus(200);
        $this->assertArrayHasKey('bylaws', $groupedResponse->json('data'));
    }

    public function test_increment_document_download(): void
    {
        $document = DownloadDocument::first();
        $initialCount = $document->download_count;

        $response = $this->postJson("/api/v1/documents/{$document->id}/download");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'id' => $document->id,
                'download_count' => $initialCount + 1,
            ]);

        $this->assertEquals($initialCount + 1, $document->fresh()->download_count);
    }

    /**
     * Test Admission Endpoints: Active Cycle, Submit, Track
     */
    public function test_admissions_active_cycle(): void
    {
        $response = $this->getJson('/api/v1/admissions/active-cycle');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cycle' => [
                    'id',
                    'title',
                    'academic_year',
                    'term',
                    'start_date',
                    'end_date',
                    'is_open',
                ],
                'programs' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('cycle.is_open'));
    }

    public function test_admissions_submit_application(): void
    {
        $program = Program::first();
        $cycle = AdmissionCycle::where('is_open', true)->first();

        $payload = [
            'admission_cycle_id' => $cycle->id,
            'program_id' => $program->id,
            'first_name' => 'Tarek',
            'last_name' => 'Ali',
            'national_id' => '30101010109999',
            'email' => 'tarek.ali.test@gmail.com',
            'phone' => '+20 10 9999 8888',
            'high_school_score' => 92.50,
            'notes' => 'Test application submission via API.',
            'documents' => [
                ['type' => 'high_school_certificate', 'path' => 'applications/test-cert.pdf', 'verification_status' => 'pending'],
            ],
        ];

        $response = $this->postJson('/api/v1/admissions/apply', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'application_number',
                    'program',
                    'cycle',
                    'first_name',
                    'last_name',
                    'national_id',
                    'email',
                    'phone',
                    'high_school_score',
                    'status',
                    'notes',
                    'documents',
                ],
            ]);

        $appNumber = $response->json('data.application_number');
        $this->assertStringStartsWith('APP-' . date('Y') . '-', $appNumber);

        // Test tracking the submitted application
        $trackResponse = $this->getJson("/api/v1/admissions/track?application_number={$appNumber}&national_id=30101010109999");
        $trackResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'application_number' => $appNumber,
                    'first_name' => 'Tarek',
                    'status' => 'submitted',
                ],
            ]);
    }

    public function test_admissions_submit_validation_errors(): void
    {
        $response = $this->postJson('/api/v1/admissions/apply', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['program_id', 'first_name', 'last_name', 'national_id', 'email', 'phone', 'high_school_score']);
    }

    public function test_admissions_track_existing_application(): void
    {
        $app = Application::where('application_number', 'APP-2025-00101')->first();

        // Track with national_id
        $response = $this->getJson("/api/v1/admissions/track?application_number={$app->application_number}&national_id={$app->national_id}");
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'application_number' => 'APP-2025-00101',
                    'first_name' => 'Ahmed',
                    'status' => 'accepted',
                ],
            ]);

        // Track with email via POST
        $postResponse = $this->postJson('/api/v1/admissions/track', [
            'application_number' => $app->application_number,
            'email' => $app->email,
        ]);
        $postResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'application_number' => 'APP-2025-00101',
                ],
            ]);

        // Track not found
        $notFoundResponse = $this->getJson('/api/v1/admissions/track?application_number=APP-9999-00000&email=none@test.com');
        $notFoundResponse->assertStatus(404);
    }

    /**
     * Test Student Portal: Inquire Result
     */
    public function test_student_portal_inquire_results(): void
    {
        $student = StudentRecord::where('student_id_number', '20241001')->first();

        $response = $this->getJson("/api/v1/student-portal/results?student_id_number={$student->student_id_number}");

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
                'term_gpa',
                'course_results' => [
                    '*' => [
                        'id',
                        'course_code',
                        'course_name',
                        'credit_hours',
                        'grade',
                        'grade_points',
                        'is_published',
                    ],
                ],
            ]);

        $this->assertEquals('20241001', $response->json('student.student_id_number'));
        $this->assertEquals(3.88, $response->json('cumulative_gpa'));
    }

    public function test_student_portal_inquire_term_specific_results(): void
    {
        $student = StudentRecord::where('student_id_number', '20241001')->first();
        $term = AcademicTerm::first();

        $response = $this->getJson("/api/v1/student-portal/results?student_id_number={$student->student_id_number}&term_id={$term->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'student',
                'cumulative_gpa',
                'term_gpa',
                'academic_term',
                'course_results',
            ]);

        $this->assertNotNull($response->json('term_gpa'));
        $this->assertNotNull($response->json('academic_term'));

        // Non-existent student
        $notFoundResponse = $this->getJson('/api/v1/student-portal/results?student_id_number=99999999');
        $notFoundResponse->assertStatus(404);
    }

    public function test_admissions_submit_application_with_file_uploads(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $program = Program::first();
        $file = \Illuminate\Http\UploadedFile::fake()->create('certificate.pdf', 150, 'application/pdf');

        $payload = [
            'program_id' => $program->id,
            'first_name' => 'Sara',
            'last_name' => 'Ibrahim',
            'national_id' => '30303030308888',
            'email' => 'sara.ibrahim.upload@gmail.com',
            'phone' => '+20 12 8888 7777',
            'high_school_score' => 95.00,
            'documents' => [
                $file,
            ],
            'document_types' => [
                'high_school_certificate',
            ],
        ];

        $response = $this->post('/api/v1/admissions/apply', $payload);
        $response->assertStatus(201);
        $this->assertNotEmpty($response->json('data.documents'));
    }

    public function test_events_filter_past_and_upcoming(): void
    {
        $upcoming = $this->getJson('/api/v1/events?filter=upcoming');
        $upcoming->assertStatus(200);

        $past = $this->getJson('/api/v1/events?filter=past');
        $past->assertStatus(200);

        $all = $this->getJson('/api/v1/events?filter=all');
        $all->assertStatus(200);
    }

    public function test_auth_login_me_and_logout(): void
    {
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'SuperAdmin@2025!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'token',
                'user' => ['id', 'name', 'email', 'roles'],
            ]);

        $token = $loginResponse->json('token');

        // Test auth/me
        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');
        $meResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'admin@university.edu.eg');

        // Test auth/logout
        $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');
        $logoutResponse->assertStatus(200);
    }

    public function test_admin_dashboard_stats_and_application_review(): void
    {
        $user = \App\Models\User::where('email', 'admin@university.edu.eg')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        // Test Stats
        $statsResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/stats');
        $statsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_colleges',
                    'total_programs',
                    'total_applications',
                    'pending_applications',
                ],
            ]);

        // Test Application list
        $appsResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/applications');
        $appsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['total', 'current_page'],
            ]);

        // Test Update Application Status
        $app = Application::first();
        $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/admin/applications/{$app->id}/status", [
                'status' => 'accepted',
                'notes' => 'Passed committee review with flying colors.',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'accepted');
    }

    public function test_admin_cms_crud_endpoints(): void
    {
        $user = \App\Models\User::where('email', 'admin@university.edu.eg')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $category = NewsCategory::first();

        // 1. Create News
        $newsResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/admin/news', [
                'category_id' => $category->id,
                'title_ar' => 'خبر تجريبي جديد',
                'title_en' => 'New Experimental News',
                'body_ar' => 'محتوى الخبر باللغة العربية بالتفصيل.',
                'body_en' => 'Detailed news content in English.',
                'is_featured' => true,
            ]);
        $newsResponse->assertStatus(201);
        $newsId = $newsResponse->json('data.id');

        // Delete News
        $deleteNews = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/admin/news/{$newsId}");
        $deleteNews->assertStatus(200);

        // 2. Create Announcement
        $annResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/admin/announcements', [
                'title_ar' => 'إعلان هام للطلاب',
                'title_en' => 'Important Announcement',
                'content_ar' => 'تفاصيل الإعلان الهام.',
                'content_en' => 'Important announcement details.',
                'target_audience' => 'students',
                'priority' => 'urgent',
            ]);
        $annResponse->assertStatus(201);
        $annId = $annResponse->json('data.id');

        // Delete Announcement
        $deleteAnn = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/admin/announcements/{$annId}");
        $deleteAnn->assertStatus(200);
    }

    public function test_site_settings_public_and_admin_endpoints(): void
    {
        // 1. Public Settings Endpoint
        $publicResponse = $this->getJson('/api/v1/settings');
        $publicResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'settings' => [
                    'site_identity',
                    'theme_colors',
                    'president_message',
                    'hero_slider',
                    'contact_info',
                    'social_links',
                    'footer_info',
                ],
            ]);

        // 2. Admin Auth
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $loginResponse->json('token');

        // 3. Admin Get All Settings
        $adminSettings = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/settings');
        $adminSettings->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'settings',
            ]);

        // 4. Update Single Setting
        $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/v1/admin/settings/site_identity', [
                'value' => [
                    'name' => [
                        'ar' => 'جامعة إيجي تك للتكنولوجيا والعلوم المحدثة',
                        'en' => 'EgyiTech University Updated',
                    ],
                    'short_name' => [
                        'ar' => 'إيجي تك المحدثة',
                        'en' => 'EgyiTech New',
                    ],
                ],
                'group' => 'branding',
            ]);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // 5. Reset to defaults
        $resetResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/admin/settings/reset');
        $resetResponse->assertStatus(200);
    }

    public function test_academic_and_student_services_endpoints(): void
    {
        // 1. Public Exam Schedules
        $examResponse = $this->getJson('/api/v1/exam-schedules');
        $examResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);

        // 2. Public Statement Verification
        $verifyResponse = $this->getJson('/api/v1/verify-statement?code=CERT-2025-EG892144');
        $verifyResponse->assertStatus(200)
            ->assertJson([
                'valid' => true,
            ]);

        // 3. Admin Auth & Electronic Student Requests
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $loginResponse->json('token');

        $requestsResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/student-requests');
        $requestsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }
}

