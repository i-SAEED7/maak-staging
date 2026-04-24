# Migration Plan 002: Students and Relationships

## الملفات

1. create_disability_categories_table
2. create_education_programs_table
3. create_students_table
4. create_student_guardians_table
5. create_teacher_student_assignments_table

## ملاحظات

- `students.full_name` حقل مشتق لكنه مفيد للبحث السريع.
- الهوية الوطنية يجب ألا تخزن نصًا مكشوفًا.
