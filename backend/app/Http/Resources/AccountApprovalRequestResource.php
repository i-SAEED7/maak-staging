<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountApprovalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'last_name' => $this->last_name,
            'full_name' => $this->fullName(),
            'email' => $this->email,
            'phone' => $this->phone,
            'account_type' => $this->account_type,
            'account_type_label' => match ($this->account_type) {
                'teacher' => 'معلم',
                'principal' => 'مدير مدرسة',
                'parent' => 'ولي أمر',
                default => 'غير محدد',
            },
            'stage' => $this->stage,
            'school_id' => $this->school_id,
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toAtomString(),
            'created_at' => $this->created_at?->toAtomString(),
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
                'stage' => $this->school->stage,
            ] : null),
            'created_user' => $this->whenLoaded('createdUser', fn (): ?array => $this->createdUser ? [
                'id' => $this->createdUser->id,
                'full_name' => $this->createdUser->full_name,
                'email' => $this->createdUser->email,
            ] : null),
            'approved_by' => $this->whenLoaded('approvedBy', fn (): ?array => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'full_name' => $this->approvedBy->full_name,
                'email' => $this->approvedBy->email,
            ] : null),
        ];
    }
}
