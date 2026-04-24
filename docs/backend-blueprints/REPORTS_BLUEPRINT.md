# Blueprint: Reports Module

## الهدف

تجميع التقارير التحليلية والتصدير.

## Endpoints

- `GET /reports/schools/{id}/summary`
- `GET /reports/students/{id}/summary`
- `GET /reports/comparison`
- `GET /reports/pivot`
- `GET /reports/export/pdf`
- `GET /reports/export/excel`

## الطبقات

- `ReportController`
- `ReportService`
- `ExportPdfJob`
- `ExportExcelJob`

## قواعد

- بعض التقارير فورية.
- التقارير الكبيرة قد تنفذ عبر queue لاحقًا.
