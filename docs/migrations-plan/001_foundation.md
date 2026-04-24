# Migration Plan 001: Foundation

## الملفات

1. create_roles_table
2. create_permissions_table
3. create_role_permissions_table
4. create_schools_table
5. create_academic_years_table
6. create_users_table
7. create_user_school_assignments_table

## ملاحظات

- يفضل إنشاء roles وpermissions مبكرًا لأن بقية النظام تعتمد عليها.
- علاقة `schools.principal_user_id` قد تحتاج migration لاحقًا بعد users.
