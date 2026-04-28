import { useEffect, useMemo, useState } from "react";
import { Link, Navigate } from "react-router-dom";
import { useSchoolSite } from "../../lib/schoolSite";
import { announcementService, type AnnouncementItem } from "../../services/announcementService";
import { getErrorMessage } from "../../services/api";

const audienceLabels: Record<AnnouncementItem["target_audience"], string> = {
  teacher: "المعلم",
  principal: "مدير المدرسة",
  supervisor: "المشرف التربوي",
  parent: "ولي الأمر",
  general: "عامة"
};

export function SchoolAnnouncementsPage() {
  const { school, schoolPath, currentSchoolId, canViewAnnouncements, canManageAnnouncements } = useSchoolSite();
  const [announcements, setAnnouncements] = useState<AnnouncementItem[]>([]);
  const [loading, setLoading] = useState(canViewAnnouncements);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!canViewAnnouncements) {
      setAnnouncements([]);
      setLoading(false);
      return;
    }

    let isActive = true;
    setLoading(true);

    void announcementService
      .list()
      .then((payload) => {
        if (!isActive) {
          return;
        }

        const filtered = payload.filter(
          (announcement) => announcement.is_all_schools || announcement.school?.id === currentSchoolId
        );

        setAnnouncements(filtered);
        setError(null);
      })
      .catch((loadError) => {
        if (!isActive) {
          return;
        }

        setError(getErrorMessage(loadError));
      })
      .finally(() => {
        if (isActive) {
          setLoading(false);
        }
      });

    return () => {
      isActive = false;
    };
  }, [canViewAnnouncements, currentSchoolId]);

  const publishedCount = useMemo(
    () => announcements.filter((announcement) => announcement.status === "active").length,
    [announcements]
  );

  if (!canViewAnnouncements) {
    return <Navigate replace to={schoolPath} />;
  }

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="page-header">
          <div className="portal-section-heading">
            <span className="portal-eyebrow">إعلانات المدرسة</span>
            <h2>إعلانات {school.name}</h2>
            <p>
              يعرض هذا القسم الإعلانات العامة أو الإعلانات الموجهة خصيصًا إلى هذه المدرسة وإلى دورك
              الحالي.
            </p>
          </div>

          {canManageAnnouncements ? (
            <Link className="portal-button portal-button-primary" to="/app/announcements">
              إدارة الإعلانات
            </Link>
          ) : null}
        </div>

        <div className="portal-grid portal-grid-three">
          <article className="portal-stat-card">
            <span>إجمالي الإعلانات</span>
            <strong>{announcements.length}</strong>
          </article>
          <article className="portal-stat-card">
            <span>الإعلانات النشطة</span>
            <strong>{publishedCount}</strong>
          </article>
          <article className="portal-stat-card">
            <span>المدرسة الحالية</span>
            <strong>{school.name}</strong>
          </article>
        </div>
      </section>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الإعلانات...</div> : null}

      {!loading ? (
        announcements.length ? (
          <div className="portal-grid portal-grid-two">
            {announcements.map((announcement) => (
              <Link className="portal-card portal-card-link" key={announcement.id} to={`${schoolPath}/announcements/${announcement.id}`}>
                <div className="button-row">
                  <span className="portal-chip">
                    {audienceLabels[announcement.target_audience] ?? announcement.target_audience}
                  </span>
                  <span className="portal-chip">
                    {announcement.is_all_schools ? "جميع المدارس" : announcement.school?.name_ar ?? school.name}
                  </span>
                </div>
                <h3>{announcement.title}</h3>
                <p>{announcement.body}</p>
                <small className="field-hint">
                  {announcement.creator?.full_name ?? "النظام"} |{" "}
                  {announcement.published_at
                    ? new Date(announcement.published_at).toLocaleString("ar-SA")
                    : "غير منشور"}
                </small>
              </Link>
            ))}
          </div>
        ) : (
          <div className="info-box">لا توجد إعلانات متاحة حاليًا لهذه المدرسة.</div>
        )
      ) : null}
    </>
  );
}
