<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Services;

use App\Models\AuditLog;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use App\Modules\Admissions\Models\ApplicationDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdmissionsService
{
    /**
     * Get the active admission cycle.
     */
    public function getActiveCycle(): ?AdmissionCycle
    {
        return AdmissionCycle::where('is_open', true)->first();
    }

    /**
     * Get all active programs with department and college relations.
     */
    public function getActivePrograms(): Collection
    {
        return Program::where('is_active', true)->with(['department.college'])->get();
    }

    /**
     * Get all admission cycles.
     */
    public function getAllCycles(): Collection
    {
        return AdmissionCycle::latest()->get();
    }

    /**
     * Get admission cycles with application statistics.
     */
    public function getCyclesWithStats(): Collection
    {
        return AdmissionCycle::withCount([
            'applications',
            'applications as pending_applications_count' => function ($query) {
                $query->whereIn('status', ['submitted', 'under_review']);
            },
            'applications as accepted_applications_count' => function ($query) {
                $query->where('status', 'accepted');
            },
            'applications as rejected_applications_count' => function ($query) {
                $query->where('status', 'rejected');
            },
        ])->latest()->get();
    }

    /**
     * Create a new admission cycle.
     */
    public function createCycle(array $data): AdmissionCycle
    {
        return AdmissionCycle::create($data);
    }

    /**
     * Update an existing admission cycle.
     */
    public function updateCycle(AdmissionCycle $cycle, array $data): AdmissionCycle
    {
        $cycle->update($data);
        return $cycle->fresh();
    }

    /**
     * Delete an admission cycle.
     */
    public function deleteCycle(AdmissionCycle $cycle): void
    {
        $cycle->delete();
    }

    /**
     * Submit an application with document uploads or data mappings.
     *
     * @param array $data
     * @param array<string|int, UploadedFile> $uploadedFiles
     * @return Application
     */
    public function submitApplication(array $data, array $uploadedFiles = []): Application
    {
        return DB::transaction(function () use ($data, $uploadedFiles) {
            $cycleId = $data['admission_cycle_id'] ?? null;
            if (!$cycleId) {
                $cycleId = AdmissionCycle::where('is_open', true)->value('id');
            }

            $year = date('Y');
            do {
                $randomCode = strtoupper(Str::random(5));
                $applicationNumber = "APP-{$year}-{$randomCode}";
            } while (Application::where('application_number', $applicationNumber)->exists());

            $application = Application::create([
                'application_number' => $applicationNumber,
                'admission_cycle_id' => $cycleId,
                'program_id' => $data['program_id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'national_id' => $data['national_id'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'high_school_score' => $data['high_school_score'],
                'status' => 'submitted',
                'notes' => $data['notes'] ?? null,
            ]);

            // Handle uploaded file objects
            if (!empty($uploadedFiles)) {
                $docTypes = $data['document_types'] ?? [];
                foreach ($uploadedFiles as $key => $file) {
                    if ($file instanceof UploadedFile) {
                        $docType = is_string($key) ? $key : ($docTypes[$key] ?? 'document');
                        $path = $file->store("applications/{$application->id}", 'public');
                        $application->documents()->create([
                            'document_type' => $docType,
                            'file_path' => $path,
                            'verification_status' => 'pending',
                        ]);
                    }
                }
            }

            // Handle documents array passed as payload if any (e.g. metadata or file paths)
            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $key => $doc) {
                    if ($doc instanceof UploadedFile) {
                        $docTypes = $data['document_types'] ?? [];
                        $docType = is_string($key) ? $key : ($docTypes[$key] ?? 'document');
                        $path = $doc->store("applications/{$application->id}", 'public');
                        $application->documents()->create([
                            'document_type' => $docType,
                            'file_path' => $path,
                            'verification_status' => 'pending',
                        ]);
                    } elseif (is_array($doc)) {
                        $application->documents()->create([
                            'document_type' => $doc['type'] ?? $doc['document_type'] ?? 'document',
                            'file_path' => $doc['path'] ?? $doc['file_path'] ?? '',
                            'verification_status' => $doc['verification_status'] ?? 'pending',
                        ]);
                    }
                }
            }

            return $application->load(['program.department.college', 'admissionCycle', 'documents']);
        });
    }

    /**
     * Track an application by application number and national ID or email.
     */
    public function trackApplication(string $applicationNumber, ?string $nationalId = null, ?string $email = null): ?Application
    {
        $query = Application::where('application_number', $applicationNumber);

        if (!empty($nationalId)) {
            $query->where('national_id', $nationalId);
        }

        if (!empty($email)) {
            $query->where('email', $email);
        }

        return $query->with(['program.department.college', 'admissionCycle', 'documents'])->first();
    }

    /**
     * Get paginated applications with filters.
     */
    public function getApplications(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Application::with(['program.department.college', 'documents', 'admissionCycle'])
            ->latest();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Update application status, stage, interview schedules, decision reason, etc.
     */
    public function updateApplicationDecision(Application $application, array $data, ?string $actor = 'Admissions Committee'): Application
    {
        return DB::transaction(function () use ($application, $data, $actor) {
            $currentUser = $actor ?: 'Admissions Committee';
            $oldStatus = $application->status;
            $newStatus = $data['status'];

            $application->status = $newStatus;
            if (isset($data['stage'])) {
                $application->stage = $data['stage'];
            }
            if (array_key_exists('interview_scheduled_at', $data)) {
                $application->interview_scheduled_at = $data['interview_scheduled_at'];
            }
            if (array_key_exists('placement_test_at', $data)) {
                $application->placement_test_at = $data['placement_test_at'];
            }
            if (isset($data['decision_reason'])) {
                $application->decision_reason = $data['decision_reason'];
            }
            if (array_key_exists('scholarship_name', $data)) {
                $application->scholarship_name = $data['scholarship_name'];
            }
            if (array_key_exists('scholarship_discount_percent', $data)) {
                $application->scholarship_discount_percent = $data['scholarship_discount_percent'] ?? 0;
            }
            if (array_key_exists('waitlist_position', $data)) {
                $application->waitlist_position = $data['waitlist_position'];
            }
            if (isset($data['enrollment_status'])) {
                $application->enrollment_status = $data['enrollment_status'];
            }
            if (isset($data['verification_checklist'])) {
                $application->verification_checklist = $data['verification_checklist'];
            }
            if (isset($data['notes'])) {
                $application->notes = $data['notes'];
            }
            $application->reviewed_by = $currentUser;
            $application->save();

            // Record history event
            $application->recordTimelineEvent(
                title: "Status changed to: {$newStatus}",
                action: $newStatus,
                actor: $currentUser,
                details: $data['decision_reason'] ?? $data['notes'] ?? "Transitioned from {$oldStatus} to {$newStatus}"
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

            return $application->load(['program.department.college', 'admissionCycle', 'documents']);
        });
    }

    /**
     * Verify or reject an individual application document.
     */
    public function verifyDocument(Application $application, int $documentId, array $data, ?string $actor = 'Admissions Committee'): ApplicationDocument
    {
        return DB::transaction(function () use ($application, $documentId, $data, $actor) {
            $document = $application->documents()->findOrFail($documentId);
            $oldDocStatus = $document->verification_status;
            $currentUser = $actor ?: 'Admissions Committee';

            $document->verification_status = $data['verification_status'];
            if (isset($data['is_original_verified'])) {
                $document->is_original_verified = (bool) $data['is_original_verified'];
            }
            if (array_key_exists('rejection_reason', $data)) {
                $document->rejection_reason = $data['rejection_reason'];
            }
            if (array_key_exists('reviewer_notes', $data)) {
                $document->reviewer_notes = $data['reviewer_notes'];
            }
            $document->verified_at = now();
            $document->verified_by = $currentUser;
            $document->save();

            // Audit timeline record on application
            $application->recordTimelineEvent(
                title: "Document {$document->document_type} marked as: {$document->verification_status}",
                action: "document_{$document->verification_status}",
                actor: $currentUser,
                details: $document->rejection_reason ?? $document->reviewer_notes ?? 'Original verified: ' . ($document->is_original_verified ? 'Yes' : 'No')
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

            return $document;
        });
    }

    /**
     * Request missing documents from the applicant and record timeline & communication log.
     */
    public function requestMissingDocuments(Application $application, array $missingDocuments, ?string $instructions = null, ?string $actor = 'Admissions Committee'): Application
    {
        return DB::transaction(function () use ($application, $missingDocuments, $instructions, $actor) {
            $currentUser = $actor ?: 'Admissions Committee';
            $missingListStr = implode(', ', $missingDocuments);
            $instructionsText = $instructions ?? 'Please provide the missing original credentials at your earliest convenience.';

            $application->recordTimelineEvent(
                title: 'Requested Missing Documents',
                action: 'missing_documents_requested',
                actor: $currentUser,
                details: "Missing: {$missingListStr}. Instructions: {$instructionsText}"
            );

            $application->logCommunication(
                channel: 'email',
                subject: 'Action Required: Submit Missing Application Credentials',
                message: "Dear {$application->first_name},\n\nPlease submit or re-upload the following documents: {$missingListStr}.\n\nInstructions: {$instructionsText}"
            );

            return $application->fresh(['documents']);
        });
    }
}
