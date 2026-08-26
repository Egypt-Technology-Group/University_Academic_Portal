<?php
declare(strict_types=1);

namespace App\Modules\Cms\Resources;

use App\Http\Resources\Concerns\HandlesTranslations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategoryResource extends JsonResource
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
            'name' => $this->translate('name'),
            'slug' => $this->slug,
        ];
    }
}
