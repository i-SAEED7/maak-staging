# خريطة Routes الخلفية

المسار الأساسي: `/api/v1`

## Public

- `POST /auth/login`
- `POST /auth/forgot-password`
- `POST /auth/verify-reset-otp`
- `POST /auth/reset-password`

## Authenticated

### Auth

- `POST /auth/logout`
- `POST /auth/change-password`
- `GET /auth/me`

### Schools

- `GET /schools`
- `POST /schools`
- `GET /schools/{id}`
- `PUT /schools/{id}`
- `PATCH /schools/{id}/status`
- `GET /schools/{id}/stats`
- `POST /schools/{id}/assign-principal`
- `POST /schools/{id}/assign-supervisor`

### Users

- `GET /users`
- `POST /users`
- `GET /users/{id}`
- `PUT /users/{id}`
- `PATCH /users/{id}/status`
- `POST /users/{id}/schools`

### Students

- `GET /students`
- `POST /students`
- `GET /students/{id}`
- `PUT /students/{id}`
- `PATCH /students/{id}/archive`
- `GET /students/{id}/guardians`
- `POST /students/{id}/guardians`

### Teacher Assignments

- `POST /teacher-student-assignments`
- `DELETE /teacher-student-assignments/{id}`
- `GET /teachers`
- `GET /teachers/{id}/students`

### Portfolios

- `GET /portfolios`
- `POST /portfolios`
- `GET /portfolios/{id}`
- `POST /portfolios/{id}/items`
- `PUT /portfolio-items/{id}`
- `DELETE /portfolio-items/{id}`

### IEP

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

### Student Reports

- `GET /student-reports`
- `POST /student-reports`
- `GET /student-reports/{id}`
- `PUT /student-reports/{id}`
- `POST /student-reports/{id}/publish`

### Files

- `POST /files`
- `GET /files/{id}`
- `POST /files/{id}/temporary-link`
- `DELETE /files/{id}`

### Supervision

- `GET /supervisor-visits`
- `POST /supervisor-visits`
- `GET /supervisor-visits/{id}`
- `PUT /supervisor-visits/{id}`
- `POST /supervisor-visits/{id}/complete`
- `POST /supervisor-visits/{id}/recommendations`
- `PATCH /supervisor-visit-recommendations/{id}`
- `GET /supervision-templates`

### Reports

- `GET /reports/schools/{id}/summary`
- `GET /reports/students/{id}/summary`
- `GET /reports/comparison`
- `GET /reports/pivot`
- `GET /reports/export/pdf`
- `GET /reports/export/excel`

### Notifications

- `GET /notifications`
- `POST /notifications/{id}/read`
- `POST /notifications/read-all`

### Messages

- `GET /messages`
- `GET /messages/thread/{threadKey}`
- `POST /messages`
- `POST /messages/{id}/read`

### Admin

- `GET /audit-logs`
- `GET /roles`
- `GET /permissions`
- `GET /settings/reference-data`
