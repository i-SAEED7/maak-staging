import { Link, Navigate } from "react-router-dom";
import { resolvePostLoginPath } from "../../lib/postLogin";
import { useAuthStore } from "../../stores/authStore";

export function LoginPage() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);

  if (status === "loading" && token && !user) {
    return <div className="fullscreen-message">جارٍ تجهيز بيانات الدخول...</div>;
  }

  if (token && user) {
    return <Navigate replace to={resolvePostLoginPath(user, { mode: "auto" })} />;
  }

  return (
    <div className="portal-page-stack">
      <section className="portal-surface portal-auth-surface">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">بوابة الدخول</span>
          <h2>اختر مسار الدخول</h2>
          <p>تم فصل الدخول إلى مسارين واضحين: دخول مدرسي للحسابات المرتبطة بمدرسة، ودخول مركزي للحسابات الإدارية والإشرافية.</p>
        </div>

        <div className="portal-login-choice-grid">
          <Link className="portal-choice-card" to="/login/school">
            <span className="portal-chip">School Access</span>
            <h3>دخول مدرسي</h3>
            <p>للمعلم، مدير المدرسة، ولي الأمر، وأي حساب مرتبط بمدرسة مع التحقق من رقم المدرسة.</p>
          </Link>

          <Link className="portal-choice-card" to="/login/central">
            <span className="portal-chip">Central Access</span>
            <h3>دخول مركزي</h3>
            <p>للسوبر أدمن، الأدمن، المشرف التربوي، وأي حساب مركزي غير مقيد برقم مدرسة أثناء الدخول.</p>
          </Link>
        </div>

        <div className="portal-button-row">
          <Link className="portal-button portal-button-secondary" to="/">
            العودة إلى الرئيسية
          </Link>
        </div>
      </section>
    </div>
  );
}
