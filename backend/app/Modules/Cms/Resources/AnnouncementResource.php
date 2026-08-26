<?php
declare(strict_types=1);

namespace App\Modules\Cms\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'content' => $this->translate('content'),
            'target_audience' => $this->target_audience,
            'priority' => $this->priority,
            'is_active' => (bool) $this->is_active,
            'expires_at' => $this->expires_at?->toISOString() ?? $this->expires_at,
            'created_at' => $this->created_at?->toISOString() ?? $this->created_at,
        ];
    }
}
