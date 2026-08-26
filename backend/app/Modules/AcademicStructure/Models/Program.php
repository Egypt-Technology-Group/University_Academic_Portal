<?php

namespace App\Modules\AcademicStructure\Models;

use App\Models\Application;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Program extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name',
        'curriculum',
        'career_opportunities',
        'tuition_fees',
        'admission_requirements',
    ];

    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'degree_level',
        'duration_years',
        'credit_hours',
        'curriculum',
        'career_opportunities',
        'tuition_fees',
        'admission_requirements',
        'study_plan_document_path',
        'study_plan_file_name',
        'study_plan_file_size',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_years' => 'integer',
            'credit_hours' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function studentRecords(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
