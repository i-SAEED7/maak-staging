import { useState } from "react";
import { Link, Navigate, useNavigate } from "react-router-dom";
import { resolvePostLoginPath } from "../../lib/postLogin";
import { getErrorMessage } from "../../services/api";
import { useAuthStore } from "../../stores/authStore";

export function CentralLoginPage() {
  const navigate = useNavigate();
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);
  const loginCentral = useAuthStore((state) => state.loginCentral);
  const [identifier, setIdentifier] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);

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
          <span className="portal-eyebrow">الدخول المركزي</span>
          <h2>الدخول إلى النظام المركزي</h2>
          <p>هذا المسار مخصص للحسابات المركزية فقط مثل السوبر أدمن، الأدمن، والمشرف التربوي.</p>
        </div>

        <form
          className="portal-form"
          onSubmit={async (event) => {
            event.preventDefault();
            setError(null);

            try {
              await loginCentral({ identifier, password });
              navigate(resolvePostLoginPath(useAuthStore.getState().user, { mode: "central" }));
            } catch (submitError) {
              setError(getErrorMessage(submitError));
            }
          }}
        >
          <label className="field">
            <span>اسم المستخدم أو البريد</span>
            <input
              autoComplete="username"
              placeholder="superadmin أو name@example.com"
              onChange={(event) => setIdentifier(event.target.value)}
              value={identifier}
            />
          </label>

          <label className="field">
            <span>كلمة المرور</span>
            <input
              autoComplete="current-password"
              placeholder="••••••••"
              onChange={(event) => setPassword(event.target.value)}
              type="password"
              value={password}
            />
          </label>

          {error ? <div className="error-box">{error}</div> : null}

          <div className="portal-button-row">
            <button className="portal-button portal-button-primary" disabled={status === "loading"} type="submit">
              {status === "loading" ? "جارٍ تسجيل الدخول..." : "الدخول المركزي"}
            </button>
            <Link className="portal-button portal-button-secondary" to="/login">
              العودة لبوابة الدخول
            </Link>
          </div>
        </form>
      </section>
    </div>
  );
}
