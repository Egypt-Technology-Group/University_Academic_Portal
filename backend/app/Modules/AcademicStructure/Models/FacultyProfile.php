<?php

namespace App\Modules\AcademicStructure\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class FacultyProfile extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'academic_title',
        'bio',
        'research_interests',
        'office_location',
    ];

    protected $fillable = [
        'user_id',
        'department_id',
        'academic_title',
        'bio',
        'research_interests',
        'email',
        'phone',
        'office_location',
        'avatar',
        'cv_path',
        'google_scholar_url',
        'orcid_id',
        'office_hours',
        'publications',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'office_hours' => 'array',
            'publications' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
