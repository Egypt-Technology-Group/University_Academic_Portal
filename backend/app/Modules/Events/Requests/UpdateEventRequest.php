<?php
declare(strict_types=1);

namespace App\Modules\Events\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('venue_ar') && !$this->filled('location_ar')) {
            $this->merge(['location_ar' => $this->input('venue_ar')]);
        }
        if ($this->filled('venue_en') && !$this->filled('location_en')) {
            $this->merge(['location_en' => $this->input('venue_en')]);
        }
        if ($this->filled('banner_image') && !$this->filled('cover_image')) {
            $this->merge(['cover_image' => $this->input('banner_image')]);
        }

        if ($this->filled('event_date') && $this->filled('start_time') && strlen((string)$this->input('start_time')) <= 8) {
            $date = $this->input('event_date');
            $time = $this->input('start_time');
            try {
                $this->merge(['start_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw start_time
            }
        }
        if ($this->filled('event_date') && $this->filled('end_time') && strlen((string)$this->input('end_time')) <= 8) {
            $date = $this->input('event_date');
            $time = $this->input('end_time');
            try {
                $this->merge(['end_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw end_time
            }
        }
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'location_ar' => 'sometimes|required|string',
            'location_en' => 'sometimes|required|string',
            'organizer_ar' => 'sometimes|required|string',
            'organizer_en' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string',
            'description_en' => 'sometimes|required|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'nullable|date',
            'cover_image' => 'nullable|string',
            'cover_image_file' => 'nullable|image|max:10240',
            'image' => 'nullable|image|max:10240',
        ];
    }
}
