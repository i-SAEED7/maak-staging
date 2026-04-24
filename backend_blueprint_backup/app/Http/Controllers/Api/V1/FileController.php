<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class FileController
{
    public function store(): void
    {
        // Upload file and persist metadata.
    }

    public function show(): void
    {
        // Return file metadata.
    }

    public function temporaryLink(): void
    {
        // Issue temporary download link.
    }

    public function destroy(): void
    {
        // Soft delete file reference.
    }
}
