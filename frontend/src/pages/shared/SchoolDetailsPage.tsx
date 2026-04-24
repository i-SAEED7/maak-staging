import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { reportService, type SchoolSummaryReport } from "../../services/reportService";
import { schoolService, type SchoolItem } from "../../services/schoolService";

const overviewLabels: Record<string, string> = {
  students_count: "عدد الطلاب",
  teachers_count: "عدد المعلمين",
  users_count: "إجمالي المستخدمين",
  iep_plans_count: "عدد الخطط",
  approved_iep_plans_count: "الخطط المعتمدة",
  messages_count: "عدد الرسائل",
  notifications_count: "عدد الإشعارات",
  unread_notifications_count: "الإشعارات غير المقروءة",
  files_count: "عدد الملفات"
};

export function SchoolDetailsPage() {
  const { schoolId } = useParams();
  const [school, setSchool] = useState<SchoolItem | null>(null);
  const [summary, setSummary] = useState<SchoolSummaryReport | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!schoolId) {
      return;
    }

    setLoading(true);

    Promise.all([schoolService.details(schoolId), reportService.schoolSummary(schoolId)])
      .then(([schoolPayload, summaryPayload]) => {
        setSchool(schoolPayload);
        setSummary(summaryPayload);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [schoolId]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">المدارس</span>
          <h2>{school?.name ?? "تفاصيل المدرسة"}</h2>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل بيانات المدرسة...</div> : null}

      {!loading && school ? (
        <>
          <section className="surface-card page-stack">
            <div className="detail-grid">
              <div>
                <span className="detail-label">كود المدرسة</span>
                <strong>{school.school_code ?? school.official_code ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المرحلة</span>
                <strong>{school.stage ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">البرنامج</span>
                <strong>{school.program_type ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الحالة</span>
                <strong>{school.status === "active" ? "نشطة" : "غير نشطة"}</strong>
              </div>
              <div>
                <span className="detail-label">مدير المدرسة</span>
                <strong>{school.principal?.full_name ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المشرف التربوي</span>
                <strong>{school.supervisor?.full_name ?? "-"}</strong>
              </div>
            </div>
          </section>

          {summary ? (
            <section className="surface-card page-stack">
              <div className="page-header">
                <div>
                  <span className="eyebrow">الملخص</span>
                  <h3>مؤشرات المدرسة</h3>
                </div>
              </div>

              <div className="stats-grid">
                {Object.entries(summary.overview).map(([key, value]) => (
                  <div className="stat-card" key={key}>
                    <span className="detail-label">{overviewLabels[key] ?? key}</span>
                    <strong>{value}</strong>
                  </div>
                ))}
              </div>
            </section>
          ) : null}
        </>
      ) : null}
    </section>
  );
}
