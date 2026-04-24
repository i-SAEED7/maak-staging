<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'method' => $this->method,
            'endpoint' => $this->endpoint,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'created_at' => $this->created_at?->toAtomString(),
            'actor' => $this->whenLoaded('actor', fn (): ?array => $this->actor ? [
                'id' => $this->actor->id,
                'full_name' => $this->actor->full_name,
                'email' => $this->actor->email,
                'role' => $this->actor->role?->name,
            ] : null),
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
                'stage' => $this->school->stage,
            ] : null),
        ];
    }
}
