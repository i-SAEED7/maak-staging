# تقرير جاهزية النشر التجريبي

## هل المشروع جاهز للنشر؟

نعم، المشروع **جاهز للنشر التجريبي Staging** بعد تجهيز البيئة، لكنه **غير مهيأ كإنتاج نهائي** بعد.

## درجة الجاهزية

- الواجهة الأمامية: جاهزة
- الخلفية: جاهزة
- قاعدة البيانات: جاهزة
- الـ migrations: جاهزة
- seeders الأساسية: جاهزة
- فصل البوابة عن النظام الداخلي: جاهز
- ملفات النشر التجريبي: تم تجهيزها

## أهم الملاحظات قبل النشر

1. ملف [backend/.env](/d:/maak/backend/.env) محلي ويحتوي إعدادات تطوير فقط، ولا يجب استخدامه في Staging.
2. `APP_DEBUG` في البيئة المحلية الحالية مفعّل، وهذا **خطر** إذا نُقل كما هو إلى خادم تجريبي أو عام.
3. الواجهة كانت تعتمد على مسارات نسبية فقط، وتم تجهيزها الآن لتقرأ `VITE_API_BASE_URL` من البيئة.
4. لم يكن هناك `cors.php` أو `sanctum.php` منشوران في `backend/config`، وتمت إضافتهما.

## المتطلبات التشغيلية

### Frontend

- Node.js 20 أو أحدث
- `npm install`
- `npm run build`

### Backend

- PHP 8.2 أو أحدث
- Composer
- امتداد قاعدة البيانات المناسبة
- قاعدة بيانات PostgreSQL أو MySQL

### قاعدة البيانات

- يوصى بـ PostgreSQL لبيئة التجربة
- يوجد `44` migration جاهزة للتنفيذ

## متغيرات البيئة المطلوبة

### Frontend

- `VITE_APP_NAME`
- `VITE_API_BASE_URL`
- `VITE_DEFAULT_LOCALE`
- `VITE_ENABLE_MAPS`

### Backend

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_DEBUG`
- `APP_URL`
- `FRONTEND_URL`
- `CORS_ALLOWED_ORIGINS`
- `SANCTUM_STATEFUL_DOMAINS`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `CACHE_STORE`
- `QUEUE_CONNECTION`
- `SESSION_DRIVER`
- `SESSION_SECURE_COOKIE`
- `SESSION_SAME_SITE`
- `FILESYSTEM_DISK`
- `MAIL_MAILER`

## ما الذي يجب عدم رفعه؟

- أي ملف `.env`
- قيمة `APP_KEY`
- كلمات مرور قاعدة البيانات
- مفاتيح `AWS_ACCESS_KEY_ID` و`AWS_SECRET_ACCESS_KEY`
- بيانات SMTP الحقيقية
- ملفات `storage/*.key`
- أي dump من قاعدة البيانات التجريبية أو المحلية

## هل قاعدة البيانات تحتاج migrate / seed؟

نعم.

### الحد الأدنى

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force
```

### للاختبار والعرض

```bash
php artisan db:seed --class=DemoBrowserSeeder --force
```

## هل يحتاج Frontend إلى VITE_API_URL؟

نعم، يحتاج الآن إلى:

```env
VITE_API_BASE_URL=https://staging-api.example.com
```

وهذا ضروري إذا كانت الواجهة والخلفية على نطاقين مختلفين.

## المخاطر الحالية إن نُشر كما هو بدون التجهيز الجديد

- فشل الواجهة في الاتصال بالخلفية إذا كانت على نطاق منفصل
- أخطاء `CORS`
- تسريب تفاصيل أخطاء إذا بقي `APP_DEBUG=true`
- ضياع الملفات إذا استُخدم `local` disk على خدمة بدون تخزين دائم
- غياب بيانات التشغيل الأساسية إذا لم تُنفذ الـ seeders

## التوصية النهائية

- Frontend: `Vercel`
- Backend API + Worker: `Render`
- Database: `Render PostgreSQL` أو قاعدة PostgreSQL منفصلة
- Seeders:
  - `DatabaseSeeder` إجباري
  - `DemoBrowserSeeder` اختياري للعرض
