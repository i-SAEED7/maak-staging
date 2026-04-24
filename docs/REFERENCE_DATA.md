# البيانات المرجعية للنظام

هذه الملفات تمثل أول نسخة من البيانات المرجعية القابلة للتحميل لاحقًا عبر Seeders.

## الملفات

- [reference-data/roles.json](./reference-data/roles.json)
- [reference-data/disability-categories.json](./reference-data/disability-categories.json)
- [reference-data/education-programs.json](./reference-data/education-programs.json)
- [reference-data/iep-statuses.json](./reference-data/iep-statuses.json)
- [reference-data/supervision-visit-statuses.json](./reference-data/supervision-visit-statuses.json)
- [reference-data/enrollment-statuses.json](./reference-data/enrollment-statuses.json)
- [PERMISSIONS_MATRIX.md](./PERMISSIONS_MATRIX.md)

## الاستخدام المتوقع لاحقًا

1. قراءة JSON داخل `ReferenceDataSeeder`
2. إدخال السجلات المرجعية إلى الجداول
3. كشف هذه البيانات للواجهة عبر endpoint مثل:
   - `GET /api/v1/settings/reference-data`

## ملاحظات

- يمكن توسيع هذه القائمة لاحقًا بحالات الرسائل والإشعارات وأنواع التقارير.
- يفضل حفظ المفاتيح بالإنجليزية والقيم المرئية بالعربية.
