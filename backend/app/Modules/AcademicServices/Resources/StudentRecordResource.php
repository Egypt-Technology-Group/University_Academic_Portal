<?php

namespace App\Modules\AcademicServices\Resources;

use App\Modules\Results\Resources\CourseResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $programName = null;
        if ($this->program) {
            $programName = $this->program->getTranslation('name', app()->getLocale(), false)
                ?: $this->program->name;
        }

        return [
            'id' => $this->id,
            'student_id_number' => $this->student_id_number,
            'student_name' => $this->user?->name,
            'email' => $this->user?->email,
            'program' => $programName,
            'current_level' => (int) $this->current_level,
            'cumulative_gpa' => (float) $this->cumulative_gpa,
            'status' => $this->status,
            'course_results' => CourseResultResource::collection($this->whenLoaded('courseResults')),
        ];
    }
}
