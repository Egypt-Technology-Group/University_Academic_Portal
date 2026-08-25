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
        'stage',
        'interview_scheduled_at',
        'placement_test_at',
        'decision_reason',
        'scholarship_name',
        'scholarship_discount_percent',
        'waitlist_position',
        'enrollment_status',
        'verification_checklist',
        'communication_logs',
        'timeline',
        'reviewed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'high_school_score' => 'decimal:2',
            'scholarship_discount_percent' => 'integer',
            'waitlist_position' => 'integer',
            'interview_scheduled_at' => 'datetime',
            'placement_test_at' => 'datetime',
            'verification_checklist' => 'array',
            'communication_logs' => 'array',
            'timeline' => 'array',
        ];
    }

    public function logCommunication(string $channel, string $subject, string $message, ?string $recipient = null): void
    {
        $logs = $this->communication_logs ?? [];
        $logs[] = [
            'channel' => $channel, // email, sms, portal_notification
            'subject' => $subject,
            'message' => $message,
            'recipient' => $recipient ?? $this->email,
            'sent_at' => now()->toIso8601String(),
        ];
        $this->communication_logs = $logs;
        $this->save();
    }

    public function recordTimelineEvent(string $title, string $action, ?string $actor = 'Committee', ?string $details = null): void
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = [
            'title' => $title,
            'action' => $action,
            'actor' => $actor,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->timeline = $timeline;
        $this->save();
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
