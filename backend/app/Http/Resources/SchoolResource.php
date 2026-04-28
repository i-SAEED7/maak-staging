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
        $canViewSchoolCode = $request->user()?->role?->name === 'super_admin';

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name_ar,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'school_code' => $canViewSchoolCode ? $this->school_code : null,
            'slug' => $this->slug,
            'official_code' => $canViewSchoolCode ? ($this->school_code ?: $this->ministry_code) : null,
            'ministry_code' => $this->ministry_code,
            'stage' => $this->stage,
            'program_type' => $this->program_type,
            'program_types' => $this->relationLoaded('educationPrograms')
                ? $this->educationPrograms->pluck('name_ar')->values()->all()
                : collect(preg_split('/،|,/', (string) $this->program_type) ?: [])
                    ->map(static fn (string $programType): string => trim($programType))
                    ->filter()
                    ->values()
                    ->all(),
            'programs' => $this->whenLoaded('educationPrograms', fn (): array => $this->educationPrograms
                ->map(static fn ($program): array => [
                    'id' => $program->id,
                    'code' => $program->code,
                    'name_ar' => $program->name_ar,
                ])
                ->values()
                ->all()),
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
