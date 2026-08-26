<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueOfficialStatementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id_number' => 'nullable|string',
            'student_name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:30',
            'program_id' => 'nullable|exists:programs,id',
            'statement_type' => 'nullable|string',
            'workflow_mode' => 'nullable|string|in:structured,file_only,both',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'recipient_entity_ar' => 'nullable|string',
            'recipient_entity_en' => 'nullable|string',
            'signatory_name' => 'nullable|string',
            'signatory_title' => 'nullable|string',
            'valid_months' => 'nullable|integer|min:1|max:24',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:51200',
            'document_path' => 'nullable|string',
            'file_name' => 'nullable|string',
            'file_size' => 'nullable|string',
        ];
    }
}
