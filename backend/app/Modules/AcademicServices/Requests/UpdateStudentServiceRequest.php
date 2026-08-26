<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,approved,ready_for_pickup,rejected',
            'admin_notes' => 'nullable|string',
            'handled_by' => 'nullable|string',
        ];
    }
}
