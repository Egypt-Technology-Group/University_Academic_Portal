<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EntitlementManager
{
    public const CACHE_KEY = 'egyitech_vendor_active_entitlement_v1';
    public const SETTING_KEY = 'vendor_entitlement_package';

    protected VendorKeyProvider $keyProvider;
    protected ?array $cachedVerification = null;
    /** @var array<string, bool>|null */
    protected ?array $cachedLicensedModuleIds = null;

    public function __construct(?VendorKeyProvider $keyProvider = null)
    {
        $this->keyProvider = $keyProvider ?? new VendorKeyProvider();
    }

    /**
     * Verify a vendor-signed entitlement package without applying it.
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

        $payload = $package['payload'] ?? null;
        if (!is_array($payload)) {
            return [
                'valid' => false,
                'error_code' => 'MALFORMED_PAYLOAD',
                'message' => 'The entitlement package is missing a valid payload dictionary.',
            ];
        }

        // Validate expiration timestamp
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

        // Validate structure of licensed_modules
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
        if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
            SiteSetting::updateOrCreate(
                ['key' => self::SETTING_KEY],
                [
                    'value' => json_encode($package, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'group' => 'system',
                    'type' => 'json',
                    'is_public' => false,
                ]
            );
        }

        // Update high-speed cache
        try {
            Cache::forever(self::CACHE_KEY, $verification['data']);
            Cache::forget('app_modules_public_manifest_ar');
            Cache::forget('app_modules_public_manifest_en');
        } catch (\Throwable $e) {
        }

        $this->cachedLicensedModuleIds = null;
        $this->cachedVerification = $verification['data'];

        return true;
    }

    /**
     * Check whether a specific module is entitled under the active verified license.
     *
     * STRICT DEFENSE-IN-DEPTH:
     * Returns false unconditionally if:
     * 1. No vendor license certificate is installed in the database/cache.
     * 2. The certificate's signature is invalid or tampered with.
     * 3. The certificate has expired.
     * 4. The requested module is not explicitly included in the verified payload's licensed_modules.
     */
    public function isModuleEntitled(string $moduleKey): bool
    {
        $normalizedKey = $this->normalizeModuleKey($moduleKey);
        return isset($this->getLicensedModuleIds()[$normalizedKey]);
    }

    /**
     * Return the verified license's normalized module lookup once per request.
     *
     * @return array<string, bool>
     */
    public function getLicensedModuleIds(): array
    {
        if ($this->cachedLicensedModuleIds !== null) {
            return $this->cachedLicensedModuleIds;
        }

        $active = $this->getActiveEntitlement();
        $licensed = $active['licensed_modules'] ?? [];
        if (!is_array($licensed)) {
            return $this->cachedLicensedModuleIds = [];
        }

        $this->cachedLicensedModuleIds = [];
        foreach ($licensed as $moduleKey) {
            if (is_string($moduleKey) && trim($moduleKey) !== '') {
                $this->cachedLicensedModuleIds[$this->normalizeModuleKey($moduleKey)] = true;
            }
        }

        return $this->cachedLicensedModuleIds;
    }

    /**
     * Return the normalized licensed module IDs as a list.
     *
     * @return string[]
     */
    public function getLicensedModuleIdList(): array
    {
        return array_keys($this->getLicensedModuleIds());
    }

    protected function normalizeModuleKey(string $moduleKey): string
    {
        return str_replace('_', '-', strtolower(trim($moduleKey)));
    }

    /**
     * Get the active verified entitlement metadata.
     *
     * Returns NULL if no valid, cryptographically verified license exists.
     */
    public function getActiveEntitlement(): ?array
    {
        if ($this->cachedVerification !== null) {
            // Verify in-memory cached verification hasn't expired
            if (!empty($this->cachedVerification['valid_until']) && Carbon::parse($this->cachedVerification['valid_until'])->isPast()) {
                $this->resetCache();
                return null;
            }
            return $this->cachedVerification;
        }

        // 1. Read from fast cache
        try {
            $cached = Cache::get(self::CACHE_KEY);
            if (is_array($cached)) {
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
                            try {
                                Cache::forever(self::CACHE_KEY, $verification['data']);
                            } catch (\Throwable $e) {
                            }
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
     * Clear all cached verification state.
     */
    public function resetCache(): void
    {
        $this->cachedVerification = null;
        $this->cachedLicensedModuleIds = null;
        try {
            Cache::forget(self::CACHE_KEY);
            Cache::forget('app_modules_public_manifest_ar');
            Cache::forget('app_modules_public_manifest_en');
        } catch (\Throwable $e) {
        }
    }

    /**
     * Reset both database setting and cache (used in tests or explicit vendor license revocation).
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
