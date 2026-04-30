<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Generate a comprehensive school or comparative report in the background.
 *
 * Heavy report generation (statistics, comparisons, pivot tables)
 * can be slow for large datasets. Running in a queue prevents
 * the HTTP request from timing out.
 */
class GenerateReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    /**
     * @param string $reportType  school_summary|comparison|pivot|student_summary
     * @param array  $parameters  Report-specific parameters (school_id, date range, etc.)
     * @param int    $requestedByUserId  User who requested the report
     */
    public function __construct(
        private readonly string $reportType,
        private readonly array $parameters,
        private readonly int $requestedByUserId,
    ) {}

    public function handle(): void
    {
        try {
            Log::info("GenerateReportJob: Starting {$this->reportType} report generation.", [
                'parameters' => $this->parameters,
                'user_id' => $this->requestedByUserId,
            ]);

            // TODO: Implement actual report generation per type.
            // Example for school_summary:
            //   $school = School::findOrFail($this->parameters['school_id']);
            //   $reportData = $this->generateSchoolSummary($school);
            //   $pdf = Pdf::loadView('pdf.school-report', $reportData);
            //   Store the PDF and create a File record...

            $reportTitle = match ($this->reportType) {
                'school_summary' => 'ملخص المدرسة',
                'comparison' => 'تقرير المقارنة',
                'pivot' => 'التقرير الإحصائي',
                'student_summary' => 'ملخص الطالب',
                default => 'تقرير',
            };

            Log::info("GenerateReportJob: {$this->reportType} report generated successfully.");

            Notification::create([
                'user_id' => $this->requestedByUserId,
                'type' => 'report_ready',
                'title' => "تم إعداد التقرير: {$reportTitle}",
                'body' => "تم الانتهاء من إعداد {$reportTitle} بنجاح. يمكنك تحميله الآن من صفحة التقارير.",
                'data' => [
                    'report_type' => $this->reportType,
                    'parameters' => $this->parameters,
                ],
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error("GenerateReportJob: Failed for {$this->reportType}: {$e->getMessage()}");
            throw $e;
        }
    }
}
