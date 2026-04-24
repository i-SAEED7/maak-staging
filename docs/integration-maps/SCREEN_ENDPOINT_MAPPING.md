# ربط الشاشات بالـ Endpoints

## Auth

### `/login`
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me` بعد نجاح الدخول

### `/forgot-password`
- `POST /api/v1/auth/forgot-password`
- `POST /api/v1/auth/verify-reset-otp`
- `POST /api/v1/auth/reset-password`

## Admin

### `/admin`
- `GET /api/v1/auth/me`
- `GET /api/v1/reports/pivot`
- `GET /api/v1/notifications`

### `/admin/schools`
- `GET /api/v1/schools`
- `POST /api/v1/schools`
- `PUT /api/v1/schools/{id}`
- `PATCH /api/v1/schools/{id}/status`

### `/admin/users`
- `GET /api/v1/users`
- `POST /api/v1/users`
- `PUT /api/v1/users/{id}`
- `PATCH /api/v1/users/{id}/status`

## Principal

### `/principal`
- `GET /api/v1/auth/me`
- `GET /api/v1/reports/schools/{id}/summary`
- `GET /api/v1/notifications`

### `/principal/students`
- `GET /api/v1/students`
- `GET /api/v1/students/{id}`
- `PUT /api/v1/students/{id}`

### `/principal/iep-plans`
- `GET /api/v1/iep-plans`
- `GET /api/v1/iep-plans/{id}`
- `POST /api/v1/iep-plans/{id}/principal-approve`
- `POST /api/v1/iep-plans/{id}/reject`

## Teacher

### `/teacher`
- `GET /api/v1/auth/me`
- `GET /api/v1/students`
- `GET /api/v1/iep-plans`
- `GET /api/v1/student-reports`
- `GET /api/v1/messages`

### `/teacher/students`
- `GET /api/v1/students`
- `POST /api/v1/students`
- `PUT /api/v1/students/{id}`
- `POST /api/v1/students/{id}/guardians`

### `/teacher/iep-plans`
- `GET /api/v1/iep-plans`
- `POST /api/v1/iep-plans`
- `PUT /api/v1/iep-plans/{id}`
- `POST /api/v1/iep-plans/{id}/submit`

### `/teacher/iep-plans/:id`
- `GET /api/v1/iep-plans/{id}`
- `GET /api/v1/iep-plans/{id}/versions`
- `POST /api/v1/iep-plans/{id}/comments`
- `GET /api/v1/iep-plans/{id}/pdf`

### `/teacher/student-reports`
- `GET /api/v1/student-reports`
- `POST /api/v1/student-reports`
- `PUT /api/v1/student-reports/{id}`
- `POST /api/v1/student-reports/{id}/publish`

## Supervisor

### `/supervisor`
- `GET /api/v1/auth/me`
- `GET /api/v1/supervisor-visits`
- `GET /api/v1/iep-plans`
- `GET /api/v1/reports/comparison`

### `/supervisor/visits`
- `GET /api/v1/supervisor-visits`
- `POST /api/v1/supervisor-visits`
- `PUT /api/v1/supervisor-visits/{id}`
- `POST /api/v1/supervisor-visits/{id}/complete`

### `/supervisor/iep-plans`
- `GET /api/v1/iep-plans`
- `GET /api/v1/iep-plans/{id}`
- `POST /api/v1/iep-plans/{id}/supervisor-approve`
- `POST /api/v1/iep-plans/{id}/reject`

## Parent

### `/parent`
- `GET /api/v1/auth/me`
- `GET /api/v1/notifications`
- `GET /api/v1/messages`

### `/parent/children`
- `GET /api/v1/students`
- `GET /api/v1/students/{id}`

### `/parent/reports`
- `GET /api/v1/student-reports`
- `GET /api/v1/iep-plans/{id}/pdf`

### `/parent/messages`
- `GET /api/v1/messages`
- `GET /api/v1/messages/thread/{threadKey}`
- `POST /api/v1/messages`
