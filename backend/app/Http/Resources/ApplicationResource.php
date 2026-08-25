<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cycleTitle = null;
        if ($this->admissionCycle) {
            $cycleTitle = $this->admissionCycle->getTranslation('title', app()->getLocale(), false)
                ?: $this->admissionCycle->title;
        }

        return [
            'id' => $this->id,
            'application_number' => $this->application_number,
            'program' => $this->program ? new ProgramResource($this->program) : null,
            'cycle' => $cycleTitle,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'national_id' => $this->national_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'high_school_score' => (float) $this->high_school_score,
            'status' => $this->status,
            'stage' => $this->stage ?? 'initial_screening',
            'interview_scheduled_at' => $this->interview_scheduled_at?->toISOString(),
            'placement_test_at' => $this->placement_test_at?->toISOString(),
            'decision_reason' => $this->decision_reason,
            'timeline' => $this->timeline ?? [
                [
                    'title' => 'Application Submitted',
                    'action' => 'submitted',
                    'actor' => 'Applicant',
                    'timestamp' => $this->created_at?->toISOString() ?? now()->toISOString(),
                ]
            ],
            'reviewed_by' => $this->reviewed_by,
            'notes' => $this->notes,
            'documents' => ApplicationDocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at?->toISOString() ?? $this->created_at,
        ];
    }
}
