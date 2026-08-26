<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use App\Modules\Admissions\Requests\RequestMissingDocumentsRequest;
use App\Modules\Admissions\Requests\StoreAdmissionCycleRequest;
use App\Modules\Admissions\Requests\UpdateAdmissionCycleRequest;
use App\Modules\Admissions\Requests\UpdateApplicationDecisionRequest;
use App\Modules\Admissions\Requests\VerifyDocumentRequest;
use App\Modules\Admissions\Resources\AdmissionCycleResource;
use App\Modules\Admissions\Resources\ApplicationResource;
use App\Modules\Admissions\Services\AdmissionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAdmissionsController extends Controller
{
    public function __construct(
        protected AdmissionsService $admissionsService
    ) {}

    /**
     * List all applications with filtering, search, pagination, and relation loading.
     */
    public function applications(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $applications = $this->admissionsService->getApplications($request->all(), $perPage);

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'total' => $applications->total(),
                'per_page' => $applications->perPage(),
            ],
        ]);
    }

    /**
     * Update application status, workflow stage, interview schedule, and committee review notes.
     */
    public function updateApplicationStatus(UpdateApplicationDecisionRequest $request, int $id): JsonResponse
    {
        $application = Application::with(['program', 'documents'])->findOrFail($id);
        $actor = $request->user()?->name ?? 'Admissions Committee';

        $updated = $this->admissionsService->updateApplicationDecision($application, $request->validated(), $actor);

        return response()->json([
            'success' => true,
            'message' => 'Application workflow updated and timeline recorded successfully.',
            'data' => new ApplicationResource($updated),
        ]);
    }

    /**
     * Verify or reject an individual application document and record audit notes.
     */
    public function verifyDocument(VerifyDocumentRequest $request, int $applicationId, int $documentId): JsonResponse
    {
        $application = Application::with('documents')->findOrFail($applicationId);
        $actor = $request->user()?->name ?? 'Admissions Committee';

        $this->admissionsService->verifyDocument($application, $documentId, $request->validated(), $actor);

        return response()->json([
            'success' => true,
            'message' => 'Document verification status updated successfully.',
            'data' => new ApplicationResource($application->fresh(['documents'])),
        ]);
    }

    /**
     * Trigger an official request notification to the applicant for missing or re-uploaded documents.
     */
    public function requestMissingDocuments(RequestMissingDocumentsRequest $request, int $applicationId): JsonResponse
    {
        $application = Application::with('documents')->findOrFail($applicationId);
        $actor = $request->user()?->name ?? 'Admissions Committee';

        $updated = $this->admissionsService->requestMissingDocuments(
            $application,
            $request->validated('missing_documents'),
            $request->validated('instructions'),
            $actor
        );

        return response()->json([
            'success' => true,
            'message' => 'Missing document notice sent and logged successfully.',
            'data' => new ApplicationResource($updated),
        ]);
    }

    /**
     * List all admission cycles.
     */
    public function cycles(): JsonResponse
    {
        $cycles = $this->admissionsService->getCyclesWithStats();

        return response()->json([
            'success' => true,
            'data' => AdmissionCycleResource::collection($cycles),
        ]);
    }

    /**
     * Store a newly created admission cycle.
     */
    public function storeCycle(StoreAdmissionCycleRequest $request): JsonResponse
    {
        $cycle = $this->admissionsService->createCycle($request->validated());

        return (new AdmissionCycleResource($cycle))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update an admission cycle.
     */
    public function updateCycle(UpdateAdmissionCycleRequest $request, AdmissionCycle $cycle): JsonResponse
    {
        $updated = $this->admissionsService->updateCycle($cycle, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Admission cycle updated successfully.',
            'data' => new AdmissionCycleResource($updated),
        ]);
    }

    /**
     * Delete an admission cycle.
     */
    public function destroyCycle(AdmissionCycle $cycle): JsonResponse
    {
        $this->admissionsService->deleteCycle($cycle);

        return response()->json([
            'success' => true,
            'message' => 'Admission cycle deleted successfully.',
        ]);
    }
}