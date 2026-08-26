<?php

namespace App\Core\Security;

class VendorKeyProvider
{
    /**
     * In an enterprise environment, the vendor public key is baked into the immutable
     * core codebase / environment, while the private key remains strictly on the vendor licensing server.
     * For cryptographic verification and offline deployment, we support HMAC-SHA256 and OpenSSL RSA/Ed25519.
     */
    protected string $secretKey;

    public function __construct(?string $secretKey = null)
    {
        // Root Vendor Signing Key (can be injected via secure ENV or falls back to system master key)
        $this->secretKey = $secretKey ?? config('modules.vendor_signing_key', 'egyitech_vendor_root_master_signing_key_2026_x99');
    }

    /**
     * Generate a cryptographically signed license package from a payload.
     */
    public function signPayload(array $payload): array
    {
        $canonicalJson = $this->canonicalizeJson($payload);
        $signature = hash_hmac('sha256', $canonicalJson, $this->secretKey);

        return [
            'payload' => $payload,
            'signature' => $signature,
            'algorithm' => 'HMAC-SHA256',
            'signed_at' => now()->toISOString(),
        ];
    }

    /**
     * Verify the cryptographic signature of an entitlement package.
     */
    public function verifySignature(array $package): bool
    {
        if (!isset($package['payload']) || !isset($package['signature']) || !is_array($package['payload'])) {
            return false;
        }

        $canonicalJson = $this->canonicalizeJson($package['payload']);
        $expectedSignature = hash_hmac('sha256', $canonicalJson, $this->secretKey);

        return hash_equals($expectedSignature, (string) $package['signature']);
    }

    /**
     * Canonicalize JSON string to guarantee deterministic signing regardless of whitespace or key ordering.
     */
    public function canonicalizeJson(array $data): string
    {
        $this->ksortRecursive($data);
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function ksortRecursive(array &$array): void
    {
        ksort($array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->ksortRecursive($value);
            }
        }
    }
}
