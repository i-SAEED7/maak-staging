import { NavLink, Outlet } from "react-router-dom";
import { portalNavigation } from "../../lib/portalContent";
import { useAuthStore } from "../../stores/authStore";
import { resolvePostLoginPath } from "../../lib/postLogin";

export function PortalLayout() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);

  return (
    <div className="portal-shell">
      <header className="portal-header">
        <div className="portal-nav-shell">
          <div className="portal-brand">
            <span>بوابة قسم ذوي الإعاقة</span>
            <strong>إدارة التعليم بمحافظة جدة</strong>
          </div>

          <nav className="portal-nav">
            {portalNavigation.map((item) => (
              <NavLink
                key={item.to}
                className={({ isActive }) => `portal-nav-link${isActive ? " is-active" : ""}`}
                end={item.to === "/"}
                to={item.to}
              >
                {item.label}
              </NavLink>
            ))}
          </nav>

          <div className="portal-nav-actions">
            {token && user ? (
              <NavLink className="portal-login-button" to={resolvePostLoginPath(user)}>
                بوابتي
              </NavLink>
            ) : (
              <NavLink className="portal-login-button" to="/login">
                تسجيل الدخول
              </NavLink>
            )}
          </div>
        </div>
      </header>

      <main className="portal-main">
        <Outlet />
      </main>

      <footer className="portal-footer">
        <div>
          <strong>بوابة قسم ذوي الإعاقة</strong>
          <p>واجهة تعريفية موحدة مع ربط آمن بمنظومة التشغيل الداخلية.</p>
        </div>
        <small>جميع الحقوق محفوظة لإدارة التعليم بمحافظة جدة.</small>
      </footer>
    </div>
  );
}
