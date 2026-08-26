<?php

namespace App\Modules\AcademicServices\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\OfficialStatement;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicServicesController extends Controller
{
    /**
     * List all exam schedules (Public).
     */
    public function indexExamSchedules(Request $request): JsonResponse
    {
        $query = ExamSchedule::with(['program.department', 'academicTerm'])
            ->orderBy('exam_date')
            ->orderBy('start_time');

        if ($request->filled('program_id') && $request->program_id !== 'all') {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('exam_type') && $request->exam_type !== 'all') {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Submit a new student electronic service request (Public).
     */
    public function submitRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id_number' => 'required|string',
            'student_name' => 'required|string|max:255',
            'program_id' => 'nullable|exists:programs,id',
            'service_type' => 'required|string',
            'purpose_ar' => 'nullable|string',
            'purpose_en' => 'nullable|string',
            'fee_amount' => 'nullable|numeric',
        ]);

        $count = StudentServiceRequest::count() + 1;
        $requestNum = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);

        $req = StudentServiceRequest::create([
            'request_number' => $requestNum,
            'student_id_number' => $validated['student_id_number'],
            'student_name' => $validated['student_name'],
            'program_id' => $validated['program_id'] ?? null,
            'service_type' => $validated['service_type'],
            'purpose' => [
                'ar' => $validated['purpose_ar'] ?? 'طلب إداري معتمد',
                'en' => $validated['purpose_en'] ?? 'Official university student request',
            ],
            'status' => 'pending',
            'fee_amount' => $validated['fee_amount'] ?? 50.00,
            'is_fee_paid' => true,
        ]);

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
        $query = StudentServiceRequest::with('program')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id_number', $request->student_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Verify official statement by certificate_code and optional hash (Public).
     */
    public function verifyStatement(Request $request): JsonResponse
    {
        $code = $request->input('code', $request->input('certificate_code'));
        $hash = $request->input('hash');

        $query = OfficialStatement::with('program.department')
            ->where('certificate_code', $code);

        if ($hash) {
            $query->where('verification_hash', 'like', "{$hash}%");
        }

        $statement = $query->first();

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
