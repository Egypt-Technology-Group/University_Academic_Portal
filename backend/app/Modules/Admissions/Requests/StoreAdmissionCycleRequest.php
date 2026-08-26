<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'title.en' => 'required_with:title.ar|string|max:255',
            'title.ar' => 'required_with:title.en|string|max:255',
            'academic_year' => 'required|string|max:50',
            'term' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_open' => 'nullable|boolean',
        ];
    }
}
