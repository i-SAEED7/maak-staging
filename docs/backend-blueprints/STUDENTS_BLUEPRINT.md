# Blueprint: Students Module

## الهدف

إدارة بيانات الطلاب والربط بالمعلمين وأولياء الأمور.

## Endpoints

- `GET /students`
- `POST /students`
- `GET /students/{id}`
- `PUT /students/{id}`
- `PATCH /students/{id}/archive`
- `GET /students/{id}/guardians`
- `POST /students/{id}/guardians`

## الطبقات

- `StudentController`
- `StoreStudentRequest`
- `UpdateStudentRequest`
- `ArchiveStudentRequest`
- `AssignGuardianRequest`
- `StudentService`
- `StudentResource`
- `StudentPolicy`

## قواعد مهمة

- المعلم يرى فقط طلابه.
- مدير المدرسة يرى طلاب مدرسته.
- ولي الأمر لا يعدل بيانات الطالب.
