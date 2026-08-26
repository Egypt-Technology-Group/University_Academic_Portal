<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicServices\Requests\SubmitStudentServiceRequest;
use App\Modules\AcademicServices\Services\AcademicServicesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicServicesController extends Controller
{
    public function __construct(
        protected AcademicServicesService $academicServicesService
    ) {}

    /**
     * List all exam schedules (Public).
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
     * Submit a new student electronic service request (Public).
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
     * List electronic service requests (Public query).
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
     * Verify official statement by certificate_code and optional hash (Public).
     */
    public function verifyStatement(Request $request): JsonResponse
    {
        $code = $request->input('code', $request->input('certificate_code'));
        $hash = $request->input('hash');

        $statement = $this->academicServicesService->verifyStatement($code, $hash);

        if (!$statement) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found or verification hash mismatch.',
            ], 404);
        }

        return response()->json([
            'valid' => !$statement->is_revoked && ($statement->valid_until === null || $statement->valid_until->isFuture()),
            'is_revoked' => $statement->is_revoked,
            'is_expired' => $statement->valid_until && $statement->valid_until->isPast(),
            'statement' => $statement,
        ]);
    }
}
