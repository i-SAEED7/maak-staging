import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";

type TeacherPlanRow = {
  teacherId: number;
  teacherName: string;
  studentsCount: number;
  programType: string;
};

export function SupervisorIepSchoolTeachersPage() {
  const { schoolId } = useParams();
  const [rows, setRows] = useState<TeacherPlanRow[]>([]);
  const [schoolName, setSchoolName] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!schoolId) {
      setError("تعذر تحديد المدرسة المطلوبة.");
      setLoading(false);
      return;
    }

    setLoading(true);

    void iepPlanService
      .list({
        per_page: 100,
        "filter[school_id]": schoolId
      })
      .then((payload) => {
        const grouped = payload.data.reduce<Record<number, TeacherPlanRow>>((collection, plan) => {
          const currentTeacherId = plan.teacher?.id;

          if (!currentTeacherId) {
            return collection;
          }

          if (!collection[currentTeacherId]) {
            collection[currentTeacherId] = {
              teacherId: currentTeacherId,
              teacherName: plan.teacher?.full_name ?? "غير محدد",
              studentsCount: 0,
              programType: plan.student?.education_program?.name_ar ?? plan.school?.program_type ?? "-"
            };
          }

          collection[currentTeacherId].studentsCount += 1;
          return collection;
        }, {});

        setRows(Object.values(grouped));
        setSchoolName(payload.data[0]?.school?.name_ar ?? payload.data[0]?.student?.school?.name_ar ?? "");
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [schoolId]);

  const columns: DataColumn<TeacherPlanRow>[] = [
    { key: "teacher", label: "اسم المعلم", render: (row) => row.teacherName },
    { key: "count", label: "عدد الطلاب", render: (row) => row.studentsCount },
    { key: "program", label: "نوع البرنامج", render: (row) => row.programType },
    {
      key: "view",
      label: "عرض الخطة",
      render: (row) => (
        <Link
          className="button button-secondary"
          to={`/app/iep-plans/schools/${schoolId}/teachers/${row.teacherId}/students`}
        >
          عرض الطلاب
        </Link>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">إشراف الخطط</span>
          <h2>معلمو المدرسة</h2>
          {schoolName ? <p className="section-description">المدرسة: {schoolName}</p> : null}
        </div>
        <Link className="button button-secondary" to="/app/iep-plans">
          العودة إلى المدارس
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل المعلمين والخطط...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد خطط مرتبطة بهذه المدرسة." /> : null}
    </section>
  );
}
