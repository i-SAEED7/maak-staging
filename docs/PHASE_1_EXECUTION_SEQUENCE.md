# التسلسل التنفيذي العملي للمرحلة 1

## عند توفر الأدوات نفذ بهذا الترتيب

1. توليد Laravel داخل `backend`
2. نسخ تصميم الجداول إلى migrations
3. تنفيذ seeders المرجعية
4. إعداد Sanctum
5. تنفيذ `AuthController + AuthService + LoginRequest`
6. تنفيذ `Role/Permission` wiring
7. تنفيذ `TenantContext + SetTenantScope + BelongsToSchool`
8. تنفيذ `SchoolController + SchoolService + SchoolPolicy`
9. تنفيذ `UserController + UserService + UserPolicy`
10. اختبار مصفوفة الصلاحيات

## Definition of Done

- يمكن الدخول بالنظام
- الأدوار والصلاحيات تعمل
- إنشاء مدرسة يعمل
- إنشاء مستخدم يعمل
- عزل المدارس يعمل
