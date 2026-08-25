<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    use HandlesTranslations;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allLocales = $request->boolean('all_locales')
            || $request->boolean('all_translations')
            || $request->hasHeader('X-All-Locales')
            || $request->query('locale') === 'all';

        $departmentName = null;
        $collegeName = null;

        if ($this->department) {
            $departmentName = $allLocales
                ? $this->department->getTranslations('name')
                : ($this->department->getTranslation('name', app()->getLocale(), false) ?: $this->department->name);

            if ($this->department->college) {
                $collegeName = $allLocales
                    ? $this->department->college->getTranslations('name')
                    : ($this->department->college->getTranslation('name', app()->getLocale(), false) ?: $this->department->college->name);
            }
        }

        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'department_name' => $departmentName,
            'college_name' => $collegeName,
            'name' => $this->translate('name'),
            'slug' => $this->slug,
            'degree_level' => $this->degree_level,
            'duration_years' => (int) $this->duration_years,
            'credit_hours' => (int) $this->credit_hours,
            'curriculum' => $this->translate('curriculum'),
            'career_opportunities' => $this->translate('career_opportunities'),
            'tuition_fees' => $this->translate('tuition_fees'),
            'admission_requirements' => $this->translate('admission_requirements'),
            'is_active' => (bool) $this->is_active,
        ];
    }
}
