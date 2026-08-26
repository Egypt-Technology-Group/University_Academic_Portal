<?php
declare(strict_types=1);

namespace App\Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class NewsCategory extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'category_id');
    }
}
