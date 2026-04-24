# سير عمل الخطط التعليمية الفردية IEP

## الحالات

- `draft`
- `pending_principal_review`
- `pending_supervisor_review`
- `approved`
- `rejected`
- `archived`

## الانتقالات المسموحة

1. `draft -> pending_principal_review`
   - المنفذ: `teacher`
   - الشرط: وجود أهداف ومحتوى أساسي مكتمل

2. `pending_principal_review -> pending_supervisor_review`
   - المنفذ: `principal`
   - الشرط: مراجعة المدخلات المدرسية

3. `pending_principal_review -> rejected`
   - المنفذ: `principal`
   - الشرط: كتابة سبب الرفض

4. `pending_supervisor_review -> approved`
   - المنفذ: `supervisor`
   - الشرط: اكتمال المراجعة القطاعية

5. `pending_supervisor_review -> rejected`
   - المنفذ: `supervisor`
   - الشرط: كتابة سبب الرفض

6. `rejected -> draft`
   - المنفذ: `teacher`
   - الشرط: فتح نسخة جديدة للتعديل

7. `approved -> archived`
   - المنفذ: النظام أو مشرف مخول
   - الشرط: نهاية الفترة أو الأرشفة الإدارية

## آثار جانبية متوقعة

- إنشاء سجل في `iep_plan_approvals`
- إرسال إشعار عند كل انتقال
- إنشاء نسخة جديدة عند إعادة العمل بعد الرفض
- تحديث PDF المعتمد عند الوصول إلى `approved`
