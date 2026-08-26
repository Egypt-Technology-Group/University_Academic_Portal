<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use App\Modules\AcademicServices\Requests\IssueOfficialStatementRequest;
use App\Modules\AcademicServices\Requests\StoreExamScheduleRequest;
use App\Modules\AcademicServices\Requests\SubmitStudentServiceRequest;
use App\Modules\AcademicServices\Requests\UpdateExamScheduleRequest;
use App\Modules\AcademicServices\Requests\UpdateStudentServiceRequest;
use App\Modules\AcademicServices\Services\AcademicServicesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAcademicServicesController extends Controller
{
    public function __construct(
        protected AcademicServicesService $academicServicesService
    ) {}

    /**
     * List all electronic service requests (with filters).
     */
    public function indexRequests(Request $request): JsonResponse
    {
        $data = $this->academicServicesService->getRequests($request->all());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Submit a new student electronic service request.
     */
    public function submitRequest(SubmitStudentServiceRequest $request): JsonResponse
    {
        $req = $this->academicServicesService->submitRequest($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Electronic service request submitted successfully.',
            'data' => $req->load('program'),
        ], 201);
    }

    /**
     * Update status and workflow notes for an electronic request.
     */
    public function updateRequestStatus(UpdateStudentServiceRequest $request, int $id): JsonResponse
    {
        $req = StudentServiceRequest::findOrFail($id);
        $req = $this->academicServicesService->updateRequestStatus($req, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Request status updated.',
            'data' => $req->load('program'),
        ]);
    }

    /**
     * Delete an electronic service request.
     */
    public function deleteRequest(int $id): JsonResponse
    {
        $req = StudentServiceRequest::findOrFail($id);
        $this->academicServicesService->deleteRequest($req);

        return response()->json([
            'success' => true,
            'message' => 'Service request deleted successfully.',
        ]);
    }

    /**
     * List all issued official statements & certificates.
     */
    public function indexStatements(Request $request): JsonResponse
    {
        $data = $this->academicServicesService->getOfficialStatements($request->all());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Issue an official verifiable statement / certificate (Supports 3 Modes: structured, file_only, both).
     */
    public function issueStatement(IssueOfficialStatementRequest $request): JsonResponse
    {
        $statement = $this->academicServicesService->issueOfficialStatement(
            $request->validated(),
            $request->file('document'),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Official certificate registered with Hybrid Document Workflow and cryptographic verification seal.',
            'data' => $statement->load('program'),
        ], 201);
    }

    /**
     * List all exam schedules (Admin).
     */
    public function indexExamSchedules(Request $request): JsonResponse
    {
        $data = $this->academicServicesService->getExamSchedules($request->all());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store exam schedule with halls and proctors (Admin - Supports 3 Modes).
     */
    public function storeExamSchedule(StoreExamScheduleRequest $request): JsonResponse
    {
        $schedule = $this->academicServicesService->createExamSchedule(
            $request->validated(),
            $request->file('timetable_document')
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam timetable entry published successfully.',
            'data' => $schedule->load(['program', 'academicTerm']),
        ], 201);
    }

    /**
     * Update exam schedule with halls and proctors (Admin).
     */
    public function updateExamSchedule(UpdateExamScheduleRequest $request, int $id): JsonResponse
    {
        $schedule = ExamSchedule::findOrFail($id);
        $schedule = $this->academicServicesService->updateExamSchedule(
            $schedule,
            $request->validated(),
            $request->file('timetable_document')
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam timetable entry updated successfully.',
            'data' => $schedule->load(['program', 'academicTerm']),
        ]);
    }

    /**
     * Delete exam schedule (Admin).
     */
    public function deleteExamSchedule(int $id): JsonResponse
    {
        $schedule = ExamSchedule::findOrFail($id);
        $this->academicServicesService->deleteExamSchedule($schedule);

        return response()->json([
            'success' => true,
            'message' => 'Exam timetable entry deleted successfully.',
        ]);
    }
}
