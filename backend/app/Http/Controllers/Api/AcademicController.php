<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollegeResource;
use App\Http\Resources\FacultyResource;
use App\Http\Resources\ProgramResource;
use App\Models\College;
use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AcademicController extends Controller
{
    /**
     * List all active colleges with department/program counts.
     */
    public function indexColleges(Request $request): AnonymousResourceCollection
    {
        $colleges = College::where('is_active', true)
            ->withCount(['departments', 'programs'])
            ->orderBy('sort_order')
            ->get();

        return CollegeResource::collection($colleges);
    }

    /**
     * List departments filterable by college_id.
     */
    public function indexDepartments(Request $request): JsonResponse
    {
        $query = Department::with('college')->orderBy('sort_order');

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Get college details with departments, programs, and featured faculty.
     */
    public function getCollege(string $slug): CollegeResource
    {
        $college = College::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'departments' => fn($q) => $q->orderBy('sort_order'),
                'departments.programs' => fn($q) => $q->where('is_active', true),
                'facultyProfiles' => fn($q) => $q->with(['user', 'department'])->where('is_featured', true),
            ])
            ->firstOrFail();

        return new CollegeResource($college);
    }

    /**
     * List programs with filters (college_id, department_id, degree_level, search query).
     */
    public function indexPrograms(Request $request): AnonymousResourceCollection
    {
        $query = Program::where('is_active', true)->with(['department.college']);

        if ($request->filled('college_id')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('college_id', $request->college_id);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('degree_level')) {
            $query->where('degree_level', $request->degree_level);
        }

        if ($request->filled('search') || $request->filled('q')) {
            $search = (string) $request->input('search', $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', "%{$search}%")
                    ->orWhere('name->ar', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('degree_level', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $programs = $request->boolean('all') ? $query->get() : $query->paginate($perPage);

        return ProgramResource::collection($programs);
    }

    /**
     * Get program details with curriculum breakdown.
     */
    public function getProgram(string $slug): ProgramResource
    {
        $program = Program::where('slug', $slug)
            ->where('is_active', true)
            ->with(['department.college', 'department.facultyProfiles.user'])
            ->firstOrFail();

        return new ProgramResource($program);
    }

    /**
     * Faculty list filterable by department, search, rank/title.
     */
    public function indexFaculty(Request $request): AnonymousResourceCollection
    {
        $query = FacultyProfile::with(['user', 'department.college']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('department')) {
            $dept = $request->input('department');
            $query->whereHas('department', function ($q) use ($dept) {
                $q->where('slug', $dept)->orWhere('id', $dept);
            });
        }

        if ($request->filled('college_id')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('college_id', $request->college_id);
            });
        }

        if ($request->boolean('is_featured') || $request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('rank') || $request->filled('academic_title')) {
            $rank = (string) $request->input('rank', $request->input('academic_title'));
            $query->where(function ($q) use ($rank) {
                $q->where('academic_title->en', 'like', "%{$rank}%")
                    ->orWhere('academic_title->ar', 'like', "%{$rank}%");
            });
        }

        if ($request->filled('search') || $request->filled('q')) {
            $search = (string) $request->input('search', $request->input('q'));
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

        $perPage = (int) $request->input('per_page', 15);
        $faculty = $request->boolean('all') ? $query->get() : $query->paginate($perPage);

        return FacultyResource::collection($faculty);
    }
}
