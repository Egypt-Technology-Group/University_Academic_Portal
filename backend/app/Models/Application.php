<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'admission_cycle_id',
        'program_id',
        'first_name',
        'last_name',
        'national_id',
        'email',
        'phone',
        'high_school_score',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'high_school_score' => 'decimal:2',
        ];
    }

    public function admissionCycle(): BelongsTo
    {
        return $this->belongsTo(AdmissionCycle::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
}
