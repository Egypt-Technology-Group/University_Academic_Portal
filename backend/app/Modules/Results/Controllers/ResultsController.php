<?php
declare(strict_types=1);

namespace App\Modules\Results\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Results\Requests\InquireResultsRequest;
use App\Modules\Results\Requests\SimulateRegistrationRequest;
use App\Modules\Results\Services\ResultsService;
use Illuminate\Http\JsonResponse;

class ResultsController extends Controller
{
    public function __construct(
        protected readonly ResultsService $resultsService
    ) {}

    /**
     * Inquire student results by student_id_number and optional term_id.
     */
    public function inquireResult(InquireResultsRequest $request): JsonResponse
    {
        $studentIdNumber = (string) $request->input('student_id_number');
        $nationalId = $request->input('national_id', $request->input('national_id_number'));
        $termIdInput = $request->input('term_id', $request->input('academic_term_id'));
        $termId = $termIdInput !== null ? (int) $termIdInput : null;

        $data = $this->resultsService->inquireResults(
            studentIdNumber: $studentIdNumber,
            nationalId: $nationalId ? (string) $nationalId : null,
            termId: $termId
        );

        if (!$data) {
            return response()->json([
                'message' => 'Student record not found.',
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * Simulate next term course registration with credit caps and prerequisites check.
     */
    public function simulateRegistration(SimulateRegistrationRequest $request): JsonResponse
    {
        $data = $this->resultsService->simulateRegistration(
            student: (string) $request->input('student_id_number'),
            selectedCourses: (array) $request->input('selected_courses')
        );

        if (!$data) {
            return response()->json([
                'message' => 'Student record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
