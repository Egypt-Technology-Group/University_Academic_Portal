<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_name',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, Model|string $auditable, ?array $oldValues = null, ?array $newValues = null): self
    {
        $user = auth('sanctum')->user() ?? auth()->user();
        $request = request();

        return self::create([
            'user_id' => $user?->id,
            'actor_name' => $user?->name ?? 'System / Admin',
            'action' => $action,
            'auditable_type' => is_object($auditable) ? get_class($auditable) : (string) $auditable,
            'auditable_id' => is_object($auditable) ? $auditable->getKey() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }
}
