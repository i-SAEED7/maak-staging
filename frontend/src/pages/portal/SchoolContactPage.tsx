import { Link } from "react-router-dom";
import { useSchoolSite } from "../../lib/schoolSite";

export function SchoolContactPage() {
  const { school, canSendMessages, isSupervisor, accessibleSchools, schoolPath } = useSchoolSite();

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">تواصل معنا</span>
          <h2>بيانات التواصل الخاصة بالمدرسة</h2>
        </div>

        <div className="portal-grid portal-grid-two">
          <article className="portal-card">
            <h3>بيانات المدرسة</h3>
            <div className="detail-list">
              <div className="detail-list-item">
                <span className="detail-label">اسم المدرسة</span>
                <strong>{school.name}</strong>
              </div>
              <div className="detail-list-item">
                <span className="detail-label">المرحلة</span>
                <strong>{school.stage ?? "-"}</strong>
              </div>
              <div className="detail-list-item">
                <span className="detail-label">البرنامج</span>
                <strong>{school.program_type ?? "-"}</strong>
              </div>
            </div>
          </article>

          <article className="portal-card">
            <h3>القيادات المرتبطة</h3>
            <div className="detail-list">
              <div className="detail-list-item">
                <span className="detail-label">مدير المدرسة</span>
                <strong>{school.principal?.full_name ?? "غير محدد"}</strong>
              </div>
              <div className="detail-list-item">
                <span className="detail-label">المشرف التربوي</span>
                <strong>{school.supervisor?.full_name ?? "غير محدد"}</strong>
              </div>
              <div className="detail-list-item">
                <span className="detail-label">العنوان</span>
                <strong>{school.address ?? school.city ?? "غير محدد"}</strong>
              </div>
            </div>
          </article>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">الوصول السريع</span>
          <h2>خيارات التواصل والتنقل</h2>
        </div>

        <div className="portal-grid portal-grid-two">
          {canSendMessages ? (
            <Link className="portal-card portal-card-link" to="/app/messages">
              <h3>الرسائل الداخلية</h3>
              <p>يمكنك التواصل مع الأدوار المرتبطة بهذه المدرسة عبر نظام الرسائل الداخلي.</p>
            </Link>
          ) : null}

          <Link className="portal-card portal-card-link" to={`${schoolPath}/announcements`}>
            <h3>الإعلانات</h3>
            <p>الانتقال إلى الإعلانات الحالية الخاصة بالمدرسة أو الإعلانات العامة المرتبطة بها.</p>
          </Link>

          {isSupervisor && accessibleSchools.length > 1 ? (
            <Link className="portal-card portal-card-link" to="/select-school">
              <h3>تغيير المدرسة</h3>
              <p>العودة إلى قائمة المدارس المسندة لك واختيار مدرسة أخرى للمتابعة.</p>
            </Link>
          ) : null}

          <article className="portal-contact-note">
            <strong>الإحداثيات</strong>
            <p>
              {school.location_lat ?? "-"} / {school.location_lng ?? "-"}
            </p>
          </article>
        </div>
      </section>
    </>
  );
}
