<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'department_id' => 'sometimes|nullable|integer',
            'name_ar' => 'sometimes|nullable|string|max:255',
            'name_en' => 'sometimes|nullable|string|max:255',
            'academic_title_ar' => 'sometimes|nullable|string|max:255',
            'academic_title_en' => 'sometimes|nullable|string|max:255',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'research_interests_ar' => 'nullable|string',
            'research_interests_en' => 'nullable|string',
            'email' => "sometimes|email|unique:faculty_profiles,email,{$id}",
            'phone' => 'nullable|string|max:30',
            'office_location_ar' => 'nullable|string|max:255',
            'office_location_en' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:51200',
            'cv_path' => 'nullable|string',
            'google_scholar_url' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:50',
            'office_hours' => 'nullable|array',
            'publications' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
        ];
    }
}
