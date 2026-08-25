<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'excerpt' => $this->translate('excerpt'),
            'body' => $this->translate('body'),
            'featured_image' => $this->featured_image,
            'is_featured' => (bool) $this->is_featured,
            'published_at' => $this->published_at?->toISOString() ?? $this->published_at,
            'views_count' => (int) $this->views_count,
            'category' => new NewsCategoryResource($this->whenLoaded('category')),
        ];
    }
}
