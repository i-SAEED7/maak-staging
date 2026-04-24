import { useState } from "react";

type FileUploaderProps = {
  onUploaded: (formData: FormData) => Promise<void>;
};

export function FileUploader({ onUploaded }: FileUploaderProps) {
  const [submitting, setSubmitting] = useState(false);

  return (
    <form
      className="form-stack"
      onSubmit={async (event) => {
        event.preventDefault();

        const form = new FormData(event.currentTarget);

        if (!(form.get("file") instanceof File) || !(form.get("file") as File).name) {
          window.alert("اختر ملفًا أولًا.");
          return;
        }

        setSubmitting(true);

        try {
          await onUploaded(form);
          event.currentTarget.reset();
        } finally {
          setSubmitting(false);
        }
      }}
    >
      <div className="grid-two">
        <label className="field">
          <span>الملف</span>
          <input name="file" type="file" />
        </label>
        <label className="field">
          <span>الفئة</span>
          <select name="category" defaultValue="general">
            <option value="general">عام</option>
            <option value="portfolio">ملف إنجاز</option>
            <option value="iep_attachment">مرفق خطة</option>
            <option value="student_report">تقرير طالب</option>
            <option value="supervision">إشراف تربوي</option>
          </select>
        </label>
      </div>

      <div className="grid-two">
        <label className="field">
          <span>الظهور</span>
          <select name="visibility" defaultValue="school">
            <option value="private">خاص</option>
            <option value="school">مدرسي</option>
            <option value="public">عام</option>
          </select>
        </label>
        <label className="field">
          <span>حساس؟</span>
          <select name="is_sensitive" defaultValue="0">
            <option value="0">لا</option>
            <option value="1">نعم</option>
          </select>
        </label>
      </div>

      <div className="grid-two">
        <label className="field">
          <span>النوع المرتبط</span>
          <select name="related_type" defaultValue="App\\Models\\Student">
            <option value="App\\Models\\Student">طالب</option>
            <option value="App\\Models\\IepPlan">خطة فردية</option>
            <option value="App\\Models\\Announcement">إعلان</option>
            <option value="App\\Models\\School">مدرسة</option>
          </select>
        </label>
        <label className="field">
          <span>المعرف المرتبط</span>
          <input name="related_id" placeholder="1" />
        </label>
      </div>

      <button className="button button-primary" disabled={submitting} type="submit">
        {submitting ? "جارٍ الرفع..." : "رفع الملف"}
      </button>
    </form>
  );
}
