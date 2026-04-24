<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class ReportController
{
    public function schoolSummary(): void
    {
        // Return school summary report.
    }

    public function studentSummary(): void
    {
        // Return student summary report.
    }

    public function comparison(): void
    {
        // Return comparison report data.
    }

    public function pivot(): void
    {
        // Return pivot report data.
    }

    public function exportPdf(): void
    {
        // Queue or return PDF export.
    }

    public function exportExcel(): void
    {
        // Queue or return Excel export.
    }
}
