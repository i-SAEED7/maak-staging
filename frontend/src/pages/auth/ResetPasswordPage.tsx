import { zodResolver } from "@hookform/resolvers/zod";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { Link, useSearchParams } from "react-router-dom";
import { resetPasswordSchema, type ResetPasswordFormValues } from "../../lib/formSchemas";
import { getErrorMessage } from "../../services/api";
import { authService } from "../../services/authService";

export function ResetPasswordPage() {
  const [searchParams] = useSearchParams();
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const token = searchParams.get("token") ?? "";
  const email = searchParams.get("email") ?? "";
  const {
    formState: { errors, isSubmitting },
    handleSubmit,
    register,
    reset,
    setError
  } = useForm<ResetPasswordFormValues>({
    defaultValues: {
      email,
      token,
      password: "",
      password_confirmation: ""
    },
    resolver: zodResolver(resetPasswordSchema)
  });

  useEffect(() => {
    reset({
      email,
      token,
      password: "",
      password_confirmation: ""
    });
  }, [email, reset, token]);

  return (
    <div className="portal-page-stack">
      <section className="portal-surface portal-auth-surface">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">استعادة كلمة المرور</span>
          <h2>تعيين كلمة مرور جديدة</h2>
          <p>أدخل البريد والرمز المرسل إلى بريدك، ثم اختر كلمة مرور جديدة.</p>
        </div>

        <form
          className="page-stack"
          onSubmit={handleSubmit(async (values) => {
            setSuccessMessage(null);

            try {
              const message = await authService.resetPassword({
                email: values.email.trim(),
                token: values.token.trim(),
                password: values.password,
                password_confirmation: values.password_confirmation
              });
              setSuccessMessage(message);
              reset({
                email: values.email,
                token: values.token,
                password: "",
                password_confirmation: ""
              });
            } catch (submitError) {
              setError("root", { message: getErrorMessage(submitError) });
            }
          })}
        >
          <div className="grid-two">
            <label className="field">
              <span>البريد الإلكتروني</span>
              <input autoComplete="email" type="email" {...register("email")} />
              {errors.email ? <small className="field-hint">{errors.email.message}</small> : null}
            </label>
            <label className="field">
              <span>رمز الاستعادة</span>
              <input autoComplete="one-time-code" {...register("token")} />
              {errors.token ? <small className="field-hint">{errors.token.message}</small> : null}
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>كلمة المرور الجديدة</span>
              <input autoComplete="new-password" type="password" {...register("password")} />
              {errors.password ? <small className="field-hint">{errors.password.message}</small> : null}
            </label>
            <label className="field">
              <span>تأكيد كلمة المرور</span>
              <input autoComplete="new-password" type="password" {...register("password_confirmation")} />
              {errors.password_confirmation ? (
                <small className="field-hint">{errors.password_confirmation.message}</small>
              ) : null}
            </label>
          </div>

          {errors.root?.message ? <div className="error-box">{errors.root.message}</div> : null}
          {successMessage ? <div className="info-box">{successMessage}</div> : null}

          <div className="portal-button-row">
            <button className="portal-button portal-button-primary" disabled={isSubmitting} type="submit">
              {isSubmitting ? "جارٍ حفظ كلمة المرور..." : "حفظ كلمة المرور الجديدة"}
            </button>
            <Link className="portal-button portal-button-secondary" to="/?login=1">
              العودة لتسجيل الدخول
            </Link>
          </div>
        </form>
      </section>
    </div>
  );
}
