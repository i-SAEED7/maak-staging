# قواعد التحقق لوحدة IEP

## StoreIepPlanRequest

- `student_id`
  - مطلوب
  - يجب أن يشير إلى طالب صالح داخل نطاق المدرسة

- `title`
  - مطلوب
  - نص
  - حد أقصى 255

- `start_date`
  - اختياري
  - تاريخ صالح

- `end_date`
  - اختياري
  - تاريخ صالح
  - يجب أن يكون بعد أو مساويًا لـ `start_date`

- `summary`
  - اختياري
  - نص

- `goals`
  - مطلوب عند الإرسال للمراجعة
  - مصفوفة
  - عنصر واحد على الأقل

- `goals.*.domain`
  - مطلوب
  - نص

- `goals.*.goal_text`
  - مطلوب
  - نص

- `goals.*.measurement_method`
  - اختياري
  - نص

- `goals.*.due_date`
  - اختياري
  - تاريخ صالح

## Business Rules

- لا يعتمد المدير خطة لم تصل لحالة `pending_principal_review`.
- لا يعتمد المشرف خطة لم تصل لحالة `pending_supervisor_review`.
- الرفض يتطلب سببًا.
- اعتماد الخطة ينشئ PDF نهائيًا أو Job لتوليده.
