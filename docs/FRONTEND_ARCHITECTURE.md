# هيكل الواجهة المقترح

## الهدف

تجهيز هيكل React TypeScript منذ الآن بشكل يعكس الوحدات الفعلية للنظام، بدل تركه عامًا أو مسطحًا.

## المبادئ

- الفصل بين `pages`, `components`, `services`, `stores`, `types`.
- كل صفحة تعتمد على hooks وخدمات API بدل تضمين النداءات داخل المكونات.
- استخدام `role-based routing`.
- دعم RTL من أول يوم.
- تجهيز بنية تسمح بإضافة shadcn/ui لاحقًا دون إعادة هيكلة.

## التقسيم

- `src/components/ui`: مكونات مكتبية مشتركة.
- `src/components/layout`: الغلاف العام والتنقل.
- `src/components/common`: مكونات وظيفية مثل الجداول والبطاقات.
- `src/pages`: صفحات حسب الدور.
- `src/services`: عميل API والوحدات المرتبطة.
- `src/stores`: حالة التطبيق.
- `src/hooks`: hooks متخصصة.
- `src/types`: الأنواع والواجهات.
- `src/styles`: الأنماط العامة والـ tokens.

## أول صفحات مطلوبة

- تسجيل الدخول
- استعادة كلمة المرور
- لوحة الإدارة العليا
- إدارة المدارس
- إدارة المستخدمين
- لوحة مدير المدرسة
- لوحة المعلم
- قائمة الطلاب
- محرر الخطة الفردية
- لوحة ولي الأمر

## أول Stores مطلوبة

- `authStore`
- `uiStore`
- لاحقًا `notificationsStore`

## أول Services مطلوبة

- `api`
- `authService`
- `schoolService`
- `userService`
- `studentService`
- `iepPlanService`
- `notificationService`
