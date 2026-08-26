<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\OfficialStatement;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AcademicServicesService
{
    /**
     * Get electronic service requests filtered and sorted.
     */
    public function getRequests(array $filters = []): Collection
    {
        $query = StudentServiceRequest::with('program')->latest();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service_type']) && $filters['service_type'] !== 'all') {
            $query->where('service_type', $filters['service_type']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id_number', $filters['student_id']);
        }

        if (!empty($filters['search'])) {
            $search = (string) $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Submit a new student electronic service request.
     */
    public function submitRequest(array $data): StudentServiceRequest
    {
        $count = StudentServiceRequest::count() + 1;
        $requestNum = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);

        return StudentServiceRequest::create([
            'request_number' => $requestNum,
            'student_id_number' => $data['student_id_number'],
            'student_name' => $data['student_name'],
            'program_id' => $data['program_id'] ?? null,
            'service_type' => $data['service_type'],
            'purpose' => [
                'ar' => $data['purpose_ar'] ?? 'طلب إداري معتمد',
                'en' => $data['purpose_en'] ?? 'Official university student request',
            ],
            'status' => 'pending',
            'fee_amount' => $data['fee_amount'] ?? 50.00,
            'is_fee_paid' => true,
        ]);
    }

    /**
     * Update status and workflow notes for an electronic request.
     */
    public function updateRequestStatus(StudentServiceRequest $req, array $data, ?User $admin = null): StudentServiceRequest
    {
        $req->status = $data['status'];
        if (isset($data['admin_notes'])) {
            $req->admin_notes = $data['admin_notes'];
        }
        if (isset($data['handled_by'])) {
            $req->handled_by = $data['handled_by'];
        } elseif ($admin && empty($req->handled_by)) {
            $req->handled_by = $admin->name;
        }

        if (in_array($data['status'], ['approved', 'ready_for_pickup'], true)) {
            $req->completed_at = now();
        }

        $req->save();

        return $req;
    }

    /**
     * Delete an electronic service request.
     */
    public function deleteRequest(StudentServiceRequest $req): void
    {
        $req->delete();
    }

    /**
     * Get exam schedules filtered and sorted.
     */
    public function getExamSchedules(array $filters = []): Collection
    {
        $query = ExamSchedule::with(['program.department', 'academicTerm'])
            ->orderBy('exam_date')
            ->orderBy('start_time');

        if (!empty($filters['program_id']) && $filters['program_id'] !== 'all') {
            $query->where('program_id', $filters['program_id']);
        }

        if (!empty($filters['exam_type']) && $filters['exam_type'] !== 'all') {
            $query->where('exam_type', $filters['exam_type']);
        }

        if (!empty($filters['search'])) {
            $search = (string) $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Create an exam schedule.
     */
    public function createExamSchedule(array $data, ?UploadedFile $timetableFile = null): ExamSchedule
    {
        $workflowMode = $data['workflow_mode'] ?? 'structured';
        $timetablePath = $data['timetable_document_path'] ?? null;
        $fileName = $data['timetable_file_name'] ?? null;
        $fileSize = $data['timetable_file_size'] ?? null;

        if ($timetableFile) {
            $stored = $timetableFile->store('exam_timetables', 'public');
            $timetablePath = '/storage/' . $stored;
            $fileName = $timetableFile->getClientOriginalName();
            $bytes = $timetableFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        }

        $courseCode = $data['course_code'] ?? ($fileName ? 'TIMETABLE-ALL' : 'EXAM-GEN');
        $courseNameAr = $data['course_name_ar'] ?? ($fileName ? 'جدول امتحانات معتمد: ' . $fileName : 'جدول الامتحانات الرسمية');
        $courseNameEn = $data['course_name_en'] ?? ($fileName ? 'Official Exam Timetable: ' . $fileName : 'Official Exam Timetable');

        return ExamSchedule::create([
            'program_id' => $data['program_id'] ?? null,
            'academic_term_id' => $data['academic_term_id'] ?? null,
            'course_code' => $courseCode,
            'course_name' => ['ar' => $courseNameAr, 'en' => $courseNameEn],
            'exam_type' => $data['exam_type'] ?? 'final',
            'workflow_mode' => $workflowMode,
            'exam_date' => $data['exam_date'] ?? now()->format('Y-m-d'),
            'start_time' => $data['start_time'] ?? '09:00',
            'end_time' => $data['end_time'] ?? '12:00',
            'hall_location' => [
                'ar' => $data['hall_location_ar'] ?? 'قاعات ومدرجات الكلية الرئيسية',
                'en' => $data['hall_location_en'] ?? 'Main Examination Halls & Auditoriums',
            ],
            'chief_invigilator' => [
                'ar' => $data['chief_invigilator_ar'] ?? 'رئيس اللجنة الامتحانية',
                'en' => $data['chief_invigilator_en'] ?? 'Chief Examination Proctor',
            ],
            'proctors_list' => $data['proctors_list'] ?? ['Eng. Ahmed (TA)', 'Eng. Sarah (TA)'],
            'seating_capacity' => $data['seating_capacity'] ?? 60,
            'timetable_document_path' => $timetablePath,
            'timetable_file_name' => $fileName,
            'timetable_file_size' => $fileSize,
        ]);
    }

    /**
     * Update an exam schedule.
     */
    public function updateExamSchedule(ExamSchedule $exam, array $data, ?UploadedFile $timetableFile = null): ExamSchedule
    {
        if ($timetableFile) {
            $stored = $timetableFile->store('exam_timetables', 'public');
            $exam->timetable_document_path = '/storage/' . $stored;
            $exam->timetable_file_name = $timetableFile->getClientOriginalName();
            $bytes = $timetableFile->getSize();
            $exam->timetable_file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        } elseif (isset($data['timetable_document_path'])) {
            $exam->timetable_document_path = $data['timetable_document_path'];
            if (isset($data['timetable_file_name'])) {
                $exam->timetable_file_name = $data['timetable_file_name'];
            }
            if (isset($data['timetable_file_size'])) {
                $exam->timetable_file_size = $data['timetable_file_size'];
            }
        }

        if (isset($data['workflow_mode'])) $exam->workflow_mode = $data['workflow_mode'];
        if (isset($data['program_id'])) $exam->program_id = $data['program_id'];
        if (isset($data['academic_term_id'])) $exam->academic_term_id = $data['academic_term_id'];
        if (isset($data['course_code'])) $exam->course_code = $data['course_code'];
        if (isset($data['course_name_ar'])) $exam->setTranslation('course_name', 'ar', $data['course_name_ar']);
        if (isset($data['course_name_en'])) $exam->setTranslation('course_name', 'en', $data['course_name_en']);
        if (isset($data['exam_type'])) $exam->exam_type = $data['exam_type'];
        if (isset($data['exam_date'])) $exam->exam_date = $data['exam_date'];
        if (isset($data['start_time'])) $exam->start_time = $data['start_time'];
        if (isset($data['end_time'])) $exam->end_time = $data['end_time'];
        if (isset($data['hall_location_ar'])) $exam->setTranslation('hall_location', 'ar', $data['hall_location_ar']);
        if (isset($data['hall_location_en'])) $exam->setTranslation('hall_location', 'en', $data['hall_location_en']);
        if (isset($data['chief_invigilator_ar'])) $exam->setTranslation('chief_invigilator', 'ar', $data['chief_invigilator_ar']);
        if (isset($data['chief_invigilator_en'])) $exam->setTranslation('chief_invigilator', 'en', $data['chief_invigilator_en']);
        if (isset($data['proctors_list'])) $exam->proctors_list = $data['proctors_list'];
        if (isset($data['seating_capacity'])) $exam->seating_capacity = (int) $data['seating_capacity'];

        $exam->save();

        return $exam;
    }

    /**
     * Delete an exam schedule.
     */
    public function deleteExamSchedule(ExamSchedule $exam): void
    {
        $exam->delete();
    }

    /**
     * Get official statements filtered and sorted.
     */
    public function getOfficialStatements(array $filters = []): Collection
    {
        $query = OfficialStatement::with('program.department')->latest();

        if (!empty($filters['student_id'])) {
            $query->where('student_id_number', $filters['student_id']);
        }

        if (!empty($filters['search'])) {
            $search = (string) $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('certificate_code', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Issue an official statement and record audit log.
     */
    public function issueOfficialStatement(array $data, ?UploadedFile $documentFile = null, ?User $issuer = null): OfficialStatement
    {
        $workflowMode = $data['workflow_mode'] ?? 'structured';
        $docPath = $data['document_path'] ?? null;
        $fileName = $data['file_name'] ?? null;
        $fileSize = $data['file_size'] ?? null;

        if ($documentFile) {
            $stored = $documentFile->store('official_statements', 'public');
            $docPath = '/storage/' . $stored;
            $fileName = $documentFile->getClientOriginalName();
            $bytes = $documentFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 0) . ' KB';
        }

        $studentId = $data['student_id_number'] ?? 'STU-' . rand(10000, 99999);
        $studentName = $data['student_name'] ?? ($fileName ? pathinfo($fileName, PATHINFO_FILENAME) : 'طالب مقيد');
        $nationalId = $data['national_id'] ?? '30000000000000';
        $stmtType = $data['statement_type'] ?? 'official_enrollment';

        $titleAr = $data['title_ar'] ?? ($fileName ? 'وثيقة رسمية معتمدة: ' . $fileName : 'إفادة قيد رسمية معتمدة');
        $titleEn = $data['title_en'] ?? ($fileName ? 'Official Verified Document: ' . $fileName : 'Official Verified Statement');

        $certCode = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        $hash = hash('sha256', $certCode . $studentId . time());
        $qrPayload = url('/verify-certificate?code=' . $certCode . '&hash=' . substr($hash, 0, 16));

        $statement = OfficialStatement::create([
            'certificate_code' => $certCode,
            'student_id_number' => $studentId,
            'student_name' => $studentName,
            'national_id' => $nationalId,
            'program_id' => $data['program_id'] ?? null,
            'statement_type' => $stmtType,
            'workflow_mode' => $workflowMode,
            'title' => ['ar' => $titleAr, 'en' => $titleEn],
            'recipient_entity' => [
                'ar' => $data['recipient_entity_ar'] ?? 'إلى من يهمه الأمر',
                'en' => $data['recipient_entity_en'] ?? 'To Whom It May Concern',
            ],
            'verification_hash' => $hash,
            'qr_payload' => $qrPayload,
            'document_path' => $docPath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'signatory_name' => $data['signatory_name'] ?? 'Prof. Dr. Academic Dean',
            'signatory_title' => $data['signatory_title'] ?? 'Dean of Academic Affairs',
            'issue_date' => now(),
            'valid_until' => now()->addMonths((int) ($data['valid_months'] ?? 6)),
            'is_revoked' => false,
        ]);

        // Audit Trail Recording
        AuditLog::record(
            action: 'create',
            auditable: $statement,
            oldValues: null,
            newValues: [
                'certificate_code' => $certCode,
                'student_id' => $studentId,
                'student_name' => $studentName,
                'workflow_mode' => $workflowMode,
            ],
            module: 'services',
            descriptionAr: "إصدار وتوثيق شهادة/إفادة رسمية جديدة بكود ({$certCode}) للطالب: {$studentName}",
            descriptionEn: "Issued and sealed official credential ({$certCode}) for student: {$studentName}",
            severity: 'notice',
            status: 'success'
        );

        return $statement;
    }

    /**
     * Verify official statement by code and optional hash.
     */
    public function verifyStatement(?string $code, ?string $hash = null): ?OfficialStatement
    {
        if (empty($code)) {
            return null;
        }

        $query = OfficialStatement::with('program.department')
            ->where('certificate_code', $code);

        if ($hash) {
            $query->where('verification_hash', 'like', "{$hash}%");
        }

        return $query->first();
    }
}
