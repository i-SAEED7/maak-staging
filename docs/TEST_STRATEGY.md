# استراتيجية الاختبار التنفيذية

## Backend

### Unit

- `AuthService`
- `TenantService`
- `IepPlanService`
- `ReportService`

### Feature

- الدخول والخروج
- إنشاء مدرسة
- إنشاء مستخدم
- إنشاء طالب
- دورة IEP كاملة
- رفع ملف
- إنشاء زيارة إشرافية

### Security

- صلاحيات الوصول حسب الدور
- منع الوصول عبر `school_id` خاطئ
- قفل الحساب بعد المحاولات الفاشلة

## Frontend

### Component

- Login form
- DataTable
- FileUploader
- IEP editor shell

### Integration

- route guards
- auth store
- student listing filters

### E2E

- teacher creates student
- teacher creates IEP and submits it
- principal reviews
- supervisor approves
- parent views report
