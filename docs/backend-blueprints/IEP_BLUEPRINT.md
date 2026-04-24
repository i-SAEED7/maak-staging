# Blueprint: IEP Module

## الهدف

إدارة دورة حياة الخطة التعليمية الفردية بالكامل.

## Endpoints

- `GET /iep-plans`
- `POST /iep-plans`
- `GET /iep-plans/{id}`
- `PUT /iep-plans/{id}`
- `POST /iep-plans/{id}/submit`
- `POST /iep-plans/{id}/principal-approve`
- `POST /iep-plans/{id}/supervisor-approve`
- `POST /iep-plans/{id}/reject`
- `GET /iep-plans/{id}/versions`
- `POST /iep-plans/{id}/comments`
- `GET /iep-plans/{id}/pdf`

## الطبقات

- `IepPlanController`
- `StoreIepPlanRequest`
- `UpdateIepPlanRequest`
- `TransitionIepPlanRequest`
- `CommentIepPlanRequest`
- `IepPlanService`
- `IepWorkflowService`
- `IepPlanResource`
- `IepPlanPolicy`
- `GenerateIepPdfJob`

## قواعد العمل

- المسودة يمكن تحديثها من المعلم.
- الإرسال يجمّد بعض الحقول إلى حين المراجعة.
- الاعتماد أو الرفض يسجلان في `iep_plan_approvals`.
- كل تعديل جوهري ينشئ نسخة في `iep_plan_versions`.
