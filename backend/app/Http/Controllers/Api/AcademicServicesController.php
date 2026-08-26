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
     * List all issued official statements & certificates (Admin).
     */
    public function indexStatements(Request $request): JsonResponse
    {
        $query = OfficialStatement::with('program.department')->latest();

        if ($request->filled('student_id')) {
            $query->where('student_id_number', $request->student_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_code', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Issue an official verifiable statement / certificate (Supports 3 Modes: structured, file_only, both).
     */
    public function issueStatement(Request $request): JsonResponse
    {
        $workflowMode = $request->input('workflow_mode', 'structured'); // structured | file_only | both
        
        $validated = $request->validate([
            'student_id_number' => 'nullable|string',
            'student_name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:30',
            'program_id' => 'nullable|exists:programs,id',
            'statement_type' => 'nullable|string',
            'workflow_mode' => 'nullable|string|in:structured,file_only,both',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'recipient_entity_ar' => 'nullable|string',
            'recipient_entity_en' => 'nullable|string',
            'signatory_name' => 'nullable|string',
            'signatory_title' => 'nullable|string',
            'valid_months' => 'nullable|integer|min:1|max:24',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:51200',
            'document_path' => 'nullable|string',
            'file_name' => 'nullable|string',
            'file_size' => 'nullable|string',
        ]);

        $docPath = $validated['document_path'] ?? null;
        $fileName = $validated['file_name'] ?? null;
        $fileSize = $validated['file_size'] ?? null;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $stored = $uploadedFile->store('official_statements', 'public');
            $docPath = '/storage/' . $stored;
            $fileName = $uploadedFile->getClientOriginalName();
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        }

        $studentId = $validated['student_id_number'] ?? 'STU-' . rand(10000, 99999);
        $studentName = $validated['student_name'] ?? ($fileName ? pathinfo($fileName, PATHINFO_FILENAME) : 'طالب مقيد');
        $nationalId = $validated['national_id'] ?? '30000000000000';
        $stmtType = $validated['statement_type'] ?? 'official_enrollment';

        $titleAr = $validated['title_ar'] ?? ($fileName ? 'وثيقة رسمية معتمدة: ' . $fileName : 'إفادة قيد رسمية معتمدة');
        $titleEn = $validated['title_en'] ?? ($fileName ? 'Official Verified Document: ' . $fileName : 'Official Verified Statement');

        $certCode = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        $hash = hash('sha256', $certCode . $studentId . time());
        $qrPayload = url('/verify-certificate?code=' . $certCode . '&hash=' . substr($hash, 0, 16));

        $statement = OfficialStatement::create([
            'certificate_code' => $certCode,
            'student_id_number' => $studentId,
            'student_name' => $studentName,
            'national_id' => $nationalId,
            'program_id' => $validated['program_id'] ?? null,
            'statement_type' => $stmtType,
            'workflow_mode' => $workflowMode,
            'title' => ['ar' => $titleAr, 'en' => $titleEn],
            'recipient_entity' => [
                'ar' => $validated['recipient_entity_ar'] ?? 'إلى من يهمه الأمر',
                'en' => $validated['recipient_entity_en'] ?? 'To Whom It May Concern',
            ],
            'verification_hash' => $hash,
            'qr_payload' => $qrPayload,
            'document_path' => $docPath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'signatory_name' => $validated['signatory_name'] ?? 'Prof. Dr. Academic Dean',
            'signatory_title' => $validated['signatory_title'] ?? 'Dean of Academic Affairs',
            'issue_date' => now(),
            'valid_until' => now()->addMonths($validated['valid_months'] ?? 6),
            'is_revoked' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Official certificate registered with Hybrid Document Workflow and cryptographic verification seal.',
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
     * Store or update exam schedule with halls and proctors (Admin - Supports 3 Modes).
     */
    public function storeExamSchedule(Request $request): JsonResponse
    {
        $workflowMode = $request->input('workflow_mode', 'structured');

        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'course_code' => 'nullable|string|max:30',
            'course_name_ar' => 'nullable|string|max:255',
            'course_name_en' => 'nullable|string|max:255',
            'exam_type' => 'nullable|in:midterm,final,practical,oral',
            'workflow_mode' => 'nullable|in:structured,file_only,both',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'hall_location_ar' => 'nullable|string|max:255',
            'hall_location_en' => 'nullable|string|max:255',
            'chief_invigilator_ar' => 'nullable|string',
            'chief_invigilator_en' => 'nullable|string',
            'proctors_list' => 'nullable|array',
            'seating_capacity' => 'nullable|integer',
            'timetable_document' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx,jpg,jpeg,png|max:51200',
            'timetable_document_path' => 'nullable|string',
            'timetable_file_name' => 'nullable|string',
            'timetable_file_size' => 'nullable|string',
        ]);

        $timetablePath = $validated['timetable_document_path'] ?? null;
        $fileName = $validated['timetable_file_name'] ?? null;
        $fileSize = $validated['timetable_file_size'] ?? null;

        if ($request->hasFile('timetable_document')) {
            $uploadedFile = $request->file('timetable_document');
            $stored = $uploadedFile->store('exam_timetables', 'public');
            $timetablePath = '/storage/' . $stored;
            $fileName = $uploadedFile->getClientOriginalName();
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        }

        $courseCode = $validated['course_code'] ?? ($fileName ? 'TIMETABLE-ALL' : 'EXAM-GEN');
        $courseNameAr = $validated['course_name_ar'] ?? ($fileName ? 'جدول امتحانات معتمد: ' . $fileName : 'جدول الامتحانات الرسمية');
        $courseNameEn = $validated['course_name_en'] ?? ($fileName ? 'Official Exam Timetable: ' . $fileName : 'Official Exam Timetable');

        $schedule = ExamSchedule::create([
            'program_id' => $validated['program_id'] ?? null,
            'academic_term_id' => $validated['academic_term_id'] ?? null,
            'course_code' => $courseCode,
            'course_name' => ['ar' => $courseNameAr, 'en' => $courseNameEn],
            'exam_type' => $validated['exam_type'] ?? 'final',
            'workflow_mode' => $workflowMode,
            'exam_date' => $validated['exam_date'] ?? now()->format('Y-m-d'),
            'start_time' => $validated['start_time'] ?? '09:00',
            'end_time' => $validated['end_time'] ?? '12:00',
            'hall_location' => [
                'ar' => $validated['hall_location_ar'] ?? 'قاعات ومدرجات الكلية الرئيسية',
                'en' => $validated['hall_location_en'] ?? 'Main Examination Halls & Auditoriums',
            ],
            'chief_invigilator' => [
                'ar' => $validated['chief_invigilator_ar'] ?? 'رئيس اللجنة الامتحانية',
                'en' => $validated['chief_invigilator_en'] ?? 'Chief Examination Proctor',
            ],
            'proctors_list' => $validated['proctors_list'] ?? ['Eng. Ahmed (TA)', 'Eng. Sarah (TA)'],
            'seating_capacity' => $validated['seating_capacity'] ?? 60,
            'timetable_document_path' => $timetablePath,
            'timetable_file_name' => $fileName,
            'timetable_file_size' => $fileSize,
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
            'course_code' => 'sometimes|nullable|string|max:30',
            'course_name_ar' => 'sometimes|nullable|string|max:255',
            'course_name_en' => 'sometimes|nullable|string|max:255',
            'exam_type' => 'sometimes|nullable|in:midterm,final,practical,oral',
            'workflow_mode' => 'nullable|in:structured,file_only,both',
            'exam_date' => 'sometimes|nullable|date',
            'start_time' => 'sometimes|nullable',
            'end_time' => 'sometimes|nullable',
            'hall_location_ar' => 'sometimes|nullable|string|max:255',
            'hall_location_en' => 'sometimes|nullable|string|max:255',
            'chief_invigilator_ar' => 'nullable|string',
            'chief_invigilator_en' => 'nullable|string',
            'proctors_list' => 'nullable|array',
            'seating_capacity' => 'nullable|integer',
            'timetable_document' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx,jpg,jpeg,png|max:51200',
            'timetable_document_path' => 'nullable|string',
            'timetable_file_name' => 'nullable|string',
            'timetable_file_size' => 'nullable|string',
        ]);

        if ($request->hasFile('timetable_document')) {
            $uploadedFile = $request->file('timetable_document');
            $stored = $uploadedFile->store('exam_timetables', 'public');
            $schedule->timetable_document_path = '/storage/' . $stored;
            $schedule->timetable_file_name = $uploadedFile->getClientOriginalName();
            $bytes = $uploadedFile->getSize();
            $schedule->timetable_file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        } elseif (isset($validated['timetable_document_path'])) {
            $schedule->timetable_document_path = $validated['timetable_document_path'];
            if (isset($validated['timetable_file_name'])) $schedule->timetable_file_name = $validated['timetable_file_name'];
            if (isset($validated['timetable_file_size'])) $schedule->timetable_file_size = $validated['timetable_file_size'];
        }

        if (isset($validated['workflow_mode'])) $schedule->workflow_mode = $validated['workflow_mode'];
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
