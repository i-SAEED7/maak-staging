import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { iepPlanService, type IepPlanSummary } from "../../services/iepPlanService";
import { useAuthStore } from "../../stores/authStore";

type SupervisorSchoolRow = {
  schoolId: number;
  schoolName: string;
  stage: string;
  plansCount: number;
};

export function IepPlansPage() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const isSupervisor = user?.role === "supervisor";
  const isPrincipal = user?.role === "principal";
  const isParent = user?.role === "parent";
  const canCreate = permissions.includes("*") || permissions.includes("iep.create");
  const canEdit = permissions.includes("*") || permissions.includes("iep.update");
  const canSubmit = permissions.includes("*") || permissions.includes("iep.submit");
  const canPrincipalApprove = permissions.includes("*") || permissions.includes("iep.principal_approve");
  const [rows, setRows] = useState<IepPlanSummary[]>([]);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);

    void iepPlanService
      .list({
        per_page: 100,
        "filter[search]": search.trim() || undefined
      })
      .then((payload) => setRows(payload.data))
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [search]);

  const supervisorRows = rows.reduce<SupervisorSchoolRow[]>((collection, row) => {
    const schoolId = row.school?.id ?? row.student?.school?.id;
    const schoolName = row.school?.name_ar ?? row.student?.school?.name_ar;

    if (!schoolId || !schoolName) {
      return collection;
    }

    const existingRow = collection.find((item) => item.schoolId === schoolId);

    if (existingRow) {
      existingRow.plansCount += 1;
      return collection;
    }

    collection.push({
      schoolId,
      schoolName,
      stage: row.school?.stage ?? row.student?.school?.stage ?? "-",
      plansCount: 1
    });

    return collection;
  }, []);

  const supervisorColumns: DataColumn<SupervisorSchoolRow>[] = [
    { key: "school", label: "المدرسة", render: (row) => row.schoolName },
    { key: "stage", label: "المرحلة", render: (row) => row.stage || "-" },
    { key: "plans", label: "عدد الخطط", render: (row) => row.plansCount },
    {
      key: "view",
      label: "عرض الخطة",
      render: (row) => (
        <Link className="button button-secondary" to={`/app/iep-plans/schools/${row.schoolId}/programs`}>
          عرض
        </Link>
      )
    }
  ];

  const parentColumns: DataColumn<IepPlanSummary>[] = [
    { key: "student", label: "الابن", render: (row) => row.student?.full_name ?? "-" },
    { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? row.student?.school?.name_ar ?? "-" },
    {
      key: "program",
      label: "البرنامج",
      render: (row) => row.student?.education_program?.name_ar ?? row.school?.program_type ?? "-"
    },
    { key: "status", label: "الحالة", render: (row) => row.status },
    {
      key: "acknowledge",
      label: "الإقرار بالاطلاع",
      render: (row) =>
        row.current_user_acknowledged_at ? (
          <span className="detail-label">
            تم الإقرار في {new Date(row.current_user_acknowledged_at).toLocaleDateString("ar-SA")}
          </span>
        ) : (
          <button
            className="button button-secondary"
            onClick={() => {
              if (!window.confirm(`هل تريد تسجيل الإقرار بالاطلاع على الخطة: ${row.title}؟`)) {
                return;
              }

              void iepPlanService.acknowledge(row.id).then((updatedPlan) => {
                setRows((current) =>
                  current.map((item) =>
                    item.id === row.id
                      ? {
                          ...item,
                          current_user_acknowledged_at: updatedPlan.current_user_acknowledged_at
                        }
                      : item
                  )
                );
                setError(null);
              }).catch((loadError) => setError(getErrorMessage(loadError)));
            }}
            type="button"
          >
            إقرار بالاطلاع
          </button>
        )
    },
    {
      key: "view",
      label: "عرض الخطة",
      render: (row) => (
        <Link className="button button-ghost" to={`/app/iep-plans/${row.id}`}>
          عرض
        </Link>
      )
    }
  ];

  const defaultColumns: DataColumn<IepPlanSummary>[] = [
    { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? row.student?.school?.name_ar ?? "-" },
    { key: "stage", label: "المرحلة", render: (row) => row.school?.stage ?? row.student?.school?.stage ?? "-" },
    { key: "teacher", label: "المعلم", render: (row) => row.teacher?.full_name ?? "-" },
    {
      key: "view",
      label: "عرض الخطة",
      render: (row) => (
        <Link className="button button-secondary" to={`/app/iep-plans/${row.id}`}>
          عرض
        </Link>
      )
    },
    ...(canSubmit && !isSupervisor
      ? [
          {
            key: "submit",
            label: "إرسال للاعتماد",
            render: (row: IepPlanSummary) =>
              row.status === "draft" || row.status === "rejected" ? (
                <button
                  className="button button-secondary"
                  onClick={() => {
                    if (!window.confirm(`هل تريد إرسال الخطة: ${row.title} إلى المدير؟`)) {
                      return;
                    }

                    void iepPlanService.submit(row.id).then((updatedPlan) => {
                      setRows((current) =>
                        current.map((item) =>
                          item.id === row.id
                            ? {
                                ...item,
                                status: updatedPlan.status
                              }
                            : item
                        )
                      );
                    });
                  }}
                  type="button"
                >
                  إرسال
                </button>
              ) : null
          }
        ]
      : []),
    ...(isPrincipal && canPrincipalApprove
      ? [
          {
            key: "approve",
            label: "اعتماد الخطة",
            render: (row: IepPlanSummary) =>
              row.status === "pending_principal_review" ? (
                <button
                  className="button button-primary"
                  onClick={() => {
                    if (!window.confirm(`هل تريد اعتماد الخطة: ${row.title}؟`)) {
                      return;
                    }

                    void iepPlanService.principalApprove(row.id).then((updatedPlan) => {
                      setRows((current) =>
                        current.map((item) =>
                          item.id === row.id
                            ? {
                                ...item,
                                status: updatedPlan.status
                              }
                            : item
                        )
                      );
                    });
                  }}
                  type="button"
                >
                  اعتماد
                </button>
              ) : null
          }
        ]
      : []),
    ...(canEdit && !isSupervisor
      ? [
          {
            key: "edit",
            label: "تعديل",
            render: (row: IepPlanSummary) => (
              <Link className="button button-ghost" to={`/app/iep-plans/${row.id}/edit`}>
                تعديل
              </Link>
            )
          },
          {
            key: "delete",
            label: "حذف",
            render: (row: IepPlanSummary) => (
              <button
                className="button button-ghost"
                onClick={() => {
                  if (!window.confirm(`هل أنت متأكد من حذف الخطة: ${row.title}؟`)) {
                    return;
                  }

                  void iepPlanService.delete(row.id).then(() => {
                    setRows((current) => current.filter((item) => item.id !== row.id));
                  });
                }}
                type="button"
              >
                حذف
              </button>
            )
          }
        ]
      : [])
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الخطط الفردية</span>
          <h2>الخطط الفردية</h2>
        </div>
        {canCreate && !isSupervisor ? (
          <Link className="button button-primary" to="/app/iep-plans/create">
            إضافة خطة
          </Link>
        ) : null}
      </div>

      {isSupervisor ? (
        <div className="info-box">
          دور المشرف في هذه الصفحة اطلاعي فقط: ابدأ من المدرسة، ثم البرنامج، ثم الفصل، ثم المعلم، ثم
          افتح الخطة الفردية.
        </div>
      ) : null}
      {isParent ? <div className="info-box">تعرض هذه الصفحة خطط الابن فقط مع إمكانية الإقرار بالاطلاع.</div> : null}

      <div className="filters-bar">
        <label className="field">
          <span>بحث</span>
          <input
            onChange={(event) => setSearch(event.target.value)}
            placeholder={isParent ? "ابحث باسم الابن أو عنوان الخطة" : "ابحث بعنوان الخطة أو اسم الطالب"}
            value={search}
          />
        </label>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الخطط...</div> : null}
      {!loading && isSupervisor ? (
        <DataTable columns={supervisorColumns} rows={supervisorRows} emptyMessage="لا توجد خطط حالية." />
      ) : null}
      {!loading && isParent ? (
        <DataTable columns={parentColumns} rows={rows} emptyMessage="لا توجد خطط مرتبطة بالأبناء حاليًا." />
      ) : null}
      {!loading && !isSupervisor && !isParent ? (
        <DataTable columns={defaultColumns} rows={rows} emptyMessage="لا توجد خطط حالية." />
      ) : null}
    </section>
  );
}
