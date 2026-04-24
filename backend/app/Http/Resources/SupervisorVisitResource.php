<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SupervisorVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'supervisor_user_id' => $this->supervisor_user_id,
            'template_id' => $this->template_id,
            'visit_date' => $this->visit_date?->toDateString(),
            'visit_status' => $this->visit_status,
            'agenda' => $this->agenda,
            'summary' => $this->summary,
            'overall_score' => $this->overall_score !== null ? (float) $this->overall_score : null,
            'next_follow_up_at' => $this->next_follow_up_at?->toAtomString(),
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
            ] : null),
            'supervisor' => $this->whenLoaded('supervisor', fn (): ?array => $this->supervisor ? [
                'id' => $this->supervisor->id,
                'full_name' => $this->supervisor->full_name,
                'email' => $this->supervisor->email,
            ] : null),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(static fn ($item): array => [
                    'id' => $item->id,
                    'criterion_key' => $item->criterion_key,
                    'criterion_label' => $item->criterion_label,
                    'score' => $item->score !== null ? (float) $item->score : null,
                    'remarks' => $item->remarks,
                ])->values()->all();
            }),
            'recommendations' => $this->whenLoaded('recommendations', function () {
                return $this->recommendations->map(static fn ($recommendation): array => [
                    'id' => $recommendation->id,
                    'recommendation_text' => $recommendation->recommendation_text,
                    'owner_user_id' => $recommendation->owner_user_id,
                    'owner_name' => $recommendation->owner?->full_name,
                    'due_date' => $recommendation->due_date?->toDateString(),
                    'status' => $recommendation->status,
                    'completed_at' => $recommendation->completed_at?->toAtomString(),
                ])->values()->all();
            }),
        ];
    }
}
