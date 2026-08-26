<?php
declare(strict_types=1);

namespace App\Modules\Results\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimulateRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id_number' => 'required|string',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*.code' => 'required|string',
            'selected_courses.*.credits' => 'required|integer|min:1|max:6',
        ];
    }
}
