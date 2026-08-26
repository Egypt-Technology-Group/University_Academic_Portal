<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'college_id' => 'required|exists:colleges,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'head_name_ar' => 'nullable|string|max:255',
            'head_name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ];
    }
}
