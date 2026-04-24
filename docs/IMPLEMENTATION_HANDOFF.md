# حزمة التسليم الحالية

هذا الملف يلخص ما يمكن لأي مطور فعله مباشرة عند استلام المستودع.

## ابدأ من هنا

1. اقرأ [docs/PROJECT_STATUS.md](./PROJECT_STATUS.md)
2. اقرأ [docs/NEXT_PHASE_PRIORITIES.md](./NEXT_PHASE_PRIORITIES.md)
3. اقرأ [docs/PHASE_1_EXECUTION_SEQUENCE.md](./PHASE_1_EXECUTION_SEQUENCE.md)
4. اعتمد [docs/DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) و[docs/API_SPEC.md](./API_SPEC.md)
5. استخدم `backend/database/migrations/stubs` كمرجع لبقية الوحدات غير المنفذة بعد

## الملفات المرجعية الأهم

- قاعدة البيانات: [docs/DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
- SQL: [docs/sql/001_initial_schema.sql](./sql/001_initial_schema.sql)
- API: [docs/API_SPEC.md](./API_SPEC.md)
- العقود: [docs/API_CONTRACTS.md](./API_CONTRACTS.md)
- الصلاحيات: [docs/PERMISSIONS_MATRIX.md](./PERMISSIONS_MATRIX.md)
- العزل: [docs/workflows/TENANCY_RULES.md](./workflows/TENANCY_RULES.md)
- ربط الواجهة والخلفية: [docs/integration-maps/SCREEN_ENDPOINT_MAPPING.md](./integration-maps/SCREEN_ENDPOINT_MAPPING.md)

## الحالة

- `backend/` قابل للإقلاع الآن كمشروع Laravel فعلي
- `frontend/` قابل للإقلاع الآن كمشروع React/Vite فعلي
- الوحدات المنفذة فعليًا حتى الآن: `auth`, `schools`, `users`, `students`, `iep`, `messages`, `notifications`, `reports`, `supervision`, `files`
- البوابة العامة `Portal` منفصلة الآن عن النظام الداخلي، وتستخدم واجهات ومسارات مستقلة دون كسر وحدات المدرسة
- العزل متعدد المدارس مفعّل حاليًا عبر `TenantService`, `TenantContext`, و`SetTenantScope`
- نتائج التحقق الحالية: `artisan route:list` ناجح و`artisan test` ناجح بالحالة الحالية و`npm run build` ناجح
- روابط التشغيل الحالية:
  - Laravel API + Browser Demo: `http://127.0.0.1:8000`
  - React Portal + System Frontend: `http://127.0.0.1:5173`

## مسارات العمل الأساسية

- البوابة العامة:
  - `/`
  - `/about`
  - `/services`
  - `/programs/:programSlug`
  - `/statistics`
  - `/interactive-map`
  - `/contact`
  - `/login`
- انتقالات ما بعد الدخول:
  - `/select-school`
  - `/schools/:schoolId`
- النظام الداخلي:
  - `/app`

## منطق التوجيه بعد تسجيل الدخول

- مستخدم مدرسة واحدة: يوجّه إلى `/schools/{id}`
- مشرف متعدد المدارس: يوجّه إلى `/select-school`
- أدمن / سوبر أدمن: يوجّه إلى `/app`

## طبقة الدخول الحالية

- `/login`:
  - بوابة اختيار بين `دخول مدرسي` و`دخول مركزي`
- `/login/school`:
  - يتطلب `identifier + password + school_code`
- `/login/central`:
  - يتطلب `identifier + password`
- الـ API المقابلة:
  - `POST /api/auth/school-login`
  - `POST /api/auth/central-login`
