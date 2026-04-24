import { useEffect, useState } from "react";
import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { getErrorMessage } from "../../services/api";
import { portalService, type PortalStatistics } from "../../services/portalService";

export function StatisticsPage() {
  const [statistics, setStatistics] = useState<PortalStatistics | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);

    portalService
      .statistics()
      .then((payload) => {
        setStatistics(payload);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="الإحصائيات"
          title="إحصائيات وأرقام"
          description="عرض موجز للمؤشرات الرئيسية داخل البوابة."
        />

        {loading ? <div className="loading-box">جارٍ تحميل الإحصائيات...</div> : null}
        {error ? <div className="error-box">{error}</div> : null}

        {!loading && statistics ? (
          <>
            <div className="portal-grid portal-grid-four">
              <article className="portal-stat-card">
                <span>عدد المدارس</span>
                <strong>{statistics.schools_count}</strong>
              </article>
              <article className="portal-stat-card">
                <span>عدد البرامج</span>
                <strong>{statistics.programs_count}</strong>
              </article>
              <article className="portal-stat-card">
                <span>عدد الطلاب</span>
                <strong>{statistics.students_count}</strong>
              </article>
              <article className="portal-stat-card">
                <span>عدد المعلمين</span>
                <strong>{statistics.teachers_count}</strong>
              </article>
            </div>

            <div className="portal-grid portal-grid-two">
              {statistics.program_breakdown.map((item) => (
                <article className="portal-card" key={item.program_type ?? "unknown"}>
                  <h3>{item.program_type ?? "غير محدد"}</h3>
                  <p>عدد المدارس المرتبطة بهذا البرنامج: {item.schools_count}</p>
                </article>
              ))}
            </div>
          </>
        ) : null}
      </section>
    </div>
  );
}
