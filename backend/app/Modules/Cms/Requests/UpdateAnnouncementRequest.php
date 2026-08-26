<?php
declare(strict_types=1);

namespace App\Modules\Cms\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('is_urgent') && !$this->has('priority')) {
            $this->merge(['priority' => $this->boolean('is_urgent') ? 'urgent' : 'normal']);
        }
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'content_ar' => 'sometimes|required|string',
            'content_en' => 'sometimes|required|string',
            'target_audience' => 'sometimes|required|in:all,students,faculty,applicants,public,staff',
            'priority' => 'sometimes|required|in:normal,urgent,pinned',
            'is_active' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ];
    }
}
