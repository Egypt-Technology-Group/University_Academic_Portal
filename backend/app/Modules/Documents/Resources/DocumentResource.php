<?php
declare(strict_types=1);

namespace App\Modules\Documents\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'category' => $this->category,
            'title' => $this->translate('title'),
            'description' => $this->translate('description'),
            'file_path' => $this->file_path,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'version' => $this->version ?? '1.0',
            'status' => $this->status ?? 'published',
            'target_audience' => $this->target_audience ?? 'all',
            'is_featured' => (bool) $this->is_featured,
            'is_archived' => (bool) $this->is_archived,
            'sort_order' => (int) ($this->sort_order ?? 0),
            'download_count' => (int) $this->download_count,
            'effective_date' => $this->effective_date?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
