<?php
declare(strict_types=1);

namespace App\Modules\Cms\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'target_audience' => 'required|in:all,students,faculty,applicants,public,staff',
            'priority' => 'required|in:normal,urgent,pinned',
            'is_active' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ];
    }
}
