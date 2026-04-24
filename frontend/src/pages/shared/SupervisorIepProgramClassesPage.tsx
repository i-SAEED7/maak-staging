import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { buildClassKey, buildClassLabel } from "../../lib/supervisorIep";
import { getErrorMessage } from "../../services/api";
import { iepPlanService } from "../../services/iepPlanService";

type ClassRow = {
  classKey: string;
  classLabel: string;
  plansCount: number;
  programName: string;
  schoolName: string;
};

export function SupervisorIepProgramClassesPage() {
  const { schoolId, programId } = useParams();
  const [rows, setRows] = useState<ClassRow[]>([]);
  const [schoolName, setSchoolName] = useState("");
  const [programName, setProgramName] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!schoolId || !programId) {
      setError("تعذر تحديد المدرسة أو البرنامج المطلوب.");
      setLoading(false);
      return;
    }

    setLoading(true);

    void iepPlanService
      .list({
        per_page: 100,
        "filter[school_id]": schoolId,
        "filter[education_program_id]": programId
      })
      .then((payload) => {
        const grouped = payload.data.reduce<Record<string, ClassRow>>((collection, plan) => {
          const classKey = buildClassKey(plan.student?.grade_level, plan.student?.classroom);

          if (!collection[classKey]) {
            collection[classKey] = {
              classKey,
              classLabel: buildClassLabel(plan.student?.grade_level, plan.student?.classroom),
              plansCount: 0,
              programName: plan.student?.education_program?.name_ar ?? plan.school?.program_type ?? "-",
              schoolName: plan.school?.name_ar ?? plan.student?.school?.name_ar ?? "-"
            };
          }

          collection[classKey].plansCount += 1;
          return collection;
        }, {});

        setRows(Object.values(grouped));
        setSchoolName(payload.data[0]?.school?.name_ar ?? payload.data[0]?.student?.school?.name_ar ?? "");
        setProgramName(payload.data[0]?.student?.education_program?.name_ar ?? payload.data[0]?.school?.program_type ?? "");
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [programId, schoolId]);

  const columns: DataColumn<ClassRow>[] = [
    { key: "class", label: "الفصل", render: (row) => row.classLabel },
    { key: "count", label: "عدد الخطط", render: (row) => row.plansCount },
    {
      key: "view",
      label: "عرض المعلمين",
      render: (row) => (
        <Link
          className="button button-secondary"
          to={`/app/iep-plans/schools/${schoolId}/programs/${programId}/classes/${row.classKey}/teachers`}
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
          <h2>فصول البرنامج</h2>
          <p className="section-description">
            {schoolName ? `المدرسة: ${schoolName}` : ""}
            {programName ? ` | البرنامج: ${programName}` : ""}
          </p>
        </div>
        <Link className="button button-secondary" to={`/app/iep-plans/schools/${schoolId}/programs`}>
          العودة إلى البرامج
        </Link>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الفصول...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد خطط ضمن هذا البرنامج." /> : null}
    </section>
  );
}
