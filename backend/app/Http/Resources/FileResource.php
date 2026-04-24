<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'uploaded_by_user_id' => $this->uploaded_by_user_id,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id,
            'category' => $this->category,
            'original_name' => $this->original_name,
            'storage_name' => $this->storage_name,
            'storage_disk' => $this->storage_disk,
            'storage_path' => $this->storage_path,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'size_bytes' => $this->size_bytes,
            'checksum_sha256' => $this->checksum_sha256,
            'is_sensitive' => $this->is_sensitive,
            'visibility' => $this->visibility,
            'uploaded_at' => $this->uploaded_at?->toAtomString(),
            'deleted_at' => $this->deleted_at?->toAtomString(),
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
                'stage' => $this->school->stage,
            ] : null),
            'uploader' => $this->whenLoaded('uploader', fn (): ?array => $this->uploader ? [
                'id' => $this->uploader->id,
                'full_name' => $this->uploader->full_name,
                'email' => $this->uploader->email,
            ] : null),
        ];
    }
}
