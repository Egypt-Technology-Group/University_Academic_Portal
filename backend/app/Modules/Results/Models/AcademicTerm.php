<?php
declare(strict_types=1);

namespace App\Modules\Results\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AcademicTerm extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'academic_year',
        'semester',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
        ];
    }

    public function courseResults(): HasMany
    {
        return $this->hasMany(CourseResult::class);
    }
}

