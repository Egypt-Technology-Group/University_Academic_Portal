<?php
declare(strict_types=1);

namespace App\Modules\Results\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InquireResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id_number' => 'required|string',
            'national_id' => 'nullable|string',
            'national_id_number' => 'nullable|string',
            'term_id' => 'nullable|integer',
            'academic_term_id' => 'nullable|integer',
        ];
    }
}
