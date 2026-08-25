<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'college_id' => $this->college_id,
            'name' => $this->translate('name'),
            'slug' => $this->slug,
            'head_name' => $this->translate('head_name'),
            'description' => $this->translate('description'),
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
            'faculty_profiles' => FacultyResource::collection($this->whenLoaded('facultyProfiles')),
        ];
    }
}
