<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicStructure\Resources\CollegeResource;
use App\Modules\AcademicStructure\Resources\FacultyResource;
use App\Modules\AcademicStructure\Resources\ProgramResource;
use App\Modules\AcademicStructure\Services\AcademicStructureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AcademicStructureController extends Controller
{
    public function __construct(
        protected AcademicStructureService $academicStructureService
    ) {}

    /**
     * List all active colleges with department/program counts.
     */
    public function indexColleges(Request $request): AnonymousResourceCollection
    {
        $colleges = $this->academicStructureService->getColleges();

        return CollegeResource::collection($colleges);
    }

    /**
     * List departments filterable by college_id.
     */
    public function indexDepartments(Request $request): JsonResponse
    {
        $departments = $this->academicStructureService->getDepartments($request->all());

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    /**
     * Get college details with departments, programs, and featured faculty.
     */
    public function getCollege(string $slug): CollegeResource
    {
        $college = $this->academicStructureService->getCollege($slug);

        return new CollegeResource($college);
    }

    /**
     * List programs with filters (college_id, department_id, degree_level, search query).
     */
    public function indexPrograms(Request $request): AnonymousResourceCollection
    {
        $programs = $this->academicStructureService->getPrograms($request->all());

        return ProgramResource::collection($programs);
    }

    /**
     * Get program details with curriculum breakdown.
     */
    public function getProgram(string $slug): ProgramResource
    {
        $program = $this->academicStructureService->getProgram($slug);

        return new ProgramResource($program);
    }

    /**
     * Faculty list filterable by department, search, rank/title.
     */
    public function indexFaculty(Request $request): AnonymousResourceCollection
    {
        $faculty = $this->academicStructureService->getFaculty($request->all());

        return FacultyResource::collection($faculty);
    }
}
