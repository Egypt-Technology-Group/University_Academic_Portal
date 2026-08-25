<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id_number',
        'user_id',
        'program_id',
        'current_level',
        'cumulative_gpa',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'current_level' => 'integer',
            'cumulative_gpa' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function courseResults(): HasMany
    {
        return $this->hasMany(CourseResult::class);
    }
}
