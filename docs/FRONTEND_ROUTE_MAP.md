# خريطة المسارات في الواجهة

المبدأ: كل دور يرى مجموعة Routes مخصصة مع حماية قبلية.

## عامة

- `/login`
- `/forgot-password`
- `/unauthorized`

## الإدارة العليا `super_admin`, `admin`

- `/admin`
- `/admin/schools`
- `/admin/schools/:id`
- `/admin/users`
- `/admin/users/:id`
- `/admin/reports`
- `/admin/audit-logs`
- `/admin/settings`

## المشرف

- `/supervisor`
- `/supervisor/schools`
- `/supervisor/visits`
- `/supervisor/visits/:id`
- `/supervisor/iep-plans`
- `/supervisor/reports`

## مدير المدرسة

- `/principal`
- `/principal/users`
- `/principal/students`
- `/principal/iep-plans`
- `/principal/reports`

## المعلم

- `/teacher`
- `/teacher/students`
- `/teacher/students/:id`
- `/teacher/iep-plans`
- `/teacher/iep-plans/:id`
- `/teacher/student-reports`
- `/teacher/portfolio`
- `/teacher/messages`

## ولي الأمر

- `/parent`
- `/parent/children`
- `/parent/children/:id`
- `/parent/reports`
- `/parent/messages`
- `/parent/notifications`

## مشتركة بعد تسجيل الدخول

- `/profile`
- `/notifications`
- `/messages`
