# مصفوفة الصلاحيات التفصيلية

هذا الملف يترجم الأدوار العامة إلى صلاحيات دقيقة قابلة للتحويل لاحقًا إلى `permissions seeder`.

## الأدوار المعتمدة

- `super_admin`
- `admin`
- `supervisor`
- `principal`
- `teacher`
- `parent`

## الوحدات

- `auth`
- `schools`
- `users`
- `students`
- `teacher_assignments`
- `portfolios`
- `iep`
- `student_reports`
- `files`
- `supervision`
- `reports`
- `notifications`
- `messages`
- `audit_logs`
- `reference_data`

## الصلاحيات الدقيقة

| المفتاح | الوصف |
|---|---|
| `schools.view_any` | عرض قائمة المدارس |
| `schools.view` | عرض مدرسة واحدة |
| `schools.create` | إنشاء مدرسة |
| `schools.update` | تعديل مدرسة |
| `schools.change_status` | تغيير حالة المدرسة |
| `schools.assign_principal` | إسناد مدير مدرسة |
| `schools.assign_supervisor` | إسناد مشرف |
| `users.view_any` | عرض المستخدمين |
| `users.view` | عرض مستخدم |
| `users.create` | إنشاء مستخدم |
| `users.update` | تعديل مستخدم |
| `users.change_status` | تعطيل أو تفعيل مستخدم |
| `users.assign_school` | ربط مستخدم بمدرسة |
| `students.view_any` | عرض الطلاب |
| `students.view` | عرض طالب |
| `students.create` | إنشاء طالب |
| `students.update` | تعديل طالب |
| `students.archive` | أرشفة طالب |
| `students.assign_guardian` | ربط ولي أمر بالطالب |
| `teacher_assignments.manage` | إدارة إسناد الطلاب للمعلمين |
| `portfolios.view_any` | عرض ملفات الإنجاز |
| `portfolios.view` | عرض ملف إنجاز |
| `portfolios.create` | إنشاء ملف إنجاز |
| `portfolios.update` | تعديل ملف إنجاز |
| `portfolios.add_item` | إضافة عنصر إلى ملف الإنجاز |
| `portfolios.delete_item` | حذف عنصر من ملف الإنجاز |
| `iep.view_any` | عرض الخطط |
| `iep.view` | عرض خطة |
| `iep.create` | إنشاء خطة |
| `iep.update` | تعديل خطة |
| `iep.submit` | إرسال الخطة للمراجعة |
| `iep.principal_approve` | اعتماد المدير |
| `iep.supervisor_approve` | اعتماد المشرف |
| `iep.reject` | رفض الخطة |
| `iep.comment` | التعليق على الخطة |
| `iep.view_versions` | عرض إصدارات الخطة |
| `iep.download_pdf` | تنزيل PDF الخطة |
| `student_reports.view_any` | عرض التقارير الدورية |
| `student_reports.view` | عرض تقرير دوري |
| `student_reports.create` | إنشاء تقرير دوري |
| `student_reports.update` | تعديل تقرير دوري |
| `student_reports.publish` | نشر التقرير |
| `files.upload` | رفع ملف |
| `files.view` | عرض بيانات ملف |
| `files.download` | تحميل ملف |
| `files.delete` | حذف منطقي لملف |
| `supervision.view_any` | عرض الزيارات |
| `supervision.view` | عرض زيارة |
| `supervision.create` | إنشاء زيارة |
| `supervision.update` | تعديل زيارة |
| `supervision.complete` | إغلاق زيارة |
| `supervision.add_recommendation` | إضافة توصية |
| `supervision.update_recommendation` | تحديث حالة توصية |
| `reports.school_summary` | تقرير مدرسة |
| `reports.student_summary` | تقرير طالب |
| `reports.comparison` | تقرير مقارنة |
| `reports.pivot` | تقرير محوري |
| `reports.export_pdf` | تصدير PDF |
| `reports.export_excel` | تصدير Excel |
| `notifications.view_any` | عرض الإشعارات |
| `notifications.mark_read` | تعليم إشعار كمقروء |
| `messages.view_any` | عرض الرسائل |
| `messages.view_thread` | عرض سلسلة رسائل |
| `messages.send` | إرسال رسالة |
| `messages.mark_read` | تعليم رسالة كمقروءة |
| `audit_logs.view_any` | عرض سجل العمليات |
| `reference_data.manage` | إدارة البيانات المرجعية |

## توزيع الصلاحيات على الأدوار

### `super_admin`

- جميع الصلاحيات.

### `admin`

- `schools.view_any`
- `schools.view`
- `users.view_any`
- `users.view`
- `students.view_any`
- `students.view`
- `reports.school_summary`
- `reports.student_summary`
- `reports.comparison`
- `reports.pivot`
- `notifications.view_any`

### `supervisor`

- `schools.view_any`
- `schools.view`
- `students.view_any`
- `students.view`
- `iep.view_any`
- `iep.view`
- `iep.comment`
- `iep.supervisor_approve`
- `iep.reject`
- `iep.view_versions`
- `iep.download_pdf`
- `supervision.view_any`
- `supervision.view`
- `supervision.create`
- `supervision.update`
- `supervision.complete`
- `supervision.add_recommendation`
- `supervision.update_recommendation`
- `reports.school_summary`
- `reports.student_summary`
- `reports.comparison`
- `reports.pivot`
- `reports.export_pdf`
- `reports.export_excel`
- `notifications.view_any`
- `notifications.mark_read`
- `messages.view_any`
- `messages.view_thread`
- `messages.send`
- `messages.mark_read`

### `principal`

- `schools.view`
- `users.view_any`
- `users.view`
- `users.create`
- `users.update`
- `students.view_any`
- `students.view`
- `students.update`
- `iep.view_any`
- `iep.view`
- `iep.comment`
- `iep.principal_approve`
- `iep.reject`
- `iep.view_versions`
- `iep.download_pdf`
- `portfolios.view_any`
- `portfolios.view`
- `reports.school_summary`
- `reports.student_summary`
- `reports.export_pdf`
- `reports.export_excel`
- `notifications.view_any`
- `notifications.mark_read`
- `messages.view_any`
- `messages.view_thread`
- `messages.send`
- `messages.mark_read`

### `teacher`

- `students.view_any`
- `students.view`
- `students.create`
- `students.update`
- `students.assign_guardian`
- `teacher_assignments.manage`
- `portfolios.view_any`
- `portfolios.view`
- `portfolios.create`
- `portfolios.update`
- `portfolios.add_item`
- `portfolios.delete_item`
- `iep.view_any`
- `iep.view`
- `iep.create`
- `iep.update`
- `iep.submit`
- `iep.view_versions`
- `iep.download_pdf`
- `student_reports.view_any`
- `student_reports.view`
- `student_reports.create`
- `student_reports.update`
- `student_reports.publish`
- `files.upload`
- `files.view`
- `files.download`
- `notifications.view_any`
- `notifications.mark_read`
- `messages.view_any`
- `messages.view_thread`
- `messages.send`
- `messages.mark_read`

### `parent`

- `students.view`
- `iep.view`
- `iep.download_pdf`
- `student_reports.view`
- `files.view`
- `files.download`
- `notifications.view_any`
- `notifications.mark_read`
- `messages.view_any`
- `messages.view_thread`
- `messages.send`
- `messages.mark_read`

## ملاحظات تنفيذية

- هذه الصلاحيات لا تكفي وحدها دون `policies`.
- بعض الصلاحيات تحتاج أيضًا تحققًا سياقيًا:
  - ولي الأمر يجب أن يكون مرتبطًا فعليًا بالطالب.
  - المعلم يرى فقط الطلاب المسندين إليه.
  - المشرف يرى المدارس المسندة له فقط.
  - مدير المدرسة يعمل داخل مدرسته فقط.
