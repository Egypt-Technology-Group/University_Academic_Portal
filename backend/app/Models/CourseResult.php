<?php

namespace App\Models;

use App\Modules\AcademicServices\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class CourseResult extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'course_name',
    ];

    protected $fillable = [
        'student_record_id',
        'academic_term_id',
        'course_code',
        'course_name',
        'credit_hours',
        'grade',
        'grade_points',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'credit_hours' => 'integer',
            'grade_points' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
