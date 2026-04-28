<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->relationLoaded('role') ? $this->role : null;
        $school = $this->relationLoaded('school') ? $this->school : null;
        $canViewSchoolCode = $request->user()?->role?->name === 'super_admin';
        $assignedSchools = $this->resolveAssignedSchools($school, $canViewSchoolCode);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $role?->name,
            'role_display_name_ar' => $role?->display_name_ar,
            'is_central' => (bool) $this->is_central,
            'school_id' => $this->school_id,
            'school' => $school ? [
                'id' => $school->id,
                'name_ar' => $school->name_ar,
                'school_code' => $canViewSchoolCode ? $school->school_code : null,
                'slug' => $school->slug,
                'stage' => $school->stage,
                'status' => $school->status,
            ] : null,
            'assigned_schools' => $assignedSchools,
            'status' => $this->status,
        ];
    }

    private function resolveAssignedSchools(mixed $primarySchool, bool $canViewSchoolCode): array
    {
        $schools = collect();

        if ($primarySchool !== null) {
            $schools->push($primarySchool);
        }

        if ($this->relationLoaded('assignedSchools')) {
            $schools = $schools->merge($this->assignedSchools);
        }

        return $schools
            ->filter()
            ->unique('id')
            ->values()
            ->map(static fn ($school): array => [
                'id' => $school->id,
                'name_ar' => $school->name_ar,
                'school_code' => $canViewSchoolCode ? $school->school_code : null,
                'slug' => $school->slug,
                'official_code' => $canViewSchoolCode ? ($school->school_code ?: $school->ministry_code) : null,
                'stage' => $school->stage,
                'program_type' => $school->program_type,
                'status' => $school->status,
            ])
            ->all();
    }
}
