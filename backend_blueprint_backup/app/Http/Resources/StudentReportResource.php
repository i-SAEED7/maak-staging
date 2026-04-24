<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class StudentReportResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'student_id' => null,
            'report_type' => null,
            'status' => null,
            'published_at' => null,
        ];
    }
}
