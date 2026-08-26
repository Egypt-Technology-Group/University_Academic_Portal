<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\AdmissionCycle;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\College;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\FacultyProfile;
use App\Models\NewsArticle;
use App\Models\Program;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    /**
     * Return high-level summary KPI metrics for the Admin Dashboard.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_colleges' => College::count(),
                'total_programs' => Program::count(),
                'total_faculty' => FacultyProfile::count(),
                'total_students' => StudentRecord::count(),
                'total_applications' => Application::count(),
                'pending_applications' => Application::where('status', 'submitted')->orWhere('status', 'under_review')->count(),
                'accepted_applications' => Application::where('status', 'accepted')->count(),
                'rejected_applications' => Application::where('status', 'rejected')->count(),
                'total_news' => NewsArticle::count(),
                'total_events' => Event::count(),
                'total_documents' => DownloadDocument::count(),
                'active_admission_cycles' => AdmissionCycle::where('is_open', true)->count(),
            ],
        ]);
    }

    /**
     * List applications for admin review with filtering by status and search.
     */
    public function applications(Request $request): JsonResponse
    {
        $query = Application::with(['program.department.college', 'documents', 'admissionCycle'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $applications = $query->paginate($perPage);

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
    public function updateApplicationStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'submitted', 'under_review', 'accepted', 'rejected'])],
            'stage' => ['nullable', 'string', Rule::in(['initial_screening', 'placement_test', 'interview', 'final_decision', 'completed'])],
            'interview_scheduled_at' => 'nullable|date',
            'placement_test_at' => 'nullable|date',
            'decision_reason' => 'nullable|string|max:500',
            'scholarship_name' => 'nullable|string|max:100',
            'scholarship_discount_percent' => 'nullable|integer|min:0|max:100',
            'waitlist_position' => 'nullable|integer|min:1',
            'enrollment_status' => ['nullable', Rule::in(['pending', 'documents_verified', 'tuition_paid', 'enrolled', 'withdrawn'])],
            'verification_checklist' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $application = Application::with(['program', 'documents'])->findOrFail($id);

        $currentUser = $request->user()?->name ?? 'Admissions Committee';
        $oldStatus = $application->status;
        $newStatus = $validated['status'];

        $application->status = $newStatus;
        if (isset($validated['stage'])) {
            $application->stage = $validated['stage'];
        }
        if (array_key_exists('interview_scheduled_at', $validated)) {
            $application->interview_scheduled_at = $validated['interview_scheduled_at'];
        }
        if (array_key_exists('placement_test_at', $validated)) {
            $application->placement_test_at = $validated['placement_test_at'];
        }
        if (isset($validated['decision_reason'])) {
            $application->decision_reason = $validated['decision_reason'];
        }
        if (array_key_exists('scholarship_name', $validated)) {
            $application->scholarship_name = $validated['scholarship_name'];
        }
        if (array_key_exists('scholarship_discount_percent', $validated)) {
            $application->scholarship_discount_percent = $validated['scholarship_discount_percent'] ?? 0;
        }
        if (array_key_exists('waitlist_position', $validated)) {
            $application->waitlist_position = $validated['waitlist_position'];
        }
        if (isset($validated['enrollment_status'])) {
            $application->enrollment_status = $validated['enrollment_status'];
        }
        if (isset($validated['verification_checklist'])) {
            $application->verification_checklist = $validated['verification_checklist'];
        }
        if (isset($validated['notes'])) {
            $application->notes = $validated['notes'];
        }
        $application->reviewed_by = $currentUser;

        // Record history event
        $application->recordTimelineEvent(
            title: "Status changed to: {$newStatus}",
            action: $newStatus,
            actor: $currentUser,
            details: $validated['decision_reason'] ?? $validated['notes'] ?? "Transitioned from {$oldStatus} to {$newStatus}"
        );

        // System-wide Audit Log recording
        AuditLog::record(
            action: 'status_change',
            auditable: $application,
            oldValues: ['status' => $oldStatus, 'stage' => $application->getOriginal('stage')],
            newValues: ['status' => $newStatus, 'stage' => $application->stage, 'decision_reason' => $application->decision_reason],
            module: 'admissions',
            descriptionAr: "تحديث حالة طلب الالتحاق ({$application->application_number}) إلى: {$newStatus}",
            descriptionEn: "Updated application ({$application->application_number}) status to: {$newStatus}",
            severity: $newStatus === 'accepted' ? 'notice' : 'info',
            status: 'success'
        );

        // Automated notification log
        if ($newStatus === 'accepted') {
            $application->logCommunication('email', 'Official Acceptance & Enrollment Offer', "Congratulations {$application->first_name}! You have been accepted to {$application->program?->name}.");
        }

        return response()->json([
            'success' => true,
            'message' => 'Application workflow updated and timeline recorded successfully.',
            'data' => new ApplicationResource($application),
        ]);
    }

    /**
     * Verify or reject an individual application document and record audit notes.
     */
    public function verifyDocument(Request $request, int $applicationId, int $documentId): JsonResponse
    {
        $validated = $request->validate([
            'verification_status' => ['required', Rule::in(['pending', 'verified', 'rejected', 'action_required'])],
            'is_original_verified' => 'nullable|boolean',
            'rejection_reason' => 'nullable|string|max:300',
            'reviewer_notes' => 'nullable|string|max:500',
        ]);

        $application = Application::with('documents')->findOrFail($applicationId);
        $document = $application->documents()->findOrFail($documentId);
        $oldDocStatus = $document->verification_status;
        $currentUser = $request->user()?->name ?? 'Admissions Committee';

        $document->verification_status = $validated['verification_status'];
        if (isset($validated['is_original_verified'])) {
            $document->is_original_verified = (bool) $validated['is_original_verified'];
        }
        if (array_key_exists('rejection_reason', $validated)) {
            $document->rejection_reason = $validated['rejection_reason'];
        }
        if (array_key_exists('reviewer_notes', $validated)) {
            $document->reviewer_notes = $validated['reviewer_notes'];
        }
        $document->verified_at = now();
        $document->verified_by = $currentUser;
        $document->save();

        // Audit timeline record on application
        $application->recordTimelineEvent(
            title: "Document {$document->document_type} marked as: {$document->verification_status}",
            action: "document_{$document->verification_status}",
            actor: $currentUser,
            details: $document->rejection_reason ?? $document->reviewer_notes ?? "Original verified: " . ($document->is_original_verified ? 'Yes' : 'No')
        );

        // System Audit Trail recording
        AuditLog::record(
            action: 'verify',
            auditable: $application,
            oldValues: ['document_id' => $documentId, 'status' => $oldDocStatus],
            newValues: ['document_id' => $documentId, 'status' => $document->verification_status, 'rejection_reason' => $document->rejection_reason],
            module: 'admissions',
            descriptionAr: "تدقيق مستند ({$document->document_type}) لطلب الالتحاق ({$application->application_number}) إلى حالة: {$document->verification_status}",
            descriptionEn: "Audited document ({$document->document_type}) for app ({$application->application_number}) as: {$document->verification_status}",
            severity: 'info',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Document verification status updated successfully.',
            'data' => new ApplicationResource($application->fresh(['documents'])),
        ]);
    }

    /**
     * Trigger an official request notification to the applicant for missing or re-uploaded documents.
     */
    public function requestMissingDocuments(Request $request, int $applicationId): JsonResponse
    {
        $validated = $request->validate([
            'missing_documents' => 'required|array|min:1',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $application = Application::with('documents')->findOrFail($applicationId);
        $currentUser = $request->user()?->name ?? 'Admissions Committee';

        $missingListStr = implode(', ', $validated['missing_documents']);
        $instructions = $validated['instructions'] ?? 'Please provide the missing original credentials at your earliest convenience.';

        $application->recordTimelineEvent(
            title: 'Requested Missing Documents',
            action: 'missing_documents_requested',
            actor: $currentUser,
            details: "Missing: {$missingListStr}. Instructions: {$instructions}"
        );

        $application->logCommunication(
            channel: 'email',
            subject: 'Action Required: Submit Missing Application Credentials',
            message: "Dear {$application->first_name},\n\nPlease submit or re-upload the following documents: {$missingListStr}.\n\nInstructions: {$instructions}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Missing document notice sent and logged successfully.',
            'data' => new ApplicationResource($application->fresh(['documents'])),
        ]);
    }
}
