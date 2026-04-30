<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\FieldEncryption;

/**
 * Automatically encrypt/decrypt sensitive fields on a model.
 *
 * Models using this trait should define a property:
 *
 *   protected array $encryptedFields = ['national_id_encrypted', 'medical_notes'];
 */
trait HasEncryptedFields
{
    public static function bootHasEncryptedFields(): void
    {
        static::saving(static function ($model): void {
            foreach ($model->getEncryptedFieldNames() as $field) {
                if ($model->isDirty($field)) {
                    $value = $model->getAttributes()[$field] ?? null;

                    if ($value !== null && $value !== '') {
                        if (is_array($value)) {
                            $model->attributes[$field] = FieldEncryption::encryptArray($value);
                        } else {
                            $model->attributes[$field] = FieldEncryption::encrypt((string) $value);
                        }
                    }
                }
            }
        });
    }

    /**
     * Override getAttribute to automatically decrypt encrypted fields.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptedFieldNames(), true) && is_string($value)) {
            // Check if the cast expects an array
            $casts = $this->getCasts();
            if (isset($casts[$key]) && $casts[$key] === 'array') {
                return FieldEncryption::decryptArray($value);
            }

            return FieldEncryption::decrypt($value);
        }

        return $value;
    }

    /**
     * Get the list of field names that should be encrypted.
     */
    public function getEncryptedFieldNames(): array
    {
        return property_exists($this, 'encryptedFields') ? $this->encryptedFields : [];
    }
}
