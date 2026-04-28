import { zodResolver } from "@hookform/resolvers/zod";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { fileUploadSchema, type FileUploadFormValues } from "../../lib/formSchemas";

type FileUploaderProps = {
  onUploaded: (formData: FormData) => Promise<void>;
};

export function FileUploader({ onUploaded }: FileUploaderProps) {
  const [submitting, setSubmitting] = useState(false);
  const {
    formState: { errors },
    handleSubmit,
    register,
    reset
  } = useForm<FileUploadFormValues>({
    resolver: zodResolver(fileUploadSchema)
  });

  return (
    <form
      className="form-stack"
      onSubmit={handleSubmit(async (values) => {
        setSubmitting(true);

        try {
          const form = new FormData();
          form.set("file", values.file[0]);
          form.set("category", values.category);
          form.set("visibility", values.visibility);
          form.set("is_sensitive", values.is_sensitive);
          form.set("related_type", values.related_type);
          form.set("related_id", values.related_id);
          await onUploaded(form);
          reset();
        } finally {
          setSubmitting(false);
        }
      })}
    >
      <div className="grid-two">
        <label className="field">
          <span>الملف</span>
          <input type="file" {...register("file")} />
          {errors.file?.message ? <small className="field-hint">{String(errors.file.message)}</small> : null}
        </label>
        <label className="field">
          <span>الفئة</span>
          <select defaultValue="general" {...register("category")}>
            <option value="general">عام</option>
            <option value="portfolio">ملف إنجاز</option>
            <option value="iep_attachment">مرفق خطة</option>
            <option value="student_report">تقرير طالب</option>
            <option value="supervision">إشراف تربوي</option>
          </select>
          {errors.category ? <small className="field-hint">{errors.category.message}</small> : null}
        </label>
      </div>

      <div className="grid-two">
        <label className="field">
          <span>الظهور</span>
          <select defaultValue="school" {...register("visibility")}>
            <option value="private">خاص</option>
            <option value="school">مدرسي</option>
            <option value="public">عام</option>
          </select>
          {errors.visibility ? <small className="field-hint">{errors.visibility.message}</small> : null}
        </label>
        <label className="field">
          <span>حساس؟</span>
          <select defaultValue="0" {...register("is_sensitive")}>
            <option value="0">لا</option>
            <option value="1">نعم</option>
          </select>
        </label>
      </div>

      <div className="grid-two">
        <label className="field">
          <span>النوع المرتبط</span>
          <select defaultValue="App\\Models\\Student" {...register("related_type")}>
            <option value="App\\Models\\Student">طالب</option>
            <option value="App\\Models\\IepPlan">خطة فردية</option>
            <option value="App\\Models\\Announcement">إعلان</option>
            <option value="App\\Models\\School">مدرسة</option>
          </select>
          {errors.related_type ? <small className="field-hint">{errors.related_type.message}</small> : null}
        </label>
        <label className="field">
          <span>المعرف المرتبط</span>
          <input placeholder="1" {...register("related_id")} />
          {errors.related_id ? <small className="field-hint">{errors.related_id.message}</small> : null}
        </label>
      </div>

      <button className="button button-primary" disabled={submitting} type="submit">
        {submitting ? "جارٍ الرفع..." : "رفع الملف"}
      </button>
    </form>
  );
}
