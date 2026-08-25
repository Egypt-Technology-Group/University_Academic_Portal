<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'document_type' => $this->document_type,
            'file_path' => $this->file_path,
            'verification_status' => $this->verification_status ?? 'pending',
            'is_original_verified' => (bool) $this->is_original_verified,
            'rejection_reason' => $this->rejection_reason,
            'reviewer_notes' => $this->reviewer_notes,
            'verified_at' => $this->verified_at?->toISOString(),
            'verified_by' => $this->verified_by,
        ];
    }
}
