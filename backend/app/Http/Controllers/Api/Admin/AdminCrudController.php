<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\CollegeResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\FacultyResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\ProgramResource;
use App\Models\Announcement;
use App\Models\College;
use App\Models\Department;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\FacultyProfile;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCrudController extends Controller
{
    // News Articles
    public function storeNews(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:news_categories,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'excerpt_ar' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'body_ar' => 'required|string',
            'body_en' => 'required|string',
            'featured_image' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $slug = Str::slug($validated['title_en']).'-'.rand(100, 999);

        $article = NewsArticle::create([
            'category_id' => $validated['category_id'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'slug' => $slug,
            'excerpt' => ['ar' => $validated['excerpt_ar'] ?? '', 'en' => $validated['excerpt_en'] ?? ''],
            'body' => ['ar' => $validated['body_ar'], 'en' => $validated['body_en']],
            'featured_image' => $validated['featured_image'] ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
            'is_featured' => $validated['is_featured'] ?? false,
            'published_at' => now(),
            'views_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'News article published successfully.',
            'data' => new NewsResource($article),
        ], 201);
    }

    public function deleteNews(int $id): JsonResponse
    {
        $article = NewsArticle::findOrFail($id);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'News article removed successfully.',
        ]);
    }

    // Announcements
    public function storeAnnouncement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'target_audience' => 'required|in:all,students,faculty,public',
            'priority' => 'required|in:normal,urgent,pinned',
        ]);

        $announcement = Announcement::create([
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'content' => ['ar' => $validated['content_ar'], 'en' => $validated['content_en']],
            'target_audience' => $validated['target_audience'],
            'priority' => $validated['priority'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function deleteAnnouncement(int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }

    // Events
    public function storeEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'location_ar' => 'required|string',
            'location_en' => 'required|string',
            'organizer_ar' => 'required|string',
            'organizer_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
        ]);

        $slug = Str::slug($validated['title_en']).'-'.rand(100, 999);

        $event = Event::create([
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'slug' => $slug,
            'location' => ['ar' => $validated['location_ar'], 'en' => $validated['location_en']],
            'organizer' => ['ar' => $validated['organizer_ar'], 'en' => $validated['organizer_en']],
            'description' => ['ar' => $validated['description_ar'], 'en' => $validated['description_en']],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'cover_image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'data' => new EventResource($event),
        ], 201);
    }

    public function deleteEvent(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event cancelled and removed.',
        ]);
    }

    // Documents & Regulations Repository
    public function storeDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200', // 50MB max
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        $filePath = $validated['file_path'] ?? '/downloads/regulation_sample.pdf';
        $fileSize = $validated['file_size'] ?? '2.4 MB';
        $fileType = $validated['file_type'] ?? 'PDF';

        // Process real uploaded file from device
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $filePath = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $fileType = strtoupper($uploadedFile->getClientOriginalExtension());
        }

        $doc = DownloadDocument::create([
            'category' => $validated['category'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'description' => [
                'ar' => $validated['description_ar'] ?? '',
                'en' => $validated['description_en'] ?? '',
            ],
            'version' => $validated['version'] ?? '1.0',
            'status' => $validated['status'] ?? 'published',
            'target_audience' => $validated['target_audience'] ?? 'all',
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_archived' => false,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'download_count' => 0,
            'effective_date' => $validated['effective_date'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded and published to document & regulations repository.',
            'data' => new DocumentResource($doc),
        ], 201);
    }

    public function updateDocument(Request $request, int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);

        $validated = $request->validate([
            'category' => 'nullable|string',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200',
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $doc->file_path = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $doc->file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $doc->file_type = strtoupper($uploadedFile->getClientOriginalExtension());
        } elseif (isset($validated['file_path'])) {
            $doc->file_path = $validated['file_path'];
            if (isset($validated['file_size'])) $doc->file_size = $validated['file_size'];
            if (isset($validated['file_type'])) $doc->file_type = $validated['file_type'];
        }

        if (isset($validated['category'])) $doc->category = $validated['category'];
        if (isset($validated['title_ar'])) $doc->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $doc->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['description_ar'])) $doc->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $doc->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['version'])) $doc->version = $validated['version'];
        if (isset($validated['status'])) $doc->status = $validated['status'];
        if (isset($validated['target_audience'])) $doc->target_audience = $validated['target_audience'];
        if (isset($validated['is_featured'])) $doc->is_featured = (bool) $validated['is_featured'];
        if (isset($validated['is_archived'])) $doc->is_archived = (bool) $validated['is_archived'];
        if (isset($validated['effective_date'])) $doc->effective_date = $validated['effective_date'];

        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Document & regulations updated successfully.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function toggleArchiveDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->is_archived = !$doc->is_archived;
        $doc->status = $doc->is_archived ? 'archived' : 'published';
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => $doc->is_archived ? 'Document archived.' : 'Document unarchived and restored.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function deleteDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document permanently deleted.',
        ]);
    }

    // ----------------------------------------------------
    // Colleges & Institutes CRUD Management
    // ----------------------------------------------------
    public function storeCollege(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'dean_name_ar' => 'nullable|string|max:255',
            'dean_name_en' => 'nullable|string|max:255',
            'about_ar' => 'nullable|string',
            'about_en' => 'nullable|string',
            'vision_ar' => 'nullable|string',
            'vision_en' => 'nullable|string',
            'mission_ar' => 'nullable|string',
            'mission_en' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $slug = Str::slug($validated['name_en']).'-'.rand(10, 99);

        $college = College::create([
            'name' => ['ar' => $validated['name_ar'], 'en' => $validated['name_en']],
            'slug' => $slug,
            'dean_name' => ['ar' => $validated['dean_name_ar'] ?? '', 'en' => $validated['dean_name_en'] ?? ''],
            'about' => ['ar' => $validated['about_ar'] ?? '', 'en' => $validated['about_en'] ?? ''],
            'vision' => ['ar' => $validated['vision_ar'] ?? '', 'en' => $validated['vision_en'] ?? ''],
            'mission' => ['ar' => $validated['mission_ar'] ?? '', 'en' => $validated['mission_en'] ?? ''],
            'banner_image' => $validated['banner_image'] ?? 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'College/Institute created successfully.',
            'data' => new CollegeResource($college),
        ], 201);
    }

    public function updateCollege(Request $request, int $id): JsonResponse
    {
        $college = College::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'dean_name_ar' => 'nullable|string|max:255',
            'dean_name_en' => 'nullable|string|max:255',
            'about_ar' => 'nullable|string',
            'about_en' => 'nullable|string',
            'vision_ar' => 'nullable|string',
            'vision_en' => 'nullable|string',
            'mission_ar' => 'nullable|string',
            'mission_en' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if (isset($validated['name_ar'])) $college->setTranslation('name', 'ar', $validated['name_ar']);
        if (isset($validated['name_en'])) $college->setTranslation('name', 'en', $validated['name_en']);
        if (isset($validated['dean_name_ar'])) $college->setTranslation('dean_name', 'ar', $validated['dean_name_ar']);
        if (isset($validated['dean_name_en'])) $college->setTranslation('dean_name', 'en', $validated['dean_name_en']);
        if (isset($validated['about_ar'])) $college->setTranslation('about', 'ar', $validated['about_ar']);
        if (isset($validated['about_en'])) $college->setTranslation('about', 'en', $validated['about_en']);
        if (isset($validated['vision_ar'])) $college->setTranslation('vision', 'ar', $validated['vision_ar']);
        if (isset($validated['vision_en'])) $college->setTranslation('vision', 'en', $validated['vision_en']);
        if (isset($validated['mission_ar'])) $college->setTranslation('mission', 'ar', $validated['mission_ar']);
        if (isset($validated['mission_en'])) $college->setTranslation('mission', 'en', $validated['mission_en']);
        if (isset($validated['banner_image'])) $college->banner_image = $validated['banner_image'];
        if (isset($validated['is_active'])) $college->is_active = (bool) $validated['is_active'];
        if (isset($validated['sort_order'])) $college->sort_order = (int) $validated['sort_order'];

        $college->save();

        return response()->json([
            'success' => true,
            'message' => 'College updated successfully.',
            'data' => new CollegeResource($college),
        ]);
    }

    public function deleteCollege(int $id): JsonResponse
    {
        $college = College::findOrFail($id);
        $college->delete();

        return response()->json([
            'success' => true,
            'message' => 'College deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Departments CRUD Management
    // ----------------------------------------------------
    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'head_name_ar' => 'nullable|string|max:255',
            'head_name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        $slug = Str::slug($validated['name_en']).'-'.rand(10, 99);

        $dept = Department::create([
            'college_id' => $validated['college_id'],
            'name' => ['ar' => $validated['name_ar'], 'en' => $validated['name_en']],
            'slug' => $slug,
            'head_name' => ['ar' => $validated['head_name_ar'] ?? '', 'en' => $validated['head_name_en'] ?? ''],
            'description' => ['ar' => $validated['description_ar'] ?? '', 'en' => $validated['description_en'] ?? ''],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data' => $dept,
        ], 201);
    }

    public function updateDepartment(Request $request, int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);

        $validated = $request->validate([
            'college_id' => 'sometimes|exists:colleges,id',
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'head_name_ar' => 'nullable|string|max:255',
            'head_name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        if (isset($validated['college_id'])) $dept->college_id = $validated['college_id'];
        if (isset($validated['name_ar'])) $dept->setTranslation('name', 'ar', $validated['name_ar']);
        if (isset($validated['name_en'])) $dept->setTranslation('name', 'en', $validated['name_en']);
        if (isset($validated['head_name_ar'])) $dept->setTranslation('head_name', 'ar', $validated['head_name_ar']);
        if (isset($validated['head_name_en'])) $dept->setTranslation('head_name', 'en', $validated['head_name_en']);
        if (isset($validated['description_ar'])) $dept->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $dept->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['sort_order'])) $dept->sort_order = (int) $validated['sort_order'];

        $dept->save();

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'data' => $dept,
        ]);
    }

    public function deleteDepartment(int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Degree Programs & Curriculum CRUD Management
    // ----------------------------------------------------
    public function storeProgram(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'degree_level' => 'required|in:bachelor,master,doctorate,diploma',
            'duration_years' => 'required|integer|min:1|max:8',
            'credit_hours' => 'required|integer|min:10|max:300',
            'curriculum_ar' => 'nullable',
            'curriculum_en' => 'nullable',
            'career_opportunities_ar' => 'nullable',
            'career_opportunities_en' => 'nullable',
            'tuition_fees_ar' => 'nullable',
            'tuition_fees_en' => 'nullable',
            'admission_requirements_ar' => 'nullable',
            'admission_requirements_en' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($validated['name_en']).'-'.rand(10, 99);

        $program = Program::create([
            'department_id' => $validated['department_id'],
            'name' => ['ar' => $validated['name_ar'], 'en' => $validated['name_en']],
            'slug' => $slug,
            'degree_level' => $validated['degree_level'],
            'duration_years' => $validated['duration_years'],
            'credit_hours' => $validated['credit_hours'],
            'curriculum' => [
                'ar' => $validated['curriculum_ar'] ?? [],
                'en' => $validated['curriculum_en'] ?? [],
            ],
            'career_opportunities' => [
                'ar' => $validated['career_opportunities_ar'] ?? [],
                'en' => $validated['career_opportunities_en'] ?? [],
            ],
            'tuition_fees' => [
                'ar' => $validated['tuition_fees_ar'] ?? '55,000 جنيه مصري / العام الدراسي',
                'en' => $validated['tuition_fees_en'] ?? '55,000 EGP / Academic Year',
            ],
            'admission_requirements' => [
                'ar' => $validated['admission_requirements_ar'] ?? ['شهادة الثانوية العامة أو ما يعادلها'],
                'en' => $validated['admission_requirements_en'] ?? ['High school certificate or equivalent'],
            ],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Academic program created successfully.',
            'data' => new ProgramResource($program),
        ], 201);
    }

    public function updateProgram(Request $request, int $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'degree_level' => 'sometimes|in:bachelor,master,doctorate,diploma',
            'duration_years' => 'sometimes|integer|min:1|max:8',
            'credit_hours' => 'sometimes|integer|min:10|max:300',
            'curriculum_ar' => 'nullable',
            'curriculum_en' => 'nullable',
            'career_opportunities_ar' => 'nullable',
            'career_opportunities_en' => 'nullable',
            'tuition_fees_ar' => 'nullable',
            'tuition_fees_en' => 'nullable',
            'admission_requirements_ar' => 'nullable',
            'admission_requirements_en' => 'nullable',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['department_id'])) $program->department_id = $validated['department_id'];
        if (isset($validated['name_ar'])) $program->setTranslation('name', 'ar', $validated['name_ar']);
        if (isset($validated['name_en'])) $program->setTranslation('name', 'en', $validated['name_en']);
        if (isset($validated['degree_level'])) $program->degree_level = $validated['degree_level'];
        if (isset($validated['duration_years'])) $program->duration_years = (int) $validated['duration_years'];
        if (isset($validated['credit_hours'])) $program->credit_hours = (int) $validated['credit_hours'];
        if (isset($validated['curriculum_ar'])) $program->setTranslation('curriculum', 'ar', $validated['curriculum_ar']);
        if (isset($validated['curriculum_en'])) $program->setTranslation('curriculum', 'en', $validated['curriculum_en']);
        if (isset($validated['career_opportunities_ar'])) $program->setTranslation('career_opportunities', 'ar', $validated['career_opportunities_ar']);
        if (isset($validated['career_opportunities_en'])) $program->setTranslation('career_opportunities', 'en', $validated['career_opportunities_en']);
        if (isset($validated['tuition_fees_ar'])) $program->setTranslation('tuition_fees', 'ar', $validated['tuition_fees_ar']);
        if (isset($validated['tuition_fees_en'])) $program->setTranslation('tuition_fees', 'en', $validated['tuition_fees_en']);
        if (isset($validated['admission_requirements_ar'])) $program->setTranslation('admission_requirements', 'ar', $validated['admission_requirements_ar']);
        if (isset($validated['admission_requirements_en'])) $program->setTranslation('admission_requirements', 'en', $validated['admission_requirements_en']);
        if (isset($validated['is_active'])) $program->is_active = (bool) $validated['is_active'];

        $program->save();

        return response()->json([
            'success' => true,
            'message' => 'Academic program updated successfully.',
            'data' => new ProgramResource($program),
        ]);
    }

    public function deleteProgram(int $id): JsonResponse
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic program deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Faculty & Researchers CRUD Management
    // ----------------------------------------------------
    public function storeFaculty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'academic_title_ar' => 'required|string|max:255',
            'academic_title_en' => 'required|string|max:255',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'research_interests_ar' => 'nullable|string',
            'research_interests_en' => 'nullable|string',
            'email' => 'required|email|unique:faculty_profiles,email',
            'phone' => 'nullable|string|max:30',
            'office_location_ar' => 'nullable|string|max:255',
            'office_location_en' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'cv_path' => 'nullable|string',
            'google_scholar_url' => 'nullable|url',
            'orcid_id' => 'nullable|string|max:50',
            'office_hours' => 'nullable|array',
            'publications' => 'nullable|array',
            'is_featured' => 'boolean',
        ]);

        // Create associated user or profile
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name_en'],
                'password' => bcrypt('password123'),
            ]
        );

        $faculty = FacultyProfile::create([
            'user_id' => $user->id,
            'department_id' => $validated['department_id'],
            'academic_title' => ['ar' => $validated['academic_title_ar'], 'en' => $validated['academic_title_en']],
            'bio' => ['ar' => $validated['bio_ar'] ?? '', 'en' => $validated['bio_en'] ?? ''],
            'research_interests' => ['ar' => $validated['research_interests_ar'] ?? '', 'en' => $validated['research_interests_en'] ?? ''],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'office_location' => ['ar' => $validated['office_location_ar'] ?? '', 'en' => $validated['office_location_en'] ?? ''],
            'avatar' => $validated['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'cv_path' => $validated['cv_path'] ?? null,
            'google_scholar_url' => $validated['google_scholar_url'] ?? null,
            'orcid_id' => $validated['orcid_id'] ?? null,
            'office_hours' => $validated['office_hours'] ?? null,
            'publications' => $validated['publications'] ?? null,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile created successfully.',
            'data' => new FacultyResource($faculty->load('department')),
        ], 201);
    }

    public function updateFaculty(Request $request, int $id): JsonResponse
    {
        $faculty = FacultyProfile::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'academic_title_ar' => 'sometimes|required|string|max:255',
            'academic_title_en' => 'sometimes|required|string|max:255',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'research_interests_ar' => 'nullable|string',
            'research_interests_en' => 'nullable|string',
            'email' => "sometimes|email|unique:faculty_profiles,email,{$id}",
            'phone' => 'nullable|string|max:30',
            'office_location_ar' => 'nullable|string|max:255',
            'office_location_en' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'cv_path' => 'nullable|string',
            'google_scholar_url' => 'nullable|url',
            'orcid_id' => 'nullable|string|max:50',
            'office_hours' => 'nullable|array',
            'publications' => 'nullable|array',
            'is_featured' => 'boolean',
        ]);

        if (isset($validated['department_id'])) $faculty->department_id = $validated['department_id'];
        if (isset($validated['academic_title_ar'])) $faculty->setTranslation('academic_title', 'ar', $validated['academic_title_ar']);
        if (isset($validated['academic_title_en'])) $faculty->setTranslation('academic_title', 'en', $validated['academic_title_en']);
        if (isset($validated['bio_ar'])) $faculty->setTranslation('bio', 'ar', $validated['bio_ar']);
        if (isset($validated['bio_en'])) $faculty->setTranslation('bio', 'en', $validated['bio_en']);
        if (isset($validated['research_interests_ar'])) $faculty->setTranslation('research_interests', 'ar', $validated['research_interests_ar']);
        if (isset($validated['research_interests_en'])) $faculty->setTranslation('research_interests', 'en', $validated['research_interests_en']);
        if (isset($validated['office_location_ar'])) $faculty->setTranslation('office_location', 'ar', $validated['office_location_ar']);
        if (isset($validated['office_location_en'])) $faculty->setTranslation('office_location', 'en', $validated['office_location_en']);
        if (isset($validated['email'])) $faculty->email = $validated['email'];
        if (isset($validated['phone'])) $faculty->phone = $validated['phone'];
        if (isset($validated['avatar'])) $faculty->avatar = $validated['avatar'];
        if (isset($validated['cv_path'])) $faculty->cv_path = $validated['cv_path'];
        if (isset($validated['google_scholar_url'])) $faculty->google_scholar_url = $validated['google_scholar_url'];
        if (isset($validated['orcid_id'])) $faculty->orcid_id = $validated['orcid_id'];
        if (isset($validated['office_hours'])) $faculty->office_hours = $validated['office_hours'];
        if (isset($validated['publications'])) $faculty->publications = $validated['publications'];
        if (isset($validated['is_featured'])) $faculty->is_featured = (bool) $validated['is_featured'];

        $faculty->save();

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile updated successfully.',
            'data' => new FacultyResource($faculty->load('department')),
        ]);
    }

    public function deleteFaculty(int $id): JsonResponse
    {
        $faculty = FacultyProfile::findOrFail($id);
        $faculty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile deleted successfully.',
        ]);
    }
}
