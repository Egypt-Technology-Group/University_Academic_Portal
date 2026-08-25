<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'admission_cycle_id' => 'nullable|exists:admission_cycles,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'national_id' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'high_school_score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable',
            'documents.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
