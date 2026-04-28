import { useAuthStore } from "../../stores/authStore";

export function PortfolioPage() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const canCreate = permissions.includes("*") || permissions.includes("teacher_portfolios.create");
  const canUpdate = permissions.includes("*") || permissions.includes("teacher_portfolios.update");
  const canDelete = permissions.includes("*") || permissions.includes("teacher_portfolios.delete");

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">المعلم</span>
          <h2>ملف إنجاز المعلم</h2>
          <p className="section-description">
            مساحة منظمة لتجميع إنجازات المعلم ومرفقاته. تم تجهيز القسم والصلاحيات الأساسية
            تمهيدًا لربطه بعمليات الرفع والتصنيف التفصيلية.
          </p>
        </div>
      </div>

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">الحساب</span>
            <h3>{user?.full_name ?? "المعلم"}</h3>
          </div>
        </div>

        <div className="portal-grid portal-grid-four">
          <article className="portal-stat-card">
            <span>صلاحية العرض</span>
            <strong>مفعلة</strong>
          </article>
          <article className="portal-stat-card">
            <span>صلاحية الإنشاء</span>
            <strong>{canCreate ? "مفعلة" : "غير مفعلة"}</strong>
          </article>
          <article className="portal-stat-card">
            <span>صلاحية التعديل</span>
            <strong>{canUpdate ? "مفعلة" : "غير مفعلة"}</strong>
          </article>
          <article className="portal-stat-card">
            <span>صلاحية الحذف</span>
            <strong>{canDelete ? "مفعلة" : "غير مفعلة"}</strong>
          </article>
        </div>

        <div className="info-box">
          القسم ظاهر الآن لحساب المعلم، وتظهر صلاحياته ضمن إدارة الصلاحيات للسوبر أدمن باسم
          "ملف إنجاز المعلم".
        </div>
      </section>
    </section>
  );
}
