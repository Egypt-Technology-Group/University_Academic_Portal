<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verification_status' => ['required', Rule::in(['pending', 'verified', 'rejected', 'action_required'])],
            'is_original_verified' => 'nullable|boolean',
            'rejection_reason' => 'nullable|string|max:300',
            'reviewer_notes' => 'nullable|string|max:500',
        ];
    }
}
