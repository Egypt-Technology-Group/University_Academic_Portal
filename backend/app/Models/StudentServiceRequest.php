<?php

namespace App\Models;

use App\Modules\AcademicStructure\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class StudentServiceRequest extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'purpose',
    ];

    protected $fillable = [
        'request_number',
        'student_id_number',
        'student_name',
        'program_id',
        'service_type',
        'purpose',
        'status',
        'admin_notes',
        'handled_by',
        'fee_amount',
        'is_fee_paid',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_fee_paid' => 'boolean',
            'fee_amount' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
