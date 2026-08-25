<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'group',
        'value',
        'is_public',
    ];

    protected $casts = [
        'value' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Helper to get setting value by key with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set / update setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general', bool $isPublic = true): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'value' => $value,
                'is_public' => $isPublic,
            ]
        );
    }
}
