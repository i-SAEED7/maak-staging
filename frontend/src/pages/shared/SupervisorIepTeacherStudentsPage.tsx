import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { buildClassLabel, parseClassKey } from "../../lib/supervisorIep";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";

type TeacherPlanRow = {
  planId: number;
  studentName: string;
  gradeLevel: string;
  classroom: string;
  teacherName: string;
  schoolName: string;
};

export function SupervisorIepTeacherStudentsPage() {
  const { schoolId, programId, classKey, teacherId } = useParams();
  const [rows, setRows] = useState<TeacherPlanRow[]>([]);
  const [schoolName, setSchoolName] = useState("");
  const [teacherName, setTeacherName] = useState("");
  const [classLabel, setClassLabel] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!schoolId || !programId || !classKey || !teacherId) {
      setError("تعذر تحديد البيانات المطلوبة لعرض الخطط.");
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
        "filter[classroom]": parsedClass.classroom,
        "filter[teacher_user_id]": teacherId
      })
      .then((payload) => {
        const mappedRows = payload.data
          .filter((plan) => plan.student?.id)
          .map((plan) => ({
            planId: plan.id,
            studentName: plan.student?.full_name ?? "طالب غير محدد",
            gradeLevel: plan.student?.grade_level ?? "-",
            classroom: plan.student?.classroom ?? "-",
            teacherName: plan.teacher?.full_name ?? "غير محدد",
            schoolName: plan.school?.name_ar ?? plan.student?.school?.name_ar ?? "-"
          }));

        setRows(mappedRows);
        setSchoolName(mappedRows[0]?.schoolName ?? "");
        setTeacherName(mappedRows[0]?.teacherName ?? "");
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [classKey, programId, schoolId, teacherId]);

  const columns: DataColumn<TeacherPlanRow>[] = [
    { key: "student", label: "اسم الطالب", render: (row) => row.studentName },
    { key: "grade", label: "الصف", render: (row) => row.gradeLevel },
    { key: "classroom", label: "الفصل", render: (row) => row.classroom },
    {
      key: "view",
      label: "عرض الخطة",
      render: (row) => (
        <Link className="button button-secondary" to={`/app/iep-plans/${row.planId}`}>
          عرض الخطة
        </Link>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">إشراف الخطط</span>
          <h2>خطط المعلم</h2>
          <p className="section-description">
            {schoolName ? `المدرسة: ${schoolName}` : ""}
            {teacherName ? ` | المعلم: ${teacherName}` : ""}
            {classLabel ? ` | ${classLabel}` : ""}
          </p>
        </div>
        <Link
          className="button button-secondary"
          to={`/app/iep-plans/schools/${schoolId}/programs/${programId}/classes/${classKey}/teachers`}
        >
          العودة إلى المعلمين
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الخطط...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد خطط مرتبطة بهذا المعلم." /> : null}
    </section>
  );
}
