<?php

declare(strict_types=1);

namespace App\Modules\Admissions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['draft', 'submitted', 'under_review', 'accepted', 'rejected'])],
            'stage' => ['nullable', 'string', Rule::in(['initial_screening', 'placement_test', 'interview', 'final_decision', 'completed'])],
            'interview_scheduled_at' => 'nullable|date',
            'placement_test_at' => 'nullable|date',
            'decision_reason' => 'nullable|string|max:500',
            'scholarship_name' => 'nullable|string|max:100',
            'scholarship_discount_percent' => 'nullable|integer|min:0|max:100',
            'waitlist_position' => 'nullable|integer|min:1',
            'enrollment_status' => ['nullable', Rule::in(['pending', 'documents_verified', 'tuition_paid', 'enrolled', 'withdrawn'])],
            'verification_checklist' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
