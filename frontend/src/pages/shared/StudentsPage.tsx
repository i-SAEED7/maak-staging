import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  educationProgramService,
  type EducationProgramOption
} from "../../services/educationProgramService";
import { schoolService } from "../../services/schoolService";
import { studentService, type StudentSummary } from "../../services/studentService";
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

const defaultFormValues: StudentFormValues = {
  school_id: "",
  education_program_id: "",
  first_name: "",
  family_name: "",
  gender: "male",
  grade_level: "",
  classroom: ""
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

export function StudentsPage() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const selectedSchoolId = useAuthStore((state) => state.schoolId);
  const canCreate = permissions.includes("*") || permissions.includes("students.create");
  const canUpdate = permissions.includes("*") || permissions.includes("students.update");
  const normalizedRole = user?.role ?? "";
  const [rows, setRows] = useState<StudentSummary[]>([]);
  const [draftSearch, setDraftSearch] = useState("");
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [programs, setPrograms] = useState<EducationProgramOption[]>([]);
  const [schools, setSchools] = useState<SchoolOption[]>([]);
  const [formValues, setFormValues] = useState<StudentFormValues>(defaultFormValues);

  const selectedSchool = schools.find((school) => String(school.id) === formValues.school_id) ?? null;
  const gradeOptions = buildGradeOptions(selectedSchool?.stage);

  const columns: DataColumn<StudentSummary>[] = [
    {
      key: "name",
      label: "الاسم",
      render: (row) => (
        <Link className="inline-link" to={`/app/students/${row.id}`}>
          {row.full_name}
        </Link>
      )
    },
    { key: "number", label: "رقم الطالب", render: (row) => row.student_number ?? "-" },
    { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? "-" },
    { key: "grade", label: "الصف", render: (row) => row.grade_level ?? "-" },
    { key: "stage", label: "المرحلة", render: (row) => row.school?.stage ?? "-" },
    { key: "program", label: "نوع البرنامج", render: (row) => row.education_program?.name_ar ?? "-" },
    { key: "status", label: "الحالة", render: (row) => row.enrollment_status },
    ...(canUpdate
      ? [
          {
            key: "edit",
            label: "الإجراء",
            render: (row: StudentSummary) => (
              <Link className="button button-secondary" to={`/app/students/${row.id}/edit`}>
                تعديل
              </Link>
            )
          }
        ]
      : [])
  ];

  const loadStudents = async () => {
    setLoading(true);

    try {
      const payload = await studentService.list({
        per_page: 100,
        "filter[search]": search.trim() || undefined
      });

      setRows(payload.data);
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    void loadStudents();
  }, [search]);

  useEffect(() => {
    const fallbackSchools = buildAccessibleSchools(user?.role, user?.assigned_schools, user?.school ?? null);

    const loadLookupData = async () => {
      try {
        const programsPayload = await educationProgramService.list();
        setPrograms(programsPayload);

        if (normalizedRole === "super_admin" || normalizedRole === "admin") {
          const schoolPayload = await schoolService.list({ perPage: 100 });
          const mappedSchools = schoolPayload.data.map((school) => ({
            id: school.id,
            name: school.name,
            stage: school.stage
          }));
          setSchools(mappedSchools);
          setFormValues((current) => ({
            ...current,
            school_id: current.school_id || selectedSchoolId || String(mappedSchools[0]?.id ?? "")
          }));

          return;
        }

        setSchools(fallbackSchools);
        setFormValues((current) => ({
          ...current,
          school_id: current.school_id || selectedSchoolId || String(fallbackSchools[0]?.id ?? "")
        }));
      } catch (loadError) {
        setError(getErrorMessage(loadError));
      }
    };

    void loadLookupData();
  }, [normalizedRole, selectedSchoolId, user?.assigned_schools, user?.role, user?.school]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الطلاب</span>
          <h2>الطلاب</h2>
        </div>
      </div>

      {canCreate ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">إنشاء</span>
              <h3>إضافة طالب جديد</h3>
              <p className="section-description">
                يتم توليد رقم الطالب تلقائيًا من النظام، كما يتم تحديد الصف من قائمة مرتبطة بمرحلة المدرسة.
              </p>
            </div>
          </div>

          <form
            className="page-stack"
            onSubmit={async (event) => {
              event.preventDefault();
              setSaving(true);
              setError(null);
              setSuccessMessage(null);

              try {
                await studentService.create({
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

                setSuccessMessage("تمت إضافة الطالب بنجاح، وتم توليد رقمه تلقائيًا.");
                setFormValues((current) => ({
                  ...defaultFormValues,
                  school_id: current.school_id,
                  gender: current.gender
                }));
                await loadStudents();
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
                  onChange={(event) => {
                    setFormValues((current) => ({
                      ...current,
                      school_id: event.target.value,
                      grade_level: ""
                    }));
                  }}
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
                    setFormValues((current) => ({
                      ...current,
                      education_program_id: event.target.value
                    }))
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
                  onChange={(event) => setFormValues((current) => ({ ...current, first_name: event.target.value }))}
                  required
                  value={formValues.first_name}
                />
              </label>

              <label className="field">
                <span>اسم العائلة</span>
                <input
                  onChange={(event) => setFormValues((current) => ({ ...current, family_name: event.target.value }))}
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
                    setFormValues((current) => ({
                      ...current,
                      gender: event.target.value as "male" | "female"
                    }))
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
                  onChange={(event) => setFormValues((current) => ({ ...current, grade_level: event.target.value }))}
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
                onChange={(event) => setFormValues((current) => ({ ...current, classroom: event.target.value }))}
                value={formValues.classroom}
              />
            </label>

            <div className="button-row">
              <button className="button button-primary" disabled={saving} type="submit">
                {saving ? "جارٍ الإضافة..." : "إضافة الطالب"}
              </button>
            </div>
          </form>
        </section>
      ) : null}

      <div className="filters-bar">
        <label className="field">
          <span>بحث</span>
          <input
            onChange={(event) => setDraftSearch(event.target.value)}
            placeholder="ابحث باسم الطالب أو رقمه"
            value={draftSearch}
          />
        </label>
        <div className="button-row filters-actions">
          <button
            className="button button-secondary"
            onClick={() => setSearch(draftSearch)}
            type="button"
          >
            بحث
          </button>
        </div>
      </div>

      {successMessage ? <div className="info-box">{successMessage}</div> : null}
      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الطلاب...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا يوجد طلاب متاحون." /> : null}
    </section>
  );
}
