# عقود الطلب والاستجابة API Contracts

هذا الملف يركز على نماذج `request/response` الأكثر أهمية في المرحلة الأولى.

## 1. Login

### Request

```json
{
  "identifier": "teacher@example.com",
  "password": "Secret123!"
}
```

### Response

```json
{
  "success": true,
  "message": "تم تسجيل الدخول",
  "data": {
    "token": "plain-text-token",
    "user": {
      "id": 25,
      "uuid": "3d5f8bb4-5ee5-4ae0-8a80-d785f3dfdb91",
      "full_name": "معلم تجريبي",
      "role": "teacher",
      "school_id": 7
    },
    "permissions": [
      "students.view_any",
      "iep.create",
      "student_reports.create"
    ]
  }
}
```

## 2. Create School

### Request

```json
{
  "name_ar": "مدرسة النور للتربية الخاصة",
  "region": "الرياض",
  "city": "الرياض",
  "district": "شمال",
  "address": "حي النرجس",
  "phone": "0500000000",
  "email": "school@example.com",
  "latitude": 24.8101000,
  "longitude": 46.6203000,
  "storage_quota_mb": 4096
}
```

### Response

```json
{
  "success": true,
  "message": "تم إنشاء المدرسة",
  "data": {
    "id": 7,
    "uuid": "3c2dfdbd-3ef2-4478-aed9-6f9f8d3fa20d",
    "name_ar": "مدرسة النور للتربية الخاصة",
    "status": "active"
  }
}
```

## 3. Create User

### Request

```json
{
  "role": "teacher",
  "school_id": 7,
  "full_name": "أحمد علي",
  "email": "teacher@example.com",
  "phone": "0555555555",
  "national_id": "1000000000",
  "password": "Secret123!",
  "must_change_password": true
}
```

### Response

```json
{
  "success": true,
  "message": "تم إنشاء المستخدم",
  "data": {
    "id": 31,
    "full_name": "أحمد علي",
    "role": "teacher",
    "school_id": 7,
    "status": "active"
  }
}
```

## 4. Create Student

### Request

```json
{
  "school_id": 7,
  "education_program_id": 2,
  "disability_category_id": 4,
  "primary_teacher_user_id": 31,
  "first_name": "محمد",
  "father_name": "عبدالله",
  "family_name": "الشهري",
  "gender": "male",
  "birth_date": "2017-02-10",
  "grade_level": "الثالث",
  "classroom": "3A",
  "student_number": "ST-1001",
  "medical_notes": {
    "hearing_support": true
  }
}
```

### Response

```json
{
  "success": true,
  "message": "تم إنشاء الطالب",
  "data": {
    "id": 1001,
    "full_name": "محمد عبدالله الشهري",
    "enrollment_status": "active"
  }
}
```

## 5. Create IEP Plan

### Request

```json
{
  "student_id": 1001,
  "academic_year_id": 2,
  "title": "الخطة الفردية للفصل الأول",
  "start_date": "2026-08-18",
  "end_date": "2026-12-20",
  "summary": "خطة تطويرية أولية",
  "strengths": "يتفاعل بصريًا بسرعة",
  "needs": "يحتاج إلى دعم في التواصل",
  "accommodations": {
    "extra_time": true
  },
  "goals": [
    {
      "domain": "التواصل",
      "goal_text": "تحسين الاستجابة للأوامر البسيطة",
      "measurement_method": "ملاحظة صفية",
      "baseline_value": "30%",
      "target_value": "70%",
      "due_date": "2026-11-01"
    }
  ]
}
```

### Response

```json
{
  "success": true,
  "message": "تم حفظ الخطة كمسودة",
  "data": {
    "id": 501,
    "status": "draft",
    "current_version_number": 1
  }
}
```

## 6. Submit IEP Plan

### Request

```json
{
  "notes": "تمت مراجعة الأهداف وإرسالها للاعتماد"
}
```

### Response

```json
{
  "success": true,
  "message": "تم إرسال الخطة للمراجعة",
  "data": {
    "id": 501,
    "status": "pending_principal_review"
  }
}
```

## 7. Reject IEP Plan

### Request

```json
{
  "reason": "يرجى تحسين صياغة الهدف الأول وإضافة مؤشر قياس واضح"
}
```

### Response

```json
{
  "success": true,
  "message": "تم رفض الخطة",
  "data": {
    "id": 501,
    "status": "rejected",
    "rejection_reason": "يرجى تحسين صياغة الهدف الأول وإضافة مؤشر قياس واضح"
  }
}
```

## 8. Create Supervisor Visit

### Request

```json
{
  "school_id": 7,
  "template_id": 2,
  "visit_date": "2026-10-14",
  "agenda": "متابعة تنفيذ الخطط الفردية"
}
```

### Response

```json
{
  "success": true,
  "message": "تمت جدولة الزيارة",
  "data": {
    "id": 201,
    "visit_status": "scheduled"
  }
}
```

## 9. Send Message

### Request

```json
{
  "recipient_ids": [31],
  "subject": "استفسار عن تقدم الطالب",
  "body": "نرغب في معرفة مستوى التقدم خلال هذا الشهر"
}
```

### Response

```json
{
  "success": true,
  "message": "تم إرسال الرسالة",
  "data": {
    "id": 801,
    "thread_key": "parent-55-teacher-31-student-1001"
  }
}
```

## 10. Standard List Response

```json
{
  "success": true,
  "message": "تم جلب البيانات",
  "data": [
    {
      "id": 1,
      "name_ar": "مدرسة"
    }
  ],
  "meta": {
    "page": 1,
    "per_page": 15,
    "total": 120
  }
}
```
