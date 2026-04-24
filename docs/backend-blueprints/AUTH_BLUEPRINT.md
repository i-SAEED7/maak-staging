# Blueprint: Auth Module

## الهدف

تنفيذ مصادقة آمنة تدعم:

- تسجيل الدخول
- تسجيل الخروج
- المستخدم الحالي
- إعادة تعيين كلمة المرور عبر OTP
- تغيير كلمة المرور
- تتبع محاولات الدخول

## الطبقات المطلوبة

### Controller

- `AuthController@login`
- `AuthController@logout`
- `AuthController@me`
- `AuthController@forgotPassword`
- `AuthController@verifyResetOtp`
- `AuthController@resetPassword`
- `AuthController@changePassword`

### Requests

- `LoginRequest`
- `ForgotPasswordRequest`
- `VerifyResetOtpRequest`
- `ResetPasswordRequest`
- `ChangePasswordRequest`

### Service

- `AuthService`

### Supporting

- `LoginAttemptTracker`
- `OtpManager`
- `AuditLogger`

## Pseudo Flow: Login

1. استلام `identifier` و`password`
2. جلب المستخدم بالبريد أو الهاتف أو الهوية
3. رفض إذا كان غير نشط أو مقفلاً
4. التحقق من كلمة المرور
5. تسجيل محاولة الدخول
6. إنشاء token
7. تحديث `last_login_at` و`last_login_ip`
8. إعادة `user + permissions + tenant context`

## قواعد أمنية

- 5 محاولات فاشلة -> قفل 15 دقيقة
- تسجيل Audit Log عند الدخول والخروج
- invalid token revocation عند الخروج
