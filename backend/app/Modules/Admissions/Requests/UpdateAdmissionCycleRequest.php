<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdmissionCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required',
            'title.en' => 'required_with:title.ar|string|max:255',
            'title.ar' => 'required_with:title.en|string|max:255',
            'academic_year' => 'sometimes|required|string|max:50',
            'term' => 'sometimes|required|string|max:50',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'is_open' => 'nullable|boolean',
        ];
    }
}
