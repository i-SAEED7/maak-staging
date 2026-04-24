# مخطط واجهات برمجة التطبيقات API

المعيار المعتمد هو REST JSON تحت المسار:

`/api/v1`

## معايير عامة

- المصادقة: `Bearer token` عبر Sanctum لاحقًا.
- الاستجابة القياسية:

```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {},
  "meta": {}
}
```

- استجابة الأخطاء:

```json
{
  "success": false,
  "message": "فشل التحقق من صحة البيانات",
  "errors": {
    "field": ["رسالة خطأ"]
  }
}
```

- الترقيم: `page`, `per_page`
- التصفية: `filter[...]`
- الترتيب: `sort=-created_at`
- التضمين الاختياري: `include=school,role`

## 1. المصادقة والحساب

### `POST /auth/login`
تسجيل الدخول بالبريد أو رقم الهوية.

الطلب:

```json
{
  "identifier": "user@example.com",
  "password": "secret"
}
```

الاستجابة:

```json
{
  "success": true,
  "message": "تم تسجيل الدخول",
  "data": {
    "token": "plain-text-token",
    "user": {
      "id": 1,
      "full_name": "أحمد محمد",
      "role": "super_admin"
    }
  }
}
```

### `POST /auth/logout`
إنهاء الجلسة الحالية.

### `GET /auth/me`
إرجاع المستخدم الحالي وصلاحياته والمدرسة الحالية.

### `POST /auth/forgot-password`
بدء رحلة OTP لإعادة تعيين كلمة المرور.

### `POST /auth/verify-reset-otp`
التحقق من الرمز.

### `POST /auth/reset-password`
ضبط كلمة مرور جديدة.

### `POST /auth/change-password`
تغيير كلمة المرور بعد تسجيل الدخول.

## 2. المدارس

### `GET /schools`
قائمة المدارس مع الفلاتر.

فلاتر مقترحة:
- `filter[status]`
- `filter[region]`
- `filter[supervisor_user_id]`

### `POST /schools`
إنشاء مدرسة.

### `GET /schools/{id}`
تفاصيل مدرسة مع الإحصاءات.

### `PUT /schools/{id}`
تحديث مدرسة.

### `PATCH /schools/{id}/status`
تغيير الحالة.

### `GET /schools/{id}/stats`
إحصاءات المدرسة.

### `POST /schools/{id}/assign-principal`
إسناد مدير مدرسة.

### `POST /schools/{id}/assign-supervisor`
إسناد مشرف.

## 3. المستخدمون

### `GET /users`
فلاتر:
- `filter[role]`
- `filter[school_id]`
- `filter[status]`
- `filter[q]`

### `POST /users`
إنشاء مستخدم.

### `GET /users/{id}`
تفاصيل مستخدم.

### `PUT /users/{id}`
تحديث مستخدم.

### `PATCH /users/{id}/status`
تعطيل أو تفعيل.

### `POST /users/{id}/schools`
ربط المستخدم بمدرسة أو أكثر.

### `GET /roles`
قائمة الأدوار.

### `GET /permissions`
قائمة الصلاحيات.

## 4. الطلاب

### `GET /students`
فلاتر:
- `filter[school_id]`
- `filter[teacher_user_id]`
- `filter[disability_category_id]`
- `filter[education_program_id]`
- `filter[enrollment_status]`
- `filter[q]`

### `POST /students`
إنشاء طالب.

### `GET /students/{id}`
تفاصيل الطالب.

### `PUT /students/{id}`
تحديث بيانات الطالب.

### `PATCH /students/{id}/archive`
أرشفة الطالب.

### `GET /students/{id}/guardians`
قائمة أولياء الأمر.

### `POST /students/{id}/guardians`
ربط ولي أمر.

### `GET /students/{id}/reports`
التقارير الدورية.

### `GET /students/{id}/iep-plans`
الخطط التعليمية.

## 5. المعلمون والإسناد

### `GET /teachers`
قائمة المعلمين.

### `GET /teachers/{id}/students`
طلاب المعلم.

### `POST /teacher-student-assignments`
إسناد طالب إلى معلم.

### `DELETE /teacher-student-assignments/{id}`
إنهاء الإسناد.

## 6. ملفات الإنجاز

### `GET /portfolios`
قائمة ملفات الإنجاز.

### `POST /portfolios`
إنشاء ملف إنجاز.

### `GET /portfolios/{id}`
تفاصيل الملف.

### `POST /portfolios/{id}/items`
إضافة عنصر جديد.

### `PUT /portfolio-items/{id}`
تحديث عنصر.

### `DELETE /portfolio-items/{id}`
حذف منطقي لعنصر.

## 7. الخطط التعليمية الفردية IEP

### `GET /iep-plans`
فلاتر:
- `filter[school_id]`
- `filter[student_id]`
- `filter[teacher_user_id]`
- `filter[status]`

### `POST /iep-plans`
إنشاء خطة جديدة أو مسودة.

### `GET /iep-plans/{id}`
تفاصيل الخطة الحالية.

### `PUT /iep-plans/{id}`
تحديث المسودة الحالية.

### `POST /iep-plans/{id}/submit`
إرسال للمدير.

### `POST /iep-plans/{id}/principal-approve`
اعتماد المدير.

### `POST /iep-plans/{id}/supervisor-approve`
اعتماد المشرف.

### `POST /iep-plans/{id}/reject`
رفض الخطة مع سبب.

### `GET /iep-plans/{id}/versions`
عرض الإصدارات.

### `POST /iep-plans/{id}/comments`
إضافة تعليق.

### `GET /iep-plans/{id}/pdf`
الحصول على ملف PDF المعتمد.

## 8. التقارير الدورية

### `GET /student-reports`
قائمة التقارير.

### `POST /student-reports`
إنشاء تقرير.

### `GET /student-reports/{id}`
عرض تقرير.

### `PUT /student-reports/{id}`
تعديل تقرير.

### `POST /student-reports/{id}/publish`
نشر التقرير لولي الأمر.

## 9. الملفات

### `POST /files`
رفع ملف.

### `GET /files/{id}`
تفاصيل ملف.

### `POST /files/{id}/temporary-link`
إنشاء رابط مؤقت.

### `DELETE /files/{id}`
حذف منطقي.

## 10. الإشراف التربوي

### `GET /supervisor-visits`
قائمة الزيارات.

### `POST /supervisor-visits`
جدولة زيارة.

### `GET /supervisor-visits/{id}`
تفاصيل الزيارة.

### `PUT /supervisor-visits/{id}`
تعديل الزيارة.

### `POST /supervisor-visits/{id}/complete`
إغلاق الزيارة بعد تعبئة التقييم.

### `POST /supervisor-visits/{id}/recommendations`
إضافة توصية.

### `PATCH /supervisor-visit-recommendations/{id}`
تحديث حالة التوصية.

### `GET /supervision-templates`
قوالب التقييم.

## 11. التقارير والإحصاءات

### `GET /reports/schools/{id}/summary`
تقرير مدرسة شامل.

### `GET /reports/students/{id}/summary`
تقرير طالب شامل.

### `GET /reports/comparison`
مقارنة بين مدارس أو برامج.

### `GET /reports/pivot`
تقرير محوري حسب الفلاتر.

### `GET /reports/export/pdf`
تصدير PDF.

### `GET /reports/export/excel`
تصدير Excel.

## 12. الإشعارات

### `GET /notifications`
إشعارات المستخدم الحالي.

### `POST /notifications/{id}/read`
تعليم كمقروء.

### `POST /notifications/read-all`
تعليم الكل كمقروء.

## 13. الرسائل

### `GET /messages`
قائمة المحادثات.

### `GET /messages/thread/{threadKey}`
عرض سلسلة محادثة.

### `POST /messages`
إرسال رسالة.

### `POST /messages/{id}/read`
تعليم رسالة كمقروءة.

## 14. السجلات والإدارة

### `GET /audit-logs`
للمدير العام فقط.

### `GET /settings/reference-data`
البيانات المرجعية للواجهة.

## ترابط الأمان

- كل endpoint بعد المصادقة يمر عبر:
  - `auth`
  - `setTenant`
  - `role/permission checks`
  - `policy checks`

## الملفات المرتبطة

- مخطط القاعدة: [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
- أولوية التنفيذ القادمة: [NEXT_PHASE_PRIORITIES.md](./NEXT_PHASE_PRIORITIES.md)
