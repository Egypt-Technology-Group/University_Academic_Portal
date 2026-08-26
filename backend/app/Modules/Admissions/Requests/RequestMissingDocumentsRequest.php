<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestMissingDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'missing_documents' => 'required|array|min:1',
            'missing_documents.*' => 'required|string|max:100',
            'instructions' => 'nullable|string|max:1000',
        ];
    }
}
