import { useState } from "react";
import { Link, Navigate, useNavigate } from "react-router-dom";
import { resolvePostLoginPath } from "../../lib/postLogin";
import { getErrorMessage } from "../../services/api";
import { useAuthStore } from "../../stores/authStore";

export function SchoolLoginPage() {
  const navigate = useNavigate();
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);
  const loginSchool = useAuthStore((state) => state.loginSchool);
  const [identifier, setIdentifier] = useState("");
  const [password, setPassword] = useState("");
  const [schoolCode, setSchoolCode] = useState("");
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
          <span className="portal-eyebrow">الدخول المدرسي</span>
          <h2>الدخول إلى موقع المدرسة</h2>
          <p>هذا المسار مخصص للحسابات المرتبطة بمدرسة، ويتطلب إدخال رقم المدرسة المعتمد.</p>
        </div>

        <form
          autoComplete="on"
          className="portal-form"
          onSubmit={async (event) => {
            event.preventDefault();
            setError(null);

            try {
              await loginSchool({ identifier, password, schoolCode: schoolCode.trim().toUpperCase() });
              navigate(resolvePostLoginPath(useAuthStore.getState().user, { mode: "school" }));
            } catch (submitError) {
              setError(getErrorMessage(submitError));
            }
          }}
        >
          <label className="field">
            <span>اسم المستخدم أو البريد</span>
            <input
              autoComplete="username"
              placeholder="teacher أو name@example.com"
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

          <label className="field">
            <span>رقم المدرسة</span>
            <input
              className="school-code-input"
              id="school_code"
              inputMode="text"
              name="school_code"
              autoCapitalize="characters"
              autoComplete="username"
              placeholder="JED-P-00001"
              spellCheck={false}
              onChange={(event) => setSchoolCode(event.target.value)}
              value={schoolCode}
            />
          </label>

          {error ? <div className="error-box">{error}</div> : null}

          <div className="portal-button-row">
            <button className="portal-button portal-button-primary" disabled={status === "loading"} type="submit">
              {status === "loading" ? "جارٍ التحقق..." : "الدخول المدرسي"}
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
