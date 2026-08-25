<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AdmissionCycle extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
    ];

    protected $fillable = [
        'title',
        'academic_year',
        'term',
        'start_date',
        'end_date',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_open' => 'boolean',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
