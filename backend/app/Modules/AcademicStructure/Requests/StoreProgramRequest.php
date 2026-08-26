<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'degree_level' => 'required|in:bachelor,master,doctorate,diploma',
            'duration_years' => 'required|integer|min:1|max:8',
            'credit_hours' => 'required|integer|min:10|max:300',
            'curriculum_ar' => 'nullable',
            'curriculum_en' => 'nullable',
            'career_opportunities_ar' => 'nullable',
            'career_opportunities_en' => 'nullable',
            'tuition_fees_ar' => 'nullable',
            'tuition_fees_en' => 'nullable',
            'admission_requirements_ar' => 'nullable',
            'admission_requirements_en' => 'nullable',
            'study_plan_document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:51200',
            'study_plan_document_path' => 'nullable|string',
            'study_plan_file_name' => 'nullable|string',
            'study_plan_file_size' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
