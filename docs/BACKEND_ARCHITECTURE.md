# هيكل الخلفية المقترح

## الهدف

تجهيز مشروع Laravel لاحقًا ضمن هيكل واضح منذ الآن، مع فصل الطبقات التالية:

- Controllers
- Requests
- Resources
- Services
- Policies
- Observers
- Jobs

## المبادئ

- الـ Controller نحيف ويمرر الطلب إلى Service.
- التحقق يتم في Form Request.
- الإخراج عبر API Resource.
- كل منطق الصلاحيات عبر Policy أو Permission.
- كل منطق العزل متعدد المدارس عبر Middleware + Scope + Policy.
- الأعمال الثقيلة عبر Queue Jobs.

## التوزيع المقترح

- `app/Http/Controllers/Api/V1`: نقاط دخول REST.
- `app/Http/Requests`: التحقق من البيانات.
- `app/Http/Resources`: توحيد الإخراج.
- `app/Models`: الكيانات الأساسية.
- `app/Services`: منطق الأعمال.
- `app/Policies`: التحقق من السماح.
- `app/Observers`: الآثار الجانبية المرتبطة بالنماذج.
- `app/Jobs`: مهام الخلفية مثل توليد PDF والإشعارات.

## الخدمات الأساسية المتوقعة

- `AuthService`
- `TenantService`
- `UserService`
- `SchoolService`
- `StudentService`
- `IepPlanService`
- `FileUploadService`
- `ReportService`
- `NotificationService`
- `MessagingService`
- `SupervisionService`

## أول Controllers مطلوبة

- `AuthController`
- `SchoolController`
- `UserController`
- `StudentController`
- `TeacherController`
- `PortfolioController`
- `IepPlanController`
- `StudentReportController`
- `FileController`
- `SupervisorVisitController`
- `NotificationController`
- `MessageController`
