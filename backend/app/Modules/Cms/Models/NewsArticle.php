<?php
declare(strict_types=1);

namespace App\Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class NewsArticle extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'excerpt',
        'body',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'is_featured',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
}
