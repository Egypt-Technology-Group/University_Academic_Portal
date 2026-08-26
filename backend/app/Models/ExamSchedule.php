<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ExamSchedule extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'course_name',
        'hall_location',
        'chief_invigilator',
    ];

    protected $fillable = [
        'program_id',
        'academic_term_id',
        'course_code',
        'course_name',
        'exam_type',
        'workflow_mode',
        'exam_date',
        'start_time',
        'end_time',
        'hall_location',
        'chief_invigilator',
        'proctors_list',
        'seating_capacity',
        'timetable_document_path',
        'timetable_file_name',
        'timetable_file_size',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'proctors_list' => 'array',
            'seating_capacity' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
