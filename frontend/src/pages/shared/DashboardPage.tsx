import { lazy, Suspense, useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { getErrorMessage } from "../../services/api";
import { dashboardService, type DashboardSummary } from "../../services/dashboardService";
import { useAuthStore } from "../../stores/authStore";
import { getRoleLabel } from "../../lib/uiText";

const DashboardChart = lazy(() =>
  import("../../components/dashboard/DashboardChart").then((module) => ({ default: module.DashboardChart }))
);

type StatCardDefinition = {
  key: string;
  label: string;
  value: number;
  icon: "schools" | "students" | "programs" | "teachers" | "supervisors" | "principals";
};

const shortcuts = [
  { to: "/app/schools", title: "إدارة المدارس", text: "إضافة المدارس وربط المديرين والمشرفين من حساب السوبر أدمن." },
  { to: "/app/programs", title: "أنواع البرامج", text: "إدارة البرامج التعليمية وإضافة خيارات جديدة من داخل النظام." },
  { to: "/app/announcements", title: "الإعلانات", text: "إنشاء الإعلانات المدرسية أو المركزية وإدارتها." },
  { to: "/app/students", title: "إدارة الطلاب", text: "استعراض الطلاب وربط البيانات الأكاديمية بسرعة." },
  { to: "/app/iep-plans", title: "الخطط الفردية", text: "متابعة الخطط الفردية والإصدارات الحالية." },
  { to: "/app/messages", title: "المحادثات", text: "مراجعة السلاسل الداخلية بين الأدوار المختلفة." },
  { to: "/app/reports", title: "التقارير", text: "تشغيل ملخص المدرسة والمقارنات من الواجهة." },
  { to: "/app/files", title: "الملفات", text: "رفع الملفات وإنشاء روابط تحميل مؤقتة." }
];

function StatIcon({ icon }: { icon: StatCardDefinition["icon"] }) {
  const paths: Record<StatCardDefinition["icon"], string> = {
    schools: "M4 19h16M6 17V8l6-4 6 4v9M9 19v-5h6v5",
    students: "M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 7a4 4 0 1 0 0-.01M20 8v6M23 11h-6",
    programs: "M4 6h16M4 12h16M4 18h10",
    teachers: "M12 3 2 8l10 5 8-4v7M6 10v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5",
    supervisors: "M12 3l8 4-8 4-8-4 8-4M4 11l8 4 8-4M4 15l8 4 8-4",
    principals: "M12 2 4 6v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-4Z"
  };

  return (
    <span className="stat-icon" aria-hidden="true">
      <svg fill="none" height="20" viewBox="0 0 24 24" width="20">
        <path
          d={paths[icon]}
          stroke="currentColor"
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth="1.8"
        />
      </svg>
    </span>
  );
}

export function DashboardPage() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const schoolId = useAuthStore((state) => state.schoolId);
  const isSuperAdmin = user?.role === "super_admin";
  const isAdmin = user?.role === "admin";
  const isSupervisor = user?.role === "supervisor";
  const assignedSchools = user?.assigned_schools ?? [];
  const hasPermission = (permission: string) => permissions.includes("*") || permissions.includes(permission);
  const [summary, setSummary] = useState<DashboardSummary | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [selectedSchoolId, setSelectedSchoolId] = useState(isSupervisor ? schoolId : "");
  const [selectedProgramType, setSelectedProgramType] = useState("");

  const selectedSchool = assignedSchools.find((school) => String(school.id) === selectedSchoolId) ?? null;
  const visibleShortcuts = shortcuts.filter((item) => {
    if (item.to === "/app/schools") {
      return user?.role === "super_admin";
    }

    if (item.to === "/app/programs") {
      return ["super_admin", "admin"].includes(user?.role ?? "");
    }

    if (item.to === "/app/announcements") {
      return ["super_admin", "principal"].includes(user?.role ?? "");
    }

    if (item.to === "/app/students") {
      return user?.role !== "parent" && (hasPermission("students.view") || hasPermission("students.view_any"));
    }

    if (item.to === "/app/files") {
      return user?.role !== "supervisor" && hasPermission("files.view");
    }

    if (item.to === "/app/iep-plans") {
      return hasPermission("iep.view") || hasPermission("iep.view_any");
    }

    if (item.to === "/app/messages") {
      return hasPermission("messages.view_any") || hasPermission("messages.view_thread");
    }

    if (item.to === "/app/reports") {
      return !["teacher", "principal"].includes(user?.role ?? "") && (hasPermission("reports.school_summary") || hasPermission("reports.student_summary"));
    }

    return true;
  });

  const availablePrograms = useMemo(() => {
    const programs = new Set(
      assignedSchools
        .map((school) => school.program_type)
        .filter((value): value is string => Boolean(value))
    );

    return Array.from(programs);
  }, [assignedSchools]);

  useEffect(() => {
    if (isSupervisor) {
      setSelectedSchoolId(schoolId);
    }
  }, [isSupervisor, schoolId]);

  useEffect(() => {
    if (!user) {
      return;
    }

    void dashboardService
      .summary({
        school_id: selectedSchoolId || undefined,
        program_type: selectedProgramType || undefined
      })
      .then(setSummary)
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [selectedProgramType, selectedSchoolId, user]);

  const statCards: StatCardDefinition[] = [];

  if (summary) {
    statCards.push(
      { key: "students_count", label: "عدد الطلاب", value: summary.students_count, icon: "students" },
      { key: "programs_count", label: "عدد البرامج", value: summary.programs_count, icon: "programs" },
      { key: "teachers_count", label: "عدد المعلمين", value: summary.teachers_count, icon: "teachers" }
    );

    if (isSuperAdmin || isAdmin) {
      statCards.unshift({ key: "schools_count", label: "عدد المدارس", value: summary.schools_count, icon: "schools" });
      statCards.push(
        { key: "supervisors_count", label: "عدد المشرفين", value: summary.supervisors_count, icon: "supervisors" },
        { key: "principals_count", label: "عدد مدراء المدارس", value: summary.principals_count, icon: "principals" }
      );
    }
  }

  const chartData = summary
    ? [
        { name: "المدارس", value: summary.schools_count },
        { name: "الطلاب", value: summary.students_count },
        { name: "البرامج", value: summary.programs_count }
      ]
    : [];

  return (
    <div className="page-stack">
      <section className="hero-card">
        <span className="eyebrow">جاهزية تنفيذية</span>
        <h2>مرحبًا، {user?.full_name ?? "مستخدم النظام"}</h2>
        <p>
          {isSupervisor
            ? "هذه لوحة إشرافية مرتبطة فقط بالمدارس المسندة لك. يمكنك تتبع المدارس والبرامج والمعلمين والطلاب ضمن نطاقك الإشرافي."
            : isSuperAdmin
              ? "يمكنك التنقل بين الوحدات الأساسية بصلاحيات كاملة على مستوى النظام."
              : "يمكنك التنقل بين الوحدات الأساسية حسب صلاحيات دورك الحالي."}
        </p>
        <div className="chip-row">
          <span className="chip">الدور: {getRoleLabel(user?.role)}</span>
          {!isSuperAdmin ? <span className="chip">المدرسة: {selectedSchool?.name_ar ?? user?.school?.name_ar ?? "-"}</span> : null}
          <span className="chip">عدد الصلاحيات: {permissions.length}</span>
        </div>
      </section>

      {isSupervisor ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">نطاق الإشراف</span>
              <h3>فلاتر المدارس والبرامج</h3>
            </div>
          </div>

          <div className="filters-bar filters-bar-wide">
            <label className="field">
              <span>المدرسة</span>
              <select onChange={(event) => setSelectedSchoolId(event.target.value)} value={selectedSchoolId}>
                <option value="">كل المدارس المسندة</option>
                {assignedSchools.map((school) => (
                  <option key={school.id} value={school.id}>
                    {school.name_ar}
                  </option>
                ))}
              </select>
            </label>

            <label className="field">
              <span>نوع البرنامج</span>
              <select
                onChange={(event) => setSelectedProgramType(event.target.value)}
                value={selectedProgramType}
              >
                <option value="">كل البرامج</option>
                {availablePrograms.map((program) => (
                  <option key={program} value={program}>
                    {program}
                  </option>
                ))}
              </select>
            </label>
          </div>
        </section>
      ) : null}

      {error ? <div className="error-box">{error}</div> : null}

      {summary ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">لوحة التشغيل</span>
              <h3>المؤشرات الرئيسية</h3>
            </div>
          </div>

          <div className="stats-grid">
            {!isSuperAdmin && !isAdmin ? (
              <>
                <div className="stat-card">
                  <div className="stat-card-head">
                    <StatIcon icon="schools" />
                    <span className="detail-label">اسم المدرسة</span>
                  </div>
                  <strong>{summary.context_school_name ?? selectedSchool?.name_ar ?? user?.school?.name_ar ?? "-"}</strong>
                </div>
                <div className="stat-card">
                  <div className="stat-card-head">
                    <StatIcon icon="principals" />
                    <span className="detail-label">اسم مدير المدرسة</span>
                  </div>
                  <strong>{summary.context_principal_name ?? "-"}</strong>
                </div>
              </>
            ) : null}
            {statCards.map((item) => (
              <div className="stat-card" key={item.key}>
                <div className="stat-card-head">
                  <StatIcon icon={item.icon} />
                  <span className="detail-label">{item.label}</span>
                </div>
                <strong>{item.value}</strong>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {summary ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">المخططات</span>
              <h3>ملخص بصري سريع</h3>
            </div>
          </div>

          <Suspense fallback={<div className="loading-box">جارٍ تحميل المخطط...</div>}>
            <DashboardChart data={chartData} />
          </Suspense>
        </section>
      ) : null}

      <section className="card-grid">
        {visibleShortcuts.map((item) => (
          <Link className="shortcut-card" key={item.to} to={item.to}>
            <strong>{item.title}</strong>
            <p>{item.text}</p>
          </Link>
        ))}
      </section>
    </div>
  );
}
