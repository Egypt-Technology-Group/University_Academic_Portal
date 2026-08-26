<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollegeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'dean_name_ar' => 'nullable|string|max:255',
            'dean_name_en' => 'nullable|string|max:255',
            'about_ar' => 'nullable|string',
            'about_en' => 'nullable|string',
            'vision_ar' => 'nullable|string',
            'vision_en' => 'nullable|string',
            'mission_ar' => 'nullable|string',
            'mission_en' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'banner_file' => 'nullable|image|max:10240',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ];
    }
}
