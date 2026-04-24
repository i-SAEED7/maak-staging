# تصميم قاعدة البيانات الأولية

هذا الملف يعرّف النسخة الأولية الكاملة لمخطط قاعدة البيانات تمهيدًا لتحويله إلى `Laravel migrations` لاحقًا.

## مبادئ التصميم

- قاعدة بيانات واحدة مع مخطط مشترك `Single Database, Shared Schema`.
- عزل بيانات المدارس عبر `school_id` في كل الجداول المدرسية.
- استخدام `UUID` للكيانات الحساسة أو المعروضة خارجياً.
- استخدام `JSONB` في PostgreSQL للحقول المرنة مثل الأهداف والتقييمات والبيانات الصحية.
- تفعيل `soft delete` منطقيًا حيث يلزم للحفاظ على الأثر التاريخي.
- عدم حذف الطلاب أو الخطط أو الزيارات حذفا نهائيا في السيناريو التشغيلي المعتاد.

## المجالات الرئيسية

1. الهوية والصلاحيات
2. المدارس والسنوات الدراسية
3. الطلاب وأولياء الأمور والمعلمون
4. الخطط التعليمية الفردية IEP
5. الملفات والأرشفة
6. الإشراف التربوي
7. التقارير الدورية
8. الإشعارات والمراسلات
9. التتبع الأمني وسجل العمليات

## الجداول الأساسية

### 1. `roles`
أدوار النظام الأساسية.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| name | varchar(50) unique | `super_admin`, `admin`, `supervisor`, `principal`, `teacher`, `parent` |
| display_name_ar | varchar(100) | |
| description | text nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 2. `permissions`
الصلاحيات الدقيقة إذا تم توسيع RBAC.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| key | varchar(100) unique | مثل `schools.view`, `students.update` |
| display_name_ar | varchar(150) | |
| module | varchar(50) | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 3. `role_permissions`
ربط الأدوار بالصلاحيات.

| الحقل | النوع | ملاحظات |
|---|---|---|
| role_id | bigint FK | `roles.id` |
| permission_id | bigint FK | `permissions.id` |
| granted_at | timestamp | |

### 4. `schools`
المدارس التي تعمل كوحدات مستأجرة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | معرّف خارجي |
| name_ar | varchar(255) | |
| name_en | varchar(255) nullable | |
| ministry_code | varchar(50) unique nullable | |
| region | varchar(100) | |
| city | varchar(100) | |
| district | varchar(100) nullable | |
| address | text nullable | |
| phone | varchar(30) nullable | |
| email | varchar(150) nullable | |
| latitude | numeric(10, 7) nullable | |
| longitude | numeric(10, 7) nullable | |
| status | varchar(20) | `active`, `inactive` |
| storage_quota_mb | integer default 2048 | |
| principal_user_id | bigint nullable | المستخدم الحالي لمدير المدرسة |
| metadata | jsonb nullable | بيانات إضافية |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp nullable | |

### 5. `academic_years`
السنوات والفصول الدراسية.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| school_id | bigint nullable | يسمح بسنة عامة أو خاصة بمدرسة |
| name_ar | varchar(100) | |
| starts_on | date | |
| ends_on | date | |
| is_active | boolean default false | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 6. `users`
المستخدمون الأساسيون للنظام.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| role_id | bigint FK | الدور الرئيسي |
| school_id | bigint nullable | مطلوب غالبًا لمدير المدرسة والمعلم |
| full_name | varchar(255) | |
| national_id_encrypted | text nullable | مشفر |
| email | varchar(150) unique nullable | |
| phone | varchar(30) unique nullable | |
| password_hash | varchar(255) | |
| status | varchar(20) | `active`, `inactive`, `locked` |
| last_login_at | timestamp nullable | |
| last_login_ip | inet nullable | |
| locale | varchar(10) default 'ar' | |
| must_change_password | boolean default false | |
| two_factor_enabled | boolean default false | |
| profile_photo_file_id | bigint nullable | |
| metadata | jsonb nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp nullable | |

### 7. `user_school_assignments`
يخدم حالات الإسناد المتعدد للمشرف أو الإدارة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| user_id | bigint FK | |
| school_id | bigint FK | |
| assignment_type | varchar(30) | `primary`, `supervising`, `supporting` |
| created_at | timestamp | |

### 8. `disability_categories`
مرجع أنواع الإعاقات.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| code | varchar(50) unique | |
| name_ar | varchar(150) | |
| description | text nullable | |
| is_active | boolean default true | |

### 9. `education_programs`
البرامج التعليمية ومسارات الخدمة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| code | varchar(50) unique | |
| name_ar | varchar(150) | |
| description | text nullable | |
| is_active | boolean default true | |

### 10. `students`
الكيان المركزي للطالب.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint FK | |
| academic_year_id | bigint nullable | |
| education_program_id | bigint nullable | |
| disability_category_id | bigint nullable | |
| primary_teacher_user_id | bigint nullable | |
| first_name | varchar(100) | |
| father_name | varchar(100) nullable | |
| grandfather_name | varchar(100) nullable | |
| family_name | varchar(100) | |
| full_name | varchar(255) | مخزن لسرعة البحث |
| national_id_encrypted | text nullable | |
| student_number | varchar(50) nullable | |
| gender | varchar(10) | |
| birth_date | date nullable | |
| grade_level | varchar(50) nullable | |
| classroom | varchar(50) nullable | |
| enrollment_status | varchar(20) | `active`, `graduated`, `transferred`, `archived` |
| medical_notes | jsonb nullable | |
| social_notes | jsonb nullable | |
| transportation_notes | text nullable | |
| joined_at | date nullable | |
| archived_at | timestamp nullable | |
| metadata | jsonb nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp nullable | |

### 11. `student_guardians`
ربط الطلاب بأولياء الأمور وحساباتهم.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| student_id | bigint FK | |
| parent_user_id | bigint FK | يجب أن يكون دوره `parent` |
| relationship | varchar(30) | `father`, `mother`, `guardian` |
| is_primary | boolean default false | |
| can_view_reports | boolean default true | |
| can_message_school | boolean default true | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 12. `teacher_student_assignments`
إسناد الطالب إلى معلم أو أكثر.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| school_id | bigint FK | |
| teacher_user_id | bigint FK | |
| student_id | bigint FK | |
| assigned_by_user_id | bigint nullable | |
| assignment_role | varchar(30) | `primary`, `secondary` |
| starts_on | date nullable | |
| ends_on | date nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 13. `portfolios`
ملفات الإنجاز للمعلمين والطلاب.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| school_id | bigint FK | |
| owner_user_id | bigint nullable | لمعلم أو مستخدم |
| student_id | bigint nullable | إذا كان الملف خاصًا بالطالب |
| type | varchar(30) | `teacher`, `student` |
| title | varchar(255) | |
| description | text nullable | |
| completion_rate | numeric(5,2) default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 14. `portfolio_items`
العناصر الفردية داخل ملف الإنجاز.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| portfolio_id | bigint FK | |
| school_id | bigint FK | |
| title | varchar(255) | |
| item_type | varchar(30) | `course`, `certificate`, `activity`, `evidence` |
| description | text nullable | |
| event_date | date nullable | |
| file_id | bigint nullable | |
| created_by_user_id | bigint nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 15. `iep_templates`
قوالب جاهزة حسب نوع الإعاقة أو البرنامج.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| disability_category_id | bigint nullable | |
| education_program_id | bigint nullable | |
| title | varchar(255) | |
| template_schema | jsonb | |
| is_active | boolean default true | |
| created_by_user_id | bigint nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 16. `iep_plans`
الرأس الرئيسي للخطة التعليمية الفردية.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint FK | |
| student_id | bigint FK | |
| academic_year_id | bigint nullable | |
| teacher_user_id | bigint FK | |
| principal_user_id | bigint nullable | |
| supervisor_user_id | bigint nullable | |
| current_version_number | integer default 1 | |
| status | varchar(30) | `draft`, `pending_principal_review`, `pending_supervisor_review`, `approved`, `rejected`, `archived` |
| title | varchar(255) | |
| start_date | date nullable | |
| end_date | date nullable | |
| summary | text nullable | |
| strengths | text nullable | |
| needs | text nullable | |
| accommodations | jsonb nullable | |
| generated_pdf_file_id | bigint nullable | |
| submitted_at | timestamp nullable | |
| approved_at | timestamp nullable | |
| rejected_at | timestamp nullable | |
| rejection_reason | text nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp nullable | |

### 17. `iep_plan_versions`
نسخ الخطة عبر الزمن.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| iep_plan_id | bigint FK | |
| school_id | bigint FK | |
| version_number | integer | |
| content_json | jsonb | النسخة الكاملة |
| change_summary | text nullable | |
| created_by_user_id | bigint FK | |
| created_at | timestamp | |

### 18. `iep_plan_goals`
الأهداف والمؤشرات في صورة قابلة للاستعلام.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| iep_plan_id | bigint FK | |
| school_id | bigint FK | |
| domain | varchar(100) | |
| goal_text | text | |
| measurement_method | text nullable | |
| baseline_value | varchar(100) nullable | |
| target_value | varchar(100) nullable | |
| due_date | date nullable | |
| sort_order | integer default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 19. `iep_plan_comments`
تعليقات المراجعة داخل الخطة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| iep_plan_id | bigint FK | |
| school_id | bigint FK | |
| author_user_id | bigint FK | |
| target_section | varchar(100) nullable | |
| comment_text | text | |
| is_internal | boolean default false | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 20. `iep_plan_approvals`
سجل انتقالات واعتمادات الخطة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| iep_plan_id | bigint FK | |
| school_id | bigint FK | |
| action_by_user_id | bigint FK | |
| action_role | varchar(30) | |
| from_status | varchar(30) nullable | |
| to_status | varchar(30) | |
| notes | text nullable | |
| created_at | timestamp | |

### 21. `student_reports`
التقارير الدورية للطالب خارج الخطة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| school_id | bigint FK | |
| student_id | bigint FK | |
| teacher_user_id | bigint FK | |
| report_type | varchar(30) | `weekly`, `monthly`, `term`, `annual` |
| report_period_label | varchar(100) | |
| content_json | jsonb | |
| summary | text nullable | |
| status | varchar(20) | `draft`, `published` |
| published_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 22. `files`
الفهرس المركزي للملفات.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint nullable | |
| uploaded_by_user_id | bigint nullable | |
| related_type | varchar(100) nullable | مثل `student`, `iep_plan`, `portfolio_item` |
| related_id | bigint nullable | |
| category | varchar(30) | `medical`, `educational`, `administrative`, `general` |
| original_name | varchar(255) | |
| storage_name | varchar(255) unique | |
| storage_disk | varchar(50) | |
| storage_path | text | |
| mime_type | varchar(150) | |
| extension | varchar(20) nullable | |
| size_bytes | bigint | |
| checksum_sha256 | varchar(64) nullable | |
| is_sensitive | boolean default false | |
| visibility | varchar(20) | `private`, `school`, `guardians` |
| uploaded_at | timestamp | |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp nullable | |

### 23. `file_access_tokens`
توكنات التحميل المؤقتة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| file_id | bigint FK | |
| token_hash | varchar(255) | |
| issued_to_user_id | bigint nullable | |
| expires_at | timestamp | |
| consumed_at | timestamp nullable | |
| created_at | timestamp | |

### 24. `supervision_templates`
قوالب تقييم قابلة للإعداد.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| title | varchar(255) | |
| criteria_schema | jsonb | |
| is_active | boolean default true | |
| created_by_user_id | bigint nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 25. `supervisor_visits`
زيارات المشرفين للمدارس.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint FK | |
| supervisor_user_id | bigint FK | |
| template_id | bigint nullable | |
| visit_date | date | |
| visit_status | varchar(20) | `scheduled`, `completed`, `cancelled` |
| agenda | text nullable | |
| summary | text nullable | |
| overall_score | numeric(5,2) nullable | |
| next_follow_up_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 26. `supervisor_visit_items`
بنود التقييم في الزيارة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| visit_id | bigint FK | |
| school_id | bigint FK | |
| criterion_key | varchar(100) | |
| criterion_label | varchar(255) | |
| score | numeric(5,2) nullable | |
| remarks | text nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 27. `supervisor_visit_recommendations`
توصيات ومتابعة التنفيذ.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| visit_id | bigint FK | |
| school_id | bigint FK | |
| recommendation_text | text | |
| owner_user_id | bigint nullable | |
| due_date | date nullable | |
| status | varchar(20) | `open`, `in_progress`, `done`, `cancelled` |
| completed_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 28. `notifications`
الإشعارات داخل النظام وقنوات الإرسال.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint nullable | |
| user_id | bigint FK | المستلم |
| type | varchar(50) | |
| channel | varchar(20) | `in_app`, `email`, `sms` |
| title | varchar(255) | |
| body | text | |
| data | jsonb nullable | |
| read_at | timestamp nullable | |
| sent_at | timestamp nullable | |
| failed_at | timestamp nullable | |
| created_at | timestamp | |

### 29. `messages`
المحادثات والرسائل المباشرة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| uuid | uuid unique | |
| school_id | bigint nullable | |
| thread_key | varchar(100) | مفتاح التجميع |
| sender_user_id | bigint FK | |
| subject | varchar(255) nullable | |
| body | text | |
| parent_message_id | bigint nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 30. `message_recipients`
المستلمون وحالة القراءة.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| message_id | bigint FK | |
| recipient_user_id | bigint FK | |
| read_at | timestamp nullable | |
| created_at | timestamp | |

### 31. `audit_logs`
سجل العمليات الحساس.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| school_id | bigint nullable | |
| user_id | bigint nullable | |
| action | varchar(100) | |
| target_type | varchar(100) nullable | |
| target_id | bigint nullable | |
| method | varchar(10) nullable | |
| endpoint | text nullable | |
| ip_address | inet nullable | |
| user_agent | text nullable | |
| old_values | jsonb nullable | |
| new_values | jsonb nullable | |
| created_at | timestamp | |

### 32. `login_attempts`
تتبع محاولات الدخول والإقفال.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| identifier | varchar(150) | بريد أو هوية أو هاتف |
| ip_address | inet nullable | |
| user_agent | text nullable | |
| success | boolean | |
| attempted_at | timestamp | |
| locked_until | timestamp nullable | |

### 33. `password_reset_otps`
رموز OTP الخاصة بإعادة التعيين.

| الحقل | النوع | ملاحظات |
|---|---|---|
| id | bigserial PK | |
| user_id | bigint FK | |
| code_hash | varchar(255) | |
| expires_at | timestamp | |
| consumed_at | timestamp nullable | |
| created_at | timestamp | |

## العلاقات المحورية

- `schools` 1..* `users`
- `schools` 1..* `students`
- `students` *..* `users` عبر `student_guardians`
- `students` *..* `users` عبر `teacher_student_assignments`
- `students` 1..* `iep_plans`
- `iep_plans` 1..* `iep_plan_versions`
- `iep_plans` 1..* `iep_plan_goals`
- `iep_plans` 1..* `iep_plan_comments`
- `iep_plans` 1..* `iep_plan_approvals`
- `schools` 1..* `supervisor_visits`
- `supervisor_visits` 1..* `supervisor_visit_items`
- `supervisor_visits` 1..* `supervisor_visit_recommendations`

## الفهارس المقترحة

- `users(role_id, school_id, status)`
- `students(school_id, enrollment_status, disability_category_id)`
- `students USING gin(to_tsvector('simple', full_name))`
- `iep_plans(school_id, student_id, status)`
- `files(school_id, related_type, related_id)`
- `notifications(user_id, read_at)`
- `messages(thread_key, created_at)`
- `audit_logs(user_id, created_at)`

## قيود تشغيلية مهمة

- كل جدول مدرسي يجب أن يحمل `school_id` إلا إذا كان مرجعياً عامًا.
- الحقول الحساسة مثل رقم الهوية تخزن بشكل مشفر على مستوى التطبيق.
- لا يسمح لولي الأمر بالوصول إلا عبر الربط الفعلي في `student_guardians`.
- حالة الخطة لا تُحدّث مباشرة دون تسجيل مدخل في `iep_plan_approvals`.

## الملفات المرتبطة

- SQL أولي: [sql/001_initial_schema.sql](./sql/001_initial_schema.sql)
- مواصفات API: [API_SPEC.md](./API_SPEC.md)
