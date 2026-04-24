import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";
import { studentService, type StudentSummary } from "../../services/studentService";
import { useAuthStore } from "../../stores/authStore";

type GoalForm = {
  local_id: string;
  domain: string;
  goal_text: string;
  measurement_method: string;
};

type PlanForm = {
  student_id: string;
  title: string;
  start_date: string;
  end_date: string;
  summary: string;
  strengths: string;
  needs: string;
  accommodations: string;
  goals: GoalForm[];
};

const defaultForm: PlanForm = {
  student_id: "",
  title: "",
  start_date: "",
  end_date: "",
  summary: "",
  strengths: "",
  needs: "",
  accommodations: "",
  goals: [{ local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }]
};

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

export function IepPlanCreatePage() {
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const [students, setStudents] = useState<StudentSummary[]>([]);
  const [form, setForm] = useState<PlanForm>(defaultForm);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);

    void studentService
      .list({ per_page: 100 })
      .then((payload) => {
        setStudents(payload.data);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, []);

  const selectedStudent = students.find((student) => String(student.id) === form.student_id) ?? null;

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">إنشاء خطة</span>
          <h2>إضافة خطة جديدة</h2>
        </div>
        <div className="button-row">
          <Link className="button button-ghost" to="/app/iep-plans">
            العودة إلى الخطط
          </Link>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الطلاب...</div> : null}

      {!loading ? (
        <section className="surface-card page-stack">
          {selectedStudent ? (
            <div className="info-box">
              الطالب: <strong>{selectedStudent.full_name}</strong>
              {" | "}المدرسة: <strong>{selectedStudent.school?.name_ar ?? "-"}</strong>
            </div>
          ) : null}

          <form
            className="page-stack"
            onSubmit={async (event) => {
              event.preventDefault();

              if (!form.student_id) {
                setError("يجب اختيار الطالب قبل إنشاء الخطة.");
                return;
              }

              if (form.goals.some((goal) => hasAnyGoalData(goal) && !isGoalComplete(goal))) {
                setError("يجب تعبئة المجال ونص الهدف وطريقة القياس لكل هدف قبل حفظ الخطة.");
                return;
              }

              if (!form.goals.some((goal) => isGoalComplete(goal))) {
                setError("يجب إضافة هدف مكتمل واحد على الأقل قبل حفظ الخطة.");
                return;
              }

              setSaving(true);
              setError(null);

              try {
                const plan = await iepPlanService.create({
                  student_id: Number(form.student_id),
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
                    .map((goal, index) => ({
                      domain: goal.domain.trim() || `هدف ${index + 1}`,
                      goal_text: goal.goal_text.trim(),
                      measurement_method: goal.measurement_method.trim(),
                      sort_order: index
                    }))
                });

                if (user?.role === "teacher" && (permissions.includes("*") || permissions.includes("iep.submit"))) {
                  const submittedPlan = await iepPlanService.submit(plan.id);
                  navigate(`/app/iep-plans/${submittedPlan.id}`);
                  return;
                }

                navigate(`/app/iep-plans/${plan.id}`);
              } catch (saveError) {
                setError(getErrorMessage(saveError));
              } finally {
                setSaving(false);
              }
            }}
          >
            <div className="grid-two">
              <label className="field">
                <span>الطالب</span>
                <select
                  onChange={(event) => setForm((current) => ({ ...current, student_id: event.target.value }))}
                  required
                  value={form.student_id}
                >
                  <option value="">اختر الطالب</option>
                  {students.map((student) => (
                    <option key={student.id} value={student.id}>
                      {student.full_name} - {student.school?.name_ar ?? "-"}
                    </option>
                  ))}
                </select>
              </label>

              <label className="field">
                <span>عنوان الخطة</span>
                <input
                  onChange={(event) => setForm((current) => ({ ...current, title: event.target.value }))}
                  required
                  value={form.title}
                />
              </label>
            </div>

            <div className="grid-two">
              <label className="field">
                <span>تاريخ البداية</span>
                <input
                  onChange={(event) => setForm((current) => ({ ...current, start_date: event.target.value }))}
                  type="date"
                  value={form.start_date}
                />
              </label>

              <label className="field">
                <span>تاريخ النهاية</span>
                <input
                  onChange={(event) => setForm((current) => ({ ...current, end_date: event.target.value }))}
                  type="date"
                  value={form.end_date}
                />
              </label>
            </div>

            <label className="field">
              <span>الملخص</span>
              <textarea
                onChange={(event) => setForm((current) => ({ ...current, summary: event.target.value }))}
                rows={3}
                value={form.summary}
              />
            </label>

            <div className="grid-two">
              <label className="field">
                <span>نقاط القوة</span>
                <textarea
                  onChange={(event) => setForm((current) => ({ ...current, strengths: event.target.value }))}
                  rows={4}
                  value={form.strengths}
                />
              </label>

              <label className="field">
                <span>الاحتياجات</span>
                <textarea
                  onChange={(event) => setForm((current) => ({ ...current, needs: event.target.value }))}
                  rows={4}
                  value={form.needs}
                />
              </label>
            </div>

            <label className="field">
              <span>التسهيلات</span>
              <input
                onChange={(event) => setForm((current) => ({ ...current, accommodations: event.target.value }))}
                placeholder="افصل بين العناصر بفاصلة عربية"
                value={form.accommodations}
              />
            </label>

            <section className="page-stack">
              <div className="page-header">
                <div>
                  <span className="eyebrow">الأهداف</span>
                  <h3>الأهداف</h3>
                </div>
                <button
                  className="button button-secondary"
                  onClick={() => {
                    const lastGoal = form.goals[form.goals.length - 1];

                    if (lastGoal && !isGoalComplete(lastGoal)) {
                      setError("يجب تعبئة الهدف الحالي بالكامل قبل إضافة هدف جديد.");
                      return;
                    }

                    setError(null);
                    setForm((current) => ({
                      ...current,
                      goals: [
                        ...current.goals,
                        { local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }
                      ]
                    }));
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
                        setForm((current) => ({
                          ...current,
                          goals: current.goals.map((item, itemIndex) =>
                              itemIndex === index ? { ...item, domain: event.target.value } : item
                            )
                          }))
                        }
                        value={goal.domain}
                      />
                    </label>

                    <label className="field">
                      <span>طريقة القياس</span>
                      <input
                      onChange={(event) =>
                        setForm((current) => ({
                          ...current,
                          goals: current.goals.map((item, itemIndex) =>
                              itemIndex === index ? { ...item, measurement_method: event.target.value } : item
                            )
                          }))
                        }
                        value={goal.measurement_method}
                      />
                    </label>
                  </div>

                  <label className="field">
                    <span>نص الهدف</span>
                    <textarea
                      onChange={(event) =>
                        setForm((current) => ({
                          ...current,
                          goals: current.goals.map((item, itemIndex) =>
                              itemIndex === index ? { ...item, goal_text: event.target.value } : item
                            )
                          }))
                        }
                      rows={3}
                      value={goal.goal_text}
                    />
                  </label>

                  <button
                    className="button button-ghost"
                    onClick={() => {
                      setForm((current) => {
                        const nextGoals = current.goals.filter((_, itemIndex) => itemIndex !== index);

                        return {
                          ...current,
                          goals: nextGoals.length
                            ? nextGoals
                            : [{ local_id: createGoalId(), domain: "", goal_text: "", measurement_method: "" }]
                        };
                      });
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
                {saving ? "جارٍ إنشاء الخطة..." : "إضافة خطة"}
              </button>
            </div>
          </form>
        </section>
      ) : null}
    </section>
  );
}
