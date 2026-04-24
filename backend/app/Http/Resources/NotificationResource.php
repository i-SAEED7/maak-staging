<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recipient = $this->relationLoaded('user') ? $this->user : null;
        $creator = $this->relationLoaded('creator') ? $this->creator : null;
        $school = $this->relationLoaded('school') ? $this->school : null;
        $data = is_array($this->data) ? $this->data : [];

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'user_id' => $this->user_id,
            'created_by_user_id' => $this->created_by_user_id,
            'type' => $this->type,
            'channel' => $this->channel,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $data,
            'school' => $school ? [
                'id' => $school->id,
                'name_ar' => $school->name_ar,
            ] : null,
            'school_name' => $data['school_name'] ?? $school?->name_ar,
            'teacher_name' => $data['teacher_name'] ?? null,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'thread_key' => $data['thread_key'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'read_at' => $this->read_at?->toAtomString(),
            'sent_at' => $this->sent_at?->toAtomString(),
            'failed_at' => $this->failed_at?->toAtomString(),
            'created_at' => $this->created_at?->toAtomString(),
            'recipient' => $recipient ? [
                'id' => $recipient->id,
                'full_name' => $recipient->full_name,
                'email' => $recipient->email,
                'role' => $recipient->role?->name,
            ] : null,
            'creator' => $creator ? [
                'id' => $creator->id,
                'full_name' => $creator->full_name,
                'email' => $creator->email,
                'role' => $creator->role?->name,
            ] : null,
        ];
    }
}
