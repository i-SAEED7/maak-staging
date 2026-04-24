# Blueprint: Schools Module

## الهدف

إدارة المدارس كوحدة المستأجر الأساسية.

## Endpoints

- `GET /schools`
- `POST /schools`
- `GET /schools/{id}`
- `PUT /schools/{id}`
- `PATCH /schools/{id}/status`
- `GET /schools/{id}/stats`
- `POST /schools/{id}/assign-principal`
- `POST /schools/{id}/assign-supervisor`

## الطبقات

### Controller

- `SchoolController`

### Requests

- `StoreSchoolRequest`
- `UpdateSchoolRequest`
- `ChangeSchoolStatusRequest`
- `AssignPrincipalRequest`
- `AssignSupervisorRequest`

### Service

- `SchoolService`

### Resource

- `SchoolResource`

### Policy

- `SchoolPolicy`

## Business Rules

- فقط `super_admin` ينشئ مدرسة.
- لا يمكن ربط أكثر من مدير مدرسة نشط كمدير رئيسي لنفس المدرسة.
- إحصاءات المدرسة تحسب من الطلاب والمعلمين والخطط.
