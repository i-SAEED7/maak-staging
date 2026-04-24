import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { buildClassLabel, parseClassKey } from "../../lib/supervisorIep";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";

type TeacherRow = {
  teacherId: number;
  teacherName: string;
  plansCount: number;
  schoolName: string;
  programName: string;
  classLabel: string;
};

export function SupervisorIepClassTeachersPage() {
  const { schoolId, programId, classKey } = useParams();
  const [rows, setRows] = useState<TeacherRow[]>([]);
  const [schoolName, setSchoolName] = useState("");
  const [programName, setProgramName] = useState("");
  const [classLabel, setClassLabel] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!schoolId || !programId || !classKey) {
      setError("تعذر تحديد المرحلة المطلوبة من مسار الخطط.");
      setLoading(false);
      return;
    }

    const parsedClass = parseClassKey(classKey);
    setClassLabel(buildClassLabel(parsedClass.gradeLevel, parsedClass.classroom));
    setLoading(true);

    void iepPlanService
      .list({
        per_page: 100,
        "filter[school_id]": schoolId,
        "filter[education_program_id]": programId,
        "filter[grade_level]": parsedClass.gradeLevel,
        "filter[classroom]": parsedClass.classroom
      })
      .then((payload) => {
        const grouped = payload.data.reduce<Record<number, TeacherRow>>((collection, plan) => {
          const teacherId = plan.teacher?.id;

          if (!teacherId) {
            return collection;
          }

          if (!collection[teacherId]) {
            collection[teacherId] = {
              teacherId,
              teacherName: plan.teacher?.full_name ?? "غير محدد",
              plansCount: 0,
              schoolName: plan.school?.name_ar ?? plan.student?.school?.name_ar ?? "-",
              programName: plan.student?.education_program?.name_ar ?? plan.school?.program_type ?? "-",
              classLabel: buildClassLabel(plan.student?.grade_level, plan.student?.classroom)
            };
          }

          collection[teacherId].plansCount += 1;
          return collection;
        }, {});

        setRows(Object.values(grouped));
        setSchoolName(payload.data[0]?.school?.name_ar ?? payload.data[0]?.student?.school?.name_ar ?? "");
        setProgramName(payload.data[0]?.student?.education_program?.name_ar ?? payload.data[0]?.school?.program_type ?? "");
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [classKey, programId, schoolId]);

  const columns: DataColumn<TeacherRow>[] = [
    { key: "teacher", label: "اسم المعلم", render: (row) => row.teacherName },
    { key: "count", label: "عدد الخطط", render: (row) => row.plansCount },
    {
      key: "view",
      label: "عرض الخطط",
      render: (row) => (
        <Link
          className="button button-secondary"
          to={`/app/iep-plans/schools/${schoolId}/programs/${programId}/classes/${classKey}/teachers/${row.teacherId}/plans`}
        >
          عرض
        </Link>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">إشراف الخطط</span>
          <h2>معلمو الفصل</h2>
          <p className="section-description">
            {schoolName ? `المدرسة: ${schoolName}` : ""}
            {programName ? ` | البرنامج: ${programName}` : ""}
            {classLabel ? ` | ${classLabel}` : ""}
          </p>
        </div>
        <Link className="button button-secondary" to={`/app/iep-plans/schools/${schoolId}/programs/${programId}/classes`}>
          العودة إلى الفصول
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل المعلمين...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد خطط ضمن هذا الفصل." /> : null}
    </section>
  );
}
