import { z } from "zod";

export const loginSchema = z.object({
  identifier: z.string().trim().min(1, "أدخل اسم المستخدم أو البريد."),
  password: z.string().min(1, "أدخل كلمة المرور.")
});

export const forgotPasswordSchema = z.object({
  email: z.string().trim().email("أدخل بريدًا إلكترونيًا صحيحًا.")
});

export const resetPasswordSchema = z
  .object({
    email: z.string().trim().email("أدخل بريدًا إلكترونيًا صحيحًا."),
    token: z.string().trim().min(1, "رمز الاستعادة مطلوب."),
    password: z.string().min(8, "كلمة المرور يجب ألا تقل عن 8 أحرف."),
    password_confirmation: z.string().min(1, "أكّد كلمة المرور.")
  })
  .superRefine((value, context) => {
    if (value.password !== value.password_confirmation) {
      context.addIssue({
        code: z.ZodIssueCode.custom,
        path: ["password_confirmation"],
        message: "كلمة المرور وتأكيدها غير متطابقين."
      });
    }
});

export const userFormSchema = z
  .object({
    full_name: z.string().trim().min(2, "أدخل اسم المستخدم."),
    email: z.union([z.string().email("البريد غير صحيح."), z.literal("")]),
    phone: z.string().trim(),
    role: z.string().trim().min(1, "اختر الدور."),
    school_id: z.string(),
    school_ids: z.array(z.string()),
    password: z.string(),
    must_change_password: z.boolean()
  })
  .superRefine((value, context) => {
    if (value.role === "supervisor" && value.school_ids.length === 0) {
      context.addIssue({
        code: z.ZodIssueCode.custom,
        path: ["school_ids"],
        message: "اختر مدرسة واحدة على الأقل للمشرف."
      });
    }
  });

export const studentFormSchema = z.object({
  school_id: z.string().min(1, "اختر المدرسة."),
  education_program_id: z.string().min(1, "اختر نوع البرنامج."),
  first_name: z.string().trim().min(2, "أدخل الاسم الأول."),
  family_name: z.string().trim().min(2, "أدخل اسم العائلة."),
  gender: z.enum(["male", "female"]),
  grade_level: z.string().trim().min(1, "اختر الصف."),
  classroom: z.string()
});

export const fileUploadSchema = z.object({
  file: z
    .any()
    .refine(
      (value) => typeof FileList !== "undefined" && value instanceof FileList && value.length > 0,
      "اختر ملفًا أولًا."
    ),
  category: z.string().min(1, "اختر الفئة."),
  visibility: z.string().min(1, "اختر مستوى الظهور."),
  is_sensitive: z.string().min(1),
  related_type: z.string().min(1, "اختر النوع المرتبط."),
  related_id: z.string().trim().min(1, "أدخل المعرف المرتبط.")
});

export const accountRegistrationSchema = z
  .object({
    first_name: z.string().trim().min(2, "أدخل الاسم الأول."),
    second_name: z.string().trim().optional(),
    last_name: z.string().trim().min(2, "أدخل الاسم الأخير."),
    email: z.string().trim().email("البريد الإلكتروني غير صحيح."),
    password: z.string().min(8, "كلمة المرور يجب ألا تقل عن 8 أحرف."),
    password_confirmation: z.string().min(1, "أكّد كلمة المرور."),
    phone: z.string().trim().min(8, "أدخل رقم جوال صحيح."),
    account_type: z
      .string()
      .refine((value) => ["parent", "teacher", "principal"].includes(value), "اختر نوع الحساب."),
    stage: z.string().trim().min(1, "اختر المرحلة."),
    school_id: z.string().trim().min(1, "اختر المدرسة.")
  })
  .superRefine((value, context) => {
    if (value.password !== value.password_confirmation) {
      context.addIssue({
        code: z.ZodIssueCode.custom,
        path: ["password_confirmation"],
        message: "كلمة المرور وتأكيدها غير متطابقين."
      });
    }
  });

export type LoginFormValues = z.infer<typeof loginSchema>;
export type ForgotPasswordFormValues = z.infer<typeof forgotPasswordSchema>;
export type ResetPasswordFormValues = z.infer<typeof resetPasswordSchema>;
export type UserFormSchemaValues = z.infer<typeof userFormSchema>;
export type StudentFormSchemaValues = z.infer<typeof studentFormSchema>;
export type FileUploadFormValues = z.infer<typeof fileUploadSchema>;
export type AccountRegistrationFormValues = z.infer<typeof accountRegistrationSchema>;
