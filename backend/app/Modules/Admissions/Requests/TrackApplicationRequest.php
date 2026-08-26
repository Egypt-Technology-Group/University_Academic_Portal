<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'application_number' => 'required|string|max:50',
            'national_id' => 'required_without:email|nullable|string|max:50',
            'email' => 'required_without:national_id|nullable|email|max:150',
        ];
    }
}
