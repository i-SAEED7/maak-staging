import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { studentService, type StudentDetails } from "../../services/studentService";

export function StudentDetailsPage() {
  const { studentId } = useParams();
  const [student, setStudent] = useState<StudentDetails | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!studentId) {
      setError("تعذر تحديد الطالب المطلوب.");
      setLoading(false);
      return;
    }

    setLoading(true);

    void studentService
      .details(studentId)
      .then(setStudent)
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [studentId]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">Student Details</span>
          <h2>تفاصيل الطالب</h2>
        </div>
        <Link className="button button-secondary" to="/app/students">
          العودة إلى الطلاب
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل بيانات الطالب...</div> : null}

      {!loading && student ? (
        <>
          <section className="surface-card page-stack">
            <div className="detail-grid">
              <div>
                <span className="detail-label">الاسم</span>
                <strong>{student.full_name}</strong>
              </div>
              <div>
                <span className="detail-label">رقم الطالب</span>
                <strong>{student.student_number ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المدرسة</span>
                <strong>{student.school?.name_ar ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المرحلة</span>
                <strong>{student.school?.stage ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الصف</span>
                <strong>{student.grade_level ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الحالة</span>
                <strong>{student.enrollment_status}</strong>
              </div>
            </div>
          </section>

          <section className="surface-card page-stack">
            <h3>البيانات التعليمية</h3>
            <div className="detail-grid">
              <div>
                <span className="detail-label">البرنامج</span>
                <strong>{student.education_program?.name_ar ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">التصنيف</span>
                <strong>{student.disability_category?.name_ar ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المعلم</span>
                <strong>{student.primary_teacher?.full_name ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">العام الدراسي</span>
                <strong>{student.academic_year?.name_ar ?? "-"}</strong>
              </div>
            </div>
          </section>

          <section className="surface-card page-stack">
            <h3>أولياء الأمور</h3>
            {student.guardians?.length ? (
              <div className="detail-list">
                {student.guardians.map((guardian) => (
                  <div className="detail-list-item" key={guardian.id}>
                    <strong>{guardian.parent_name}</strong>
                    <span>
                      {guardian.relationship}
                      {guardian.is_primary ? " - أساسي" : ""}
                    </span>
                  </div>
                ))}
              </div>
            ) : (
              <div className="info-box">لا يوجد أولياء أمور مرتبطون بهذا الطالب حاليًا.</div>
            )}
          </section>
        </>
      ) : null}
    </section>
  );
}
