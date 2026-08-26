<?php

namespace App\Core\Security;

use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EntitlementManager
{
    const CACHE_KEY = 'egyitech_vendor_active_entitlement_v1';
    const SETTING_KEY = 'vendor_entitlement_package';

    protected VendorKeyProvider $keyProvider;
    protected ?array $cachedVerification = null;

    public function __construct(?VendorKeyProvider $keyProvider = null)
    {
        $this->keyProvider = $keyProvider ?? new VendorKeyProvider();
    }

    /**
     * Verify an entitlement package without applying it.
     */
    public function verifyPackage(array $package): array
    {
        if (!$this->keyProvider->verifySignature($package)) {
            return [
                'valid' => false,
                'error_code' => 'SIGNATURE_VERIFICATION_FAILED',
                'message' => 'The cryptographic vendor signature on this entitlement package is invalid or corrupted.',
            ];
        }

        $payload = $package['payload'];

        // Validate expiration
        if (!empty($payload['valid_until'])) {
            $validUntil = Carbon::parse($payload['valid_until']);
            if ($validUntil->isPast()) {
                return [
                    'valid' => false,
                    'error_code' => 'ENTITLEMENT_EXPIRED',
                    'message' => 'The subscription entitlement expired on ' . $validUntil->toDateTimeString(),
                    'expired_at' => $validUntil->toISOString(),
                ];
            }
        }

        // Validate structure
        if (!isset($payload['licensed_modules']) || !is_array($payload['licensed_modules'])) {
            return [
                'valid' => false,
                'error_code' => 'MALFORMED_PAYLOAD',
                'message' => 'The entitlement payload lacks a valid licensed_modules array.',
            ];
        }

        return [
            'valid' => true,
            'data' => $payload,
            'signature_algorithm' => $package['algorithm'] ?? 'HMAC-SHA256',
            'signed_at' => $package['signed_at'] ?? null,
        ];
    }

    /**
     * Apply a vendor-signed entitlement package to the system.
     */
    public function applySignedPackage(array $package): bool
    {
        $verification = $this->verifyPackage($package);
        if (!$verification['valid']) {
            Log::warning('[EntitlementManager] Attempted to apply invalid entitlement package: ' . ($verification['message'] ?? ''));
            return false;
        }

        // Store package in persistent database site_settings
        SiteSetting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            [
                'value' => json_encode($package, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'group' => 'system',
                'type' => 'json',
                'is_public' => false,
            ]
        );

        // Update high-speed cache
        Cache::forever(self::CACHE_KEY, $verification['data']);
        $this->cachedVerification = $verification['data'];

        return true;
    }

    /**
     * Check whether a specific module is entitled under the active verified license.
     */
    public function isModuleEntitled(string $moduleKey): bool
    {
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                $setting = SiteSetting::where('key', self::SETTING_KEY)->first();
                if ($setting && !empty($setting->value)) {
                    // A license package is configured; it MUST pass cryptographic verification
                    $active = $this->getActiveEntitlement();
                    if (!$active) {
                        return false; // Tampered or expired package strictly denies entitlement
                    }
                    $licensed = $active['licensed_modules'] ?? [];
                    $normalizedKey = str_replace('_', '-', strtolower($moduleKey));
                    $normalizedLicensed = array_map(fn($m) => str_replace('_', '-', strtolower($m)), $licensed);
                    return in_array($normalizedKey, $normalizedLicensed, true);
                }
            }
        } catch (\Throwable $e) {
            // DB not ready or not migrated
        }

        // If no license package is stored at all, fall back to evaluation mode policy
        $active = $this->getActiveEntitlement();
        if (!$active) {
            return $this->isEvaluationModeEnabled();
        }

        $licensed = $active['licensed_modules'] ?? [];
        $normalizedKey = str_replace('_', '-', strtolower($moduleKey));
        $normalizedLicensed = array_map(fn($m) => str_replace('_', '-', strtolower($m)), $licensed);
        return in_array($normalizedKey, $normalizedLicensed, true);
    }

    /**
     * Get the active verified entitlement metadata.
     */
    public function getActiveEntitlement(): ?array
    {
        if ($this->cachedVerification !== null) {
            return $this->cachedVerification;
        }

        // 1. Read from cache
        try {
            $cached = Cache::get(self::CACHE_KEY);
            if ($cached && is_array($cached)) {
                // Verify timestamp hasn't expired since cached
                if (!empty($cached['valid_until']) && Carbon::parse($cached['valid_until'])->isPast()) {
                    $this->resetCache();
                    return null;
                }
                $this->cachedVerification = $cached;
                return $cached;
            }
        } catch (\Throwable $e) {
        }

        // 2. Read from database setting and verify cryptographic signature
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                $setting = SiteSetting::where('key', self::SETTING_KEY)->first();
                if ($setting && !empty($setting->value)) {
                    $package = is_array($setting->value) ? $setting->value : json_decode($setting->value, true);
                    if (is_array($package)) {
                        $verification = $this->verifyPackage($package);
                        if ($verification['valid']) {
                            Cache::forever(self::CACHE_KEY, $verification['data']);
                            $this->cachedVerification = $verification['data'];
                            return $verification['data'];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Check if evaluation mode allows all registered modules when no explicit license is installed.
     */
    protected function isEvaluationModeEnabled(): bool
    {
        return (bool) config('modules.allow_evaluation_mode', true);
    }

    /**
     * Clear all cached verification state.
     */
    public function resetCache(): void
    {
        $this->cachedVerification = null;
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
        }
    }

    /**
     * Reset both database and cache (used in test isolation).
     */
    public function reset(): void
    {
        $this->resetCache();
        try {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                SiteSetting::where('key', self::SETTING_KEY)->delete();
            }
        } catch (\Throwable $e) {
        }
    }
}
