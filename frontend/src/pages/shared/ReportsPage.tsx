import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  reportService,
  type ComparisonReportRow,
  type PivotReport,
  type SchoolSummaryReport,
  type StudentSummaryReport
} from "../../services/reportService";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { studentService, type StudentSummary } from "../../services/studentService";
import { useAuthStore } from "../../stores/authStore";

const REPORT_ACTIONS = [
  { value: "school_management", label: "إدارة المدرسة" },
  { value: "school_files", label: "ملفات المدارس" },
  { value: "school_summary", label: "تقرير المدرسة" },
  { value: "comparison", label: "تقرير المقارنة" },
  { value: "pivot", label: "التقرير المحوري" }
];

const dimensionLabels: Record<string, string> = {
  grade_level: "الصف",
  gender: "الجنس",
  education_program: "البرنامج",
  disability_category: "التصنيف",
  iep_status: "حالة الخطة",
  teacher: "المعلم"
};

const overviewLabels: Record<string, string> = {
  students_count: "عدد الطلاب",
  teachers_count: "عدد المعلمين",
  users_count: "إجمالي المستخدمين",
  iep_plans_count: "عدد الخطط",
  approved_iep_plans_count: "الخطط المعتمدة",
  messages_count: "عدد الرسائل",
  notifications_count: "عدد الإشعارات",
  unread_notifications_count: "الإشعارات غير المقروءة",
  files_count: "عدد الملفات"
};

function translateStatus(value?: string | null) {
  const dictionary: Record<string, string> = {
    active: "نشطة",
    inactive: "غير نشطة",
    draft: "مسودة",
    pending_principal_review: "بانتظار اعتماد المدير",
    pending_supervisor_review: "بانتظار مراجعة المشرف",
    approved: "معتمدة",
    rejected: "مرفوضة",
    archived: "مؤرشفة"
  };

  if (!value) {
    return "-";
  }

  return dictionary[value] ?? value;
}

function ReportsSchoolCell({ row }: { row: ComparisonReportRow }) {
  return (
    <Link className="inline-link" to={`/app/schools/${row.school_id}`}>
      {row.school_name}
    </Link>
  );
}

export function ReportsPage() {
  const user = useAuthStore((state) => state.user);
  const schoolId = useAuthStore((state) => state.schoolId);
  const isSuperAdmin = user?.role === "super_admin";
  const isSupervisor = user?.role === "supervisor";
  const isParent = user?.role === "parent";
  const [schools, setSchools] = useState<SchoolItem[]>([]);
  const [children, setChildren] = useState<StudentSummary[]>([]);
  const [selectedSchoolId, setSelectedSchoolId] = useState("");
  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [selectedAction, setSelectedAction] = useState("school_summary");
  const [search, setSearch] = useState("");
  const [comparisonRows, setComparisonRows] = useState<ComparisonReportRow[]>([]);
  const [schoolSummary, setSchoolSummary] = useState<SchoolSummaryReport | null>(null);
  const [studentSummary, setStudentSummary] = useState<StudentSummaryReport | null>(null);
  const [pivot, setPivot] = useState<PivotReport | null>(null);
  const [dimension, setDimension] = useState("grade_level");
  const [caption, setCaption] = useState("اختر نوع التقرير أو استخدم البحث للوصول السريع.");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const comparisonColumns: DataColumn<ComparisonReportRow>[] = [
    { key: "school_name", label: "المدرسة", render: (row) => <ReportsSchoolCell row={row} /> },
    { key: "official_code", label: "الكود", render: (row) => row.official_code ?? "-" },
    { key: "stage", label: "المرحلة", render: (row) => row.stage ?? "-" },
    { key: "program_type", label: "البرنامج", render: (row) => row.program_type ?? "-" },
    { key: "status", label: "الحالة", render: (row) => translateStatus(row.status) },
    { key: "students_count", label: "الطلاب", render: (row) => row.students_count },
    { key: "teachers_count", label: "المعلمين", render: (row) => row.teachers_count },
    { key: "iep_plans_count", label: "الخطط", render: (row) => row.iep_plans_count },
    { key: "files_count", label: "الملفات", render: (row) => row.files_count ?? 0 },
    {
      key: "files_link",
      label: "ملفات المدارس",
      render: (row) =>
        isSupervisor ? (
          <span className="detail-label">غير متاح للمشرف</span>
        ) : (
          <Link className="button button-secondary" to={`/app/files?schoolId=${row.school_id}`}>
            استعراض الملفات
          </Link>
        )
    }
  ];

  useEffect(() => {
    if (isParent) {
      void studentService
        .list({ per_page: 100 })
        .then((payload) => {
          setChildren(payload.data);
          if (!selectedStudentId && payload.data[0]?.id) {
            setSelectedStudentId(String(payload.data[0].id));
          }
        })
        .catch((loadError) => setError(getErrorMessage(loadError)));

      return;
    }

    void schoolService
      .list({ perPage: 100 })
      .then((payload) => {
        setSchools(payload.data);

        if (payload.data.length && !selectedSchoolId) {
          setSelectedSchoolId(isSuperAdmin ? "" : schoolId || String(payload.data[0].id));
        }
      })
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [isParent, isSuperAdmin, schoolId, selectedSchoolId, selectedStudentId]);

  const filteredSchools = useMemo(() => {
    const normalizedSearch = search.trim();

    if (!normalizedSearch) {
      return schools;
    }

    return schools.filter((school) =>
      [school.name, school.stage, school.program_type]
        .filter(Boolean)
        .some((value) => String(value).includes(normalizedSearch))
    );
  }, [schools, search]);

  const matchingActions = useMemo(() => {
    const normalizedSearch = search.trim();

    if (!normalizedSearch) {
      return REPORT_ACTIONS;
    }

    return REPORT_ACTIONS.filter((action) => action.label.includes(normalizedSearch));
  }, [search]);

  const loadSchoolSummary = async () => {
    if (!selectedSchoolId) {
      setError("اختر مدرسة أولًا لعرض ملخصها.");
      return;
    }

    setLoading(true);

    try {
      const payload = await reportService.schoolSummary(selectedSchoolId);
      setSchoolSummary(payload);
      setCaption("تم تحديث تقرير المدرسة المحددة.");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  const loadStudentSummary = async () => {
    if (!selectedStudentId) {
      setError("اختر الابن أولًا لعرض التقرير.");
      return;
    }

    setLoading(true);

    try {
      const payload = await reportService.studentSummary(selectedStudentId);
      setStudentSummary(payload);
      setCaption("تم تحديث متابعة تقدم الابن المحدد.");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  const loadComparison = async () => {
    setLoading(true);

    try {
      const payload = await reportService.comparison(selectedSchoolId ? [selectedSchoolId] : []);
      setComparisonRows(payload);
      setCaption("تم تحديث تقرير المقارنة للمدارس المتاحة.");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  const loadPivot = async () => {
    setLoading(true);

    try {
      const payload = await reportService.pivot(dimension, selectedSchoolId ? [selectedSchoolId] : []);
      setPivot(payload);
      setCaption("تم تحديث التقرير المحوري حسب التصنيف المختار.");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async () => {
    if (isParent) {
      await loadStudentSummary();
      return;
    }

    if (selectedAction === "school_management") {
      if (!selectedSchoolId) {
        setError("اختر مدرسة أولًا لفتح صفحة المدرسة.");
        return;
      }

      window.location.assign(`/app/schools/${selectedSchoolId}`);
      return;
    }

    if (selectedAction === "school_files") {
      if (isSupervisor) {
        setCaption("قسم الملفات مخفي حاليًا عن حساب المشرف التربوي.");
        return;
      }

      if (!selectedSchoolId) {
        setError("اختر مدرسة أولًا لاستعراض ملفاتها.");
        return;
      }

      window.location.assign(`/app/files?schoolId=${selectedSchoolId}`);
      return;
    }

    if (selectedAction === "comparison") {
      await loadComparison();
      return;
    }

    if (selectedAction === "pivot") {
      await loadPivot();
      return;
    }

    await loadSchoolSummary();
  };

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">التقارير</span>
          <h2>التقارير</h2>
        </div>
        {!isParent ? (
          <div className="button-row">
            <button className="button button-primary" onClick={() => void handleAction()} type="button">
              تشغيل
            </button>
          </div>
        ) : null}
      </div>

      {isParent ? (
        <>
          <section className="surface-card page-stack">
            <div className="filters-bar filters-bar-wide">
              <label className="field">
                <span>الابن</span>
                <select onChange={(event) => setSelectedStudentId(event.target.value)} value={selectedStudentId}>
                  <option value="">اختر الابن</option>
                  {children.map((child) => (
                    <option key={child.id} value={child.id}>
                      {child.full_name}
                    </option>
                  ))}
                </select>
              </label>

              <label className="field">
                <span>متابعة التقدم</span>
                <input disabled value="تقرير الابن فقط" />
              </label>

              <div className="button-row">
                <button className="button button-primary" onClick={() => void handleAction()} type="button">
                  عرض التقرير
                </button>
              </div>
            </div>
          </section>

          <div className="info-box">{caption}</div>
          {error ? <div className="error-box">{error}</div> : null}
          {loading ? <div className="loading-box">جارٍ تجهيز تقرير الابن...</div> : null}

          {!loading && studentSummary ? (
            <section className="surface-card page-stack">
              <div className="page-header">
                <div>
                  <span className="eyebrow">متابعة التقدم</span>
                  <h3>{studentSummary.student.full_name}</h3>
                </div>
              </div>

              <div className="stats-grid">
                <div className="stat-card">
                  <span className="detail-label">عدد الخطط</span>
                  <strong>{studentSummary.iep.plans_count}</strong>
                </div>
                <div className="stat-card">
                  <span className="detail-label">عدد التقارير</span>
                  <strong>{studentSummary.activity.student_reports_count}</strong>
                </div>
                <div className="stat-card">
                  <span className="detail-label">متابعة التقدم</span>
                  <strong>{studentSummary.activity.portfolio_items_count}</strong>
                </div>
              </div>

              <div className="detail-grid">
                <div className="detail-list-item">
                  <span className="detail-label">المدرسة</span>
                  <strong>{studentSummary.school.name_ar ?? "-"}</strong>
                </div>
                <div className="detail-list-item">
                  <span className="detail-label">البرنامج</span>
                  <strong>{studentSummary.education.program ?? "-"}</strong>
                </div>
                <div className="detail-list-item">
                  <span className="detail-label">المعلم</span>
                  <strong>{studentSummary.education.primary_teacher ?? "-"}</strong>
                </div>
              </div>

              {studentSummary.iep.latest_plan ? (
                <div className="detail-list-item">
                  <span className="detail-label">أحدث خطة</span>
                  <strong>{studentSummary.iep.latest_plan.title}</strong>
                  <small>
                    الحالة: {translateStatus(studentSummary.iep.latest_plan.status)} | آخر تحديث:{" "}
                    {studentSummary.iep.latest_plan.updated_at
                      ? new Date(studentSummary.iep.latest_plan.updated_at).toLocaleString("ar-SA")
                      : "-"}
                  </small>
                </div>
              ) : null}
            </section>
          ) : null}
        </>
      ) : (
        <>

      <section className="surface-card page-stack">
        <div className="filters-bar filters-bar-wide">
          <label className="field">
            <span>بحث</span>
            <input
              onChange={(event) => setSearch(event.target.value)}
              placeholder="ابحث عن المدرسة أو التصنيف أو إدارة المدرسة أو ملفات المدارس"
              value={search}
            />
          </label>

          <label className="field">
            <span>المدرسة</span>
            <select onChange={(event) => setSelectedSchoolId(event.target.value)} value={selectedSchoolId}>
              <option value="">{isSuperAdmin ? "كل المدارس" : "اختر مدرسة"}</option>
              {filteredSchools.map((school) => (
                <option key={school.id} value={school.id}>
                  {school.name}
                </option>
              ))}
            </select>
          </label>

          <label className="field">
            <span>التصنيف</span>
            <select onChange={(event) => setDimension(event.target.value)} value={dimension}>
              {Object.entries(dimensionLabels).map(([value, label]) => (
                <option key={value} value={value}>
                  {label}
                </option>
              ))}
            </select>
          </label>

          <label className="field">
            <span>الإجراء</span>
            <select onChange={(event) => setSelectedAction(event.target.value)} value={selectedAction}>
              {REPORT_ACTIONS.map((action) => (
                <option key={action.value} value={action.value}>
                  {action.label}
                </option>
              ))}
            </select>
          </label>
        </div>
      </section>

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">نتائج البحث</span>
            <h3>المدارس والإجراءات السريعة</h3>
          </div>
        </div>

        <div className="card-grid">
          {matchingActions.map((action) => (
            <button
              className="shortcut-card shortcut-card-button"
              key={action.value}
              onClick={() => setSelectedAction(action.value)}
              type="button"
            >
              <strong>{action.label}</strong>
              <p>انقر لاختيار هذا النوع من التقارير أو التنقل.</p>
            </button>
          ))}

          {filteredSchools.map((school) => (
            <Link className="shortcut-card" key={school.id} to={`/app/schools/${school.id}`}>
              <strong>{school.name}</strong>
              <p>
                {school.stage ?? "-"} | {school.program_type ?? "-"}
              </p>
            </Link>
          ))}
        </div>
      </section>

      <div className="info-box">{caption}</div>
      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تشغيل التقرير...</div> : null}

      {!loading && schoolSummary ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">تقرير المدرسة</span>
              <h3>{schoolSummary.school.name_ar}</h3>
            </div>
          </div>

          <div className="stats-grid">
            {Object.entries(schoolSummary.overview).map(([key, value]) => (
              <div className="stat-card" key={key}>
                <span className="detail-label">{overviewLabels[key] ?? key}</span>
                <strong>{value}</strong>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {!loading ? (
        <DataTable
          columns={comparisonColumns}
          rows={comparisonRows}
          emptyMessage="لم يتم تشغيل تقرير المقارنة بعد."
        />
      ) : null}

      {!loading && pivot ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">التقرير المحوري</span>
              <h3>التصنيف: {dimensionLabels[pivot.dimension] ?? pivot.dimension}</h3>
            </div>
          </div>
          <DataTable
            columns={[
              { key: "label", label: "البند", render: (row) => translateStatus(row.label) },
              { key: "value", label: "القيمة", render: (row) => row.value }
            ]}
            emptyMessage="لا توجد بيانات محورية."
            rows={pivot.rows}
          />
        </section>
      ) : null}
        </>
      )}
    </section>
  );
}
