import { useEffect, useMemo, useState } from "react";
import { Link, useLocation, useNavigate, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getRoleLabel } from "../../lib/uiText";
import { getErrorMessage } from "../../services/api";
import { announcementService, type AnnouncementItem } from "../../services/announcementService";
import { useAuthStore } from "../../stores/authStore";

const audienceLabels: Record<AnnouncementItem["target_audience"], string> = {
  teacher: "معلم",
  principal: "مدير",
  supervisor: "مشرف",
  parent: "ولي أمر",
  general: "عامة"
};

export function AnnouncementDetailsPage() {
  const { announcementId } = useParams();
  const location = useLocation();
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const [announcement, setAnnouncement] = useState<AnnouncementItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const canViewViews = ["super_admin", "admin"].includes(user?.role ?? "");

  const viewsColumns: DataColumn<NonNullable<AnnouncementItem["views"]>[number]>[] = [
    { key: "viewer_name", label: "اسم المستخدم", render: (row) => row.viewer_name ?? "-" },
    { key: "viewer_role", label: "نوع الحساب", render: (row) => getRoleLabel(row.viewer_role) },
    {
      key: "viewed_at",
      label: "التاريخ والوقت",
      render: (row) => (row.viewed_at ? new Date(row.viewed_at).toLocaleString("ar-SA") : "-")
    }
  ];

  const backTarget = useMemo(() => {
    if (location.pathname.includes("/schools/")) {
      const parts = location.pathname.split("/");
      const schoolSlug = parts[2];
      return `/schools/${schoolSlug}/announcements`;
    }

    return "/app/announcements";
  }, [location.pathname]);

  useEffect(() => {
    if (!announcementId) {
      setError("تعذر تحديد الإعلان المطلوب.");
      setLoading(false);
      return;
    }

    let isActive = true;
    setLoading(true);

    void announcementService
      .details(announcementId)
      .then((payload) => {
        if (!isActive) {
          return;
        }

        setAnnouncement(payload);
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
  }, [announcementId]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الإعلانات</span>
          <h2>تفاصيل الإعلان</h2>
        </div>

        <div className="button-row">
          <Link className="button button-secondary" to={backTarget}>
            العودة إلى الإعلانات
          </Link>
          <button className="button button-ghost" onClick={() => navigate(-1)} type="button">
            رجوع
          </button>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الإعلان...</div> : null}

      {!loading && announcement ? (
        <>
          <section className="surface-card page-stack">
            <div className="chip-row">
              <span className="chip">{audienceLabels[announcement.target_audience] ?? announcement.target_audience}</span>
              <span className="chip">
                {announcement.is_all_schools ? "جميع المدارس" : announcement.school?.name_ar ?? "مدرسة محددة"}
              </span>
              <span className="chip">{announcement.status === "active" ? "نشط" : "غير نشط"}</span>
            </div>

            <div className="detail-list">
              <div className="detail-list-item">
                <span className="detail-label">العنوان</span>
                <strong>{announcement.title}</strong>
              </div>
              <div className="detail-list-item">
                <span className="detail-label">نص الإعلان</span>
                <div className="detail-paragraph">
                  <p>{announcement.body}</p>
                </div>
              </div>
              <div className="detail-grid">
                <div className="detail-list-item">
                  <span className="detail-label">أنشأه</span>
                  <strong>{announcement.creator?.full_name ?? "-"}</strong>
                </div>
                <div className="detail-list-item">
                  <span className="detail-label">الدور</span>
                  <strong>{getRoleLabel(announcement.creator?.role)}</strong>
                </div>
                <div className="detail-list-item">
                  <span className="detail-label">تاريخ النشر</span>
                  <strong>
                    {announcement.published_at
                      ? new Date(announcement.published_at).toLocaleString("ar-SA")
                      : "-"}
                  </strong>
                </div>
              </div>
            </div>
          </section>

          {canViewViews ? (
            <section className="surface-card page-stack">
              <div className="page-header">
                <div>
                  <span className="eyebrow">المشاهدات</span>
                  <h3>سجل المشاهدات</h3>
                </div>
              </div>

              <DataTable
                columns={viewsColumns}
                rows={announcement.views ?? []}
                emptyMessage="لا توجد مشاهدات مسجلة حتى الآن."
              />
            </section>
          ) : null}
        </>
      ) : null}
    </section>
  );
}
