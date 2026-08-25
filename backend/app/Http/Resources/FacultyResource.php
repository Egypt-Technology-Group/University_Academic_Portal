<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
{
    use HandlesTranslations;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user?->name ?? $this->name ?? null,
            'academic_title' => $this->translate('academic_title'),
            'bio' => $this->translate('bio'),
            'research_interests' => $this->translate('research_interests'),
            'email' => $this->email,
            'phone' => $this->phone,
            'office_location' => $this->translate('office_location'),
            'avatar' => $this->avatar,
            'cv_path' => $this->cv_path,
            'is_featured' => (bool) $this->is_featured,
            'department' => new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}
