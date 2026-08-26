<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Services;

use App\Models\User;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AcademicStructureService
{
    /**
     * Get all active colleges with department and program counts.
     */
    public function getColleges(): Collection
    {
        return College::where('is_active', true)
            ->withCount(['departments', 'programs'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get college details by slug with departments, programs, and featured faculty.
     */
    public function getCollege(string $slug): College
    {
        return College::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'departments' => fn($q) => $q->orderBy('sort_order'),
                'departments.programs' => fn($q) => $q->where('is_active', true),
                'facultyProfiles' => fn($q) => $q->with(['user', 'department'])->where('is_featured', true),
            ])
            ->firstOrFail();
    }

    /**
     * Get departments optionally filtered by college_id.
     */
    public function getDepartments(array $filters = []): Collection
    {
        $query = Department::with('college')->orderBy('sort_order');

        if (!empty($filters['college_id'])) {
            $query->where('college_id', $filters['college_id']);
        }

        return $query->get();
    }

    /**
     * Get programs filtered and paginated or as a collection.
     */
    public function getPrograms(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = Program::where('is_active', true)->with(['department.college']);

        if (!empty($filters['college_id'])) {
            $collegeId = $filters['college_id'];
            $query->whereHas('department', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            });
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['degree_level'])) {
            $query->where('degree_level', $filters['degree_level']);
        }

        if (!empty($filters['search']) || !empty($filters['q'])) {
            $search = (string) ($filters['search'] ?? $filters['q']);
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', "%{$search}%")
                    ->orWhere('name->ar', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('degree_level', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $returnAll = !empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN);

        return $returnAll ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get program details by slug with department and faculty.
     */
    public function getProgram(string $slug): Program
    {
        return Program::where('slug', $slug)
            ->where('is_active', true)
            ->with(['department.college', 'department.facultyProfiles.user'])
            ->firstOrFail();
    }

    /**
     * Get faculty profiles with filters and pagination.
     */
    public function getFaculty(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = FacultyProfile::with(['user', 'department.college']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['department'])) {
            $dept = $filters['department'];
            $query->whereHas('department', function ($q) use ($dept) {
                $q->where('slug', $dept)->orWhere('id', $dept);
            });
        }

        if (!empty($filters['college_id'])) {
            $collegeId = $filters['college_id'];
            $query->whereHas('department', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            });
        }

        $isFeatured = !empty($filters['is_featured']) || !empty($filters['featured']);
        if ($isFeatured) {
            $query->where('is_featured', true);
        }

        if (!empty($filters['rank']) || !empty($filters['academic_title'])) {
            $rank = (string) ($filters['rank'] ?? $filters['academic_title']);
            $query->where(function ($q) use ($rank) {
                $q->where('academic_title->en', 'like', "%{$rank}%")
                    ->orWhere('academic_title->ar', 'like', "%{$rank}%");
            });
        }

        if (!empty($filters['search']) || !empty($filters['q'])) {
            $search = (string) ($filters['search'] ?? $filters['q']);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('academic_title->en', 'like', "%{$search}%")
                    ->orWhere('academic_title->ar', 'like', "%{$search}%")
                    ->orWhere('bio->en', 'like', "%{$search}%")
                    ->orWhere('bio->ar', 'like', "%{$search}%")
                    ->orWhere('research_interests->en', 'like', "%{$search}%")
                    ->orWhere('research_interests->ar', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $returnAll = !empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN);

        return $returnAll ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Create a new college.
     */
    public function createCollege(array $data, ?UploadedFile $bannerFile = null): College
    {
        $slug = Str::slug($data['name_en'] ?? 'college') . '-' . rand(10, 99);

        $bannerImage = $data['banner_image'] ?? 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80';
        if ($bannerFile) {
            $path = $bannerFile->store('college_banners', 'public');
            $bannerImage = '/storage/' . $path;
        }

        return College::create([
            'name' => [
                'ar' => $data['name_ar'],
                'en' => $data['name_en'],
            ],
            'slug' => $slug,
            'dean_name' => [
                'ar' => $data['dean_name_ar'] ?? '',
                'en' => $data['dean_name_en'] ?? '',
            ],
            'about' => [
                'ar' => $data['about_ar'] ?? '',
                'en' => $data['about_en'] ?? '',
            ],
            'vision' => [
                'ar' => $data['vision_ar'] ?? '',
                'en' => $data['vision_en'] ?? '',
            ],
            'mission' => [
                'ar' => $data['mission_ar'] ?? '',
                'en' => $data['mission_en'] ?? '',
            ],
            'banner_image' => $bannerImage,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Update an existing college.
     */
    public function updateCollege(College $college, array $data, ?UploadedFile $bannerFile = null): College
    {
        if (!empty($data['name_ar'])) {
            $college->setTranslation('name', 'ar', $data['name_ar']);
        }
        if (!empty($data['name_en'])) {
            $college->setTranslation('name', 'en', $data['name_en']);
        }
        if (array_key_exists('dean_name_ar', $data)) {
            $college->setTranslation('dean_name', 'ar', $data['dean_name_ar'] ?? '');
        }
        if (array_key_exists('dean_name_en', $data)) {
            $college->setTranslation('dean_name', 'en', $data['dean_name_en'] ?? '');
        }
        if (array_key_exists('about_ar', $data)) {
            $college->setTranslation('about', 'ar', $data['about_ar'] ?? '');
        }
        if (array_key_exists('about_en', $data)) {
            $college->setTranslation('about', 'en', $data['about_en'] ?? '');
        }
        if (array_key_exists('vision_ar', $data)) {
            $college->setTranslation('vision', 'ar', $data['vision_ar'] ?? '');
        }
        if (array_key_exists('vision_en', $data)) {
            $college->setTranslation('vision', 'en', $data['vision_en'] ?? '');
        }
        if (array_key_exists('mission_ar', $data)) {
            $college->setTranslation('mission', 'ar', $data['mission_ar'] ?? '');
        }
        if (array_key_exists('mission_en', $data)) {
            $college->setTranslation('mission', 'en', $data['mission_en'] ?? '');
        }

        if ($bannerFile) {
            $path = $bannerFile->store('college_banners', 'public');
            $college->banner_image = '/storage/' . $path;
        } elseif (array_key_exists('banner_image', $data)) {
            $college->banner_image = $data['banner_image'];
        }

        if (isset($data['is_active'])) {
            $college->is_active = (bool) $data['is_active'];
        }
        if (isset($data['sort_order'])) {
            $college->sort_order = (int) $data['sort_order'];
        }

        $college->save();

        return $college;
    }

    /**
     * Delete a college.
     */
    public function deleteCollege(College $college): void
    {
        $college->delete();
    }

    /**
     * Create a new department.
     */
    public function createDepartment(array $data): Department
    {
        $slug = Str::slug($data['name_en'] ?? 'department') . '-' . rand(10, 99);

        return Department::create([
            'college_id' => $data['college_id'],
            'name' => [
                'ar' => $data['name_ar'],
                'en' => $data['name_en'],
            ],
            'slug' => $slug,
            'head_name' => [
                'ar' => $data['head_name_ar'] ?? '',
                'en' => $data['head_name_en'] ?? '',
            ],
            'description' => [
                'ar' => $data['description_ar'] ?? '',
                'en' => $data['description_en'] ?? '',
            ],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Update an existing department.
     */
    public function updateDepartment(Department $dept, array $data): Department
    {
        if (isset($data['college_id'])) {
            $dept->college_id = $data['college_id'];
        }
        if (isset($data['name_ar'])) {
            $dept->setTranslation('name', 'ar', $data['name_ar']);
        }
        if (isset($data['name_en'])) {
            $dept->setTranslation('name', 'en', $data['name_en']);
        }
        if (isset($data['head_name_ar'])) {
            $dept->setTranslation('head_name', 'ar', $data['head_name_ar']);
        }
        if (isset($data['head_name_en'])) {
            $dept->setTranslation('head_name', 'en', $data['head_name_en']);
        }
        if (isset($data['description_ar'])) {
            $dept->setTranslation('description', 'ar', $data['description_ar']);
        }
        if (isset($data['description_en'])) {
            $dept->setTranslation('description', 'en', $data['description_en']);
        }
        if (isset($data['sort_order'])) {
            $dept->sort_order = (int) $data['sort_order'];
        }

        $dept->save();

        return $dept;
    }

    /**
     * Delete a department.
     */
    public function deleteDepartment(Department $dept): void
    {
        $dept->delete();
    }

    /**
     * Create a new degree program.
     */
    public function createProgram(array $data, ?UploadedFile $studyPlanFile = null): Program
    {
        $slug = Str::slug($data['name_en'] ?? 'program') . '-' . rand(10, 99);

        $docPath = $data['study_plan_document_path'] ?? null;
        $fileName = $data['study_plan_file_name'] ?? null;
        $fileSize = $data['study_plan_file_size'] ?? null;

        if ($studyPlanFile) {
            $stored = $studyPlanFile->store('curriculum_plans', 'public');
            $docPath = '/storage/' . $stored;
            $fileName = $studyPlanFile->getClientOriginalName();
            $bytes = $studyPlanFile->getSize();
            $fileSize = $bytes >= 1048576
                ? number_format($bytes / 1048576, 1) . ' MB'
                : number_format($bytes / 1024, 0) . ' KB';
        }

        return Program::create([
            'department_id' => $data['department_id'],
            'name' => [
                'ar' => $data['name_ar'],
                'en' => $data['name_en'],
            ],
            'slug' => $slug,
            'degree_level' => $data['degree_level'],
            'duration_years' => $data['duration_years'],
            'credit_hours' => $data['credit_hours'],
            'curriculum' => [
                'ar' => $data['curriculum_ar'] ?? [],
                'en' => $data['curriculum_en'] ?? [],
            ],
            'career_opportunities' => [
                'ar' => $data['career_opportunities_ar'] ?? [],
                'en' => $data['career_opportunities_en'] ?? [],
            ],
            'tuition_fees' => [
                'ar' => $data['tuition_fees_ar'] ?? '55,000 جنيه مصري / العام الدراسي',
                'en' => $data['tuition_fees_en'] ?? '55,000 EGP / Academic Year',
            ],
            'admission_requirements' => [
                'ar' => $data['admission_requirements_ar'] ?? ['شهادة الثانوية العامة أو ما يعادلها'],
                'en' => $data['admission_requirements_en'] ?? ['High school certificate or equivalent'],
            ],
            'study_plan_document_path' => $docPath,
            'study_plan_file_name' => $fileName,
            'study_plan_file_size' => $fileSize,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update an existing degree program.
     */
    public function updateProgram(Program $prog, array $data, ?UploadedFile $studyPlanFile = null): Program
    {
        if ($studyPlanFile) {
            $stored = $studyPlanFile->store('curriculum_plans', 'public');
            $prog->study_plan_document_path = '/storage/' . $stored;
            $prog->study_plan_file_name = $studyPlanFile->getClientOriginalName();
            $bytes = $studyPlanFile->getSize();
            $prog->study_plan_file_size = $bytes >= 1048576
                ? number_format($bytes / 1048576, 1) . ' MB'
                : number_format($bytes / 1024, 0) . ' KB';
        } elseif (isset($data['study_plan_document_path'])) {
            $prog->study_plan_document_path = $data['study_plan_document_path'];
            if (isset($data['study_plan_file_name'])) {
                $prog->study_plan_file_name = $data['study_plan_file_name'];
            }
            if (isset($data['study_plan_file_size'])) {
                $prog->study_plan_file_size = $data['study_plan_file_size'];
            }
        }

        if (isset($data['department_id'])) {
            if ($data['department_id'] && Department::where('id', $data['department_id'])->exists()) {
                $prog->department_id = $data['department_id'];
            }
        }
        if (isset($data['name_ar'])) {
            $prog->setTranslation('name', 'ar', $data['name_ar']);
        }
        if (isset($data['name_en'])) {
            $prog->setTranslation('name', 'en', $data['name_en']);
        }
        if (isset($data['degree_level'])) {
            $prog->degree_level = $data['degree_level'];
        }
        if (isset($data['duration_years'])) {
            $prog->duration_years = (int) $data['duration_years'];
        }
        if (isset($data['credit_hours'])) {
            $prog->credit_hours = (int) $data['credit_hours'];
        }
        if (isset($data['curriculum_ar'])) {
            $prog->setTranslation('curriculum', 'ar', $data['curriculum_ar']);
        }
        if (isset($data['curriculum_en'])) {
            $prog->setTranslation('curriculum', 'en', $data['curriculum_en']);
        }
        if (isset($data['career_opportunities_ar'])) {
            $prog->setTranslation('career_opportunities', 'ar', $data['career_opportunities_ar']);
        }
        if (isset($data['career_opportunities_en'])) {
            $prog->setTranslation('career_opportunities', 'en', $data['career_opportunities_en']);
        }
        if (isset($data['tuition_fees_ar'])) {
            $prog->setTranslation('tuition_fees', 'ar', $data['tuition_fees_ar']);
        }
        if (isset($data['tuition_fees_en'])) {
            $prog->setTranslation('tuition_fees', 'en', $data['tuition_fees_en']);
        }
        if (isset($data['admission_requirements_ar'])) {
            $val = is_string($data['admission_requirements_ar'])
                ? array_map('trim', explode(',', $data['admission_requirements_ar']))
                : $data['admission_requirements_ar'];
            $prog->setTranslation('admission_requirements', 'ar', $val);
        }
        if (isset($data['admission_requirements_en'])) {
            $val = is_string($data['admission_requirements_en'])
                ? array_map('trim', explode(',', $data['admission_requirements_en']))
                : $data['admission_requirements_en'];
            $prog->setTranslation('admission_requirements', 'en', $val);
        }
        if (isset($data['is_active'])) {
            $prog->is_active = (bool) $data['is_active'];
        }

        $prog->save();

        return $prog;
    }

    /**
     * Delete a program.
     */
    public function deleteProgram(Program $prog): void
    {
        $prog->delete();
    }

    /**
     * Create a new faculty profile.
     */
    public function createFaculty(array $data, ?UploadedFile $cvFile = null): FacultyProfile
    {
        $nameAr = $data['name_ar'] ?? ($data['name_en'] ?? 'عضو هيئة تدريس');
        $nameEn = $data['name_en'] ?? ($data['name_ar'] ?? 'Faculty Member');
        $titleAr = $data['academic_title_ar'] ?? ($data['academic_title_en'] ?? 'أستاذ دكتور');
        $titleEn = $data['academic_title_en'] ?? ($data['academic_title_ar'] ?? 'Professor');

        $deptId = $data['department_id'] ?? null;
        if (!$deptId || !Department::where('id', $deptId)->exists()) {
            $deptId = Department::value('id') ?? 1;
        }

        $cvPath = $data['cv_path'] ?? null;
        if ($cvFile) {
            $storedCv = $cvFile->store('faculty_cvs', 'public');
            $cvPath = '/storage/' . $storedCv;
        }

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $nameEn,
                'password' => bcrypt('password123'),
            ]
        );

        return FacultyProfile::create([
            'user_id' => $user->id,
            'department_id' => $deptId,
            'academic_title' => ['ar' => $titleAr, 'en' => $titleEn],
            'bio' => ['ar' => $data['bio_ar'] ?? '', 'en' => $data['bio_en'] ?? ''],
            'research_interests' => ['ar' => $data['research_interests_ar'] ?? '', 'en' => $data['research_interests_en'] ?? ''],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'office_location' => ['ar' => $data['office_location_ar'] ?? '', 'en' => $data['office_location_en'] ?? ''],
            'avatar' => $data['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'cv_path' => $cvPath,
            'google_scholar_url' => $data['google_scholar_url'] ?? null,
            'orcid_id' => $data['orcid_id'] ?? null,
            'office_hours' => $data['office_hours'] ?? null,
            'publications' => $data['publications'] ?? null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ]);
    }

    /**
     * Update an existing faculty profile.
     */
    public function updateFaculty(FacultyProfile $fac, array $data, ?UploadedFile $cvFile = null): FacultyProfile
    {
        if ($cvFile) {
            $storedCv = $cvFile->store('faculty_cvs', 'public');
            $fac->cv_path = '/storage/' . $storedCv;
        } elseif (isset($data['cv_path'])) {
            $fac->cv_path = $data['cv_path'];
        }

        if (isset($data['department_id'])) {
            if ($data['department_id'] && Department::where('id', $data['department_id'])->exists()) {
                $fac->department_id = $data['department_id'];
            }
        }
        if (isset($data['name_en']) || isset($data['name_ar'])) {
            $nameVal = $data['name_en'] ?? $data['name_ar'];
            if ($fac->user) {
                $fac->user->update(['name' => $nameVal]);
            }
        }
        if (isset($data['academic_title_ar'])) {
            $fac->setTranslation('academic_title', 'ar', $data['academic_title_ar']);
        }
        if (isset($data['academic_title_en'])) {
            $fac->setTranslation('academic_title', 'en', $data['academic_title_en']);
        }
        if (isset($data['bio_ar'])) {
            $fac->setTranslation('bio', 'ar', $data['bio_ar']);
        }
        if (isset($data['bio_en'])) {
            $fac->setTranslation('bio', 'en', $data['bio_en']);
        }
        if (isset($data['research_interests_ar'])) {
            $fac->setTranslation('research_interests', 'ar', $data['research_interests_ar']);
        }
        if (isset($data['research_interests_en'])) {
            $fac->setTranslation('research_interests', 'en', $data['research_interests_en']);
        }
        if (isset($data['office_location_ar'])) {
            $fac->setTranslation('office_location', 'ar', $data['office_location_ar']);
        }
        if (isset($data['office_location_en'])) {
            $fac->setTranslation('office_location', 'en', $data['office_location_en']);
        }
        if (isset($data['email'])) {
            $fac->email = $data['email'];
            if ($fac->user) {
                $fac->user->update(['email' => $data['email']]);
            }
        }
        if (isset($data['phone'])) {
            $fac->phone = $data['phone'];
        }
        if (isset($data['avatar'])) {
            $fac->avatar = $data['avatar'];
        }
        if (isset($data['google_scholar_url'])) {
            $fac->google_scholar_url = $data['google_scholar_url'];
        }
        if (isset($data['orcid_id'])) {
            $fac->orcid_id = $data['orcid_id'];
        }
        if (isset($data['office_hours'])) {
            $fac->office_hours = $data['office_hours'];
        }
        if (isset($data['publications'])) {
            $fac->publications = $data['publications'];
        }
        if (isset($data['is_featured'])) {
            $fac->is_featured = (bool) $data['is_featured'];
        }

        $fac->save();

        return $fac;
    }

    /**
     * Delete a faculty profile.
     */
    public function deleteFaculty(FacultyProfile $fac): void
    {
        $fac->delete();
    }
}
