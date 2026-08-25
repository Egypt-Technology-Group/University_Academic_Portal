<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\OfficialStatement;
use App\Models\Program;
use App\Models\StudentRecord;
use App\Models\StudentServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicServicesController extends Controller
{
    /**
     * List all electronic service requests (with filters).
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
     * Submit a new student electronic service request.
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
        $requestNum = 'REQ-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

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
     * Update status and workflow notes for an electronic request (Admin).
     */
    public function updateRequestStatus(Request $request, int $id): JsonResponse
    {
        $req = StudentServiceRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,approved,ready_for_pickup,rejected',
            'admin_notes' => 'nullable|string',
            'handled_by' => 'nullable|string',
        ]);

        $req->status = $validated['status'];
        if (isset($validated['admin_notes'])) $req->admin_notes = $validated['admin_notes'];
        if (isset($validated['handled_by'])) $req->handled_by = $validated['handled_by'];
        if (in_array($validated['status'], ['approved', 'ready_for_pickup'])) {
            $req->completed_at = now();
        }

        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Request status updated.',
            'data' => $req->load('program'),
        ]);
    }

    /**
     * Issue an official verifiable statement / certificate.
     */
    public function issueStatement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id_number' => 'required|string',
            'student_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:30',
            'program_id' => 'nullable|exists:programs,id',
            'statement_type' => 'required|string',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'recipient_entity_ar' => 'nullable|string',
            'recipient_entity_en' => 'nullable|string',
            'signatory_name' => 'nullable|string',
            'signatory_title' => 'nullable|string',
            'valid_months' => 'nullable|integer|min:1|max:24',
        ]);

        $certCode = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        $hash = hash('sha256', $certCode . $validated['student_id_number'] . time());
        $qrPayload = url('/verify-certificate?code=' . $certCode . '&hash=' . substr($hash, 0, 16));

        $statement = OfficialStatement::create([
            'certificate_code' => $certCode,
            'student_id_number' => $validated['student_id_number'],
            'student_name' => $validated['student_name'],
            'national_id' => $validated['national_id'],
            'program_id' => $validated['program_id'] ?? null,
            'statement_type' => $validated['statement_type'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'recipient_entity' => [
                'ar' => $validated['recipient_entity_ar'] ?? 'إلى من يهمه الأمر',
                'en' => $validated['recipient_entity_en'] ?? 'To Whom It May Concern',
            ],
            'verification_hash' => $hash,
            'qr_payload' => $qrPayload,
            'signatory_name' => $validated['signatory_name'] ?? 'Prof. Dr. Academic Dean',
            'signatory_title' => $validated['signatory_title'] ?? 'Dean of Academic Affairs',
            'issue_date' => now(),
            'valid_until' => now()->addMonths($validated['valid_months'] ?? 6),
            'is_revoked' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Official certificate issued with cryptographic verification seal.',
            'data' => $statement->load('program'),
        ], 201);
    }

    /**
     * Verify official statement by certificate_code and optional hash.
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

    /**
     * List all exam schedules (Public & Admin).
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
     * Store or update exam schedule with halls and proctors (Admin).
     */
    public function storeExamSchedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'course_code' => 'required|string|max:30',
            'course_name_ar' => 'required|string|max:255',
            'course_name_en' => 'required|string|max:255',
            'exam_type' => 'required|in:midterm,final,practical,oral',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'hall_location_ar' => 'required|string|max:255',
            'hall_location_en' => 'required|string|max:255',
            'chief_invigilator_ar' => 'nullable|string',
            'chief_invigilator_en' => 'nullable|string',
            'proctors_list' => 'nullable|array',
            'seating_capacity' => 'nullable|integer',
        ]);

        $schedule = ExamSchedule::create([
            'program_id' => $validated['program_id'] ?? null,
            'academic_term_id' => $validated['academic_term_id'] ?? null,
            'course_code' => $validated['course_code'],
            'course_name' => ['ar' => $validated['course_name_ar'], 'en' => $validated['course_name_en']],
            'exam_type' => $validated['exam_type'],
            'exam_date' => $validated['exam_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hall_location' => ['ar' => $validated['hall_location_ar'], 'en' => $validated['hall_location_en']],
            'chief_invigilator' => [
                'ar' => $validated['chief_invigilator_ar'] ?? 'رئيس اللجنة الامتحانية',
                'en' => $validated['chief_invigilator_en'] ?? 'Chief Examination Proctor',
            ],
            'proctors_list' => $validated['proctors_list'] ?? ['Eng. Ahmed (TA)', 'Eng. Sarah (TA)'],
            'seating_capacity' => $validated['seating_capacity'] ?? 60,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Exam timetable entry published successfully.',
            'data' => $schedule->load(['program', 'academicTerm']),
        ], 201);
    }

    /**
     * Delete an electronic service request (Admin).
     */
    public function deleteRequest(int $id): JsonResponse
    {
        $req = StudentServiceRequest::findOrFail($id);
        $req->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service request deleted successfully.',
        ]);
    }

    /**
     * Update exam schedule with halls and proctors (Admin).
     */
    public function updateExamSchedule(Request $request, int $id): JsonResponse
    {
        $schedule = ExamSchedule::findOrFail($id);

        $validated = $request->validate([
            'program_id' => 'nullable|integer',
            'academic_term_id' => 'nullable|integer',
            'course_code' => 'sometimes|required|string|max:30',
            'course_name_ar' => 'sometimes|required|string|max:255',
            'course_name_en' => 'sometimes|required|string|max:255',
            'exam_type' => 'sometimes|required|in:midterm,final,practical,oral',
            'exam_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required',
            'hall_location_ar' => 'sometimes|required|string|max:255',
            'hall_location_en' => 'sometimes|required|string|max:255',
            'chief_invigilator_ar' => 'nullable|string',
            'chief_invigilator_en' => 'nullable|string',
            'proctors_list' => 'nullable|array',
            'seating_capacity' => 'nullable|integer',
        ]);

        if (isset($validated['program_id'])) $schedule->program_id = $validated['program_id'];
        if (isset($validated['academic_term_id'])) $schedule->academic_term_id = $validated['academic_term_id'];
        if (isset($validated['course_code'])) $schedule->course_code = $validated['course_code'];
        if (isset($validated['course_name_ar'])) $schedule->setTranslation('course_name', 'ar', $validated['course_name_ar']);
        if (isset($validated['course_name_en'])) $schedule->setTranslation('course_name', 'en', $validated['course_name_en']);
        if (isset($validated['exam_type'])) $schedule->exam_type = $validated['exam_type'];
        if (isset($validated['exam_date'])) $schedule->exam_date = $validated['exam_date'];
        if (isset($validated['start_time'])) $schedule->start_time = $validated['start_time'];
        if (isset($validated['end_time'])) $schedule->end_time = $validated['end_time'];
        if (isset($validated['hall_location_ar'])) $schedule->setTranslation('hall_location', 'ar', $validated['hall_location_ar']);
        if (isset($validated['hall_location_en'])) $schedule->setTranslation('hall_location', 'en', $validated['hall_location_en']);
        if (isset($validated['chief_invigilator_ar'])) $schedule->setTranslation('chief_invigilator', 'ar', $validated['chief_invigilator_ar']);
        if (isset($validated['chief_invigilator_en'])) $schedule->setTranslation('chief_invigilator', 'en', $validated['chief_invigilator_en']);
        if (isset($validated['proctors_list'])) $schedule->proctors_list = $validated['proctors_list'];
        if (isset($validated['seating_capacity'])) $schedule->seating_capacity = (int) $validated['seating_capacity'];

        $schedule->save();

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
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam timetable entry deleted successfully.',
        ]);
    }
}
