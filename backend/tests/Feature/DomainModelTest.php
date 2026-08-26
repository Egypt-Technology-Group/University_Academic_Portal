<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\CourseResult;
use App\Models\DownloadDocument;
use App\Modules\AcademicServices\Models\StudentRecord;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Models\NewsCategory;
use App\Modules\Events\Models\Event;
use App\Models\User;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_seeders_populate_all_domain_entities(): void
    {
        $this->assertGreaterThanOrEqual(4, College::count());
        $this->assertGreaterThanOrEqual(10, Department::count());
        $this->assertGreaterThanOrEqual(15, Program::count());
        $this->assertGreaterThanOrEqual(8, FacultyProfile::count());
        $this->assertGreaterThanOrEqual(4, NewsCategory::count());
        $this->assertGreaterThanOrEqual(6, NewsArticle::count());
        $this->assertGreaterThanOrEqual(4, Event::count());
        $this->assertGreaterThanOrEqual(5, Announcement::count());
        $this->assertGreaterThanOrEqual(6, DownloadDocument::count());
        $this->assertGreaterThanOrEqual(2, AdmissionCycle::count());
        $this->assertGreaterThanOrEqual(5, Application::count());
        $this->assertGreaterThanOrEqual(1, AcademicTerm::count());
        $this->assertGreaterThanOrEqual(5, StudentRecord::count());
        $this->assertGreaterThanOrEqual(10, CourseResult::count());
    }

    public function test_college_and_department_relationships_and_translations(): void
    {
        $college = College::where('slug', 'faculty-of-engineering-and-technology')->first();
        $this->assertNotNull($college);
        $this->assertEquals('Faculty of Engineering & Technology', $college->getTranslation('name', 'en'));
        $this->assertEquals('كلية الهندسة والتكنولوجيا', $college->getTranslation('name', 'ar'));
        $this->assertNotEmpty($college->getTranslation('dean_name', 'ar'));

        $this->assertGreaterThanOrEqual(2, $college->departments->count());

        $firstDept = $college->departments->first();
        $this->assertInstanceOf(Department::class, $firstDept);
        $this->assertEquals($college->id, $firstDept->college->id);
        $this->assertNotEmpty($firstDept->getTranslation('name', 'ar'));
        $this->assertGreaterThanOrEqual(1, $firstDept->programs->count());
    }

    public function test_program_curriculum_and_career_opportunities(): void
    {
        $program = Program::where('slug', 'bsc-artificial-intelligence-machine-learning')->first();
        $this->assertNotNull($program);
        $this->assertEquals('bachelor', $program->degree_level);
        $this->assertEquals(4, $program->duration_years);
        $this->assertEquals(136, $program->credit_hours);

        $curriculumEn = $program->getTranslation('curriculum', 'en');
        $this->assertIsArray($curriculumEn);
        $this->assertNotEmpty($curriculumEn);

        $careersAr = $program->getTranslation('career_opportunities', 'ar');
        $this->assertIsArray($careersAr);
        $this->assertNotEmpty($careersAr);
    }

    public function test_faculty_profile_relationships_and_user(): void
    {
        $profile = FacultyProfile::with(['user', 'department'])->first();
        $this->assertNotNull($profile);
        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertInstanceOf(Department::class, $profile->department);
        $this->assertNotEmpty($profile->getTranslation('academic_title', 'en'));
        $this->assertNotEmpty($profile->getTranslation('bio', 'ar'));
    }

    public function test_news_articles_and_categories(): void
    {
        $category = NewsCategory::with('articles')->first();
        $this->assertNotNull($category);
        $this->assertNotEmpty($category->getTranslation('name', 'en'));
        $this->assertNotEmpty($category->getTranslation('name', 'ar'));

        $article = NewsArticle::with('category')->first();
        $this->assertNotNull($article);
        $this->assertNotNull($article->category);
        $this->assertNotEmpty($article->getTranslation('title', 'ar'));
        $this->assertNotEmpty($article->getTranslation('body', 'en'));
    }

    public function test_student_records_and_course_results(): void
    {
        $student = StudentRecord::with(['user', 'program', 'courseResults.academicTerm'])->first();
        $this->assertNotNull($student);
        $this->assertInstanceOf(User::class, $student->user);
        $this->assertInstanceOf(Program::class, $student->program);
        $this->assertGreaterThan(0, $student->courseResults->count());

        $firstResult = $student->courseResults->first();
        $this->assertInstanceOf(CourseResult::class, $firstResult);
        $this->assertInstanceOf(AcademicTerm::class, $firstResult->academicTerm);
        $this->assertNotEmpty($firstResult->getTranslation('course_name', 'en'));
        $this->assertNotEmpty($firstResult->grade);
    }

    public function test_applications_and_documents(): void
    {
        $application = Application::with(['admissionCycle', 'program', 'documents'])->first();
        $this->assertNotNull($application);
        $this->assertInstanceOf(AdmissionCycle::class, $application->admissionCycle);
        $this->assertInstanceOf(Program::class, $application->program);
        $this->assertGreaterThan(0, $application->documents->count());
    }

    public function test_announcements_and_events(): void
    {
        $urgentAnn = Announcement::where('priority', 'urgent')->first();
        $this->assertNotNull($urgentAnn);
        $this->assertTrue($urgentAnn->is_active);
        $this->assertNotEmpty($urgentAnn->getTranslation('title', 'ar'));

        $event = Event::first();
        $this->assertNotNull($event);
        $this->assertNotNull($event->start_time);
        $this->assertNotEmpty($event->getTranslation('location', 'ar'));
    }

    public function test_download_documents(): void
    {
        $doc = DownloadDocument::where('category', 'bylaws')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('bylaws', $doc->category);
        $this->assertNotEmpty($doc->getTranslation('title', 'en'));
        $this->assertNotEmpty($doc->getTranslation('title', 'ar'));
    }
}
