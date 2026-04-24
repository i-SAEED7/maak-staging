# خريطة الأحداث والوظائف المؤجلة

## Events

- `IepPlanSubmitted`
- `IepPlanApproved`
- `StudentReportPublished`
- `MessageSent`
- `SupervisorVisitCompleted`

## Listeners

- `SendWorkflowNotification`
- `QueueIepPdfGeneration`
- `NotifyGuardiansOfReport`
- `StoreAuditLogEntry`

## Jobs

- `GenerateIepPdfJob`
- `ExportReportPdfJob`
- `ExportReportExcelJob`

## أمثلة الربط

### عند اعتماد الخطة

1. إطلاق `IepPlanApproved`
2. `QueueIepPdfGeneration`
3. `SendWorkflowNotification`
4. `StoreAuditLogEntry`

### عند نشر التقرير الدوري

1. إطلاق `StudentReportPublished`
2. `NotifyGuardiansOfReport`
3. `StoreAuditLogEntry`
