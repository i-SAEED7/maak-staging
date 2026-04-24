import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { iepPlanService, type IepGoalPayload, type IepPlanSummary } from "../../services/iepPlanService";

type GoalForm = {
  local_id: string;
  domain: string;
  goal_text: string;
  measurement_method: string;
};

type PlanForm = {
  title: string;
  start_date: string;
  end_date: string;
  summary: string;
  strengths: string;
  needs: string;
  accommodations: string;
  goals: GoalForm[];
};

function toForm(plan: IepPlanSummary): PlanForm {
  const goals = (plan.goals ?? []).map((goal) => {
    const item = goal as {
      domain?: string;
      goal_text?: string;
      measurement_method?: string;
    };

    return {
      local_id: createGoalId(),
      domain: item.domain ?? "",
      goal_text: item.goal_text ?? "",
      measurement_method: item.measurement_method ?? ""
    };
  });

  return {
    title: plan.title ?? "",
    start_date: plan.start_date ?? "",
    end_date: plan.end_date ?? "",
    summary: plan.summary ?? "",
    strengths: plan.strengths ?? "",
    needs: plan.needs ?? "",
    accommodations: plan.accommodations?.join("، ") ?? "",
    goals: goals.length ? goals : [{ local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }]
  };
}

function createGoalId() {
  return `${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
}

function isGoalComplete(goal: GoalForm) {
  return (
    goal.domain.trim() !== "" &&
    goal.goal_text.trim() !== "" &&
    goal.measurement_method.trim() !== ""
  );
}

function hasAnyGoalData(goal: GoalForm) {
  return (
    goal.domain.trim() !== "" ||
    goal.goal_text.trim() !== "" ||
    goal.measurement_method.trim() !== ""
  );
}

function toPayload(form: PlanForm) {
  return {
    title: form.title.trim(),
    start_date: form.start_date || null,
    end_date: form.end_date || null,
    summary: form.summary.trim() || null,
    strengths: form.strengths.trim() || null,
    needs: form.needs.trim() || null,
    accommodations: form.accommodations
      .split("،")
      .map((item) => item.trim())
      .filter(Boolean),
    goals: form.goals
      .filter((goal) => isGoalComplete(goal))
      .map(
        (goal, index): IepGoalPayload => ({
          domain: goal.domain.trim() || `هدف ${index + 1}`,
          goal_text: goal.goal_text.trim(),
          measurement_method: goal.measurement_method.trim(),
          sort_order: index
        })
      )
  };
}

export function IepPlanEditPage() {
  const { planId } = useParams();
  const navigate = useNavigate();
  const [plan, setPlan] = useState<IepPlanSummary | null>(null);
  const [form, setForm] = useState<PlanForm | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
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
      .then((payload) => {
        setPlan(payload);
        setForm(toForm(payload));
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [planId]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">تعديل الخطة</span>
          <h2>تعديل الخطة الفردية</h2>
        </div>
        <div className="button-row">
          {planId ? (
            <Link className="button button-secondary" to={`/app/iep-plans/${planId}`}>
              عرض الخطة
            </Link>
          ) : null}
          <Link className="button button-ghost" to="/app/iep-plans">
            العودة إلى الخطط
          </Link>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل بيانات الخطة...</div> : null}

      {!loading && plan && form ? (
        <section className="surface-card page-stack">
          <div className="info-box">
            تعديل خطة: <strong>{plan.title}</strong> للطالب <strong>{plan.student?.full_name ?? "-"}</strong>
          </div>

          <form
            className="page-stack"
            onSubmit={async (event) => {
              event.preventDefault();

              if (!planId || !form) {
                return;
              }

              if (form.goals.some((goal) => hasAnyGoalData(goal) && !isGoalComplete(goal))) {
                setError("يجب تعبئة المجال ونص الهدف وطريقة القياس لكل هدف قبل حفظ التعديلات.");
                return;
              }

              if (!form.goals.some((goal) => isGoalComplete(goal))) {
                setError("يجب الإبقاء على هدف مكتمل واحد على الأقل داخل الخطة.");
                return;
              }

              setSaving(true);
              setError(null);

              try {
                await iepPlanService.update(planId, toPayload(form));
                navigate(`/app/iep-plans/${planId}`);
              } catch (saveError) {
                setError(getErrorMessage(saveError));
              } finally {
                setSaving(false);
              }
            }}
          >
            <div className="grid-two">
              <label className="field">
                <span>عنوان الخطة</span>
                <input
                  onChange={(event) => setForm((current) => current ? { ...current, title: event.target.value } : current)}
                  required
                  value={form.title}
                />
              </label>
              <label className="field">
                <span>التسهيلات</span>
                <input
                  onChange={(event) =>
                    setForm((current) => current ? { ...current, accommodations: event.target.value } : current)
                  }
                  placeholder="افصل بين العناصر بفاصلة عربية"
                  value={form.accommodations}
                />
              </label>
            </div>

            <div className="grid-two">
              <label className="field">
                <span>تاريخ البداية</span>
                <input
                  onChange={(event) =>
                    setForm((current) => current ? { ...current, start_date: event.target.value } : current)
                  }
                  type="date"
                  value={form.start_date}
                />
              </label>
              <label className="field">
                <span>تاريخ النهاية</span>
                <input
                  onChange={(event) =>
                    setForm((current) => current ? { ...current, end_date: event.target.value } : current)
                  }
                  type="date"
                  value={form.end_date}
                />
              </label>
            </div>

            <label className="field">
              <span>الملخص</span>
              <textarea
                onChange={(event) =>
                  setForm((current) => current ? { ...current, summary: event.target.value } : current)
                }
                rows={3}
                value={form.summary}
              />
            </label>

            <div className="grid-two">
              <label className="field">
                <span>نقاط القوة</span>
                <textarea
                  onChange={(event) =>
                    setForm((current) => current ? { ...current, strengths: event.target.value } : current)
                  }
                  rows={4}
                  value={form.strengths}
                />
              </label>
              <label className="field">
                <span>الاحتياجات</span>
                <textarea
                  onChange={(event) =>
                    setForm((current) => current ? { ...current, needs: event.target.value } : current)
                  }
                  rows={4}
                  value={form.needs}
                />
              </label>
            </div>

            <section className="page-stack">
              <div className="page-header">
                <div>
                  <span className="eyebrow">الأهداف</span>
                  <h3>أهداف الخطة</h3>
                </div>
                <button
                  className="button button-secondary"
                  onClick={() => {
                    if (form.goals.some((goal) => !isGoalComplete(goal))) {
                      setError("يجب تعبئة الهدف الحالي بالكامل قبل إضافة هدف جديد.");
                      return;
                    }

                    setError(null);
                    setForm((current) =>
                      current
                        ? {
                            ...current,
                            goals: [
                              ...current.goals,
                              { local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }
                            ]
                          }
                        : current
                    );
                  }}
                  type="button"
                >
                  إضافة هدف
                </button>
              </div>

              {form.goals.map((goal, index) => (
                <div className="goal-card" key={goal.local_id}>
                  <div className="grid-two">
                    <label className="field">
                      <span>المجال</span>
                      <input
                        onChange={(event) =>
                          setForm((current) =>
                            current
                              ? {
                                  ...current,
                                  goals: current.goals.map((item, itemIndex) =>
                                    itemIndex === index ? { ...item, domain: event.target.value } : item
                                  )
                                }
                              : current
                          )
                        }
                        value={goal.domain}
                      />
                    </label>
                    <label className="field">
                      <span>طريقة القياس</span>
                      <input
                        onChange={(event) =>
                          setForm((current) =>
                            current
                              ? {
                                  ...current,
                                  goals: current.goals.map((item, itemIndex) =>
                                    itemIndex === index
                                      ? { ...item, measurement_method: event.target.value }
                                      : item
                                  )
                                }
                              : current
                          )
                        }
                        value={goal.measurement_method}
                      />
                    </label>
                  </div>

                  <label className="field">
                    <span>نص الهدف</span>
                    <textarea
                      onChange={(event) =>
                        setForm((current) =>
                          current
                            ? {
                                ...current,
                                goals: current.goals.map((item, itemIndex) =>
                                  itemIndex === index ? { ...item, goal_text: event.target.value } : item
                                )
                              }
                            : current
                        )
                      }
                      rows={3}
                      value={goal.goal_text}
                    />
                  </label>

                  <button
                    className="button button-ghost"
                    onClick={() => {
                      setForm((current) =>
                        current
                          ? {
                              ...current,
                              goals: current.goals.filter((_, itemIndex) => itemIndex !== index).length
                                ? current.goals.filter((_, itemIndex) => itemIndex !== index)
                                : [{ local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }]
                            }
                          : current
                      );
                    }}
                    type="button"
                  >
                    حذف الهدف
                  </button>
                </div>
              ))}
            </section>

            <div className="button-row">
              <button className="button button-primary" disabled={saving} type="submit">
                {saving ? "جارٍ حفظ التعديلات..." : "حفظ التعديلات"}
              </button>
            </div>
          </form>
        </section>
      ) : null}
    </section>
  );
}
