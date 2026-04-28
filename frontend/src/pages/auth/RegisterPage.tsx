import { zodResolver } from "@hookform/resolvers/zod";
import { useEffect, useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { accountRegistrationSchema, type AccountRegistrationFormValues } from "../../lib/formSchemas";
import { ApiError, getErrorMessage } from "../../services/api";
import { accountApprovalService } from "../../services/accountApprovalService";
import { portalService, type PortalSchool } from "../../services/portalService";

const STAGES = ["ابتدائي", "متوسط", "ثانوي", "متعدد المراحل"];
const REGISTRATION_SUCCESS_MESSAGE =
  "تم إرسال طلب التسجيل إلى مسؤول الصلاحية، سيتم مراجعة طلبك شكرًا لانضمامك";
const DUPLICATE_REGISTRATION_MESSAGE = "رقم الجوال أو البريد الإلكتروني مسجل مسبقًا.";
const ACCOUNT_TYPES = [
  { value: "parent", label: "ولي أمر" },
  { value: "teacher", label: "معلم" },
  { value: "principal", label: "مدير مدرسة" }
] as const;

function resolveRegistrationError(error: unknown) {
  const message = getErrorMessage(error);
  const payload = error instanceof ApiError ? JSON.stringify(error.payload ?? {}) : "";
  const normalized = `${message} ${payload}`.toLowerCase();
  const mentionsContactField =
    normalized.includes("email") ||
    normalized.includes("phone") ||
    normalized.includes("البريد") ||
    normalized.includes("الجوال");
  const looksDuplicated =
    normalized.includes("already") ||
    normalized.includes("taken") ||
    normalized.includes("unique") ||
    normalized.includes("duplicate") ||
    normalized.includes("مسجل") ||
    normalized.includes("مستخدم");

  if (mentionsContactField && looksDuplicated) {
    return DUPLICATE_REGISTRATION_MESSAGE;
  }

  return message;
}

export function RegisterPage() {
  const [schools, setSchools] = useState<PortalSchool[]>([]);
  const [loadingSchools, setLoadingSchools] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const {
    formState: { errors },
    handleSubmit,
    register,
    reset,
    setValue,
    watch
  } = useForm<AccountRegistrationFormValues>({
    defaultValues: {
      first_name: "",
      second_name: "",
      last_name: "",
      email: "",
      password: "",
      password_confirmation: "",
      phone: "",
      account_type: "",
      stage: "",
      school_id: ""
    },
    resolver: zodResolver(accountRegistrationSchema)
  });
  const selectedStage = watch("stage");

  useEffect(() => {
    setLoadingSchools(true);

    void portalService
      .schools()
      .then((payload) => {
        setSchools(payload);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoadingSchools(false));
  }, []);

  const filteredSchools = useMemo(() => {
    if (!selectedStage) {
      return [];
    }

    return schools.filter((school) => school.stage === selectedStage);
  }, [schools, selectedStage]);

  useEffect(() => {
    setValue("school_id", "");
  }, [selectedStage, setValue]);

  return (
    <div className="portal-page-stack">
      <section className="portal-surface portal-auth-surface">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">تسجيل حساب جديد</span>
          <h2>طلب اعتماد حساب مدرسي</h2>
          <p>
            يتم إرسال الطلب للسوبر أدمن للمراجعة. لا يتم تفعيل الحساب إلا بعد الاعتماد وربطه
            بالمدرسة المختارة.
          </p>
        </div>

        {error ? <div className="error-box">{error}</div> : null}
        {successMessage ? (
          <div className="modal-backdrop" role="presentation">
            <section
              aria-labelledby="registration-success-title"
              aria-modal="true"
              className="modal-card modal-card-narrow registration-success-dialog"
              role="dialog"
            >
              <div className="page-stack">
                <span className="portal-chip">تم إرسال الطلب</span>
                <h3 id="registration-success-title">طلبك قيد المراجعة</h3>
                <p className="registration-dialog-message">{successMessage}</p>
                <button
                  className="portal-button portal-button-primary"
                  onClick={() => setSuccessMessage(null)}
                  type="button"
                >
                  موافق
                </button>
              </div>
            </section>
          </div>
        ) : null}

        <form
          className="page-stack"
          onSubmit={handleSubmit(async (values) => {
            setSubmitting(true);
            setError(null);
            setSuccessMessage(null);

            try {
              await accountApprovalService.register({
                first_name: values.first_name.trim(),
                second_name: values.second_name?.trim() || null,
                last_name: values.last_name.trim(),
                email: values.email.trim(),
                password: values.password,
                password_confirmation: values.password_confirmation,
                phone: values.phone.trim(),
                account_type: values.account_type,
                stage: values.stage,
                school_id: Number(values.school_id)
              });
              setSuccessMessage(REGISTRATION_SUCCESS_MESSAGE);
              reset();
            } catch (submitError) {
              setError(resolveRegistrationError(submitError));
            } finally {
              setSubmitting(false);
            }
          })}
        >
          <div className="grid-three">
            <label className="field">
              <span>الاسم الأول</span>
              <input autoComplete="given-name" {...register("first_name")} />
              {errors.first_name ? <small className="field-hint">{errors.first_name.message}</small> : null}
            </label>
            <label className="field">
              <span>الاسم الثاني</span>
              <input autoComplete="additional-name" {...register("second_name")} />
            </label>
            <label className="field">
              <span>الاسم الأخير</span>
              <input autoComplete="family-name" {...register("last_name")} />
              {errors.last_name ? <small className="field-hint">{errors.last_name.message}</small> : null}
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>البريد الإلكتروني</span>
              <input autoComplete="email" type="email" {...register("email")} />
              {errors.email ? <small className="field-hint">{errors.email.message}</small> : null}
            </label>
            <label className="field">
              <span>رقم الجوال</span>
              <input autoComplete="tel" inputMode="tel" {...register("phone")} />
              {errors.phone ? <small className="field-hint">{errors.phone.message}</small> : null}
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>كلمة المرور</span>
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

          <div className="grid-two">
            <label className="field">
              <span>نوع الحساب</span>
              <select {...register("account_type")}>
                <option value="">اختر نوع الحساب</option>
                {ACCOUNT_TYPES.map((accountType) => (
                  <option key={accountType.value} value={accountType.value}>
                    {accountType.label}
                  </option>
                ))}
              </select>
              {errors.account_type ? <small className="field-hint">{errors.account_type.message}</small> : null}
            </label>
            <label className="field">
              <span>المرحلة</span>
              <select {...register("stage")}>
                <option value="">اختر المرحلة</option>
                {STAGES.map((stage) => (
                  <option key={stage} value={stage}>
                    {stage}
                  </option>
                ))}
              </select>
              {errors.stage ? <small className="field-hint">{errors.stage.message}</small> : null}
            </label>
            <label className="field">
              <span>المدرسة</span>
              <select disabled={!selectedStage || loadingSchools} {...register("school_id")}>
                <option value="">
                  {selectedStage ? "اختر المدرسة" : "اختر المرحلة أولًا"}
                </option>
                {filteredSchools.map((school) => (
                  <option key={school.id} value={school.id}>
                    {school.name_ar}
                  </option>
                ))}
              </select>
              {errors.school_id ? <small className="field-hint">{errors.school_id.message}</small> : null}
              {selectedStage && !loadingSchools && filteredSchools.length === 0 ? (
                <small className="field-hint">لا توجد مدارس متاحة لهذه المرحلة حاليًا.</small>
              ) : null}
            </label>
          </div>

          <div className="portal-button-row">
            <button className="portal-button portal-button-primary" disabled={submitting} type="submit">
              {submitting ? "جارٍ إرسال الطلب..." : "إرسال طلب الاعتماد"}
            </button>
          </div>
        </form>
      </section>
    </div>
  );
}
