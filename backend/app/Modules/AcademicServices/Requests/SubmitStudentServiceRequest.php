<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitStudentServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id_number' => 'required|string',
            'student_name' => 'required|string|max:255',
            'program_id' => 'nullable|exists:programs,id',
            'service_type' => 'required|string',
            'purpose_ar' => 'nullable|string',
            'purpose_en' => 'nullable|string',
            'fee_amount' => 'nullable|numeric',
        ];
    }
}
