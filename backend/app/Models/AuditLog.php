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
        'actor_email',
        'actor_role',
        'action',
        'module',
        'auditable_type',
        'auditable_id',
        'description_ar',
        'description_en',
        'old_values',
        'new_values',
        'severity',
        'status',
        'request_method',
        'request_url',
        'context',
        'ip_address',
        'user_agent',
        'integrity_hash',
        'previous_hash',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'context' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Compute tamper-evident HMAC SHA-256 integrity hash linking to the previous record.
     */
    public static function computeIntegrityHash(array $attributes, ?string $previousHash = null): string
    {
        $appKey = config('app.key') ?: 'EgyiTechSecureSecretAuditKey2026';
        $payload = implode('|', [
            $previousHash ?? 'GENESIS_HASH_ROOT_0000000000000000',
            $attributes['user_id'] ?? 'null',
            $attributes['action'] ?? '',
            $attributes['module'] ?? '',
            $attributes['auditable_type'] ?? '',
            $attributes['auditable_id'] ?? '',
            json_encode($attributes['old_values'] ?? null),
            json_encode($attributes['new_values'] ?? null),
            $attributes['ip_address'] ?? '',
            $attributes['created_at'] ?? now()->toIso8601String(),
        ]);

        return hash_hmac('sha256', $payload, $appKey);
    }

    /**
     * Main API to record comprehensive, tamper-evident audit logs.
     */
    public static function record(
        string $action,
        Model|string $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $module = 'general',
        ?string $descriptionAr = null,
        ?string $descriptionEn = null,
        string $severity = 'info',
        string $status = 'success',
        ?array $context = null
    ): self {
        $user = auth('sanctum')->user() ?? auth()->user();
        $request = request();

        $lastRecord = self::latest('id')->first();
        $prevHash = $lastRecord?->integrity_hash ?? 'GENESIS_HASH_ROOT_0000000000000000';

        $roleName = 'guest';
        if ($user) {
            $roleName = method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty()
                ? $user->getRoleNames()->first()
                : ($user->role ?? 'admin');
        }

        $auditableType = is_object($auditable) ? get_class($auditable) : (string) $auditable;
        $auditableId = is_object($auditable) ? $auditable->getKey() : null;

        $attrs = [
            'user_id' => $user?->id,
            'actor_name' => $user?->name ?? 'System Process',
            'actor_email' => $user?->email,
            'actor_role' => $roleName,
            'action' => $action,
            'module' => $module,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'description_ar' => $descriptionAr ?: self::generateDefaultDescription($action, $auditableType, 'ar'),
            'description_en' => $descriptionEn ?: self::generateDefaultDescription($action, $auditableType, 'en'),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'severity' => $severity,
            'status' => $status,
            'request_method' => $request ? $request->method() : 'CLI',
            'request_url' => $request ? $request->fullUrl() : 'console',
            'context' => $context ?: ($request ? ['route' => $request->route()?->getName(), 'query' => $request->query()] : null),
            'ip_address' => $request ? $request->ip() : '127.0.0.1',
            'user_agent' => $request ? substr($request->userAgent() ?? 'System', 0, 250) : 'System Worker',
            'previous_hash' => $prevHash,
        ];

        $attrs['integrity_hash'] = self::computeIntegrityHash($attrs, $prevHash);

        return self::create($attrs);
    }

    /**
     * Helper to generate humanized fallback descriptions.
     */
    protected static function generateDefaultDescription(string $action, string $type, string $lang): string
    {
        $shortType = class_basename($type);
        if ($lang === 'ar') {
            return match ($action) {
                'create' => "تم إنشاء سجل جديد في وحدة {$shortType}",
                'update' => "تم تحديث وتعديل بيانات في وحدة {$shortType}",
                'delete' => "تم حذف وإزالة سجل من وحدة {$shortType}",
                'login' => "تم تسجيل الدخول إلى النظام",
                'logout' => "تم تسجيل الخروج من النظام",
                'verify' => "تم التحقق وتوثيق الوثيقة أو السجل في {$shortType}",
                'status_change' => "تم تغيير وتحديث حالة الطلب أو السجل في {$shortType}",
                'file_upload' => "تم رفع وتخزين ملف رقمي جديد في {$shortType}",
                default => "تم تنفيذ إجراء {$action} على {$shortType}",
            };
        }

        return match ($action) {
            'create' => "Created a new record in {$shortType}",
            'update' => "Updated attributes in {$shortType}",
            'delete' => "Deleted record from {$shortType}",
            'login' => "User authenticated successfully",
            'logout' => "User terminated active session",
            'verify' => "Verified official record/document in {$shortType}",
            'status_change' => "Updated lifecycle status in {$shortType}",
            'file_upload' => "Uploaded a digital asset in {$shortType}",
            default => "Executed action {$action} on {$shortType}",
        };
    }
}
