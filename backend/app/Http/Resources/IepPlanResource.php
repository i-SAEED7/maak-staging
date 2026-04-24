<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IepPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();
        $currentUserAcknowledgedAt = null;

        if ($currentUser !== null && $currentUser->role?->name === 'parent') {
            if ($this->relationLoaded('approvals')) {
                $matchedApproval = $this->approvals
                    ->firstWhere('action_by_user_id', $currentUser->id);

                $currentUserAcknowledgedAt = $matchedApproval?->created_at?->toAtomString();
            } else {
                $matchedApproval = $this->approvals()
                    ->where('action_by_user_id', $currentUser->id)
                    ->where('action_role', 'parent')
                    ->latest('created_at')
                    ->first();

                $currentUserAcknowledgedAt = $matchedApproval?->created_at?->toAtomString();
            }
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'student_id' => $this->student_id,
            'academic_year_id' => $this->academic_year_id,
            'teacher_user_id' => $this->teacher_user_id,
            'principal_user_id' => $this->principal_user_id,
            'supervisor_user_id' => $this->supervisor_user_id,
            'current_version_number' => $this->current_version_number,
            'status' => $this->status,
            'title' => $this->title,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'summary' => $this->summary,
            'strengths' => $this->strengths,
            'needs' => $this->needs,
            'accommodations' => $this->accommodations,
            'generated_pdf_file_id' => $this->generated_pdf_file_id,
            'current_user_acknowledged_at' => $currentUserAcknowledgedAt,
            'submitted_at' => $this->submitted_at?->toAtomString(),
            'approved_at' => $this->approved_at?->toAtomString(),
            'rejected_at' => $this->rejected_at?->toAtomString(),
            'rejection_reason' => $this->rejection_reason,
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
                'stage' => $this->school->stage,
                'program_type' => $this->school->program_type,
            ] : null),
            'student' => $this->whenLoaded('student', fn (): ?array => $this->student ? [
                'id' => $this->student->id,
                'full_name' => $this->student->full_name,
                'student_number' => $this->student->student_number,
                'grade_level' => $this->student->grade_level,
                'classroom' => $this->student->classroom,
                'education_program' => $this->student->relationLoaded('educationProgram') && $this->student->educationProgram ? [
                    'id' => $this->student->educationProgram->id,
                    'name_ar' => $this->student->educationProgram->name_ar,
                ] : null,
                'school' => $this->student->school ? [
                    'id' => $this->student->school->id,
                    'name_ar' => $this->student->school->name_ar,
                    'stage' => $this->student->school->stage,
                ] : null,
            ] : null),
            'teacher' => $this->whenLoaded('teacher', fn (): ?array => $this->teacher ? [
                'id' => $this->teacher->id,
                'full_name' => $this->teacher->full_name,
            ] : null),
            'principal' => $this->whenLoaded('principal', fn (): ?array => $this->principal ? [
                'id' => $this->principal->id,
                'full_name' => $this->principal->full_name,
            ] : null),
            'supervisor' => $this->whenLoaded('supervisor', fn (): ?array => $this->supervisor ? [
                'id' => $this->supervisor->id,
                'full_name' => $this->supervisor->full_name,
            ] : null),
            'goals' => $this->whenLoaded('goals', function () {
                return $this->goals->map(static fn ($goal): array => [
                    'id' => $goal->id,
                    'domain' => $goal->domain,
                    'goal_text' => $goal->goal_text,
                    'measurement_method' => $goal->measurement_method,
                    'baseline_value' => $goal->baseline_value,
                    'target_value' => $goal->target_value,
                    'due_date' => $goal->due_date?->toDateString(),
                    'sort_order' => $goal->sort_order,
                ])->values()->all();
            }),
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->map(static fn ($comment): array => [
                    'id' => $comment->id,
                    'author_user_id' => $comment->author_user_id,
                    'author_name' => $comment->author?->full_name,
                    'target_section' => $comment->target_section,
                    'comment_text' => $comment->comment_text,
                    'is_internal' => $comment->is_internal,
                    'created_at' => $comment->created_at?->toAtomString(),
                ])->values()->all();
            }),
            'versions' => $this->whenLoaded('versions', function () {
                return $this->versions->map(static fn ($version): array => [
                    'id' => $version->id,
                    'version_number' => $version->version_number,
                    'change_summary' => $version->change_summary,
                    'created_by_user_id' => $version->created_by_user_id,
                    'created_by_name' => $version->creator?->full_name,
                    'created_at' => $version->created_at?->toAtomString(),
                ])->values()->all();
            }),
            'approvals' => $this->whenLoaded('approvals', function () {
                return $this->approvals->map(static fn ($approval): array => [
                    'id' => $approval->id,
                    'action_by_user_id' => $approval->action_by_user_id,
                    'actor_name' => $approval->actor?->full_name,
                    'action_role' => $approval->action_role,
                    'from_status' => $approval->from_status,
                    'to_status' => $approval->to_status,
                    'notes' => $approval->notes,
                    'created_at' => $approval->created_at?->toAtomString(),
                ])->values()->all();
            }),
        ];
    }
}
