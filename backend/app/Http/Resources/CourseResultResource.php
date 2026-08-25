<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResultResource extends JsonResource
{
    use HandlesTranslations;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $termName = null;
        if ($this->academicTerm) {
            $termName = $this->academicTerm->getTranslation('name', app()->getLocale(), false)
                ?: $this->academicTerm->name;
        }

        return [
            'id' => $this->id,
            'student_record_id' => $this->student_record_id,
            'academic_term_id' => $this->academic_term_id,
            'academic_term' => $termName,
            'course_code' => $this->course_code,
            'course_name' => $this->translate('course_name'),
            'credit_hours' => (int) $this->credit_hours,
            'grade' => $this->grade,
            'grade_points' => (float) $this->grade_points,
            'is_published' => (bool) $this->is_published,
        ];
    }
}
