<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\AcademicStructure\Requests\StoreCollegeRequest;
use App\Modules\AcademicStructure\Requests\StoreDepartmentRequest;
use App\Modules\AcademicStructure\Requests\StoreFacultyRequest;
use App\Modules\AcademicStructure\Requests\StoreProgramRequest;
use App\Modules\AcademicStructure\Requests\UpdateCollegeRequest;
use App\Modules\AcademicStructure\Requests\UpdateDepartmentRequest;
use App\Modules\AcademicStructure\Requests\UpdateFacultyRequest;
use App\Modules\AcademicStructure\Requests\UpdateProgramRequest;
use App\Modules\AcademicStructure\Resources\CollegeResource;
use App\Modules\AcademicStructure\Resources\FacultyResource;
use App\Modules\AcademicStructure\Resources\ProgramResource;
use App\Modules\AcademicStructure\Services\AcademicStructureService;
use Illuminate\Http\JsonResponse;

class AdminAcademicStructureController extends Controller
{
    public function __construct(
        protected AcademicStructureService $academicStructureService
    ) {}

    // ----------------------------------------------------
    // Colleges & Institutes CRUD Management
    // ----------------------------------------------------
    public function storeCollege(StoreCollegeRequest $request): JsonResponse
    {
        $bannerFile = $request->file('banner_file') ?? ($request->file('banner_image') ?: null);
        $college = $this->academicStructureService->createCollege($request->validated(), $bannerFile);

        return response()->json([
            'success' => true,
            'message' => 'College/Institute created successfully.',
            'data' => new CollegeResource($college),
        ], 201);
    }

    public function updateCollege(UpdateCollegeRequest $request, int $id): JsonResponse
    {
        $college = College::findOrFail($id);
        $bannerFile = $request->file('banner_file') ?? ($request->file('banner_image') ?: null);
        $college = $this->academicStructureService->updateCollege($college, $request->validated(), $bannerFile);

        return response()->json([
            'success' => true,
            'message' => 'College updated successfully.',
            'data' => new CollegeResource($college),
        ]);
    }

    public function deleteCollege(int $id): JsonResponse
    {
        $college = College::findOrFail($id);
        $this->academicStructureService->deleteCollege($college);

        return response()->json([
            'success' => true,
            'message' => 'College deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Departments CRUD Management
    // ----------------------------------------------------
    public function storeDepartment(StoreDepartmentRequest $request): JsonResponse
    {
        $dept = $this->academicStructureService->createDepartment($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data' => $dept,
        ], 201);
    }

    public function updateDepartment(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $dept = $this->academicStructureService->updateDepartment($dept, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'data' => $dept,
        ]);
    }

    public function deleteDepartment(int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $this->academicStructureService->deleteDepartment($dept);

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Degree Programs & Curriculum CRUD Management
    // ----------------------------------------------------
    public function storeProgram(StoreProgramRequest $request): JsonResponse
    {
        $program = $this->academicStructureService->createProgram(
            $request->validated(),
            $request->file('study_plan_document')
        );

        return response()->json([
            'success' => true,
            'message' => 'Academic program created successfully.',
            'data' => new ProgramResource($program),
        ], 201);
    }

    public function updateProgram(UpdateProgramRequest $request, int $id): JsonResponse
    {
        $program = Program::findOrFail($id);
        $program = $this->academicStructureService->updateProgram(
            $program,
            $request->validated(),
            $request->file('study_plan_document')
        );

        return response()->json([
            'success' => true,
            'message' => 'Academic program updated successfully.',
            'data' => new ProgramResource($program),
        ]);
    }

    public function deleteProgram(int $id): JsonResponse
    {
        $program = Program::findOrFail($id);
        $this->academicStructureService->deleteProgram($program);

        return response()->json([
            'success' => true,
            'message' => 'Academic program deleted successfully.',
        ]);
    }

    // ----------------------------------------------------
    // Faculty & Researchers CRUD Management
    // ----------------------------------------------------
    public function storeFaculty(StoreFacultyRequest $request): JsonResponse
    {
        $faculty = $this->academicStructureService->createFaculty(
            $request->validated(),
            $request->file('cv_file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile created successfully.',
            'data' => new FacultyResource($faculty->load('department')),
        ], 201);
    }

    public function updateFaculty(UpdateFacultyRequest $request, int $id): JsonResponse
    {
        $faculty = FacultyProfile::findOrFail($id);
        $faculty = $this->academicStructureService->updateFaculty(
            $faculty,
            $request->validated(),
            $request->file('cv_file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile updated successfully.',
            'data' => new FacultyResource($faculty->load('department')),
        ]);
    }

    public function deleteFaculty(int $id): JsonResponse
    {
        $faculty = FacultyProfile::findOrFail($id);
        $this->academicStructureService->deleteFaculty($faculty);

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile deleted successfully.',
        ]);
    }
}
