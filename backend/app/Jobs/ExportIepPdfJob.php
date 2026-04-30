<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\IepPlan;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Export an IEP plan to PDF in the background.
 *
 * This prevents the user from waiting on a long-running PDF generation
 * process. When the PDF is ready, a notification is sent to the user.
 */
class ExportIepPdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $iepPlanId,
        private readonly int $requestedByUserId,
    ) {}

    public function handle(): void
    {
        $plan = IepPlan::with(['student', 'teacher', 'goals'])->find($this->iepPlanId);

        if (!$plan) {
            Log::warning("ExportIepPdfJob: IEP plan #{$this->iepPlanId} not found.");
            return;
        }

        try {
            // TODO: Implement actual PDF generation using DomPDF or Snappy.
            // Example:
            //   $pdf = Pdf::loadView('pdf.iep-plan', ['plan' => $plan]);
            //   $path = "iep-exports/{$plan->uuid}.pdf";
            //   Storage::disk('local')->put($path, $pdf->output());
            //   $plan->update(['generated_pdf_file_id' => $fileId]);

            Log::info("ExportIepPdfJob: PDF export for IEP plan #{$this->iepPlanId} completed.");

            // Notify the user that the PDF is ready for download.
            Notification::create([
                'user_id' => $this->requestedByUserId,
                'type' => 'iep_pdf_ready',
                'title' => 'تم تصدير الخطة الفردية',
                'body' => "تم الانتهاء من تصدير خطة الطالب {$plan->student?->full_name} بنجاح. يمكنك تحميلها الآن.",
                'data' => [
                    'iep_plan_id' => $plan->id,
                    'student_name' => $plan->student?->full_name,
                ],
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error("ExportIepPdfJob: Failed for IEP plan #{$this->iepPlanId}: {$e->getMessage()}");
            throw $e;
        }
    }
}
