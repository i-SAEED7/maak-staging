import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import {
  educationProgramService,
  type EducationProgramOption
} from "../../services/educationProgramService";
import { schoolService } from "../../services/schoolService";
import { studentService } from "../../services/studentService";
import { useAuthStore } from "../../stores/authStore";

type SchoolOption = {
  id: number;
  name: string;
  stage?: string | null;
};

type StudentFormValues = {
  school_id: string;
  education_program_id: string;
  first_name: string;
  family_name: string;
  gender: "male" | "female";
  grade_level: string;
  classroom: string;
};

function buildAccessibleSchools(
  role: string | undefined,
  assignedSchools: Array<{ id: number; name_ar: string; stage?: string | null }> | undefined,
  currentSchool: { id?: number; name_ar?: string | null; stage?: string | null } | null | undefined
): SchoolOption[] {
  if (role === "supervisor" && assignedSchools?.length) {
    return assignedSchools.map((school) => ({
      id: school.id,
      name: school.name_ar,
      stage: school.stage
    }));
  }

  if (currentSchool?.id && currentSchool?.name_ar) {
    return [
      {
        id: currentSchool.id,
        name: currentSchool.name_ar,
        stage: currentSchool.stage
      }
    ];
  }

  return [];
}

function buildGradeOptions(stage?: string | null) {
  if (stage === "ابتدائي") {
    return ["أول", "ثاني", "ثالث", "رابع", "خامس", "سادس"];
  }

  if (stage === "متوسط" || stage === "ثانوي") {
    return ["أول", "ثاني", "ثالث"];
  }

  return ["أول", "ثاني", "ثالث", "رابع", "خامس", "سادس"];
}

export function StudentEditPage() {
  const { studentId } = useParams();
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const selectedSchoolId = useAuthStore((state) => state.schoolId);
  const normalizedRole = user?.role ?? "";
  const [programs, setPrograms] = useState<EducationProgramOption[]>([]);
  const [schools, setSchools] = useState<SchoolOption[]>([]);
  const [formValues, setFormValues] = useState<StudentFormValues | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const selectedSchool = schools.find((school) => String(school.id) === formValues?.school_id) ?? null;
  const gradeOptions = buildGradeOptions(selectedSchool?.stage);

  useEffect(() => {
    if (!studentId) {
      setError("تعذر تحديد الطالب المطلوب.");
      setLoading(false);
      return;
    }

    const fallbackSchools = buildAccessibleSchools(user?.role, user?.assigned_schools, user?.school ?? null);

    const load = async () => {
      setLoading(true);

      try {
        const [student, programsPayload] = await Promise.all([
          studentService.details(studentId),
          educationProgramService.list()
        ]);

        setPrograms(programsPayload);

        let resolvedSchools = fallbackSchools;

        if (normalizedRole === "super_admin" || normalizedRole === "admin") {
          const schoolPayload = await schoolService.list({ perPage: 100 });
          resolvedSchools = schoolPayload.data.map((school) => ({
            id: school.id,
            name: school.name,
            stage: school.stage
          }));
        }

        setSchools(resolvedSchools);
        setFormValues({
          school_id: String(student.school_id ?? selectedSchoolId ?? resolvedSchools[0]?.id ?? ""),
          education_program_id: String(student.education_program?.id ?? student.education_program_id ?? ""),
          first_name: student.first_name ?? "",
          family_name: student.family_name ?? "",
          gender: student.gender === "female" ? "female" : "male",
          grade_level: student.grade_level ?? "",
          classroom: student.classroom ?? ""
        });
        setError(null);
      } catch (loadError) {
        setError(getErrorMessage(loadError));
      } finally {
        setLoading(false);
      }
    };

    void load();
  }, [normalizedRole, selectedSchoolId, studentId, user?.assigned_schools, user?.role, user?.school]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">تعديل الطالب</span>
          <h2>تعديل بيانات الطالب</h2>
        </div>
        <div className="button-row">
          {studentId ? (
            <Link className="button button-secondary" to={`/app/students/${studentId}`}>
              عرض الطالب
            </Link>
          ) : null}
          <Link className="button button-ghost" to="/app/students">
            العودة إلى الطلاب
          </Link>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل بيانات الطالب...</div> : null}

      {!loading && formValues ? (
        <section className="surface-card page-stack">
          <div className="info-box">رقم الطالب يُدار تلقائيًا من النظام ولا يمكن تعديله يدويًا.</div>

          <form
            className="page-stack"
            onSubmit={async (event) => {
              event.preventDefault();

              if (!studentId) {
                return;
              }

              setSaving(true);
              setError(null);

              try {
                await studentService.update(studentId, {
                  school_id: formValues.school_id ? Number(formValues.school_id) : undefined,
                  education_program_id: formValues.education_program_id
                    ? Number(formValues.education_program_id)
                    : undefined,
                  first_name: formValues.first_name.trim(),
                  family_name: formValues.family_name.trim(),
                  gender: formValues.gender,
                  grade_level: formValues.grade_level.trim() || undefined,
                  classroom: formValues.classroom.trim() || undefined
                });

                navigate(`/app/students/${studentId}`);
              } catch (saveError) {
                setError(getErrorMessage(saveError));
              } finally {
                setSaving(false);
              }
            }}
          >
            <div className="grid-two">
              <label className="field">
                <span>المدرسة</span>
                <select
                  onChange={(event) =>
                    setFormValues((current) =>
                      current
                        ? {
                            ...current,
                            school_id: event.target.value,
                            grade_level: ""
                          }
                        : current
                    )
                  }
                  required
                  value={formValues.school_id}
                >
                  <option value="">اختر المدرسة</option>
                  {schools.map((school) => (
                    <option key={school.id} value={school.id}>
                      {school.name}
                    </option>
                  ))}
                </select>
              </label>

              <label className="field">
                <span>نوع البرنامج</span>
                <select
                  onChange={(event) =>
                    setFormValues((current) =>
                      current
                        ? {
                            ...current,
                            education_program_id: event.target.value
                          }
                        : current
                    )
                  }
                  required
                  value={formValues.education_program_id}
                >
                  <option value="">اختر البرنامج</option>
                  {programs.map((program) => (
                    <option key={program.id} value={program.id}>
                      {program.name_ar}
                    </option>
                  ))}
                </select>
              </label>
            </div>

            <div className="grid-two">
              <label className="field">
                <span>الاسم الأول</span>
                <input
                  onChange={(event) =>
                    setFormValues((current) => (current ? { ...current, first_name: event.target.value } : current))
                  }
                  required
                  value={formValues.first_name}
                />
              </label>

              <label className="field">
                <span>اسم العائلة</span>
                <input
                  onChange={(event) =>
                    setFormValues((current) => (current ? { ...current, family_name: event.target.value } : current))
                  }
                  required
                  value={formValues.family_name}
                />
              </label>
            </div>

            <div className="grid-two">
              <label className="field">
                <span>الجنس</span>
                <select
                  onChange={(event) =>
                    setFormValues((current) =>
                      current ? { ...current, gender: event.target.value as "male" | "female" } : current
                    )
                  }
                  value={formValues.gender}
                >
                  <option value="male">ذكر</option>
                  <option value="female">أنثى</option>
                </select>
              </label>

              <label className="field">
                <span>الصف</span>
                <select
                  onChange={(event) =>
                    setFormValues((current) => (current ? { ...current, grade_level: event.target.value } : current))
                  }
                  value={formValues.grade_level}
                >
                  <option value="">اختر الصف</option>
                  {gradeOptions.map((grade) => (
                    <option key={grade} value={grade}>
                      {grade}
                    </option>
                  ))}
                </select>
              </label>
            </div>

            <label className="field">
              <span>الفصل</span>
              <input
                onChange={(event) =>
                  setFormValues((current) => (current ? { ...current, classroom: event.target.value } : current))
                }
                value={formValues.classroom}
              />
            </label>

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
