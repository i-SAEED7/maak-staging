# قواعد التحقق لوحدة المصادقة

## LoginRequest

- `identifier`
  - مطلوب
  - نص
  - حد أقصى 150

- `password`
  - مطلوب
  - نص
  - حد أدنى 8

## ForgotPasswordRequest

- `identifier`
  - مطلوب
  - نص
  - يجب أن يطابق مستخدمًا نشطًا

## ResetPasswordRequest

- `identifier`
  - مطلوب
- `otp`
  - مطلوب
  - طول 4 إلى 8
- `password`
  - مطلوب
  - حد أدنى 8
  - يجب أن يحتوي حرفًا كبيرًا وصغيرًا ورقمًا على الأقل
- `password_confirmation`
  - مطابق لكلمة المرور
