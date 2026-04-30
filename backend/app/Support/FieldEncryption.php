<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Centralized service for encrypting/decrypting sensitive fields.
 *
 * Fields like national_id, medical_notes, and social_notes contain
 * personally identifiable information (PII) that must be encrypted
 * at rest to comply with data protection requirements.
 */
final class FieldEncryption
{
    /**
     * Encrypt a value.
     */
    public static function encrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString($value);
    }

    /**
     * Decrypt a value, returning null if decryption fails.
     */
    public static function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            \Illuminate\Support\Facades\Log::critical('Decryption failed for field. Check app key or data integrity.', ['value_start' => substr($value, 0, 10)]);
            return null;
        }
    }

    /**
     * Encrypt an array field (stored as JSON).
     * Useful for medical_notes, social_notes, etc.
     */
    public static function encryptArray(?array $value): ?string
    {
        if ($value === null || $value === []) {
            return null;
        }

        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        return self::encrypt($json);
    }

    /**
     * Decrypt a JSON-encoded encrypted field back to an array.
     */
    public static function decryptArray(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $decrypted = self::decrypt($value);

        if ($decrypted === null) {
            return null;
        }

        $decoded = json_decode($decrypted, true);

        return is_array($decoded) ? $decoded : null;
    }
}
