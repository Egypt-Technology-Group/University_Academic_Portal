<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\AcademicStructure\Resources\CollegeResource;
use App\Modules\AcademicStructure\Resources\FacultyResource;
use App\Modules\AcademicStructure\Resources\ProgramResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAcademicStructureController extends Controller
{
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
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'dean_name_ar' => 'nullable|string|max:255',
            'dean_name_en' => 'nullable|string|max:255',
            'about_ar' => 'nullable|string',
            'about_en' => 'nullable|string',
            'vision_ar' => 'nullable|string',
            'vision_en' => 'nullable|string',
            'mission_ar' => 'nullable|string',
            'mission_en' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (!empty($validated['name_ar'])) $college->setTranslation('name', 'ar', $validated['name_ar']);
        if (!empty($validated['name_en'])) $college->setTranslation('name', 'en', $validated['name_en']);
        if (array_key_exists('dean_name_ar', $validated)) $college->setTranslation('dean_name', 'ar', $validated['dean_name_ar'] ?? '');
        if (array_key_exists('dean_name_en', $validated)) $college->setTranslation('dean_name', 'en', $validated['dean_name_en'] ?? '');
        if (array_key_exists('about_ar', $validated)) $college->setTranslation('about', 'ar', $validated['about_ar'] ?? '');
        if (array_key_exists('about_en', $validated)) $college->setTranslation('about', 'en', $validated['about_en'] ?? '');
        if (array_key_exists('vision_ar', $validated)) $college->setTranslation('vision', 'ar', $validated['vision_ar'] ?? '');
        if (array_key_exists('vision_en', $validated)) $college->setTranslation('vision', 'en', $validated['vision_en'] ?? '');
        if (array_key_exists('mission_ar', $validated)) $college->setTranslation('mission', 'ar', $validated['mission_ar'] ?? '');
        if (array_key_exists('mission_en', $validated)) $college->setTranslation('mission', 'en', $validated['mission_en'] ?? '');
        if (array_key_exists('banner_image', $validated)) $college->banner_image = $validated['banner_image'];
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
            'study_plan_document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:51200',
            'study_plan_document_path' => 'nullable|string',
            'study_plan_file_name' => 'nullable|string',
            'study_plan_file_size' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($validated['name_en']).'-'.rand(10, 99);

        $docPath = $validated['study_plan_document_path'] ?? null;
        $fileName = $validated['study_plan_file_name'] ?? null;
        $fileSize = $validated['study_plan_file_size'] ?? null;

        if ($request->hasFile('study_plan_document')) {
            $uploaded = $request->file('study_plan_document');
            $stored = $uploaded->store('curriculum_plans', 'public');
            $docPath = '/storage/' . $stored;
            $fileName = $uploaded->getClientOriginalName();
            $bytes = $uploaded->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        }

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
            'study_plan_document_path' => $docPath,
            'study_plan_file_name' => $fileName,
            'study_plan_file_size' => $fileSize,
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
            'department_id' => 'sometimes|nullable|integer',
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
            'study_plan_document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:51200',
            'study_plan_document_path' => 'nullable|string',
            'study_plan_file_name' => 'nullable|string',
            'study_plan_file_size' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('study_plan_document')) {
            $uploaded = $request->file('study_plan_document');
            $stored = $uploaded->store('curriculum_plans', 'public');
            $program->study_plan_document_path = '/storage/' . $stored;
            $program->study_plan_file_name = $uploaded->getClientOriginalName();
            $bytes = $uploaded->getSize();
            $program->study_plan_file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        } elseif (isset($validated['study_plan_document_path'])) {
            $program->study_plan_document_path = $validated['study_plan_document_path'];
            if (isset($validated['study_plan_file_name'])) $program->study_plan_file_name = $validated['study_plan_file_name'];
            if (isset($validated['study_plan_file_size'])) $program->study_plan_file_size = $validated['study_plan_file_size'];
        }

        if (isset($validated['department_id'])) {
            if ($validated['department_id'] && Department::where('id', $validated['department_id'])->exists()) {
                $program->department_id = $validated['department_id'];
            }
        }
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
        if (isset($validated['admission_requirements_ar'])) {
            $val = is_string($validated['admission_requirements_ar'])
                ? array_map('trim', explode(',', $validated['admission_requirements_ar']))
                : $validated['admission_requirements_ar'];
            $program->setTranslation('admission_requirements', 'ar', $val);
        }
        if (isset($validated['admission_requirements_en'])) {
            $val = is_string($validated['admission_requirements_en'])
                ? array_map('trim', explode(',', $validated['admission_requirements_en']))
                : $validated['admission_requirements_en'];
            $program->setTranslation('admission_requirements', 'en', $val);
        }
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
            'department_id' => 'nullable|integer',
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'academic_title_ar' => 'nullable|string|max:255',
            'academic_title_en' => 'nullable|string|max:255',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'research_interests_ar' => 'nullable|string',
            'research_interests_en' => 'nullable|string',
            'email' => 'required|email|unique:faculty_profiles,email',
            'phone' => 'nullable|string|max:30',
            'office_location_ar' => 'nullable|string|max:255',
            'office_location_en' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:51200',
            'cv_path' => 'nullable|string',
            'google_scholar_url' => 'nullable|url',
            'orcid_id' => 'nullable|string|max:50',
            'office_hours' => 'nullable|array',
            'publications' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
        ]);

        $nameAr = $validated['name_ar'] ?? ($validated['name_en'] ?? 'عضو هيئة تدريس');
        $nameEn = $validated['name_en'] ?? ($validated['name_ar'] ?? 'Faculty Member');
        $titleAr = $validated['academic_title_ar'] ?? ($validated['academic_title_en'] ?? 'أستاذ دكتور');
        $titleEn = $validated['academic_title_en'] ?? ($validated['academic_title_ar'] ?? 'Professor');

        $deptId = $validated['department_id'] ?? null;
        if (!$deptId || !Department::where('id', $deptId)->exists()) {
            $deptId = Department::value('id') ?? 1;
        }

        $cvPath = $validated['cv_path'] ?? null;
        if ($request->hasFile('cv_file')) {
            $uploadedCv = $request->file('cv_file');
            $storedCv = $uploadedCv->store('faculty_cvs', 'public');
            $cvPath = '/storage/' . $storedCv;
        }

        // Create associated user or profile
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $nameEn,
                'password' => bcrypt('password123'),
            ]
        );

        $faculty = FacultyProfile::create([
            'user_id' => $user->id,
            'department_id' => $deptId,
            'academic_title' => ['ar' => $titleAr, 'en' => $titleEn],
            'bio' => ['ar' => $validated['bio_ar'] ?? '', 'en' => $validated['bio_en'] ?? ''],
            'research_interests' => ['ar' => $validated['research_interests_ar'] ?? '', 'en' => $validated['research_interests_en'] ?? ''],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'office_location' => ['ar' => $validated['office_location_ar'] ?? '', 'en' => $validated['office_location_en'] ?? ''],
            'avatar' => $validated['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'cv_path' => $cvPath,
            'google_scholar_url' => $validated['google_scholar_url'] ?? null,
            'orcid_id' => $validated['orcid_id'] ?? null,
            'office_hours' => $validated['office_hours'] ?? null,
            'publications' => $validated['publications'] ?? null,
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
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
            'department_id' => 'sometimes|nullable|integer',
            'name_ar' => 'sometimes|nullable|string|max:255',
            'name_en' => 'sometimes|nullable|string|max:255',
            'academic_title_ar' => 'sometimes|nullable|string|max:255',
            'academic_title_en' => 'sometimes|nullable|string|max:255',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'research_interests_ar' => 'nullable|string',
            'research_interests_en' => 'nullable|string',
            'email' => "sometimes|email|unique:faculty_profiles,email,{$id}",
            'phone' => 'nullable|string|max:30',
            'office_location_ar' => 'nullable|string|max:255',
            'office_location_en' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:51200',
            'cv_path' => 'nullable|string',
            'google_scholar_url' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:50',
            'office_hours' => 'nullable|array',
            'publications' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('cv_file')) {
            $uploadedCv = $request->file('cv_file');
            $storedCv = $uploadedCv->store('faculty_cvs', 'public');
            $faculty->cv_path = '/storage/' . $storedCv;
        } elseif (isset($validated['cv_path'])) {
            $faculty->cv_path = $validated['cv_path'];
        }

        if (isset($validated['department_id'])) {
            if ($validated['department_id'] && Department::where('id', $validated['department_id'])->exists()) {
                $faculty->department_id = $validated['department_id'];
            }
        }
        if (isset($validated['name_en']) || isset($validated['name_ar'])) {
            $nameVal = $validated['name_en'] ?? $validated['name_ar'];
            if ($faculty->user) {
                $faculty->user->update(['name' => $nameVal]);
            }
        }
        if (isset($validated['academic_title_ar'])) $faculty->setTranslation('academic_title', 'ar', $validated['academic_title_ar']);
        if (isset($validated['academic_title_en'])) $faculty->setTranslation('academic_title', 'en', $validated['academic_title_en']);
        if (isset($validated['bio_ar'])) $faculty->setTranslation('bio', 'ar', $validated['bio_ar']);
        if (isset($validated['bio_en'])) $faculty->setTranslation('bio', 'en', $validated['bio_en']);
        if (isset($validated['research_interests_ar'])) $faculty->setTranslation('research_interests', 'ar', $validated['research_interests_ar']);
        if (isset($validated['research_interests_en'])) $faculty->setTranslation('research_interests', 'en', $validated['research_interests_en']);
        if (isset($validated['office_location_ar'])) $faculty->setTranslation('office_location', 'ar', $validated['office_location_ar']);
        if (isset($validated['office_location_en'])) $faculty->setTranslation('office_location', 'en', $validated['office_location_en']);
        if (isset($validated['email'])) {
            $faculty->email = $validated['email'];
            if ($faculty->user) {
                $faculty->user->update(['email' => $validated['email']]);
            }
        }
        if (isset($validated['phone'])) $faculty->phone = $validated['phone'];
        if (isset($validated['avatar'])) $faculty->avatar = $validated['avatar'];
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
