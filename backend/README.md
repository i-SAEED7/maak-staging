# Backend

هذا المجلد يحتوي الآن على مشروع `Laravel 11` فعلي للخدمات الخلفية.

## الحالة الحالية

- `Auth`, `Schools`, `Users`, `Students`, `IEP`, `Messages`, `Notifications`, `Reports`, `Supervision`, `Files` منفذة فعليًا على مستوى الـ API.
- `Sanctum` مفعّل للمصادقة عبر التوكن.
- العزل متعدد المدارس `tenant context` مفعّل.
- `migrations` الأساسية و`seeders` وبيانات Demo موجودة.
- توجد واجهة Browser Demo مؤقتة على المسار `/`.

## تجربة المتصفح

- الرابط المحلي: `http://127.0.0.1:8000`
- الواجهة المؤقتة الحالية تدعم:
  - تسجيل الدخول
  - استعراض المدارس والمستخدمين والطلاب
  - إنشاء مدرسة ومستخدم وطالب
  - ربط ولي أمر بطالب
  - استعراض خطط `IEP`
  - إنشاء خطة `IEP`
  - تجربة دورة الاعتماد: `submit -> principal approve -> supervisor approve`
  - جلب الإصدارات وبيانات `PDF` والتعليقات للخطة
  - استعراض المحادثات الداخلية
  - إرسال رسالة داخلية
  - فتح سلسلة رسائل
  - تعليم الرسالة كمقروءة
  - استعراض الإشعارات
  - تعليم إشعار واحد أو جميع الإشعارات كمقروءة
  - تشغيل تقارير المدرسة والطالب والمقارنة و`Pivot`
  - معاينة تصدير `PDF/Excel` للتقارير
  - رفع ملف فعلي
  - استعراض الملفات
  - إنشاء رابط تحميل مؤقت
  - حذف مرجع الملف منطقيًا
- الحسابات التجريبية:
  - `superadmin@maak.local` / `Password@123`
  - `admin@maak.local` / `Password@123`
  - `supervisor@maak.local` / `Password@123`
  - `principal@maak.local` / `Password@123`
  - `teacher@maak.local` / `Password@123`
  - `parent@maak.local` / `Password@123`

## التحقق الحالي

- `artisan route:list` ناجح.
- `artisan test` ناجح بالحالة الحالية: `3 passed, 10 skipped`.
