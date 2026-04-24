# تنفيذ التحكم بالوصول

## طبقات الحماية

1. `permission checks`
2. `policy checks`
3. `tenant checks`
4. `record ownership checks`

## مصدر البيانات

- الصلاحيات الأساسية: [PERMISSIONS_MATRIX.md](./PERMISSIONS_MATRIX.md)
- ربط الأدوار بالصلاحيات: [../backend/database/seed-data/mappings/role-permissions.json](../backend/database/seed-data/mappings/role-permissions.json)

## قواعد مهمة

- `super_admin` يمكن منحه wildcard داخليًا.
- بقية الأدوار لا تعتمد فقط على permission string، بل أيضًا على السياق.
- `teacher` و`parent` يحتاجان تحقق ملكية أو إسناد فوق الصلاحية العامة.
