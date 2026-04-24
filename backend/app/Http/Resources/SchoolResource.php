<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $principal = $this->relationLoaded('principal') ? $this->principal : null;
        $supervisor = $this->relationLoaded('supervisor') ? $this->supervisor : null;
        $locationLat = $this->location_lat ?? $this->latitude;
        $locationLng = $this->location_lng ?? $this->longitude;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name_ar,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'school_code' => $this->school_code,
            'slug' => $this->slug,
            'official_code' => $this->school_code ?: $this->ministry_code,
            'ministry_code' => $this->ministry_code,
            'stage' => $this->stage,
            'program_type' => $this->program_type,
            'gender' => $this->gender,
            'region' => $this->region,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'location_lat' => $locationLat,
            'location_lng' => $locationLng,
            'latitude' => $locationLat,
            'longitude' => $locationLng,
            'status' => $this->status,
            'principal_id' => $this->principal_id ?? $this->principal_user_id,
            'supervisor_id' => $this->supervisor_id,
            'principal_user_id' => $this->principal_user_id,
            'principal' => $principal ? [
                'id' => $principal->id,
                'full_name' => $principal->full_name,
                'email' => $principal->email,
            ] : null,
            'supervisor' => $supervisor ? [
                'id' => $supervisor->id,
                'full_name' => $supervisor->full_name,
                'email' => $supervisor->email,
            ] : null,
            'teachers_count' => (int) ($this->teachers_count ?? 0),
            'students_count' => (int) ($this->students_count ?? 0),
        ];
    }
}
