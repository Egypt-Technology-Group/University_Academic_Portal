<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource
{
    use HandlesTranslations;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $departmentsCount = $this->departments_count;
        if ($departmentsCount === null) {
            $departmentsCount = $this->relationLoaded('departments')
                ? $this->departments->count()
                : $this->departments()->count();
        }

        $programsCount = $this->programs_count;
        if ($programsCount === null) {
            $programsCount = $this->relationLoaded('programs')
                ? $this->programs->count()
                : $this->programs()->count();
        }

        return [
            'id' => $this->id,
            'name' => $this->translate('name', $request),
            'slug' => $this->slug,
            'dean_name' => $this->translate('dean_name', $request),
            'about' => $this->translate('about', $request),
            'vision' => $this->translate('vision', $request),
            'mission' => $this->translate('mission', $request),
            'banner_image' => $this->banner_image,
            'is_active' => (bool) $this->is_active,
            'departments_count' => (int) $departmentsCount,
            'programs_count' => (int) $programsCount,
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'faculty_profiles' => FacultyResource::collection($this->whenLoaded('facultyProfiles')),
        ];
    }
}
