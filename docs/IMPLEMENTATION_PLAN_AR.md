# خطة عمل تنفيذية لإكمال ونشر منصة المتابعة التعليمية والإشرافية

# Execution Work Plan for Completing and Deploying the Educational Supervision Platform

## مقدمة / Introduction

هذه الوثيقة هي خطة عمل تنفيذية مقسمة إلى أجزاء واضحة. كل جزء يوضح:

- ماذا سنفعل.
- لماذا نفعله.
- الملفات أو الوحدات المتوقع العمل عليها.
- المخرجات المطلوبة.
- معيار القبول قبل الانتقال للجزء التالي.

This document is an execution work plan divided into clear parts. Each part explains:

- What we will do.
- Why we will do it.
- Expected files or modules to work on.
- Required deliverables.
- Acceptance criteria before moving to the next part.

## هدف النظام / Product Goal

بناء منصة تساعد مدير النظام، مدير المدرسة، المشرف، المعلم، وولي الأمر على إدارة الطلاب كبيانات، إنشاء الخطط التعليمية، اعتمادها، متابعتها، إرسال الرسائل الداخلية، استقبال الإشعارات، وإدارة الملفات.

The goal is to build a platform that helps the system admin, school principal, supervisor, teacher, and guardian manage student records, educational plans, approvals, follow-up, internal messages, notifications, and files.

## نطاق النسخة الأولى / First Release Scope

### داخل النطاق / In Scope

- تسجيل الدخول حسب الدور.
- إدارة المدارس.
- إدارة المستخدمين.
- إدارة الطلاب كبيانات.
- ربط الطالب بالمعلم.
- ربط الطالب بولي الأمر.
- إنشاء الخطة التعليمية من المعلم.
- اعتماد الخطة من مدير المدرسة.
- مراجعة واعتماد الخطة من المشرف.
- إشعارات داخلية.
- رسائل داخلية.
- ملفات ومرفقات.
- تقارير تشغيلية بسيطة.
- تجربة ولي الأمر لعرض الأبناء والخطط المعتمدة فقط.
- نشر النظام على رابط حقيقي لتجربة فريق العمل.

- Role-based login.
- School management.
- User management.
- Student records management.
- Assigning students to teachers.
- Linking students to guardians.
- Teacher-created educational plans.
- Principal approval.
- Supervisor review and final approval.
- Internal notifications.
- Internal messages.
- Files and attachments.
- Simple operational reports.
- Guardian experience for viewing children and approved plans only.
- Deployment to a real URL for team testing.

### خارج النطاق / Out of Scope

- لا يوجد حساب طالب.
- لا يوجد بريد إلكتروني.
- لا توجد رسوم أو فواتير أو مدفوعات.
- لا توجد درجات أو شهادات أو كشوف درجات.
- لا توجد جداول دراسية.
- لا يوجد حضور.
- لا يوجد HR أو رواتب.
- لا توجد صفحات وهمية أو أزرار لا تعمل.
- لا توجد بيانات ثابتة داخل الواجهة النهائية.

- No student login account.
- No email notifications.
- No fees, invoices, or payments.
- No grades, certificates, or transcripts.
- No timetable module.
- No attendance module.
- No HR or payroll.
- No fake pages or non-working buttons.
- No hardcoded fake data in the final UI.

---

## الجزء 1: تثبيت نطاق المنتج والقرارات الأساسية

## Part 1: Confirm Product Scope and Core Decisions

### ماذا سنفعل / What We Will Do

- تثبيت أن النظام ليس ERP مالي ولا نظام درجات.
- تثبيت أن الطالب لا يملك حساب دخول.
- تثبيت أن ولي الأمر هو من يدخل لعرض بيانات أبنائه.
- تثبيت أن الإشعارات ستكون داخل النظام فقط.
- تثبيت مسار الخطة التعليمية: معلم، مدير مدرسة، مشرف.
- تثبيت الأدوار النهائية والصلاحيات العامة.

- Confirm that the system is not a finance ERP or gradebook system.
- Confirm that students do not have login accounts.
- Confirm that guardians log in to view their children.
- Confirm that notifications are internal only.
- Confirm the educational plan workflow: teacher, principal, supervisor.
- Confirm final roles and high-level permissions.

### الأدوار المعتمدة / Approved Roles

| الدور | Role | نوع الحساب | Account Type | النطاق | Scope |
|---|---|---|---|---|---|
| مدير النظام | System Admin | حساب دخول | Login account | كل النظام | Whole system |
| مدير المدرسة | School Principal | حساب دخول | Login account | مدرسته فقط | Own school only |
| المشرف | Supervisor | حساب دخول | Login account | المدارس المسندة له | Assigned schools |
| المعلم | Teacher | حساب دخول | Login account | طلابه فقط | Own students only |
| ولي الأمر | Guardian | حساب دخول | Login account | أبناؤه فقط | Own children only |
| الطالب | Student | لا يوجد حساب | No account | بيانات فقط | Data record only |

### المخرجات / Deliverables

- اعتماد نطاق النسخة الأولى.
- اعتماد الأدوار.
- اعتماد ما هو خارج النطاق.
- اعتماد خريطة سير الخطة التعليمية.

- Approved first release scope.
- Approved roles.
- Approved out-of-scope list.
- Approved educational plan workflow.

### معيار القبول / Acceptance Criteria

- لا يبدأ التطوير قبل اعتماد هذه القرارات.
- أي ميزة جديدة يجب أن تخدم أحد الأدوار الأساسية مباشرة.

- Development does not start before these decisions are approved.
- Any new feature must directly serve one of the core roles.

---

## الجزء 2: تشغيل المشروع وتدقيق الحالة الحالية

## Part 2: Run the Project and Audit the Current State

### ماذا سنفعل / What We Will Do

- تشغيل backend محليا.
- تشغيل frontend محليا.
- تشغيل migrations من الصفر.
- تشغيل seeders الأساسية.
- تشغيل build للواجهة.
- تشغيل الاختبارات الموجودة.
- استخراج قائمة API routes الفعلية.
- مقارنة مسارات الواجهة مع مسارات الخلفية.
- تحديد الصفحات غير المكتملة.
- تحديد الاختبارات المتخطاة أو المكسورة.

- Run the backend locally.
- Run the frontend locally.
- Run migrations from scratch.
- Run essential seeders.
- Build the frontend.
- Run existing tests.
- Extract actual API routes.
- Compare frontend routes/services with backend routes.
- Identify incomplete pages.
- Identify skipped or failing tests.

### الملفات والوحدات المتوقعة / Expected Files and Modules

- `backend/routes/api.php`
- `backend/routes/web.php`
- `backend/database/migrations`
- `backend/database/seeders`
- `backend/tests`
- `frontend/src/router.tsx`
- `frontend/src/services`
- `frontend/src/pages`
- `frontend/package.json`
- `backend/composer.json`

### المخرجات / Deliverables

- تقرير حالة تقني مختصر.
- قائمة أخطاء مؤكدة.
- قائمة صفحات مكتملة.
- قائمة صفحات تحتاج تنفيذ.
- قائمة صفحات يجب إخفاؤها مؤقتا.
- قائمة اختبارات يجب إصلاحها.

- Short technical status report.
- Confirmed bug list.
- Completed pages list.
- Pages requiring implementation.
- Pages to temporarily hide.
- Tests requiring fixes.

### معيار القبول / Acceptance Criteria

- نعرف بالضبط ما يعمل وما لا يعمل.
- لا تتم إضافة ميزات جديدة قبل إنهاء هذا التدقيق.

- We know exactly what works and what does not.
- No new features are added before completing this audit.

---

## الجزء 3: إصلاح تسجيل الدخول وتوجيه المستخدمين

## Part 3: Fix Authentication and Role-Based Routing

### ماذا سنفعل / What We Will Do

- اعتماد مسارات دخول واحدة وواضحة.
- إزالة أو تجاهل أي مسارات قديمة غير مستخدمة.
- إصلاح الاختبارات التي تستخدم مسارات قديمة.
- إصلاح تخزين التوكن في الواجهة.
- إصلاح جلب بيانات المستخدم الحالي.
- توجيه المستخدم بعد الدخول حسب دوره.

- Standardize the login API routes.
- Remove or ignore old unused auth routes.
- Fix tests using outdated routes.
- Fix token storage on the frontend.
- Fix current-user loading.
- Redirect users after login based on role.

### المسارات المعتمدة / Approved Routes

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`

### التوجيه بعد الدخول / Post-Login Redirects

| الدور | Role | الوجهة |
|---|---|---|
| مدير النظام | System Admin | لوحة مدير النظام |
| مدير المدرسة | School Principal | لوحة مدير المدرسة |
| المشرف | Supervisor | لوحة المشرف |
| المعلم | Teacher | لوحة المعلم |
| ولي الأمر | Guardian | لوحة ولي الأمر |

### الملفات المتوقعة / Expected Files

- `backend/routes/api.php`
- `backend/app/Http/Controllers/Api/V1/AuthController.php`
- `backend/app/Services/AuthService.php`
- `backend/tests/Feature/AuthFlowTest.php`
- `frontend/src/services/authService.ts`
- `frontend/src/stores/authStore.ts`
- `frontend/src/lib/postLogin.ts`
- `frontend/src/router.tsx`

### المخرجات / Deliverables

- تسجيل دخول مستقر.
- خروج مستقر.
- جلب المستخدم الحالي يعمل.
- توجيه صحيح لكل دور.
- اختبارات مصادقة محدثة.

- Stable login.
- Stable logout.
- Current-user endpoint works.
- Correct role-based redirects.
- Updated authentication tests.

### معيار القبول / Acceptance Criteria

- كل دور يستطيع تسجيل الدخول.
- كل دور ينتقل إلى لوحته الصحيحة.
- المستخدم غير المسجل لا يستطيع دخول صفحات النظام.

- Each role can log in.
- Each role is redirected to the correct dashboard.
- Guests cannot access protected app pages.

---

## الجزء 4: إصلاح الصلاحيات وعزل البيانات

## Part 4: Fix Permissions and Data Isolation

### ماذا سنفعل / What We Will Do

- مراجعة الصلاحيات الحالية.
- إصلاح صلاحية اعتماد المشرف للخطة.
- تثبيت عزل المدارس.
- منع المعلم من رؤية طلاب غيره.
- منع المدير من رؤية مدرسة أخرى.
- منع المشرف من رؤية مدارس غير مسندة له.
- منع ولي الأمر من رؤية طالب غير ابنه.
- إضافة اختبارات لهذه القواعد.

- Review current permissions.
- Fix supervisor plan approval permission.
- Stabilize school-level data isolation.
- Prevent teachers from seeing other teachers' students.
- Prevent principals from seeing other schools.
- Prevent supervisors from seeing unassigned schools.
- Prevent guardians from seeing unrelated students.
- Add tests for these rules.

### الملفات المتوقعة / Expected Files

- `backend/app/Policies`
- `backend/app/Services/TenantService.php`
- `backend/app/Http/Middleware/SetTenantScope.php`
- `backend/app/Support/TenantContext.php`
- `backend/app/Services/StudentService.php`
- `backend/app/Services/IepPlanService.php`
- `backend/tests/Feature`
- `backend/database/seed-data/permissions.json`
- `backend/database/seed-data/mappings/role-permissions.json`

### المخرجات / Deliverables

- صلاحيات واضحة لكل دور.
- عزل بيانات صحيح.
- اختبارات وصول وصلاحيات.

- Clear permissions for each role.
- Correct data isolation.
- Access-control tests.

### معيار القبول / Acceptance Criteria

- لا يستطيع أي مستخدم رؤية بيانات خارج نطاقه.
- اعتماد المشرف للخطة يعمل فقط للمشرف المصرح.
- فشل الوصول غير المصرح يرجع Forbidden أو Validation مناسب.

- No user can see data outside their scope.
- Supervisor plan approval works only for authorized supervisors.
- Unauthorized access returns the proper Forbidden or validation response.

---

## الجزء 5: تنظيف الواجهة من الصفحات الوهمية

## Part 5: Remove Fake or Incomplete UI

### ماذا سنفعل / What We Will Do

- فحص كل صفحات الواجهة.
- إخفاء الصفحات غير المكتملة من القوائم.
- حذف أي أزرار لا تعمل.
- حذف أي بيانات وهمية.
- ربط كل شاشة ظاهرة ببيانات حقيقية من API.
- إضافة حالات التحميل والخطأ والحالة الفارغة.

- Inspect all frontend pages.
- Hide incomplete pages from navigation.
- Remove non-working buttons.
- Remove fake hardcoded data.
- Connect every visible screen to real API data.
- Add loading, error, and empty states.

### الملفات المتوقعة / Expected Files

- `frontend/src/router.tsx`
- `frontend/src/components/layout/AppShell.tsx`
- `frontend/src/lib/routeConfig.ts`
- `frontend/src/lib/routeGuards.ts`
- `frontend/src/pages`
- `frontend/src/services`

### المخرجات / Deliverables

- واجهة بدون صفحات وهمية.
- قوائم تنقل حسب الدور.
- شاشات مرتبطة ببيانات حقيقية.
- حالات فارغة واضحة.

- UI with no fake pages.
- Role-based navigation.
- Screens connected to real data.
- Clear empty states.

### معيار القبول / Acceptance Criteria

- أي عنصر ظاهر للمستخدم يعمل فعليا.
- لا توجد صفحة "قريبا" أو "تحت التطوير" داخل نسخة التجربة.

- Every visible UI element works.
- No "coming soon" or "under development" pages in the trial release.

---

## الجزء 6: بناء تجربة مدير النظام

## Part 6: Build the System Admin Experience

### ماذا سنفعل / What We Will Do

- إكمال لوحة مدير النظام.
- إكمال إدارة المدارس.
- إكمال إدارة المستخدمين.
- إكمال تعيين مدير المدرسة.
- إكمال تعيين المشرفين.
- إكمال إدارة البرامج التعليمية.
- إكمال سجل العمليات.

- Complete the system admin dashboard.
- Complete school management.
- Complete user management.
- Complete principal assignment.
- Complete supervisor assignment.
- Complete education program management.
- Complete audit log viewing.

### الشاشات / Screens

- لوحة مدير النظام / System admin dashboard
- المدارس / Schools
- إنشاء وتعديل مدرسة / Create and edit school
- المستخدمون / Users
- إنشاء وتعديل مستخدم / Create and edit user
- البرامج التعليمية / Education programs
- سجل العمليات / Audit logs

### المخرجات / Deliverables

- يستطيع مدير النظام تجهيز بيئة تجربة كاملة.
- يستطيع إنشاء المدارس والمستخدمين وربطهم بالأدوار.

- System admin can prepare the full trial environment.
- System admin can create schools and users and assign roles.

### معيار القبول / Acceptance Criteria

- مدير النظام يستطيع إنشاء مدرسة ومدير ومشرف ومعلم وولي أمر.
- مدير النظام يستطيع ربط المشرف بمدرسة أو أكثر.

- System admin can create a school, principal, supervisor, teacher, and guardian.
- System admin can assign a supervisor to one or more schools.

---

## الجزء 7: بناء تجربة المعلم

## Part 7: Build the Teacher Experience

### ماذا سنفعل / What We Will Do

- إكمال لوحة المعلم.
- عرض طلاب المعلم فقط.
- إكمال ملف الطالب.
- إنشاء خطة تعليمية.
- تعديل خطة.
- إرسال خطة إلى المدير.
- عرض الخطط المرفوضة.
- عرض تعليقات المدير والمشرف.
- ربط الملفات والرسائل والإشعارات.

- Complete teacher dashboard.
- Show only the teacher's students.
- Complete student profile.
- Create educational plans.
- Edit plans.
- Submit plans to the principal.
- Show rejected plans.
- Show principal and supervisor comments.
- Connect files, messages, and notifications.

### الشاشات / Screens

- لوحة المعلم / Teacher dashboard
- طلابي / My students
- ملف الطالب / Student profile
- إنشاء خطة / Create plan
- تعديل خطة / Edit plan
- تفاصيل الخطة / Plan details
- الخطط المرفوضة / Rejected plans
- الملفات / Files
- الرسائل / Messages
- الإشعارات / Notifications

### المخرجات / Deliverables

- المعلم يستطيع إدارة خطط طلابه من البداية للنهاية.

- Teacher can manage student plans from start to submission.

### معيار القبول / Acceptance Criteria

- المعلم يرى طلابه فقط.
- المعلم يستطيع إنشاء خطة وحفظها كمسودة.
- المعلم يستطيع إرسال الخطة للمدير.
- المعلم يستطيع تعديل خطة مرفوضة وإعادة إرسالها.

- Teacher sees only assigned students.
- Teacher can create and save a draft plan.
- Teacher can submit the plan to the principal.
- Teacher can revise and resubmit a rejected plan.

---

## الجزء 8: بناء تجربة مدير المدرسة

## Part 8: Build the School Principal Experience

### ماذا سنفعل / What We Will Do

- إكمال لوحة مدير المدرسة.
- عرض طلاب المدرسة.
- عرض معلمي المدرسة.
- عرض الخطط بانتظار الاعتماد.
- اعتماد الخطة.
- رفض الخطة مع سبب.
- عرض توصيات المشرف.
- ربط الرسائل والإشعارات.

- Complete principal dashboard.
- Show school students.
- Show school teachers.
- Show plans pending principal approval.
- Approve plans.
- Reject plans with a reason.
- Show supervisor recommendations.
- Connect messages and notifications.

### الشاشات / Screens

- لوحة مدير المدرسة / Principal dashboard
- الطلاب / Students
- المعلمون / Teachers
- الخطط بانتظار الاعتماد / Plans pending approval
- تفاصيل الخطة / Plan details
- الزيارات الإشرافية / Supervisor visits
- التوصيات / Recommendations
- الرسائل / Messages
- الإشعارات / Notifications

### المخرجات / Deliverables

- مدير المدرسة يستطيع متابعة مدرسته واعتماد الخطط.

- Principal can monitor the school and approve plans.

### معيار القبول / Acceptance Criteria

- مدير المدرسة يرى مدرسته فقط.
- يستطيع اعتماد أو رفض خطة مع سبب.
- عند اعتماد الخطة، تصل للمشرف.

- Principal sees only their school.
- Principal can approve or reject a plan with a reason.
- Once approved, the plan moves to the supervisor.

---

## الجزء 9: بناء تجربة المشرف

## Part 9: Build the Supervisor Experience

### ماذا سنفعل / What We Will Do

- إكمال لوحة المشرف.
- عرض المدارس المسندة.
- عرض الخطط بانتظار المراجعة.
- اعتماد الخطة نهائيا.
- رفض الخطة مع سبب.
- إنشاء زيارة إشرافية.
- إضافة توصيات.
- متابعة حالة التوصيات.
- ربط الرسائل والإشعارات.

- Complete supervisor dashboard.
- Show assigned schools.
- Show plans pending review.
- Final approve plans.
- Reject plans with a reason.
- Create supervision visits.
- Add recommendations.
- Track recommendation status.
- Connect messages and notifications.

### الشاشات / Screens

- لوحة المشرف / Supervisor dashboard
- المدارس التابعة / Assigned schools
- الخطط بانتظار المراجعة / Plans pending review
- تفاصيل الخطة / Plan details
- الزيارات الإشرافية / Supervision visits
- التوصيات / Recommendations
- التقارير / Reports
- الرسائل / Messages
- الإشعارات / Notifications

### المخرجات / Deliverables

- المشرف يستطيع مراجعة واعتماد الخطط داخل نطاقه.
- المشرف يستطيع إنشاء زيارات وتوصيات.

- Supervisor can review and approve plans within scope.
- Supervisor can create visits and recommendations.

### معيار القبول / Acceptance Criteria

- المشرف يرى المدارس المسندة فقط.
- يستطيع اعتماد أو رفض الخطط التي وصلت له فقط.
- يستطيع إضافة توصيات ومتابعتها.

- Supervisor sees only assigned schools.
- Supervisor can approve or reject only plans assigned to their scope.
- Supervisor can add and track recommendations.

---

## الجزء 10: بناء تجربة ولي الأمر

## Part 10: Build the Guardian Experience

### ماذا سنفعل / What We Will Do

- إكمال لوحة ولي الأمر.
- عرض الأبناء المرتبطين بالحساب.
- عرض بيانات الطالب الأساسية.
- عرض الخطة المعتمدة فقط.
- إخفاء المسودات والرفض الداخلي.
- إظهار الملفات المسموحة فقط.
- ربط الرسائل والإشعارات.

- Complete guardian dashboard.
- Show children linked to the account.
- Show basic student information.
- Show approved plans only.
- Hide drafts and internal rejection workflow.
- Show allowed files only.
- Connect messages and notifications.

### الشاشات / Screens

- لوحة ولي الأمر / Guardian dashboard
- أبنائي / My children
- ملف الابن / Child profile
- الخطة المعتمدة / Approved plan
- الملفات المسموحة / Allowed files
- الرسائل / Messages
- الإشعارات / Notifications

### المخرجات / Deliverables

- ولي الأمر يرى أبناءه فقط والخطط المعتمدة فقط.

- Guardian sees only their children and approved plans.

### معيار القبول / Acceptance Criteria

- ولي الأمر لا يرى أي طالب غير مرتبط به.
- ولي الأمر لا يرى المسودات أو الرفض الداخلي.
- ولي الأمر لا يرى إلا الملفات المسموحة.

- Guardian cannot see unrelated students.
- Guardian cannot see drafts or internal rejection details.
- Guardian sees only allowed files.

---

## الجزء 11: الإشعارات الداخلية

## Part 11: Internal Notifications

### ماذا سنفعل / What We Will Do

- إنشاء إشعارات عند الأحداث المهمة.
- إضافة عداد الإشعارات غير المقروءة.
- إظهار قائمة الإشعارات.
- تعليم إشعار كمقروء.
- تعليم الكل كمقروء.
- فتح العنصر المرتبط من الإشعار.

- Create notifications for important events.
- Add unread notification count.
- Show notification list.
- Mark notification as read.
- Mark all as read.
- Open linked item from notification.

### الأحداث المطلوبة / Required Events

| الحدث | Event | المستلم | Recipient |
|---|---|---|---|
| إرسال خطة للمدير | Plan submitted to principal | مدير المدرسة | Principal |
| اعتماد المدير للخطة | Principal approved plan | المشرف | Supervisor |
| رفض المدير للخطة | Principal rejected plan | المعلم | Teacher |
| اعتماد المشرف للخطة | Supervisor approved plan | المعلم، المدير، ولي الأمر | Teacher, principal, guardian |
| رفض المشرف للخطة | Supervisor rejected plan | المعلم، المدير | Teacher, principal |
| رسالة جديدة | New message | المستلم | Recipient |
| زيارة إشرافية جديدة | New supervision visit | مدير المدرسة | Principal |
| توصية جديدة | New recommendation | المسؤول عنها | Owner |

### معيار القبول / Acceptance Criteria

- كل حدث مهم ينتج إشعارا صحيحا للمستخدم الصحيح.
- لا تصل إشعارات لمستخدم خارج النطاق.

- Every important event creates the correct notification for the correct user.
- No notifications are sent to users outside the permitted scope.

---

## الجزء 12: الرسائل الداخلية

## Part 12: Internal Messaging

### ماذا سنفعل / What We Will Do

- ضبط من يستطيع مراسلة من.
- إكمال قائمة المحادثات.
- إكمال تفاصيل المحادثة.
- إرسال رسالة.
- تعليم الرسائل كمقروءة.
- عداد الرسائل غير المقروءة.
- ربط الرسائل بطالب أو خطة عند الحاجة.

- Define who can message whom.
- Complete conversation list.
- Complete conversation details.
- Send messages.
- Mark messages as read.
- Add unread message counter.
- Link messages to a student or plan when needed.

### قواعد المراسلة / Messaging Rules

- المعلم يراسل مدير المدرسة والمشرف ضمن نطاقه.
- المدير يراسل معلمي مدرسته والمشرف.
- المشرف يراسل مديري ومعلمي المدارس المسندة له.
- ولي الأمر يراسل المدرسة أو المعلم إذا كانت الصلاحية مفعلة.

- Teacher can message the principal and supervisor within scope.
- Principal can message school teachers and supervisor.
- Supervisor can message principals and teachers in assigned schools.
- Guardian can message the school or teacher if enabled.

### معيار القبول / Acceptance Criteria

- لا توجد مراسلة خارج النطاق.
- الرسائل تظهر للمستلم فقط.
- عداد غير المقروء يعمل.

- No out-of-scope messaging.
- Messages appear only to recipients.
- Unread counter works.

---

## الجزء 13: الملفات والمرفقات

## Part 13: Files and Attachments

### ماذا سنفعل / What We Will Do

- رفع ملف للطالب.
- رفع ملف للخطة.
- تصنيف الملف.
- تحديد صلاحية رؤية الملف.
- تحميل الملف عبر رابط مؤقت.
- حذف منطقي للملف.
- تسجيل من رفع الملف.
- تسجيل من أنشأ رابط التحميل.

- Upload files for students.
- Upload files for plans.
- Categorize files.
- Define file visibility.
- Download files through temporary links.
- Soft delete files.
- Record uploader.
- Record temporary link issuer.

### شروط الأمان / Security Rules

- تحديد أنواع الملفات المسموحة.
- تحديد حجم أقصى.
- منع الملفات التنفيذية.
- التحقق من MIME.
- حماية الروابط المؤقتة.

- Allow only approved file types.
- Enforce maximum file size.
- Block executable files.
- Validate MIME type.
- Protect temporary links.

### معيار القبول / Acceptance Criteria

- لا يستطيع مستخدم تحميل ملف غير مصرح له.
- لا يمكن رفع نوع ملف ممنوع.
- روابط التحميل المؤقتة تعمل ضمن القيود.

- Users cannot download unauthorized files.
- Blocked file types cannot be uploaded.
- Temporary links work within the defined constraints.

---

## الجزء 14: التقارير العملية

## Part 14: Operational Reports

### ماذا سنفعل / What We Will Do

- بناء تقارير بسيطة تساعد الفريق في التجربة.
- عدم بناء تصدير PDF أو Excel في النسخة الأولى.
- عدم بناء لوحات تحليل متقدمة.

- Build simple reports for the trial team.
- Do not build PDF or Excel export in the first release.
- Do not build advanced analytics dashboards.

### التقارير المطلوبة / Required Reports

| التقرير | Report | المستفيد | User |
|---|---|---|---|
| طلاب بلا خطة معتمدة | Students without approved plans | المدير، المشرف | Principal, supervisor |
| خطط بانتظار المدير | Plans pending principal | المدير | Principal |
| خطط بانتظار المشرف | Plans pending supervisor | المشرف | Supervisor |
| خطط مرفوضة | Rejected plans | المعلم، المدير | Teacher, principal |
| نشاط المعلمين | Teacher activity | المدير | Principal |
| توصيات مفتوحة | Open recommendations | المدير، المشرف | Principal, supervisor |
| ملخص طالب | Student summary | حسب الصلاحية | Based on permission |

### معيار القبول / Acceptance Criteria

- التقارير تعتمد على بيانات حقيقية.
- كل دور يرى التقارير المناسبة له فقط.

- Reports use real data.
- Each role sees only the reports allowed for them.

---

## الجزء 15: الاختبارات وضمان الجودة

## Part 15: Testing and Quality Assurance

### ماذا سنفعل / What We Will Do

- إصلاح الاختبارات الحالية.
- إضافة اختبارات مصادقة.
- إضافة اختبارات صلاحيات.
- إضافة اختبارات عزل بيانات.
- إضافة اختبارات تدفق الخطة.
- إضافة اختبارات إشعارات.
- إضافة اختبارات رسائل.
- إضافة اختبارات ملفات.
- إضافة اختبار UAT يدوي كامل.

- Fix existing tests.
- Add authentication tests.
- Add permission tests.
- Add data isolation tests.
- Add plan workflow tests.
- Add notification tests.
- Add messaging tests.
- Add file tests.
- Add a complete manual UAT scenario.

### سيناريو UAT الرئيسي / Main UAT Scenario

1. مدير النظام ينشئ مدرسة.
2. مدير النظام ينشئ مدير مدرسة.
3. مدير النظام ينشئ مشرفا.
4. مدير النظام ينشئ معلما.
5. مدير النظام ينشئ ولي أمر.
6. مدير النظام ينشئ طالبا.
7. ربط الطالب بالمعلم.
8. ربط الطالب بولي الأمر.
9. المعلم ينشئ خطة.
10. المعلم يرسل الخطة للمدير.
11. المدير يعتمد الخطة.
12. المشرف يعتمد الخطة.
13. ولي الأمر يرى الخطة المعتمدة.
14. يتم التأكد من الإشعارات في كل خطوة.

1. System admin creates a school.
2. System admin creates a principal.
3. System admin creates a supervisor.
4. System admin creates a teacher.
5. System admin creates a guardian.
6. System admin creates a student.
7. Student is assigned to teacher.
8. Student is linked to guardian.
9. Teacher creates a plan.
10. Teacher submits the plan to the principal.
11. Principal approves the plan.
12. Supervisor approves the plan.
13. Guardian sees the approved plan.
14. Notifications are verified at each step.

### معيار القبول / Acceptance Criteria

- ينجح سيناريو UAT كاملا 3 مرات بدون تدخل مطور.
- لا توجد أخطاء حرجة مفتوحة قبل النشر.

- The full UAT scenario passes 3 times without developer intervention.
- No critical bugs remain open before deployment.

---

## الجزء 16: الأمان قبل النشر

## Part 16: Pre-Deployment Security

### ماذا سنفعل / What We Will Do

- تعطيل أي Demo في الإنتاج.
- عدم عرض كلمات مرور تجريبية.
- ضبط `APP_DEBUG=false`.
- استخدام `APP_KEY` حقيقي.
- ضبط CORS على رابط الواجهة فقط.
- حماية الملفات.
- حماية روابط التحميل المؤقتة.
- تفعيل rate limiting لتسجيل الدخول.
- التأكد من عدم وجود أسرار داخل الكود.
- مراجعة صلاحيات كل route.
- تجهيز نسخة احتياطية لقاعدة البيانات.

- Disable all demo features in production.
- Do not show demo passwords.
- Set `APP_DEBUG=false`.
- Use a real `APP_KEY`.
- Restrict CORS to the frontend URL.
- Secure files.
- Secure temporary download links.
- Enable login rate limiting.
- Ensure no secrets exist in code.
- Review permissions for every route.
- Prepare database backup.

### معيار القبول / Acceptance Criteria

- لا توجد بيانات حساسة مكشوفة.
- لا يوجد Demo ظاهر في الإنتاج.
- لا يوجد تسريب بيانات بين الأدوار أو المدارس.

- No sensitive data is exposed.
- No demo content is visible in production.
- No data leakage exists between roles or schools.

---

## الجزء 17: تجهيز بيانات التجربة

## Part 17: Prepare Trial Data

### ماذا سنفعل / What We Will Do

- تجهيز المدارس المشاركة.
- تجهيز حسابات المستخدمين.
- تجهيز ملفات الطلاب.
- تجهيز علاقات الطلاب بالمعلمين.
- تجهيز علاقات الطلاب بأولياء الأمور.
- تجهيز علاقات المشرف بالمدارس.
- تجهيز برامج تعليمية وأنواع احتياج.

- Prepare participating schools.
- Prepare user accounts.
- Prepare student records.
- Prepare student-teacher relationships.
- Prepare student-guardian relationships.
- Prepare supervisor-school relationships.
- Prepare education programs and needs categories.

### ملاحظة مهمة / Important Note

إذا كانت التجربة أولية، يفضل استخدام بيانات غير حساسة أو بيانات تجريبية قريبة من الواقع. لا تستخدم بيانات طلاب حقيقية إلا بعد تأمين البيئة وأخذ الموافقات اللازمة.

For the first trial, use non-sensitive or realistic sample data. Do not use real student data unless the environment is secured and approvals are obtained.

### معيار القبول / Acceptance Criteria

- كل حساب تجربة يعمل.
- كل طالب مرتبط بمدرسة ومعلم وولي أمر حسب الحاجة.

- Every trial account works.
- Every student is linked to a school, teacher, and guardian when needed.

---

## الجزء 18: النشر

## Part 18: Deployment

### ماذا سنفعل / What We Will Do

- تجهيز branch مستقر.
- تشغيل الاختبارات.
- بناء الواجهة.
- تجهيز متغيرات البيئة.
- تشغيل migrations.
- تشغيل seeders الأساسية فقط.
- إنشاء أول حساب مدير نظام.
- نشر backend.
- نشر frontend.
- اختبار تسجيل الدخول.
- اختبار سيناريو UAT.
- فتح الرابط لفريق العمل.

- Prepare a stable branch.
- Run tests.
- Build frontend.
- Prepare environment variables.
- Run migrations.
- Run essential seeders only.
- Create the first system admin account.
- Deploy backend.
- Deploy frontend.
- Test login.
- Test UAT scenario.
- Share the URL with the team.

### إعدادات الإنتاج الأساسية / Core Production Settings

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL`
- `FRONTEND_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `CACHE_STORE`
- `QUEUE_CONNECTION`
- `FILESYSTEM_DISK`
- `CORS_ALLOWED_ORIGINS`
- `SANCTUM_STATEFUL_DOMAINS`

### معيار القبول / Acceptance Criteria

- النظام يعمل على رابط حقيقي.
- يستطيع الفريق تنفيذ سيناريو UAT الكامل.
- لا توجد صفحات وهمية أو أخطاء حرجة.

- The system works on a real URL.
- The team can execute the full UAT scenario.
- No fake pages or critical errors remain.

---

## الجزء 19: تجربة الفريق

## Part 19: Team Trial

### ماذا سنفعل / What We Will Do

- تشغيل تجربة محدودة لمدة 5 إلى 10 أيام.
- مراقبة استخدام المعلمين والمدير والمشرف وولي الأمر.
- تسجيل الأخطاء.
- تسجيل ملاحظات تجربة المستخدم.
- ترتيب الملاحظات حسب الأولوية.

- Run a limited trial for 5 to 10 days.
- Observe usage by teachers, principal, supervisor, and guardians.
- Record bugs.
- Record user experience feedback.
- Prioritize feedback.

### فريق التجربة المقترح / Suggested Trial Team

- مدير نظام واحد / 1 system admin
- مدير مدرسة واحد / 1 principal
- مشرف واحد / 1 supervisor
- من 2 إلى 5 معلمين / 2 to 5 teachers
- من 2 إلى 5 أولياء أمور / 2 to 5 guardians
- من 10 إلى 20 طالبا كبيانات / 10 to 20 student records

### معيار القبول / Acceptance Criteria

- يتم جمع ملاحظات واضحة.
- يتم فصل الأخطاء عن طلبات الميزات.
- يتم تحديد قرار: توسيع التجربة أو إصلاحات إضافية.

- Clear feedback is collected.
- Bugs are separated from feature requests.
- Decision is made: expand the trial or perform another fix cycle.

---

## الجزء 20: ما بعد التجربة

## Part 20: Post-Trial Improvements

### ماذا سنفعل / What We Will Do

- تصنيف الملاحظات.
- إصلاح الأخطاء الحرجة أولا.
- تحسين الشاشات غير الواضحة.
- تأجيل الميزات الخارجة عن النطاق.
- تجهيز نسخة تجربة ثانية إذا لزم.

- Classify feedback.
- Fix critical bugs first.
- Improve unclear screens.
- Defer out-of-scope features.
- Prepare a second trial release if needed.

### تصنيف الملاحظات / Feedback Classification

| النوع | Type | مثال | Example | الإجراء | Action |
|---|---|---|---|---|---|
| خطأ حرج | Critical bug | مستخدم يرى بيانات غيره | User sees someone else's data | إصلاح فوري | Immediate fix |
| خطأ وظيفي | Functional bug | الاعتماد لا يعمل | Approval does not work | إصلاح قبل التوسع | Fix before expansion |
| تحسين تجربة | UX improvement | زر غير واضح | Unclear button | تحسين قريب | Improve soon |
| طلب ميزة | Feature request | تصدير PDF | PDF export | يدرس لاحقا | Evaluate later |
| خارج النطاق | Out of scope | فواتير | Invoices | يؤجل أو يرفض | Defer or reject |

---

## الجدول الزمني المقترح / Suggested Timeline

| الأسبوع | Week | العمل | Work |
|---|---|---|---|
| 1 | 1 | التدقيق والتشغيل وإصلاح الأساس | Audit, run project, fix foundation |
| 2 | 2 | الصلاحيات وتدفق الخطط والإشعارات | Permissions, plan workflow, notifications |
| 3 | 3 | واجهات المعلم ومدير المدرسة | Teacher and principal UI |
| 4 | 4 | واجهات المشرف وولي الأمر | Supervisor and guardian UI |
| 5 | 5 | الرسائل والملفات والتقارير العملية | Messaging, files, operational reports |
| 6 | 6 | الاختبارات والأمان والنشر | Testing, security, deployment |
| 7 | 7 | تجربة الفريق | Team trial |
| 8 | 8 | إصلاح ملاحظات التجربة | Fix trial feedback |

---

## تعريف النسخة الجاهزة للتجربة / Definition of Trial-Ready Release

النسخة لا تعتبر جاهزة للتجربة إلا إذا:

- تسجيل الدخول يعمل لكل دور.
- كل دور يرى صفحاته الصحيحة.
- لا توجد صفحات وهمية في التنقل.
- المعلم يستطيع إنشاء خطة.
- المدير يستطيع اعتماد أو رفض الخطة.
- المشرف يستطيع اعتماد أو رفض الخطة.
- ولي الأمر يرى الخطة المعتمدة فقط.
- الإشعارات الداخلية تعمل.
- الرسائل الداخلية تعمل.
- الملفات تعمل بأمان.
- لا يوجد تسريب بيانات بين المدارس أو المستخدمين.
- النظام منشور على رابط حقيقي.
- يوجد حسابات تجربة جاهزة.
- سيناريو UAT مكتوب ومجرب.

The release is not trial-ready unless:

- Login works for every role.
- Each role sees the correct pages.
- No fake pages exist in navigation.
- Teacher can create a plan.
- Principal can approve or reject the plan.
- Supervisor can approve or reject the plan.
- Guardian sees only approved plans.
- Internal notifications work.
- Internal messages work.
- Files work securely.
- No data leakage exists between schools or users.
- The system is deployed to a real URL.
- Trial accounts are ready.
- UAT scenario is documented and tested.

---

## أولويات التنفيذ المختصرة / Execution Priorities Summary

### أولوية 1 / Priority 1

- إصلاح تسجيل الدخول.
- إصلاح الصلاحيات.
- إصلاح اعتماد المشرف.
- تثبيت عزل المدارس.
- تنظيف الصفحات الوهمية.
- تحديث الاختبارات.

- Fix authentication.
- Fix permissions.
- Fix supervisor approval.
- Stabilize school isolation.
- Remove fake pages.
- Update tests.

### أولوية 2 / Priority 2

- إكمال واجهة المعلم.
- إكمال واجهة مدير المدرسة.
- إكمال واجهة المشرف.
- إكمال واجهة ولي الأمر.
- ربط كل شاشة ببيانات حقيقية.

- Complete teacher UI.
- Complete principal UI.
- Complete supervisor UI.
- Complete guardian UI.
- Connect every screen to real data.

### أولوية 3 / Priority 3

- الإشعارات الداخلية.
- الرسائل الداخلية.
- الملفات.
- التقارير العملية.

- Internal notifications.
- Internal messaging.
- Files.
- Operational reports.

### أولوية 4 / Priority 4

- الاختبارات.
- الأمان.
- النشر.
- تجربة الفريق.

- Testing.
- Security.
- Deployment.
- Team trial.

