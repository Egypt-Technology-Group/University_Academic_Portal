<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => 'nullable|exists:programs,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'course_code' => 'nullable|string|max:30',
            'course_name_ar' => 'nullable|string|max:255',
            'course_name_en' => 'nullable|string|max:255',
            'exam_type' => 'nullable|in:midterm,final,practical,oral',
            'workflow_mode' => 'nullable|in:structured,file_only,both',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'hall_location_ar' => 'nullable|string|max:255',
            'hall_location_en' => 'nullable|string|max:255',
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
