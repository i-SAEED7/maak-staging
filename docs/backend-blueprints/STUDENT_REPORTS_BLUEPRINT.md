# Blueprint: Student Reports Module

## الهدف

إدارة التقارير الدورية للطالب خارج دورة IEP.

## Endpoints

- `GET /student-reports`
- `POST /student-reports`
- `GET /student-reports/{id}`
- `PUT /student-reports/{id}`
- `POST /student-reports/{id}/publish`

## الطبقات

- `StudentReportController`
- `StoreStudentReportRequest`
- `StudentReportService`
- `StudentReportResource`
- `StudentReportPolicy`

## قواعد

- المعلم يكتب ويعدل المسودة.
- عند النشر تصبح مرئية لولي الأمر ضمن نطاق الصلاحية.
