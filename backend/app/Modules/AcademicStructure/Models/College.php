<?php

namespace App\Modules\AcademicStructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class College extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name',
        'dean_name',
        'about',
        'vision',
        'mission',
    ];

    protected $fillable = [
        'name',
        'slug',
        'dean_name',
        'about',
        'vision',
        'mission',
        'banner_image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class)->orderBy('sort_order');
    }

    public function programs(): HasManyThrough
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }

    public function facultyProfiles(): HasManyThrough
    {
        return $this->hasManyThrough(FacultyProfile::class, Department::class);
    }
}
