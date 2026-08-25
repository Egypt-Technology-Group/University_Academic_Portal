<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    use HandlesTranslations;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translate('title'),
            'slug' => $this->slug,
            'location' => $this->translate('location'),
            'organizer' => $this->translate('organizer'),
            'description' => $this->translate('description'),
            'cover_image' => $this->cover_image,
            'start_time' => $this->start_time?->toISOString() ?? $this->start_time,
            'end_time' => $this->end_time?->toISOString() ?? $this->end_time,
        ];
    }
}
