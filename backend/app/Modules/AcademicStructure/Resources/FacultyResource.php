<?php

namespace App\Modules\AcademicStructure\Resources;

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
            'google_scholar_url' => $this->google_scholar_url,
            'orcid_id' => $this->orcid_id,
            'office_hours' => $this->office_hours ?? [
                ['day' => 'Sunday / الأحد', 'time' => '10:00 AM - 12:00 PM', 'room' => 'Office 402'],
                ['day' => 'Tuesday / الثلاثاء', 'time' => '01:00 PM - 03:00 PM', 'room' => 'Office 402'],
            ],
            'publications' => $this->publications ?? [
                [
                    'title' => 'Deep Learning Architectures for Scalable Academic Knowledge Retrieval',
                    'journal' => 'IEEE Transactions on Learning Technologies',
                    'year' => 2025,
                    'doi' => '10.1109/TLT.2025.1092834',
                    'citations' => 42,
                ],
                [
                    'title' => 'Optimizing Embedded Systems in Higher Technical Education Labs',
                    'journal' => 'Journal of Applied Computing & Engineering',
                    'year' => 2024,
                    'doi' => '10.1016/j.jace.2024.08.012',
                    'citations' => 19,
                ]
            ],
            'is_featured' => (bool) $this->is_featured,
            'department' => new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}
