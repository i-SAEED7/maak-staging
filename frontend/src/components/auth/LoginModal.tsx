import { zodResolver } from "@hookform/resolvers/zod";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import {
  forgotPasswordSchema,
  loginSchema,
  type ForgotPasswordFormValues,
  type LoginFormValues
} from "../../lib/formSchemas";
import { resolvePostLoginPath } from "../../lib/postLogin";
import { getErrorMessage } from "../../services/api";
import { authService } from "../../services/authService";
import { useAuthStore } from "../../stores/authStore";

type LoginModalProps = {
  open: boolean;
  onClose: () => void;
};

type ModalMode = "login" | "forgot";

export function LoginModal({ open, onClose }: LoginModalProps) {
  const navigate = useNavigate();
  const login = useAuthStore((state) => state.login);
  const status = useAuthStore((state) => state.status);
  const [mode, setMode] = useState<ModalMode>("login");
  const [forgotSuccess, setForgotSuccess] = useState<string | null>(null);
  const loginForm = useForm<LoginFormValues>({
    defaultValues: {
      identifier: "",
      password: ""
    },
    resolver: zodResolver(loginSchema)
  });
  const forgotForm = useForm<ForgotPasswordFormValues>({
    defaultValues: {
      email: ""
    },
    resolver: zodResolver(forgotPasswordSchema)
  });

  useEffect(() => {
    if (!open) {
      setMode("login");
      setForgotSuccess(null);
      loginForm.clearErrors();
      forgotForm.clearErrors();
    }
  }, [forgotForm, loginForm, open]);

  if (!open) {
    return null;
  }

  const closeAndGo = (path: string) => {
    onClose();
    navigate(path);
  };

  return (
    <div className="modal-backdrop" role="presentation">
      <section
        aria-labelledby="login-modal-title"
        aria-modal="true"
        className="modal-card modal-card-narrow auth-dialog"
        role="dialog"
      >
        <div className="modal-title-row">
          <div>
            <span className="portal-chip">{mode === "login" ? "تسجيل الدخول" : "استعادة كلمة المرور"}</span>
            <h3 id="login-modal-title">{mode === "login" ? "مرحبًا بك" : "استعادة كلمة المرور"}</h3>
          </div>
          <button aria-label="إغلاق" className="modal-close-button" onClick={onClose} type="button">
            ×
          </button>
        </div>

        {mode === "login" ? (
          <form
            className="page-stack"
            onSubmit={loginForm.handleSubmit(async (values) => {
              loginForm.clearErrors("root");

              try {
                await login(values);
                const user = useAuthStore.getState().user;
                onClose();
                navigate(resolvePostLoginPath(user, { mode: "auto" }), { replace: true });
              } catch (submitError) {
                loginForm.setError("root", { message: getErrorMessage(submitError) });
              }
            })}
          >
            <label className="field">
              <span>اسم المستخدم أو البريد الإلكتروني</span>
              <input
                autoComplete="username"
                placeholder="name@example.com"
                {...loginForm.register("identifier")}
              />
              {loginForm.formState.errors.identifier ? (
                <small className="field-hint">{loginForm.formState.errors.identifier.message}</small>
              ) : null}
            </label>

            <label className="field">
              <span>كلمة المرور</span>
              <input
                autoComplete="current-password"
                placeholder="••••••••"
                type="password"
                {...loginForm.register("password")}
              />
              {loginForm.formState.errors.password ? (
                <small className="field-hint">{loginForm.formState.errors.password.message}</small>
              ) : null}
            </label>

            {loginForm.formState.errors.root?.message ? (
              <div className="error-box">{loginForm.formState.errors.root.message}</div>
            ) : null}

            <button className="portal-button portal-button-primary" disabled={status === "loading"} type="submit">
              {status === "loading" ? "جارٍ تسجيل الدخول..." : "تسجيل الدخول"}
            </button>

            <div className="auth-dialog-links">
              <button className="link-button" onClick={() => closeAndGo("/register")} type="button">
                مستخدم جديد
              </button>
              <button
                className="link-button"
                onClick={() => {
                  loginForm.clearErrors();
                  setMode("forgot");
                }}
                type="button"
              >
                نسيت كلمة المرور
              </button>
            </div>
          </form>
        ) : (
          <form
            className="page-stack"
            onSubmit={forgotForm.handleSubmit(async (values) => {
              forgotForm.clearErrors("root");
              setForgotSuccess(null);

              try {
                const message = await authService.forgotPassword(values.email.trim());
                setForgotSuccess(message);
              } catch (submitError) {
                forgotForm.setError("root", { message: getErrorMessage(submitError) });
              }
            })}
          >
            <p className="auth-dialog-description">
              أدخل بريدك الإلكتروني، وسنرسل لك رابطًا آمنًا لإعادة تعيين كلمة المرور.
            </p>

            <label className="field">
              <span>البريد الإلكتروني</span>
              <input autoComplete="email" placeholder="name@example.com" type="email" {...forgotForm.register("email")} />
              {forgotForm.formState.errors.email ? (
                <small className="field-hint">{forgotForm.formState.errors.email.message}</small>
              ) : null}
            </label>

            {forgotForm.formState.errors.root?.message ? (
              <div className="error-box">{forgotForm.formState.errors.root.message}</div>
            ) : null}
            {forgotSuccess ? <div className="info-box">{forgotSuccess}</div> : null}

            <div className="portal-button-row">
              <button className="portal-button portal-button-primary" type="submit">
                إرسال
              </button>
              <button className="portal-button portal-button-secondary" onClick={() => setMode("login")} type="button">
                العودة لتسجيل الدخول
              </button>
            </div>
          </form>
        )}
      </section>
    </div>
  );
}
