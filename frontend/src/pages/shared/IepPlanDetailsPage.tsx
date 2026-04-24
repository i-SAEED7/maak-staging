import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { iepPlanService, type IepPlanSummary } from "../../services/iepPlanService";
import { useAuthStore } from "../../stores/authStore";

export function IepPlanDetailsPage() {
  const { planId } = useParams();
  const user = useAuthStore((state) => state.user);
  const isParent = user?.role === "parent";
  const [plan, setPlan] = useState<IepPlanSummary | null>(null);
  const [loading, setLoading] = useState(true);
  const [acknowledging, setAcknowledging] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!planId) {
      setError("تعذر تحديد الخطة المطلوبة.");
      setLoading(false);
      return;
    }

    setLoading(true);

    void iepPlanService
      .details(planId)
      .then(setPlan)
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [planId]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">تفاصيل الخطة</span>
          <h2>تفاصيل الخطة الفردية</h2>
        </div>
        <div className="button-row">
          {planId && !isParent ? (
            <Link className="button button-ghost" to={`/app/iep-plans/${planId}/edit`}>
              تعديل الخطة
            </Link>
          ) : null}
          {plan && isParent ? (
            plan.current_user_acknowledged_at ? (
              <span className="chip">
                تم الإقرار بالاطلاع في{" "}
                {new Date(plan.current_user_acknowledged_at).toLocaleString("ar-SA")}
              </span>
            ) : (
              <button
                className="button button-primary"
                disabled={acknowledging}
                onClick={() => {
                  if (!planId) {
                    return;
                  }

                  setAcknowledging(true);
                  setError(null);

                  void iepPlanService
                    .acknowledge(planId)
                    .then((updatedPlan) => setPlan(updatedPlan))
                    .catch((loadError) => setError(getErrorMessage(loadError)))
                    .finally(() => setAcknowledging(false));
                }}
                type="button"
              >
                {acknowledging ? "جارٍ تسجيل الإقرار..." : "إقرار بالاطلاع"}
              </button>
            )
          ) : null}
          <Link className="button button-secondary" to="/app/iep-plans">
            العودة إلى الخطط
          </Link>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الخطة...</div> : null}

      {!loading && plan ? (
        <>
          <section className="surface-card page-stack">
            <div className="detail-grid">
              <div>
                <span className="detail-label">العنوان</span>
                <strong>{plan.title}</strong>
              </div>
              <div>
                <span className="detail-label">المدرسة</span>
                <strong>{plan.school?.name_ar ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المرحلة</span>
                <strong>{plan.school?.stage ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">المعلم</span>
                <strong>{plan.teacher?.full_name ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الطالب</span>
                <strong>{plan.student?.full_name ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الحالة</span>
                <strong>{plan.status}</strong>
              </div>
            </div>
          </section>

          <section className="surface-card page-stack">
            <h3>محتوى الخطة</h3>
            <div className="detail-paragraph">
              <strong>الملخص</strong>
              <p>{plan.summary ?? "لا يوجد ملخص."}</p>
            </div>
            <div className="detail-paragraph">
              <strong>نقاط القوة</strong>
              <p>{plan.strengths ?? "لا توجد بيانات."}</p>
            </div>
            <div className="detail-paragraph">
              <strong>الاحتياجات</strong>
              <p>{plan.needs ?? "لا توجد بيانات."}</p>
            </div>
            <div className="detail-paragraph">
              <strong>التسهيلات</strong>
              <p>{plan.accommodations?.join("، ") ?? "لا توجد تسهيلات محددة."}</p>
            </div>
          </section>

          <section className="surface-card page-stack">
            <h3>الأهداف</h3>
            {plan.goals?.length ? (
              <div className="detail-list">
                {plan.goals.map((goal, index) => {
                  const item = goal as {
                    id?: number;
                    domain?: string;
                    goal_text?: string;
                    measurement_method?: string;
                  };

                  return (
                    <div className="detail-list-item" key={item.id ?? index}>
                      <strong>{item.domain ?? `هدف ${index + 1}`}</strong>
                      <span>{item.goal_text ?? "-"}</span>
                      <small>{item.measurement_method ?? "بدون طريقة قياس محددة"}</small>
                    </div>
                  );
                })}
              </div>
            ) : (
              <div className="info-box">لا توجد أهداف مرتبطة بهذه الخطة.</div>
            )}
          </section>
        </>
      ) : null}
    </section>
  );
}
