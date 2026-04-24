# دليل النشر التجريبي

هذا الدليل خاص ببيئة `Staging` فقط، وليس نشرًا نهائيًا للإنتاج.

## النتيجة الحالية

- الواجهة الأمامية قابلة للبناء بنجاح عبر `npm run build`.
- الخلفية Laravel 11 تعمل فعليًا وتستخدم `Sanctum` عبر `Bearer Token`.
- يوجد `44` migration جاهزة.
- توجد seeders أساسية للنظام، مع Seeder تجريبي اختياري لبيانات العرض.
- تمت إضافة:
  - [frontend/.env.staging.example](/d:/maak/frontend/.env.staging.example)
  - [backend/.env.staging.example](/d:/maak/backend/.env.staging.example)
  - [frontend/vercel.json](/d:/maak/frontend/vercel.json)
  - [render.yaml](/d:/maak/render.yaml)
  - [backend/config/cors.php](/d:/maak/backend/config/cors.php)
  - [backend/config/sanctum.php](/d:/maak/backend/config/sanctum.php)

## التوصية المعتمدة للـ Staging

1. الواجهة الأمامية على `Vercel`
2. الخلفية على `Render`
3. قاعدة البيانات `PostgreSQL` منفصلة لبيئة التجربة
4. عامل Queue منفصل على `Render Worker`

## لماذا هذا المسار؟

- الواجهة React/Vite تعمل بسلاسة على `Vercel`
- الخلفية Laravel أبسط تشغيليًا على `Render`
- الفصل بين الخدمات أوضح في بيئة الاختبار
- يمكن عزل قاعدة البيانات وبيانات التجربة عن أي بيئات أخرى

## إعداد الواجهة الأمامية

### Vercel

- Root Directory: `frontend`
- Install Command: `npm install`
- Build Command: `npm run build`
- Output Directory: `dist`

### متغيرات البيئة

انسخ القيم من [frontend/.env.staging.example](/d:/maak/frontend/.env.staging.example):

```env
VITE_APP_NAME=بوابة معاك - التجربة
VITE_API_BASE_URL=https://staging-api.example.com
VITE_DEFAULT_LOCALE=ar
VITE_ENABLE_MAPS=true
```

### ملاحظة مهمة

الواجهة أصبحت تقرأ `VITE_API_BASE_URL` فعليًا من [frontend/src/services/api.ts](/d:/maak/frontend/src/services/api.ts).
إذا لم يتم ضبطه، ستستخدم المسارات النسبية المحلية كما في بيئة التطوير.

## إعداد الخلفية

### Render Web Service

- Root Directory: `backend`
- Build Command:

```bash
composer install --no-dev --optimize-autoloader
```

- Start Command:

```bash
php artisan serve --host 0.0.0.0 --port $PORT
```

- Health Check Path:

```text
/up
```

### Render Worker

- Root Directory: `backend`
- Build Command:

```bash
composer install --no-dev --optimize-autoloader
```

- Start Command:

```bash
php artisan queue:work --tries=1 --timeout=90
```

## متغيرات البيئة الخلفية

استخدم [backend/.env.staging.example](/d:/maak/backend/.env.staging.example) كنقطة بداية.

أهم المتغيرات:

```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging-api.example.com
FRONTEND_URL=https://staging-frontend.example.com
CORS_ALLOWED_ORIGINS=https://staging-frontend.example.com
SANCTUM_STATEFUL_DOMAINS=staging-frontend.example.com
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=maak_staging
DB_USERNAME=...
DB_PASSWORD=...
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
MAIL_MAILER=log
```

## CORS و Sanctum

### الوضع الحالي

- النظام يستخدم `Bearer Token` في الواجهة.
- لا يعتمد حاليًا على `cookie-based SPA auth`.
- لذلك أهم نقطة في `Staging` هي السماح بـ `CORS` الصحيح بين الواجهة والخلفية.

### ما تم تجهيزه

- [backend/config/cors.php](/d:/maak/backend/config/cors.php)
  - يقرأ `CORS_ALLOWED_ORIGINS`
  - يشمل `api/*` و`temporary-files/*`
- [backend/config/sanctum.php](/d:/maak/backend/config/sanctum.php)
  - يقرأ `SANCTUM_STATEFUL_DOMAINS`
  - جاهز إذا أردت مستقبلًا التحول إلى `cookie auth`

### التوصية في Staging

- أبقِ `supports_credentials=false` طالما أن التوثيق عبر `Bearer Token`
- لا تحتاج `SANCTUM_STATEFUL_DOMAINS` تشغيليًا الآن، لكنها جاهزة احتياطيًا

## قاعدة البيانات

### المطلوب

نفّذ:

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force
```

### بيانات العرض الاختيارية

إذا أردت بيئة استعراضية جاهزة للحسابات والبيانات:

```bash
php artisan db:seed --class=DemoBrowserSeeder --force
```

### ملاحظات

- `DatabaseSeeder` يجهز الأدوار والصلاحيات والبيانات المرجعية
- `DemoBrowserSeeder` يضيف حسابات وبيانات تجربة، ولا يُنصح به إلا في `Staging`

## التخزين والملفات

### الخيار الأبسط

```env
FILESYSTEM_DISK=local
```

هذا مناسب للتجربة السريعة، لكن الملفات قد تضيع عند إعادة البناء أو إعادة نشر الخدمة.

### الخيار الأفضل

استخدم تخزين `S3-compatible` في `Staging` إذا أردت بقاء الملفات.

## البريد

في `Staging` يفضّل:

```env
MAIL_MAILER=log
```

أو خدمة SMTP تجريبية منفصلة إن كنت تريد اختبار الإرسال فعليًا.

## ترتيب التنفيذ المقترح

1. أنشئ قاعدة بيانات `PostgreSQL` منفصلة للـ Staging
2. أنشئ خدمة الخلفية `backend`
3. أنشئ عامل `queue worker`
4. اضبط متغيرات البيئة الخلفية
5. نفّذ `migrate`
6. نفّذ `DatabaseSeeder`
7. نفّذ `DemoBrowserSeeder` إذا أردت بيانات استعراض
8. أنشئ مشروع `Vercel` للواجهة
9. اضبط `VITE_API_BASE_URL`
10. اختبر:
   - `/api/auth/central-login`
   - `/api/auth/school-login`
   - `/api/v1/auth/me`
   - الواجهة `/login`
   - التوجيه بعد الدخول
