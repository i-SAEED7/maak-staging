# استراتيجية الـ Seeders

## الترتيب المقترح

1. `RoleSeeder`
2. `PermissionSeeder`
3. `RolePermissionSeeder`
4. `ReferenceDataSeeder`
5. `SuperAdminSeeder`

## مصادر البيانات

- `backend/database/seed-data/roles.json`
- `backend/database/seed-data/permissions.json`
- `backend/database/seed-data/mappings/role-permissions.json`
- `backend/database/seed-data/education-programs.json`
- `backend/database/seed-data/disability-categories.json`

## أسلوب التنفيذ

- استخدام `upsert` بدل insert خام
- جعل التشغيل متكررًا وآمنًا
- فصل البيانات المرجعية عن بيانات الحسابات الأولية
