<?php

namespace App\Modules\AcademicServices\Models;

use App\Modules\AcademicStructure\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class OfficialStatement extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'recipient_entity',
    ];

    protected $fillable = [
        'certificate_code',
        'student_id_number',
        'student_name',
        'national_id',
        'program_id',
        'statement_type',
        'workflow_mode',
        'title',
        'recipient_entity',
        'verification_hash',
        'qr_payload',
        'document_path',
        'file_name',
        'file_size',
        'signatory_name',
        'signatory_title',
        'issue_date',
        'valid_until',
        'is_revoked',
    ];

    protected function casts(): array
    {
        return [
            'is_revoked' => 'boolean',
            'issue_date' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
