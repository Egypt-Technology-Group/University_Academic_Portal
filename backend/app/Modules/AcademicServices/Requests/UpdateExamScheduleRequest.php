<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => 'nullable|integer',
            'academic_term_id' => 'nullable|integer',
            'course_code' => 'sometimes|nullable|string|max:30',
            'course_name_ar' => 'sometimes|nullable|string|max:255',
            'course_name_en' => 'sometimes|nullable|string|max:255',
            'exam_type' => 'sometimes|nullable|in:midterm,final,practical,oral',
            'workflow_mode' => 'nullable|in:structured,file_only,both',
            'exam_date' => 'sometimes|nullable|date',
            'start_time' => 'sometimes|nullable',
            'end_time' => 'sometimes|nullable',
            'hall_location_ar' => 'sometimes|nullable|string|max:255',
            'hall_location_en' => 'sometimes|nullable|string|max:255',
            'chief_invigilator_ar' => 'nullable|string',
            'chief_invigilator_en' => 'nullable|string',
            'proctors_list' => 'nullable|array',
            'seating_capacity' => 'nullable|integer',
            'timetable_document' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx,jpg,jpeg,png|max:51200',
            'timetable_document_path' => 'nullable|string',
            'timetable_file_name' => 'nullable|string',
            'timetable_file_size' => 'nullable|string',
        ];
    }
}
