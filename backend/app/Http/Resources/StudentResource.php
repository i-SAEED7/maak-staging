<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'academic_year_id' => $this->academic_year_id,
            'education_program_id' => $this->education_program_id,
            'disability_category_id' => $this->disability_category_id,
            'primary_teacher_user_id' => $this->primary_teacher_user_id,
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'grandfather_name' => $this->grandfather_name,
            'family_name' => $this->family_name,
            'full_name' => $this->full_name,
            'student_number' => $this->student_number,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->toDateString(),
            'grade_level' => $this->grade_level,
            'classroom' => $this->classroom,
            'enrollment_status' => $this->enrollment_status,
            'medical_notes' => $this->medical_notes,
            'social_notes' => $this->social_notes,
            'transportation_notes' => $this->transportation_notes,
            'joined_at' => $this->joined_at?->toDateString(),
            'archived_at' => $this->archived_at?->toAtomString(),
            'metadata' => $this->metadata,
            'school' => $this->whenLoaded('school', fn (): array => [
                'id' => $this->school?->id,
                'name_ar' => $this->school?->name_ar,
                'stage' => $this->school?->stage,
                'status' => $this->school?->status,
            ]),
            'academic_year' => $this->whenLoaded('academicYear', fn (): ?array => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'name_ar' => $this->academicYear->name_ar,
            ] : null),
            'education_program' => $this->whenLoaded('educationProgram', fn (): ?array => $this->educationProgram ? [
                'id' => $this->educationProgram->id,
                'code' => $this->educationProgram->code,
                'name_ar' => $this->educationProgram->name_ar,
            ] : null),
            'disability_category' => $this->whenLoaded('disabilityCategory', fn (): ?array => $this->disabilityCategory ? [
                'id' => $this->disabilityCategory->id,
                'code' => $this->disabilityCategory->code,
                'name_ar' => $this->disabilityCategory->name_ar,
            ] : null),
            'primary_teacher' => $this->whenLoaded('primaryTeacher', fn (): ?array => $this->primaryTeacher ? [
                'id' => $this->primaryTeacher->id,
                'full_name' => $this->primaryTeacher->full_name,
                'email' => $this->primaryTeacher->email,
            ] : null),
            'guardians' => $this->whenLoaded('guardians', function () {
                return $this->guardians->map(static fn ($guardian): array => [
                    'id' => $guardian->id,
                    'parent_user_id' => $guardian->parent_user_id,
                    'parent_name' => $guardian->parent?->full_name,
                    'relationship' => $guardian->relationship,
                    'is_primary' => $guardian->is_primary,
                    'can_view_reports' => $guardian->can_view_reports,
                    'can_message_school' => $guardian->can_message_school,
                ])->values()->all();
            }),
        ];
    }
}
