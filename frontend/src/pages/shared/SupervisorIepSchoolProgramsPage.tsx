import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";

type ProgramRow = {
  programId: number;
  programName: string;
  schoolName: string;
  plansCount: number;
};

export function SupervisorIepSchoolProgramsPage() {
  const { schoolId } = useParams();
  const [rows, setRows] = useState<ProgramRow[]>([]);
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
        const grouped = payload.data.reduce<Record<number, ProgramRow>>((collection, plan) => {
          const programId = plan.student?.education_program?.id;
          const programName = plan.student?.education_program?.name_ar ?? plan.school?.program_type ?? "غير محدد";

          if (!programId) {
            return collection;
          }

          if (!collection[programId]) {
            collection[programId] = {
              programId,
              programName,
              schoolName: plan.school?.name_ar ?? plan.student?.school?.name_ar ?? "-",
              plansCount: 0
            };
          }

          collection[programId].plansCount += 1;
          return collection;
        }, {});

        setRows(Object.values(grouped));
        setSchoolName(payload.data[0]?.school?.name_ar ?? payload.data[0]?.student?.school?.name_ar ?? "");
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [schoolId]);

  const columns: DataColumn<ProgramRow>[] = [
    { key: "program", label: "البرنامج", render: (row) => row.programName },
    { key: "count", label: "عدد الخطط", render: (row) => row.plansCount },
    {
      key: "view",
      label: "عرض الفصول",
      render: (row) => (
        <Link className="button button-secondary" to={`/app/iep-plans/schools/${schoolId}/programs/${row.programId}/classes`}>
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
          <h2>برامج المدرسة</h2>
          {schoolName ? <p className="section-description">المدرسة: {schoolName}</p> : null}
        </div>
        <Link className="button button-secondary" to="/app/iep-plans">
          العودة إلى المدارس
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل البرامج...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد خطط ضمن هذه المدرسة." /> : null}
    </section>
  );
}
