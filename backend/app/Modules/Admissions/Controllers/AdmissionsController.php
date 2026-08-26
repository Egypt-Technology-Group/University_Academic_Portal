<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicStructure\Resources\ProgramResource;
use App\Modules\Admissions\Requests\SubmitApplicationRequest;
use App\Modules\Admissions\Requests\TrackApplicationRequest;
use App\Modules\Admissions\Resources\AdmissionCycleResource;
use App\Modules\Admissions\Resources\ApplicationResource;
use App\Modules\Admissions\Services\AdmissionsService;
use Illuminate\Http\JsonResponse;

class AdmissionsController extends Controller
{
    public function __construct(
        protected AdmissionsService $admissionsService
    ) {}

    /**
     * Returns current open admission cycle with available programs.
     */
    public function activeCycle(): JsonResponse
    {
        $cycle = $this->admissionsService->getActiveCycle();
        $programs = $this->admissionsService->getActivePrograms();

        return response()->json([
            'cycle' => $cycle ? new AdmissionCycleResource($cycle) : null,
            'programs' => ProgramResource::collection($programs),
        ]);
    }

    /**
     * Creates application, generates tracking code, handles document attachments, returns application data.
     */
    public function submitApplication(SubmitApplicationRequest $request): JsonResponse
    {
        $uploadedFiles = $request->hasFile('documents') ? $request->file('documents') : [];
        $application = $this->admissionsService->submitApplication($request->validated(), $uploadedFiles);

        return (new ApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Lookup by application_number & national_id or email, returns application status & documents.
     */
    public function trackApplication(TrackApplicationRequest $request): JsonResponse|ApplicationResource
    {
        $application = $this->admissionsService->trackApplication(
            $request->validated('application_number'),
            $request->validated('national_id'),
            $request->validated('email')
        );

        if (!$application) {
            return response()->json([
                'message' => 'Application not found matching the provided credentials.',
            ], 404);
        }

        return new ApplicationResource($application);
    }
}