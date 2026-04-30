import { useEffect, useState } from "react";
import { NavLink, Outlet, useSearchParams } from "react-router-dom";
import { Sparkles } from "lucide-react";
import { LoginModal } from "../auth/LoginModal";
import { portalNavigation } from "../../lib/portalContent";
import { useAuthStore } from "../../stores/authStore";
import { resolvePostLoginPath } from "../../lib/postLogin";

const portalTickerItems = [
  {
    title: "بوابة معاك",
    body: "تجربة موحدة للوصول إلى الخدمات التعليمية والإشرافية"
  },
  {
    title: "البرامج التعليمية",
    body: "استعراض برامج يسير وفرط الحركة وتشتت الانتباه"
  },
  {
    title: "مواقع المدارس",
    body: "خريطة تعريفية تعرض المدارس والخدمات المتاحة"
  }
];

const portalTickerLoopItems = [
  ...portalTickerItems,
  ...portalTickerItems,
  ...portalTickerItems,
  ...portalTickerItems
];

export function PortalLayout() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const [searchParams, setSearchParams] = useSearchParams();
  const [loginModalOpen, setLoginModalOpen] = useState(searchParams.get("login") === "1");

  useEffect(() => {
    if (searchParams.get("login") === "1") {
      setLoginModalOpen(true);
    }
  }, [searchParams]);

  const closeLoginModal = () => {
    setLoginModalOpen(false);

    if (searchParams.get("login") === "1") {
      const nextParams = new URLSearchParams(searchParams);
      nextParams.delete("login");
      setSearchParams(nextParams, { replace: true });
    }
  };

  return (
    <div className="portal-shell">
      <header className="portal-header">
        <div className="portal-nav-shell" data-version="maak-header-v2">
          <div className="portal-brand">
            <strong>بوابة معاك</strong>
            <span>إدارة تنمية القدرات - قسم ذوي الإعاقة</span>
          </div>

          <nav className="portal-nav hidden md:flex">
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
              <>
                <NavLink className="portal-register-button hidden sm:inline-flex" to="/register">
                  سجل الآن
                </NavLink>
                <button className="portal-login-button" onClick={() => setLoginModalOpen(true)} type="button">
                  تسجيل الدخول
                </button>
              </>
            )}
          </div>
        </div>

        <div className="maak-announcement-bar" aria-label="شريط الإعلانات">
          <div className="maak-announcement-bar__track">
            {portalTickerLoopItems.map((item, index) => (
              <span className="maak-announcement-bar__item" key={`${item.title}-${index}`}>
                <Sparkles aria-hidden="true" size={18} />
                <strong>{item.title}</strong>
                <span>:</span>
                <span>{item.body}</span>
              </span>
            ))}
          </div>
        </div>
      </header>

      <main className="portal-main">
        <Outlet />
      </main>

      <footer className="portal-footer">
        <div>
          <strong>بوابة معاك</strong>
          <p>واجهة تعريفية موحدة مع ربط آمن بمنظومة التشغيل الداخلية.</p>
        </div>
        <small>جميع الحقوق محفوظة لبوابة معاك.</small>
      </footer>

      <LoginModal open={loginModalOpen} onClose={closeLoginModal} />
    </div>
  );
}
