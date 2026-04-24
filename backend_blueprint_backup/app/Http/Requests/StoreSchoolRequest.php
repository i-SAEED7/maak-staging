<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreSchoolRequest
{
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'storage_quota_mb' => ['nullable', 'integer', 'min:256'],
        ];
    }
}
