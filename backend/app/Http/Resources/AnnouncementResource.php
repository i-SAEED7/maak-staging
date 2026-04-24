<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $school = $this->relationLoaded('school') ? $this->school : null;
        $creator = $this->relationLoaded('creator') ? $this->creator : null;
        $views = $this->relationLoaded('views') ? $this->views : null;
        $canViewViews = in_array($request->user()?->role?->name, ['super_admin', 'admin'], true);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'body' => $this->body,
            'target_audience' => $this->target_audience,
            'is_all_schools' => (bool) $this->is_all_schools,
            'status' => $this->status,
            'published_at' => $this->published_at?->toAtomString(),
            'school' => $school ? [
                'id' => $school->id,
                'name_ar' => $school->name_ar,
                'school_code' => $school->school_code,
            ] : null,
            'creator' => $creator ? [
                'id' => $creator->id,
                'full_name' => $creator->full_name,
                'email' => $creator->email,
                'role' => $creator->role?->name,
            ] : null,
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
            'views' => $canViewViews && $views !== null
                ? $views->map(static fn ($view): array => [
                    'id' => $view->id,
                    'viewer_name' => $view->viewer?->full_name,
                    'viewer_role' => $view->viewer_role,
                    'viewed_at' => $view->viewed_at?->toAtomString(),
                ])->values()->all()
                : null,
        ];
    }
}
