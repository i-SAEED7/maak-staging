# 📘 وثيقة المواصفات الشاملة وخطة التنفيذ
## مشروع: البوابة المتكاملة لقسم ذوي الإعاقة في الإدارة التعليمية

---

**رقم الوثيقة:** PRD-DSN-001
**الإصدار:** 2.0 (النسخة الموحّدة النهائية)
**الحالة:** جاهزة للتنفيذ
**نوع الوثيقة:** وثيقة مواصفات المنتج (PRD) + خطة تنفيذ تقنية (SDD)
**الجمهور المستهدف:** فريق التطوير، مدير المشروع، صاحب المنتج، قسم ذوي الإعاقة

---

# 📑 جدول المحتويات

1. [الملخص التنفيذي](#1-الملخص-التنفيذي)
2. [الرؤية والأهداف](#2-الرؤية-والأهداف)
3. [أصحاب المصلحة والمستخدمون](#3-أصحاب-المصلحة-والمستخدمون)
4. [نطاق المشروع](#4-نطاق-المشروع)
5. [البنية المعمارية العامة](#5-البنية-المعمارية-العامة)
6. [الوحدات الوظيفية التفصيلية](#6-الوحدات-الوظيفية-التفصيلية)
7. [قاعدة البيانات التفصيلية](#7-قاعدة-البيانات-التفصيلية)
8. [تصميم واجهات المستخدم والصفحات](#8-تصميم-واجهات-المستخدم-والصفحات)
9. [واجهات برمجة التطبيقات (API)](#9-واجهات-برمجة-التطبيقات-api)
10. [الأمن والحماية](#10-الأمن-والحماية)
11. [خطة التنفيذ البرمجي التفصيلية](#11-خطة-التنفيذ-البرمجي-التفصيلية)
12. [هيكلة المجلدات والملفات](#12-هيكلة-المجلدات-والملفات)
13. [الجدول الزمني ومراحل التنفيذ](#13-الجدول-الزمني-ومراحل-التنفيذ)
14. [الاختبار والنشر](#14-الاختبار-والنشر)
15. [المخاطر والتحديات](#15-المخاطر-والتحديات)
16. [الصيانة والتطوير المستقبلي](#16-الصيانة-والتطوير-المستقبلي)

---

# 1. الملخص التنفيذي

## 1.1 وصف المشروع

**البوابة المتكاملة لقسم ذوي الإعاقة** هي منصة رقمية مركزية (Centralized Multi-Tenant Web Portal) تهدف إلى أتمتة وإدارة جميع العمليات التعليمية والإشرافية المتعلقة بطلاب التربية الخاصة ضمن الإدارة التعليمية. تعمل البوابة بنمط **تعدد المستأجرين (Multi-Tenancy)** بحيث تخدم عدة مدارس في آنٍ واحد، مع عزل تام للبيانات والصلاحيات بين كل مدرسة.

## 1.2 المشكلة التي يحلها المشروع

- تشتّت بيانات الطلاب ذوي الإعاقة بين أنظمة وملفات ورقية متفرقة.
- صعوبة متابعة الإشراف التربوي لعدة مدارس في الوقت نفسه.
- عدم وجود توثيق موحّد لملفات إنجاز المعلمين والخطط الفردية.
- ضعف التواصل بين المدرسة وأولياء الأمور.
- غياب التقارير الإحصائية التي تدعم اتخاذ القرار.

## 1.3 الحل المقترح

بوابة مركزية واحدة تتيح:

- لوحة تحكم خاصة لكل دور (مدير عام، مشرف، مدير مدرسة، معلم، ولي أمر).
- عزل بيانات كل مدرسة عن الأخرى (كل مدرسة ترى بياناتها فقط).
- إدارة كاملة للخطط التعليمية الفردية (IEP) من الإنشاء حتى الاعتماد.
- أرشفة رقمية آمنة لجميع الملفات.
- تقارير تحليلية ذكية تدعم اتخاذ القرار.

## 1.4 الأثر المتوقع

| المؤشر | القيمة المستهدفة |
|---|---|
| تقليل العمل الورقي | ≥ 80% |
| تسريع إصدار التقارير | من أيام إلى دقائق |
| رفع جودة متابعة الخطط الفردية | 100% تتبع إلكتروني |
| تحسين التواصل مع ولي الأمر | متابعة لحظية |

---

# 2. الرؤية والأهداف

## 2.1 الرؤية

> «أن نكون المنصة الرقمية المرجعية لإدارة تعليم ذوي الإعاقة، بحيث تجمع كل أصحاب العلاقة تحت مظلة واحدة وتوفّر بيانات دقيقة تدعم القرار التربوي.»

## 2.2 الأهداف الاستراتيجية

1. **توحيد العمل** داخل قسم ذوي الإعاقة على مستوى الإدارة التعليمية.
2. **أتمتة العمليات التربوية** (خطط فردية، تقارير، زيارات إشرافية).
3. **تمكين الإشراف التربوي** بأدوات رقمية للمتابعة والتقييم.
4. **تعزيز الشراكة مع الأسرة** عبر قنوات تواصل مباشرة.
5. **توفير بيانات تحليلية** تدعم القرار.

## 2.3 الأهداف الوظيفية (Functional Goals)

- تسجيل دخول آمن متعدد المستويات.
- إدارة بيانات الطلاب والمعلمين والمدارس.
- تحرير واعتماد الخطط التعليمية الفردية.
- أرشفة الملفات مع ضوابط الصلاحية.
- إصدار تقارير إحصائية وإشرافية.
- إشعارات لحظية للمستخدمين.

## 2.4 الأهداف غير الوظيفية (Non-Functional Goals)

| الهدف | المعيار |
|---|---|
| الأداء | تحميل الصفحة ≤ 2 ثانية |
| التوفر | Uptime ≥ 99.5% |
| الأمان | تشفير AES-256 + HTTPS إلزامي |
| قابلية التوسع | دعم حتى 100 مدرسة و20,000 طالب |
| سهولة الاستخدام | واجهة عربية RTL متجاوبة مع الجوال |

---

# 3. أصحاب المصلحة والمستخدمون

## 3.1 الأدوار الرئيسية (Roles)

### 3.1.1 المدير العام للنظام (Super Admin)
- **الوصف:** مسؤول تقنية المعلومات أو مدير القسم المركزي.
- **النطاق:** كلي (Global).
- **الصلاحيات:**
  - إدارة جميع الحسابات (إضافة/تعديل/تعطيل).
  - إضافة وحذف المدارس.
  - تعيين المشرفين التربويين على القطاعات.
  - الاطلاع على كافة التقارير والإحصائيات.
  - تعديل إعدادات النظام العامة (قوائم الإعاقات، البرامج، السنوات الدراسية…).
  - إدارة سجلّ العمليات (Audit Log).

### المدير العام للنظام (Admin)
- **الوصف:** مساعد تقنية المعلومات أو مدير القسم المركزي.
- **النطاق:** يتم تحديد الصلاحيات لاحقًا في لوحة التحكم
- **الصلاحيات:**


### 3.1.2 المشرف التربوي (Supervisor)
- **الوصف:** مشرف تخصصي لذوي الإعاقة.
- **النطاق:** إقليمي/قطاعي (عدة مدارس).
- **الصلاحيات:**
  - الاطلاع على المدارس التابعة له فقط.
  - تقييم أداء المعلمين والمدارس إلكترونياً.
  - اعتماد الخطط الفردية (IEP) جزئياً أو كلياً.
  - تسجيل الزيارات الميدانية ونتائجها.
  - إصدار تقارير الأداء القطاعية.

### 3.1.3 مدير المدرسة (Principal)
- **الوصف:** مدير مدرسة تطبّق برامج ذوي الإعاقة.
- **النطاق:** مدرسي (School Level).
- **الصلاحيات:**
  - إدارة بيانات مدرسته فقط.
  - متابعة حضور وانضباط معلمي وطلاب مدرسته.
  - مراجعة واعتماد ملفات الطلاب قبل رفعها للمشرف.
  - الاطلاع على ملفات إنجاز معلمي مدرسته.
  - إصدار تقارير مدرسية.

### 3.1.4 المعلم (Teacher)
- **الوصف:** معلم التربية الخاصة أو الدمج.
- **النطاق:** صفّي/فردي (طلابه فقط).
- **الصلاحيات:**
  - إدخال وتحديث بيانات طلابه.
  - كتابة ورفع الخطط الفردية والتقارير الدورية.
  - توثيق ملف الإنجاز الشخصي (دورات، شهادات، أنشطة).
  - التواصل مع أولياء الأمور عبر النظام.

### 3.1.5 ولي الأمر (Parent)
- **الوصف:** والد/والدة الطالب.
- **النطاق:** أبنائي فقط.
- **الصلاحيات:**
  - الاطلاع على تقارير ابنه/ابنته.
  - تحميل الخطط الفردية (بعد الاعتماد).
  - استلام الإشعارات والتنبيهات.
  - مراسلة المعلم.

## 3.2 جدول مصفوفة الصلاحيات (Permissions Matrix)

| العملية | Super Admin | Supervisor | Principal | Teacher | Parent |
|---|:---:|:---:|:---:|:---:|:---:|
| إدارة المدارس | ✅ | ❌ | ❌ | ❌ | ❌ |
| إدارة المستخدمين | ✅ | ❌ | جزئي | ❌ | ❌ |
| عرض كل الطلاب | ✅ | مدارسه | مدرسته | طلابه | أبناؤه |
| كتابة خطة فردية | ❌ | ❌ | ❌ | ✅ | ❌ |
| اعتماد خطة فردية | ❌ | ✅ | ✅ | ❌ | ❌ |
| زيارات ميدانية | ❌ | ✅ | ❌ | ❌ | ❌ |
| تقارير قطاع | ✅ | ✅ | ❌ | ❌ | ❌ |
| تقارير مدرسة | ✅ | ✅ | ✅ | ❌ | ❌ |
| مراسلة المعلم | ✅ | ✅ | ✅ | ✅ | ✅ |

---

# 4. نطاق المشروع

## 4.1 داخل النطاق (In-Scope)

- بوابة ويب متجاوبة (Responsive Web).
- دعم كامل للغة العربية RTL.
- نظام تسجيل دخول متعدد الأدوار.
- وحدات: المدارس، المستخدمون، الطلاب، المعلمون، الخطط الفردية، الملفات، التقارير، الإشراف، الإشعارات.
- خريطة جغرافية لعرض المدارس.
- تقارير PDF وExcel.

## 4.2 خارج النطاق (Out-of-Scope) — في المرحلة الأولى

- تطبيق جوال Native (iOS/Android) — يُؤجل للمرحلة الثانية.
- تكامل مع أنظمة حكومية خارجية (نور، فارس…) — يُدرس لاحقاً.
- ذكاء اصطناعي لاقتراح الأهداف التعليمية — للمرحلة الثالثة.
- نظام اجتماعات فيديو داخلي — غير مطلوب حالياً.

---

# 5. البنية المعمارية العامة

## 5.1 النمط المعماري

**نمط العمارة: 3-Tier Multi-Tenant Web Architecture**

```
┌─────────────────────────────────────────────────────┐
│              Client Layer (Browser)                  │
│   React SPA + TailwindCSS + RTL + Responsive        │
└────────────────────┬────────────────────────────────┘
                     │ HTTPS / JSON
┌────────────────────▼────────────────────────────────┐
│           Application Layer (Backend)                │
│   Laravel 11 (PHP 8.3) REST API + Sanctum Auth      │
│   Services / Controllers / Middleware / Policies     │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│             Data Layer                               │
│   PostgreSQL 15 + Redis (Cache) + S3/MinIO (Files) │
└─────────────────────────────────────────────────────┘
```

## 5.2 مبدأ تعدد المستأجرين (Multi-Tenancy)

نعتمد **Single Database, Shared Schema** مع عزل البيانات عبر `school_id` في كل الجداول ذات الصلة، وتطبيق **Row-Level Security** عبر:
1. **Global Scopes** في Eloquent تضيف `WHERE school_id = ?` تلقائياً.
2. **Middleware** يحقن معرّف المدرسة في كل طلب.
3. **Policies** تتحقق من الصلاحية قبل أي عملية.

## 5.3 لماذا هذه التقنيات؟

| الطبقة | التقنية المختارة | البدائل المرفوضة ولماذا |
|---|---|---|
| الواجهة الأمامية | **React 18 + TypeScript + Vite** | Vue: مجتمع أصغر • Angular: منحنى تعلّم حاد |
| التنسيق | **TailwindCSS + shadcn/ui** | Bootstrap: قديم وغير مرن • Material UI: ثقيل |
| الخلفية | **Laravel 11 (PHP 8.3)** | Node/Express: أقل جاهزية لـ ORM المركّب • Django: مجتمع عربي أضعف |
| المصادقة | **Laravel Sanctum** (SPA Token) | JWT مباشرة: تعقيد إدارة الإبطال |
| قاعدة البيانات | **PostgreSQL 15** | MySQL: أضعف في JSON والـ Row-Level Security |
| التخزين المؤقت | **Redis 7** | Memcached: محدود (بدون persistence) |
| تخزين الملفات | **MinIO** (S3-Compatible) داخلي | رفع للمجلد المحلي: لا يدعم النسخ الاحتياطي العنقودي |
| خادم الويب | **Nginx + PHP-FPM** | Apache: أبطأ في الحمل العالي |
| التشغيل | **Docker + Docker Compose** | تثبيت مباشر: صعوبة تكرار البيئات |

---

# 6. الوحدات الوظيفية التفصيلية

## 6.1 وحدة المصادقة وإدارة الحسابات (Auth Module)

### المتطلبات:
- تسجيل دخول عبر (رقم الهوية + كلمة المرور) أو (البريد الإلكتروني + كلمة المرور).
- OTP للجوال عند إعادة تعيين كلمة المرور.
- قفل الحساب بعد 5 محاولات دخول فاشلة لمدة 15 دقيقة.
- انتهاء الجلسة بعد 20 دقيقة عدم نشاط.
- سجل دخول (IP، الجهاز، الوقت).

### المكونات:
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `POST /api/auth/forgot-password`
- `POST /api/auth/reset-password`
- `POST /api/auth/change-password`
- `GET /api/auth/me`

## 6.2 وحدة المدارس (Schools Module)

### المتطلبات:
- إضافة مدرسة بيانات أساسية + خريطة.
- تعيين مدير للمدرسة.
- ربط المدرسة بمشرف.
- حالة (نشطة/معطّلة).
- إحصائيات فورية (عدد الطلاب، المعلمين، نسبة الاكتمال).

## 6.3 وحدة الطلاب (Students Module)

### المتطلبات:
- نموذج تسجيل شامل (بيانات شخصية، صحية، اجتماعية).
- تصنيف حسب نوع الإعاقة والبرنامج.
- ربط الطالب بولي الأمر (حساب مستخدم).
- أرشفة الطالب عند التخرج/النقل (لا حذف).
- بحث متقدم (اسم، هوية، إعاقة، برنامج، مدرسة).

## 6.4 وحدة المعلمين (Teachers Module)

### المتطلبات:
- حساب لكل معلم مرتبط بمدرسة.
- ملف إنجاز إلكتروني (دورات، شهادات، أنشطة، شواهد).
- قائمة بالطلاب المسنَدين له.
- مؤشر أداء (Completion Rate للخطط والتقارير).

## 6.5 وحدة الخطط التعليمية الفردية (IEP Module)

### سير العمل (Workflow):

```
   [المعلم يكتب]
        │
        ▼
   [مسودة Draft] ────► [بانتظار مراجعة المدير]
                              │
                              ▼
                      [بانتظار اعتماد المشرف]
                              │
                        ┌─────┴─────┐
                        ▼           ▼
                   [معتمدة]     [مرفوضة]
                        │           │
                        ▼           └── يعود للمعلم للتعديل
            تصبح مرئية لولي الأمر
```

### المتطلبات:
- محرر غني (Rich Text Editor) للأهداف.
- قوالب جاهزة لكل نوع إعاقة.
- مرفقات داعمة.
- تعليقات المشرف مباشرةً على الخطة.
- إصدار نسخ (Versioning) لتتبع التعديلات.

## 6.6 وحدة الملفات والأرشفة (Files Module)

### المتطلبات:
- رفع ملفات (PDF, Word, Excel, صور) حتى 20MB لكل ملف.
- تصنيف تلقائي (طبي، تعليمي، إداري).
- أسماء ملفات مُشوّشة (UUID) على الخادم.
- تحميل عبر Token مؤقت (5 دقائق).
- منع فهرسة محركات البحث (robots.txt + X-Robots-Tag).
- حد أقصى للتخزين لكل مدرسة (Configurable).

## 6.7 وحدة الإشراف التربوي (Supervision Module)

### المتطلبات:
- جدولة زيارات ميدانية.
- نموذج تقييم متعدد المعايير (ديناميكي قابل للإعداد).
- تسجيل التوصيات ومتابعة التنفيذ.
- تقارير قطاعية (مقارنة بين المدارس).

## 6.8 وحدة التقارير (Reports Module)

### الأنواع:
- تقرير مدرسة شامل.
- تقرير طالب شامل (خطة + تقارير + حضور).
- تقرير مقارنة (بين مدارس).
- تقرير إحصائي (Pivot) حسب: البرنامج، الإعاقة، الجنس، المنطقة.
- تصدير PDF و Excel.

## 6.9 وحدة الإشعارات (Notifications Module)

### الأنواع:
- إشعار داخل النظام (Bell Icon).
- إشعار SMS (للتنبيهات العاجلة).
- إشعار بريد إلكتروني.

### الأحداث التي تُطلق إشعاراً:
- خطة بانتظار الاعتماد.
- زيارة ميدانية مجدولة.
- رسالة جديدة من ولي أمر.
- تذكير تسليم تقرير دوري.

## 6.10 وحدة الخريطة (Maps Module) — اختيارية

### المتطلبات:
- عرض المدارس على خريطة (Leaflet + OpenStreetMap).
- دبابيس ملوّنة حسب حالة المدرسة.
- فلترة حسب البرنامج أو المنطقة.

---

# 7. قاعدة البيانات التفصيلية

## 7.1 مخطط العلاقات (ERD) المبسّط

```
users ─┐
       ├──< user_schools >── schools ──< students ──< iep_plans
       │                         │            │
       │                         │            └──< student_reports
       │                         │
       └──< portfolios           └──< supervisor_visits
       └──< notifications        └──< files
       └──< audit_logs
```

## 7.2 الجداول التفصيلية

### جدول `roles`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | المعرّف |
| name | VARCHAR(50) | super_admin / supervisor / principal / teacher / parent |
| display_name_ar | VARCHAR(100) | الاسم العربي |
| description | TEXT | الوصف |

### جدول `users`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | المعرّف |
| full_name | VARCHAR(150) | الاسم الرباعي |
| national_id | VARCHAR(10) **Encrypted** | رقم الهوية |
| last_5_id | VARCHAR(5) INDEX | آخر 5 أرقام (للبحث السريع) |
| phone | VARCHAR(15) | الجوال (للـ OTP) |
| email | VARCHAR(150) UNIQUE | البريد |
| password | VARCHAR(255) | مُشفّرة bcrypt |
| role_id | BIGINT FK | الدور |
| school_id | BIGINT FK NULL | المدرسة المرتبطة |
| is_active | BOOLEAN | نشط/معطّل |
| last_login_at | TIMESTAMP | آخر دخول |
| failed_attempts | INT | عدد المحاولات الفاشلة |
| locked_until | TIMESTAMP NULL | نهاية القفل |
| created_at, updated_at | TIMESTAMP | |

### جدول `schools`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(200) | اسم المدرسة |
| code | VARCHAR(20) UNIQUE | كود رسمي |
| type | ENUM | يسير/دمج فكري/توحد/صعوبات تعلم/متعدد |
| location_lat | DECIMAL(10,7) | خط العرض |
| location_lng | DECIMAL(10,7) | خط الطول |
| address | TEXT | العنوان الوطني |
| principal_id | BIGINT FK | مدير المدرسة |
| supervisor_id | BIGINT FK | المشرف |
| status | ENUM | نشطة/غير نشطة |
| created_at, updated_at | TIMESTAMP | |

### جدول `students`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| first_name, middle_name, last_name | VARCHAR | الاسم |
| national_id | VARCHAR(10) **Encrypted** | هوية الطالب |
| date_of_birth | DATE | الميلاد |
| gender | ENUM | ذكر/أنثى |
| disability_type | VARCHAR(50) | نوع الإعاقة |
| program_type | VARCHAR(50) | البرنامج |
| school_id | BIGINT FK | المدرسة |
| teacher_id | BIGINT FK | المعلم المسؤول |
| parent_user_id | BIGINT FK | ولي الأمر |
| medical_report_path | VARCHAR(255) | الملف الطبي |
| enrollment_date | DATE | تاريخ القبول |
| status | ENUM | نشط/مؤرشف/متخرج |
| created_at, updated_at | TIMESTAMP | |

### جدول `iep_plans`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| student_id | BIGINT FK | الطالب |
| teacher_id | BIGINT FK | المعلم |
| academic_year | VARCHAR(9) | مثل 2025-2026 |
| term | ENUM | أول/ثاني/ثالث |
| goals | JSONB | الأهداف |
| strategies | JSONB | الاستراتيجيات |
| assessments | JSONB | التقييمات |
| status | ENUM | draft/pending_principal/pending_supervisor/approved/rejected |
| principal_notes | TEXT | ملاحظات المدير |
| supervisor_notes | TEXT | ملاحظات المشرف |
| file_path | VARCHAR(255) | PDF مُصدَّر |
| approved_at | TIMESTAMP | تاريخ الاعتماد |
| created_at, updated_at | TIMESTAMP | |

### جدول `portfolios` (ملفات الإنجاز)
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK | صاحب الملف |
| type | ENUM | certificate/course/activity/report |
| title | VARCHAR(200) | |
| description | TEXT | |
| file_path | VARCHAR(255) | UUID-named |
| issued_by | VARCHAR(150) NULL | الجهة المانحة |
| issue_date | DATE NULL | |
| created_at, updated_at | TIMESTAMP | |

### جدول `supervisor_visits`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| school_id | BIGINT FK | |
| supervisor_id | BIGINT FK | |
| visit_date | DATE | |
| visit_type | ENUM | مجدولة/مفاجئة/متابعة |
| criteria_scores | JSONB | درجات المعايير |
| recommendations | TEXT | |
| overall_rating | DECIMAL(3,2) | من 0 إلى 5 |
| follow_up_required | BOOLEAN | |
| created_at | TIMESTAMP | |

### جدول `files`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| uploader_id | BIGINT FK | من رفع الملف |
| school_id | BIGINT FK NULL | |
| related_type | VARCHAR(50) | students/iep_plans/portfolios |
| related_id | BIGINT | |
| original_name | VARCHAR(255) | الاسم الأصلي |
| stored_name | VARCHAR(255) | UUID.ext |
| mime_type | VARCHAR(100) | |
| size_bytes | BIGINT | |
| category | ENUM | medical/educational/administrative |
| is_encrypted | BOOLEAN | |
| created_at | TIMESTAMP | |

### جدول `notifications`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK | المستقبِل |
| type | VARCHAR(100) | |
| title | VARCHAR(200) | |
| body | TEXT | |
| data | JSONB | بيانات إضافية |
| is_read | BOOLEAN | |
| read_at | TIMESTAMP NULL | |
| created_at | TIMESTAMP | |

### جدول `audit_logs`
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK | |
| action | VARCHAR(100) | login/update/delete… |
| model_type | VARCHAR(100) | |
| model_id | BIGINT | |
| old_values | JSONB | |
| new_values | JSONB | |
| ip_address | VARCHAR(45) | |
| user_agent | TEXT | |
| created_at | TIMESTAMP | |

### جدول `messages` (المراسلات)
| الحقل | النوع | الوصف |
|---|---|---|
| id | BIGINT PK | |
| sender_id | BIGINT FK | |
| receiver_id | BIGINT FK | |
| subject | VARCHAR(200) | |
| body | TEXT | |
| is_read | BOOLEAN | |
| parent_message_id | BIGINT FK NULL | للرد |
| created_at | TIMESTAMP | |

## 7.3 الفهارس (Indexes)

- `users(national_id)`, `users(email)`, `users(school_id)`
- `students(school_id, status)`, `students(teacher_id)`, `students(parent_user_id)`
- `iep_plans(student_id, academic_year, term)` — Unique Composite
- `iep_plans(status, school_id)`
- `files(related_type, related_id)`
- `notifications(user_id, is_read)`

## 7.4 الـ Seeders الافتراضية

- صلاحيات (Roles): 5 أدوار.
- قوائم ثابتة: أنواع الإعاقات، البرامج، السنوات الدراسية.
- حساب Super Admin افتراضي (يُغيَّر لاحقاً).

---

# 8. تصميم واجهات المستخدم والصفحات

## 8.1 المبادئ العامة للتصميم

- **RTL كامل** (اللغة العربية).
- **ألوان هادئة** (أزرق #1E6091 + أخضر #52B788 + رمادي فاتح).
- **خط:** Cairo أو Tajawal (Google Fonts).
- **متجاوب** (Mobile-First).
- **إمكانية وصول (Accessibility)** WCAG 2.1 AA: تباين عالٍ، دعم قارئ الشاشة، اختصارات لوحة المفاتيح.
- **Dark Mode** (اختياري).

## 8.2 قائمة الصفحات الكاملة

### 8.2.1 الصفحات العامة (Public Pages)
1. **الصفحة الرئيسية** (`/`) — تعريف بالبوابة + زر دخول.
2. **صفحة تسجيل الدخول** (`/login`) — نموذج الدخول.
3. **نسيت كلمة المرور** (`/forgot-password`).
4. **إعادة تعيين كلمة المرور** (`/reset-password/:token`).
5. **صفحة 404** / **صفحة 500** / **صفحة 403**.

### 8.2.2 صفحات Super Admin
1. **لوحة التحكم** (`/admin/dashboard`) — KPIs + رسوم + تنبيهات.
2. **إدارة المدارس** (`/admin/schools`) — قائمة + بحث + فلترة + Export.
3. **إضافة/تعديل مدرسة** (`/admin/schools/create` & `/admin/schools/:id/edit`).
4. **عرض مدرسة** (`/admin/schools/:id`) — تفاصيل + تبويبات.
5. **إدارة المستخدمين** (`/admin/users`).
6. **إضافة/تعديل مستخدم** (`/admin/users/create` & `/admin/users/:id/edit`).
7. **إدارة المشرفين** (`/admin/supervisors`).
8. **إدارة القوائم الثابتة** (`/admin/settings/lists`) — أنواع الإعاقات، البرامج.
9. **سجل العمليات** (`/admin/audit-logs`).
10. **التقارير الشاملة** (`/admin/reports`).
11. **الإعدادات العامة** (`/admin/settings`).

### 8.2.3 صفحات المشرف (Supervisor)
1. **لوحة التحكم** (`/supervisor/dashboard`) — خريطة + أداء القطاع.
2. **مدارسي** (`/supervisor/schools`) — قائمة المدارس التابعة.
3. **عرض مدرسة** (`/supervisor/schools/:id`) — تفاصيل.
4. **الخطط بانتظار الاعتماد** (`/supervisor/iep-approvals`).
5. **مراجعة خطة** (`/supervisor/iep-plans/:id/review`).
6. **الزيارات الميدانية** (`/supervisor/visits`).
7. **جدولة زيارة** (`/supervisor/visits/create`).
8. **تقرير زيارة** (`/supervisor/visits/:id`).
9. **تقارير الأداء** (`/supervisor/reports`).

### 8.2.4 صفحات مدير المدرسة (Principal)
1. **لوحة التحكم** (`/principal/dashboard`).
2. **المعلمون** (`/principal/teachers`).
3. **ملف إنجاز معلم** (`/principal/teachers/:id/portfolio`).
4. **الطلاب** (`/principal/students`).
5. **ملف طالب** (`/principal/students/:id`).
6. **الخطط بانتظار مراجعتي** (`/principal/iep-approvals`).
7. **مراجعة خطة** (`/principal/iep-plans/:id/review`).
8. **تقارير المدرسة** (`/principal/reports`).
9. **التعاميم** (`/principal/announcements`).

### 8.2.5 صفحات المعلم (Teacher)
1. **لوحة التحكم** (`/teacher/dashboard`).
2. **طلابي** (`/teacher/students`).
3. **إضافة طالب جديد** (`/teacher/students/create`).
4. **ملف طالب** (`/teacher/students/:id`).
5. **الخطط الفردية** (`/teacher/iep-plans`).
6. **كتابة خطة جديدة** (`/teacher/iep-plans/create/:student_id`).
7. **تعديل خطة** (`/teacher/iep-plans/:id/edit`).
8. **ملف إنجازي** (`/teacher/portfolio`).
9. **رفع إنجاز** (`/teacher/portfolio/add`).
10. **تقاريري الدورية** (`/teacher/reports`).
11. **الرسائل** (`/teacher/messages`).

### 8.2.6 صفحات ولي الأمر (Parent)
1. **لوحة التحكم** (`/parent/dashboard`).
2. **أبنائي** (`/parent/children`).
3. **ملف ابني** (`/parent/children/:id`) — تقارير + خطة.
4. **الخطة الفردية** (`/parent/children/:id/iep`).
5. **التقارير الدورية** (`/parent/children/:id/reports`).
6. **الرسائل** (`/parent/messages`).

### 8.2.7 الصفحات المشتركة
1. **الملف الشخصي** (`/profile`).
2. **تغيير كلمة المرور** (`/profile/change-password`).
3. **الإشعارات** (`/notifications`).
4. **الرسائل** (`/messages`).

## 8.3 مكوّنات واجهة المستخدم المتكررة (UI Components)

| المكوّن | الوصف |
|---|---|
| `<AppShell>` | هيكل التطبيق (Sidebar + Navbar) |
| `<Sidebar>` | القائمة الجانبية (تختلف حسب الدور) |
| `<Navbar>` | شريط علوي (إشعارات، الملف الشخصي) |
| `<DataTable>` | جدول بيانات (بحث + فرز + pagination) |
| `<KPI Card>` | بطاقة إحصائية |
| `<StatusBadge>` | شارة حالة ملوّنة |
| `<FileUploader>` | رافع ملفات مع معاينة |
| `<FormBuilder>` | مُولِّد نماذج ديناميكي |
| `<Modal>` | نافذة منبثقة |
| `<ConfirmDialog>` | تأكيد الإجراءات الحرجة |
| `<NotificationBell>` | جرس الإشعارات |
| `<MapView>` | خريطة Leaflet |
| `<RichEditor>` | محرر النصوص الغني (TipTap) |
| `<Chart>` | رسوم (Recharts) |

## 8.4 تدفقات المستخدم الرئيسية (User Flows)

### تدفق: المعلم يكتب خطة فردية ويُرسلها للاعتماد
```
  Login → Dashboard → طلابي → اختيار طالب → "خطة جديدة"
       → نموذج الخطة (أهداف + استراتيجيات + مرفقات)
       → حفظ كمسودة  ── أو ──  إرسال للمدير
       → [إشعار للمدير]
       → المدير يراجع → يعتمد → [إشعار للمشرف]
       → المشرف يعتمد → [إشعار للمعلم وولي الأمر]
       → ولي الأمر يرى الخطة في لوحته
```

---

# 9. واجهات برمجة التطبيقات (API)

## 9.1 المبادئ العامة

- REST API مع JSON.
- Versioning: `/api/v1/...`
- Authentication: Bearer Token (Sanctum).
- Content-Type: `application/json`.
- صيغة الاستجابات:

```json
{
  "success": true,
  "message": "تم بنجاح",
  "data": { ... },
  "meta": { "page": 1, "total": 100 }
}
```

## 9.2 نقاط النهاية (Endpoints) الرئيسية

### المصادقة
| Method | Endpoint | الوصف |
|---|---|---|
| POST | `/api/v1/auth/login` | دخول |
| POST | `/api/v1/auth/logout` | خروج |
| POST | `/api/v1/auth/forgot-password` | نسيت |
| POST | `/api/v1/auth/reset-password` | إعادة |
| GET | `/api/v1/auth/me` | الملف الشخصي |

### المدارس
| Method | Endpoint | الصلاحية |
|---|---|---|
| GET | `/api/v1/schools` | Super Admin, Supervisor |
| POST | `/api/v1/schools` | Super Admin |
| GET | `/api/v1/schools/:id` | حسب النطاق |
| PUT | `/api/v1/schools/:id` | Super Admin |
| DELETE | `/api/v1/schools/:id` | Super Admin |
| GET | `/api/v1/schools/:id/statistics` | حسب النطاق |

### المستخدمون
| Method | Endpoint | الصلاحية |
|---|---|---|
| GET | `/api/v1/users` | Super Admin, Principal |
| POST | `/api/v1/users` | Super Admin |
| GET | `/api/v1/users/:id` | حسب النطاق |
| PUT | `/api/v1/users/:id` | حسب النطاق |

### الطلاب
| Method | Endpoint | الصلاحية |
|---|---|---|
| GET | `/api/v1/students` | حسب النطاق |
| POST | `/api/v1/students` | Teacher, Principal |
| GET | `/api/v1/students/:id` | حسب النطاق |
| PUT | `/api/v1/students/:id` | Teacher, Principal |
| POST | `/api/v1/students/:id/archive` | Principal |

### الخطط الفردية
| Method | Endpoint | الصلاحية |
|---|---|---|
| GET | `/api/v1/iep-plans` | حسب النطاق |
| POST | `/api/v1/iep-plans` | Teacher |
| GET | `/api/v1/iep-plans/:id` | حسب النطاق |
| PUT | `/api/v1/iep-plans/:id` | Teacher (draft only) |
| POST | `/api/v1/iep-plans/:id/submit` | Teacher |
| POST | `/api/v1/iep-plans/:id/approve` | Principal, Supervisor |
| POST | `/api/v1/iep-plans/:id/reject` | Principal, Supervisor |
| GET | `/api/v1/iep-plans/:id/export-pdf` | حسب النطاق |

### الملفات
| Method | Endpoint |
|---|---|
| POST | `/api/v1/files/upload` |
| GET | `/api/v1/files/:id/download-token` |
| GET | `/api/v1/files/download/:token` |
| DELETE | `/api/v1/files/:id` |

### التقارير
| Method | Endpoint |
|---|---|
| GET | `/api/v1/reports/school/:id` |
| GET | `/api/v1/reports/student/:id` |
| POST | `/api/v1/reports/compare` |
| GET | `/api/v1/reports/statistics` |

### الإشعارات والرسائل
| Method | Endpoint |
|---|---|
| GET | `/api/v1/notifications` |
| POST | `/api/v1/notifications/:id/read` |
| GET | `/api/v1/messages` |
| POST | `/api/v1/messages` |

---

# 10. الأمن والحماية

## 10.1 التشفير

| البيانات | الخوارزمية |
|---|---|
| كلمات المرور | **bcrypt** (cost=12) |
| رقم الهوية في قاعدة البيانات | **AES-256-GCM** (Laravel Crypt) |
| الملفات المرفوعة | **AES-256-CBC** |
| النقل | **TLS 1.3** |
| الجلسات | مفتاح APP_KEY مُدار في `.env` مُقيَّد |

## 10.2 الحماية من الهجمات

| الهجوم | الوسيلة |
|---|---|
| SQL Injection | Eloquent ORM + Prepared Statements |
| XSS | Laravel Blade Auto-Escape + DOMPurify في الواجهة |
| CSRF | Laravel CSRF Middleware + Sanctum CSRF Cookie |
| Brute Force | Rate Limiting (60 req/min) + قفل الحساب |
| File Upload Attack | فحص MIME + Whitelist + مسح بـ ClamAV |
| IDOR | Policies + Global Scopes |
| Session Hijacking | HttpOnly + Secure + SameSite=Strict Cookies |
| Directory Traversal | Storage المعزول + asserts على المسارات |

## 10.3 إدارة الصلاحيات (RBAC)

- حزمة **Spatie Laravel-Permission**.
- Policies مخصصة لكل موديل.
- Global Scopes تعزل بيانات المدرسة تلقائياً.

## 10.4 WAF + Monitoring

- **ModSecurity** على Nginx (مع قواعد OWASP CRS).
- **Fail2Ban** لحظر IP المشبوهة.
- **Sentry** لتتبع الأخطاء.
- **Grafana + Prometheus** للمراقبة.
- **Audit Log** شامل لكل العمليات الحساسة.

## 10.5 النسخ الاحتياطي

- نسخ يومية لقاعدة البيانات (PostgreSQL pg_dump) → تُرفع إلى MinIO.
- احتفاظ بآخر 30 يوم + أسبوعية شهرية + شهرية سنوية.
- اختبار استرجاع ربع سنوي.

## 10.6 الخصوصية وحماية البيانات

- الالتزام بـ **نظام حماية البيانات الشخصية السعودي (PDPL)**.
- حذف بيانات المستخدم عند الطلب (Right to Erasure) — في حدود اللوائح التعليمية.
- ربط الملفات الطبية بصلاحيات مشددة (المعلم المسؤول + ولي الأمر فقط).

---

# 11. خطة التنفيذ البرمجي التفصيلية

## 11.1 مجموعة التقنيات (Tech Stack) النهائية

### 11.1.1 الواجهة الأمامية (Frontend)
```
- React 18.2
- TypeScript 5.x
- Vite 5.x (build tool)
- TailwindCSS 3.4
- shadcn/ui (مكتبة مكونات)
- React Router v6 (التنقل)
- TanStack Query (React Query) (إدارة حالة الشبكة)
- Zustand (إدارة الحالة العامة)
- React Hook Form + Zod (النماذج والتحقق)
- Axios (HTTP client)
- TipTap (محرر نصوص غني)
- Recharts (رسوم بيانية)
- Leaflet + React-Leaflet (خرائط)
- date-fns (تواريخ هجرية + ميلادية)
- i18next (ترجمة)
```

### 11.1.2 الواجهة الخلفية (Backend)
```
- PHP 8.3
- Laravel 11
- Laravel Sanctum (مصادقة SPA)
- Spatie Laravel-Permission (RBAC)
- Spatie Laravel-Medialibrary (إدارة الملفات)
- Laravel-Excel (Export)
- DomPDF / Snappy (Export PDF)
- Laravel Horizon (Queue monitoring)
- Intervention Image (معالجة الصور)
```

### 11.1.3 قاعدة البيانات والبنية التحتية
```
- PostgreSQL 15
- Redis 7 (cache + queue + session)
- MinIO (تخزين ملفات متوافق مع S3)
- Nginx 1.25
- Docker + Docker Compose
- GitHub Actions (CI/CD)
- Ubuntu 22.04 LTS (الخادم)
```

### 11.1.4 أدوات التطوير
```
- Git + GitHub / GitLab
- VS Code / PhpStorm
- Postman / Insomnia (اختبار API)
- Figma (تصميم)
- ESLint + Prettier
- PHPStan + Laravel Pint
- PHPUnit + Pest (اختبارات خلفية)
- Vitest + React Testing Library (اختبارات واجهة)
- Playwright (E2E tests)
```

## 11.2 الخطوات التنفيذية بالتسلسل (Like a real developer would do)

### **المرحلة صفر — الإعداد (Week 0)**

**اليوم 1-2: إعداد البيئة**
```bash
# إعداد مستودع Git
git init educational-disability-portal
cd educational-disability-portal

# هيكلية مجلدات عليا
mkdir -p backend frontend docs docker infrastructure

# إعداد Docker
```

**اليوم 3-4: إعداد Backend**
```bash
cd backend
composer create-project laravel/laravel . "^11.0"

# تثبيت الحزم الأساسية
composer require laravel/sanctum spatie/laravel-permission
composer require spatie/laravel-medialibrary maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require --dev pestphp/pest laravel/pint phpstan/phpstan

# إعداد البيئة
cp .env.example .env
# تعديل .env: DB_CONNECTION=pgsql, REDIS, MAIL…

php artisan key:generate
php artisan migrate
```

**اليوم 5: إعداد Frontend**
```bash
cd ../frontend
npm create vite@latest . -- --template react-ts

npm install react-router-dom @tanstack/react-query zustand
npm install axios react-hook-form @hookform/resolvers zod
npm install tailwindcss postcss autoprefixer
npx tailwindcss init -p
npx shadcn-ui@latest init

# مكونات shadcn الأساسية
npx shadcn-ui@latest add button card input table dialog form
npx shadcn-ui@latest add dropdown-menu select tabs toast sidebar
```

**اليوم 6-7: إعداد Docker**
- `docker-compose.yml` يحتوي على: nginx, php, postgres, redis, minio.
- ملفات Dockerfile لكل خدمة.
- Seed أولي لقاعدة البيانات.

---

### **المرحلة 1 — الأساس والمصادقة (Week 1-2)**

**الأسبوع 1:**
1. إنشاء جداول `roles`, `users`, `schools` (Migrations).
2. Models: `User`, `Role`, `School`.
3. Seeder للأدوار الخمسة.
4. إعداد Sanctum للـ SPA.
5. إنشاء Auth Controllers:
   - `AuthController@login`
   - `AuthController@logout`
   - `AuthController@forgotPassword`
   - `AuthController@resetPassword`
6. Middleware: `CheckRole`, `SetTenant`.
7. Policies الأساسية.

**الأسبوع 2:**
1. واجهة Frontend:
   - `AuthLayout`, `LoginPage`, `ForgotPasswordPage`.
   - حماية الـ Routes.
   - Zustand store للمصادقة.
2. اختبار كامل للدخول والخروج ومصفوفة الصلاحيات.

---

### **المرحلة 2 — إدارة المدارس والمستخدمين (Week 3-4)**

**الأسبوع 3:**
1. CRUD كامل للمدارس (Backend + Frontend).
2. CRUD المستخدمين مع ربط الأدوار.
3. لوحة تحكم Super Admin الأساسية.

**الأسبوع 4:**
1. لوحة تحكم مدير المدرسة.
2. إضافة Global Scope للعزل.
3. اختبار متكامل لمصفوفة الصلاحيات.

---

### **المرحلة 3 — الطلاب والمعلمون (Week 5-6)**

**الأسبوع 5:**
1. جدول `students` + Model + Policies.
2. CRUD الطلاب (مع التشفير للهوية).
3. البحث المتقدم.
4. واجهة Teacher Dashboard + "طلابي".

**الأسبوع 6:**
1. جدول `portfolios` + Model.
2. رفع الملفات (مع التشويش).
3. ملف إنجاز المعلم.
4. ملف إنجاز الطالب.

---

### **المرحلة 4 — الخطط التعليمية الفردية (Week 7-9)**

**الأسبوع 7:**
1. جدول `iep_plans` (JSONB للأهداف).
2. Model + Observer لتوليد PDF تلقائي.
3. محرر الخطة (TipTap) في الواجهة.
4. حفظ كمسودة.

**الأسبوع 8:**
1. Workflow الاعتماد (State Machine).
2. واجهة مراجعة المدير.
3. واجهة مراجعة المشرف.
4. إشعارات عند كل انتقال حالة.

**الأسبوع 9:**
1. Export PDF مع الأختام الرقمية.
2. قوالب جاهزة لكل نوع إعاقة.
3. Versioning للخطة.
4. اختبارات End-to-End لدورة الاعتماد.

---

### **المرحلة 5 — الإشراف التربوي (Week 10-11)**

**الأسبوع 10:**
1. جدول `supervisor_visits`.
2. جدولة الزيارات.
3. نموذج تقييم ديناميكي.

**الأسبوع 11:**
1. خريطة تفاعلية لمدارس المشرف.
2. مؤشرات أداء القطاع.
3. تقارير الزيارات.

---

### **المرحلة 6 — التقارير والإحصائيات (Week 12)**

1. تقرير المدرسة الشامل (PDF).
2. تقرير الطالب الشامل (PDF).
3. تقرير المقارنة.
4. Export Excel.
5. رسوم بيانية (Recharts).

---

### **المرحلة 7 — الإشعارات والمراسلات (Week 13)**

1. نظام Notifications (Database + Broadcasting).
2. بريد إلكتروني (SMTP).
3. SMS (بوابة سعودية مثل Unifonic أو Oursms).
4. المراسلات بين المستخدمين.

---

### **المرحلة 8 — ولي الأمر (Week 14)**

1. لوحة تحكم ولي الأمر.
2. عرض الأبناء والتقارير.
3. المراسلة مع المعلم.

---

### **المرحلة 9 — الأمن والتحسين (Week 15)**

1. تدقيق أمني (Security Audit).
2. Rate Limiting + WAF.
3. Audit Log كامل.
4. اختبار الاختراق (Pen Test).
5. تحسين الأداء (Indexes، Caching، Lazy Loading).

---

### **المرحلة 10 — الاختبار والنشر (Week 16-17)**

**الأسبوع 16:**
1. اختبارات وحدة (Unit) لـ Backend.
2. اختبارات مكونات (Component) لـ Frontend.
3. اختبارات E2E (Playwright).
4. تصحيح الأخطاء.

**الأسبوع 17:**
1. نشر بيئة Staging.
2. تجارب مستخدم (UAT) مع قسم ذوي الإعاقة.
3. تصحيح ملاحظات.
4. نشر Production.
5. تدريب المستخدمين + توثيق.

---

# 12. هيكلة المجلدات والملفات

## 12.1 المشروع الكلّي

```
educational-disability-portal/
├── backend/                     # Laravel 11
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/
│   │   │   │       ├── AuthController.php
│   │   │   │       ├── SchoolController.php
│   │   │   │       ├── UserController.php
│   │   │   │       ├── StudentController.php
│   │   │   │       ├── TeacherController.php
│   │   │   │       ├── IepPlanController.php
│   │   │   │       ├── PortfolioController.php
│   │   │   │       ├── SupervisorVisitController.php
│   │   │   │       ├── FileController.php
│   │   │   │       ├── ReportController.php
│   │   │   │       ├── NotificationController.php
│   │   │   │       └── MessageController.php
│   │   │   ├── Middleware/
│   │   │   │   ├── CheckRole.php
│   │   │   │   ├── SetTenantScope.php
│   │   │   │   └── AuditLog.php
│   │   │   ├── Requests/        # Form Requests (Validation)
│   │   │   └── Resources/        # API Resources
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Role.php
│   │   │   ├── School.php
│   │   │   ├── Student.php
│   │   │   ├── IepPlan.php
│   │   │   ├── Portfolio.php
│   │   │   ├── SupervisorVisit.php
│   │   │   ├── File.php
│   │   │   ├── Notification.php
│   │   │   └── Message.php
│   │   ├── Policies/
│   │   ├── Services/             # Business Logic
│   │   │   ├── AuthService.php
│   │   │   ├── IepPlanService.php
│   │   │   ├── ReportService.php
│   │   │   └── FileUploadService.php
│   │   ├── Observers/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   └── Jobs/                 # Queued jobs
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   └── config/
│
├── frontend/                    # React 18 + TS
│   ├── src/
│   │   ├── main.tsx
│   │   ├── App.tsx
│   │   ├── router.tsx
│   │   ├── components/
│   │   │   ├── ui/              # shadcn components
│   │   │   ├── layout/
│   │   │   │   ├── AppShell.tsx
│   │   │   │   ├── Sidebar.tsx
│   │   │   │   └── Navbar.tsx
│   │   │   └── common/
│   │   │       ├── DataTable.tsx
│   │   │       ├── KpiCard.tsx
│   │   │       ├── FileUploader.tsx
│   │   │       └── RichEditor.tsx
│   │   ├── pages/
│   │   │   ├── auth/
│   │   │   │   ├── LoginPage.tsx
│   │   │   │   └── ForgotPasswordPage.tsx
│   │   │   ├── admin/
│   │   │   │   ├── DashboardPage.tsx
│   │   │   │   ├── SchoolsPage.tsx
│   │   │   │   └── UsersPage.tsx
│   │   │   ├── supervisor/
│   │   │   ├── principal/
│   │   │   ├── teacher/
│   │   │   │   ├── DashboardPage.tsx
│   │   │   │   ├── StudentsPage.tsx
│   │   │   │   ├── IepEditorPage.tsx
│   │   │   │   └── PortfolioPage.tsx
│   │   │   └── parent/
│   │   ├── hooks/
│   │   │   ├── useAuth.ts
│   │   │   ├── useStudents.ts
│   │   │   └── useIepPlans.ts
│   │   ├── stores/              # Zustand
│   │   │   ├── authStore.ts
│   │   │   └── uiStore.ts
│   │   ├── services/            # API clients
│   │   │   ├── api.ts
│   │   │   ├── authService.ts
│   │   │   └── studentService.ts
│   │   ├── types/               # TypeScript types
│   │   ├── utils/
│   │   ├── lib/
│   │   └── styles/
│   │       └── globals.css
│   ├── public/
│   ├── index.html
│   ├── vite.config.ts
│   ├── tailwind.config.js
│   └── tsconfig.json
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── postgres/
│       └── init.sql
│
├── infrastructure/
│   ├── terraform/                # اختياري للسحابة
│   └── scripts/
│       ├── deploy.sh
│       └── backup.sh
│
├── docs/
│   ├── PRD.md
│   ├── API.md
│   ├── DEPLOYMENT.md
│   └── USER_MANUAL_AR.md
│
├── docker-compose.yml
├── docker-compose.prod.yml
├── .gitignore
├── .env.example
└── README.md
```

---

# 13. الجدول الزمني ومراحل التنفيذ

## 13.1 الجدول الزمني المرئي (Gantt مبسط)

| الأسبوع | المرحلة | المخرجات |
|---|---|---|
| W0 | الإعداد | بيئة Docker + مستودع Git |
| W1-2 | المصادقة | تسجيل دخول كامل + RBAC |
| W3-4 | المدارس والمستخدمون | CRUD كامل + Dashboards |
| W5-6 | الطلاب والمعلمون | CRUD + ملف الإنجاز |
| W7-9 | الخطط الفردية | Workflow كامل + PDF |
| W10-11 | الإشراف التربوي | الزيارات + التقييم |
| W12 | التقارير | PDF + Excel + Charts |
| W13 | الإشعارات | DB + Email + SMS |
| W14 | ولي الأمر | Dashboard الأسرة |
| W15 | الأمن والتحسين | Pen Test + Optimization |
| W16-17 | الاختبار والنشر | UAT + Production |

**الإجمالي: ~17 أسبوع (≈ 4 أشهر)** لفريق من 3-4 مطورين.

## 13.2 توزيع الفريق المقترح

| الدور | عدد | المهمة الرئيسية |
|---|:---:|---|
| Backend Developer (Laravel) | 1-2 | API, DB, Security |
| Frontend Developer (React) | 1-2 | UI/UX, SPA |
| DevOps Engineer | 0.5 | Docker, CI/CD, Server |
| UI/UX Designer | 0.5 | Figma Mockups |
| QA Tester | 0.5 | اختبارات يدوية |
| Product Owner | 1 | متابعة المتطلبات |

---

# 14. الاختبار والنشر

## 14.1 استراتيجية الاختبار

### 14.1.1 اختبارات الوحدة (Unit Tests)
- **Backend (Pest/PHPUnit):** تغطية ≥ 70% للخدمات والـ Policies.
- **Frontend (Vitest):** Hooks + Utils.

### 14.1.2 اختبارات التكامل (Integration)
- Laravel Feature Tests لكل Endpoint.
- React Testing Library للمكونات.

### 14.1.3 اختبارات النهاية إلى النهاية (E2E)
- Playwright لتدفقات المستخدم الرئيسية:
  - دخول + خروج.
  - دورة اعتماد الخطة الفردية كاملة.
  - رفع ملف وتحميله.

### 14.1.4 اختبارات الأمان
- OWASP ZAP Scan.
- Manual Pen Test لأهم الوظائف.

### 14.1.5 اختبارات الأداء
- Apache Bench (ab) — 1000 concurrent users.
- Lighthouse Score ≥ 85 للواجهة.

## 14.2 خطة النشر (Deployment Plan)

### 14.2.1 البيئات (Environments)
```
Development  →  Staging  →  Production
  (local)      (pre-prod)    (live)
```

### 14.2.2 CI/CD Pipeline (GitHub Actions)
```yaml
on: push
jobs:
  1. lint-backend   (Pint + PHPStan)
  2. test-backend   (Pest)
  3. lint-frontend  (ESLint)
  4. test-frontend  (Vitest)
  5. build-docker
  6. deploy-staging (تلقائي لفرع dev)
  7. deploy-prod    (يدوي لفرع main)
```

### 14.2.3 خطوات النشر الأولي
1. إعداد الخادم (Ubuntu 22.04 LTS + Docker).
2. نطاق + SSL (Let's Encrypt).
3. قاعدة بيانات + Redis + MinIO.
4. بناء صور Docker ونشرها.
5. تنفيذ Migrations + Seeders.
6. إنشاء Super Admin الأولي.
7. اختبار الصحة (Health Check).
8. المراقبة (Sentry + Grafana).

### 14.2.4 خطة التعافي (Disaster Recovery)
- RTO (Recovery Time Objective): ≤ 4 ساعات.
- RPO (Recovery Point Objective): ≤ 24 ساعة.
- نسخ احتياطية خارج الموقع (Offsite).

---

# 15. المخاطر والتحديات

| # | الخطر | الاحتمال | الأثر | الحلول المقترحة |
|---|---|:---:|:---:|---|
| 1 | تأخر الحصول على بيانات حقيقية للاختبار | متوسط | متوسط | استخدام Factories + بيانات وهمية واقعية |
| 2 | تغيّر المتطلبات أثناء التطوير | عالي | عالي | Agile Sprints + مراجعات أسبوعية مع الجهة |
| 3 | ضعف البنية التحتية لدى المدارس | عالي | متوسط | تصميم متجاوب + تحسين للأداء على اتصالات بطيئة |
| 4 | مقاومة التغيير من المستخدمين | متوسط | عالي | تدريب مبكر + واجهة بسيطة + أدلة فيديو |
| 5 | هجمات أمنية | متوسط | عالي جداً | Pen Test دوري + WAF + مراقبة مستمرة |
| 6 | فقدان بيانات | منخفض | عالي جداً | نسخ احتياطية يومية + اختبار الاسترجاع |
| 7 | عدم الالتزام بـ PDPL | منخفض | عالي | مراجعة قانونية + سياسة خصوصية واضحة |

---

# 16. الصيانة والتطوير المستقبلي

## 16.1 خطة الصيانة

- **صيانة تصحيحية (Corrective):** إصلاح الأعطال خلال 48 ساعة.
- **صيانة وقائية (Preventive):** تحديثات أمنية شهرية.
- **صيانة تكييفية (Adaptive):** تحديثات PHP/Laravel/React سنوياً.

## 16.2 ميزات مقترحة للمرحلة الثانية

1. **تطبيق جوال Native** (React Native أو Flutter).
2. **ذكاء اصطناعي** لاقتراح الأهداف التعليمية حسب نوع الإعاقة.
3. **تكامل مع نور** (نظام التعليم الموحّد).
4. **مكتبة محتوى تعليمي** (فيديوهات + ألعاب تعليمية).
5. **مؤتمرات فيديو مدمجة** (Jitsi).
6. **لوحة معلومات BI متقدمة** (Metabase).
7. **Chatbot دعم فني** للمعلمين.

## 16.3 مؤشرات النجاح (KPIs) بعد الإطلاق

- عدد المدارس المسجلة خلال 3 أشهر: ≥ 80%.
- عدد المعلمين النشطين: ≥ 90%.
- عدد الخطط الفردية المكتملة إلكترونياً: ≥ 95%.
- رضا المستخدم (NPS): ≥ 8/10.
- Uptime: ≥ 99.5%.

---

# 🎯 الخلاصة

هذه الوثيقة **دليل تنفيذي كامل** يغطي:
1. ✅ الرؤية والأهداف والمستخدمون.
2. ✅ قاعدة بيانات تفصيلية (10+ جداول مع الحقول والعلاقات).
3. ✅ 60+ صفحة موزعة على 5 أدوار.
4. ✅ تقنيات محددة (React + Laravel + PostgreSQL + Docker).
5. ✅ جدول زمني واقعي (17 أسبوع).
6. ✅ استراتيجية أمنية متكاملة.
7. ✅ خطة نشر واختبار.
8. ✅ تحديد المخاطر وحلولها.

**الخطوة التالية:** مراجعة الوثيقة مع أصحاب المصلحة + اعتماد الجدول الزمني + تشكيل الفريق + بدء المرحلة صفر.

---

**تم إعداد هذه الوثيقة بـ ❤️ لخدمة قسم ذوي الإعاقة.**

*نهاية الوثيقة.*
