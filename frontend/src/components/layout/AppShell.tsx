import { Link, NavLink, Outlet } from "react-router-dom";
import { useAuthStore } from "../../stores/authStore";
import { getRoleLabel } from "../../lib/uiText";

type NavItem = {
  to: string;
  label: string;
  icon?: "school";
};

function NavItemIcon({ icon }: { icon?: NavItem["icon"] }) {
  if (icon !== "school") {
    return null;
  }

  return (
    <span aria-hidden="true" className="nav-link-icon">
      <svg fill="none" height="18" viewBox="0 0 24 24" width="18">
        <path
          d="M4 10.5 12 6l8 4.5"
          stroke="currentColor"
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth="1.8"
        />
        <path
          d="M6 11.5V18h12v-6.5"
          stroke="currentColor"
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth="1.8"
        />
        <path
          d="M10 18v-3h4v3M12 6v12"
          stroke="currentColor"
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth="1.8"
        />
      </svg>
    </span>
  );
}

export function AppShell() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const schoolId = useAuthStore((state) => state.schoolId);
  const setSchoolId = useAuthStore((state) => state.setSchoolId);
  const logout = useAuthStore((state) => state.logout);
  const normalizedRole = (user?.role ?? "").trim().toLowerCase();
  const hasPermission = (permission: string) => permissions.includes("*") || permissions.includes(permission);
  const assignedSchools = user?.assigned_schools ?? [];
  const selectedSchool = assignedSchools.find((school) => String(school.id) === schoolId) ?? null;
  const showSupervisorSchoolSwitcher = normalizedRole === "supervisor";
  const showStaticSchoolInfo = ["principal", "teacher", "parent"].includes(normalizedRole);
  const canManageAnnouncements = ["super_admin", "principal"].includes(normalizedRole);
  const canViewAnnouncements = hasPermission("announcements.view_any") || canManageAnnouncements;
  const canViewReports = !["teacher", "principal"].includes(normalizedRole) && (hasPermission("reports.school_summary") || hasPermission("reports.student_summary"));
  const canViewFiles = normalizedRole !== "supervisor" && hasPermission("files.view");
  const canViewStudents = normalizedRole !== "parent" && (hasPermission("students.view") || hasPermission("students.view_any"));
  const canViewPlans = hasPermission("iep.view") || hasPermission("iep.view_any");
  const canViewMessages = hasPermission("messages.view_any") || hasPermission("messages.view_thread");
  const currentSchoolCode = showSupervisorSchoolSwitcher
    ? selectedSchool?.school_code ?? selectedSchool?.official_code ?? "-"
    : user?.school?.school_code ?? "-";
  const currentPortalSchool = showSupervisorSchoolSwitcher
    ? assignedSchools.find((school) => String(school.id) === schoolId) ?? null
    : user?.school ?? null;
  const schoolPortalHref = currentPortalSchool
    ? `/schools/${currentPortalSchool.slug ?? currentPortalSchool.id}`
    : null;
  const navItems: NavItem[] = [
    { to: "/app", label: "الواجهة" },
    ...(normalizedRole === "super_admin" ? [{ to: "/app/schools", label: "المدارس", icon: "school" as const }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/users", label: "الحسابات والصلاحيات" }] : []),
    ...(["super_admin", "admin"].includes(normalizedRole) ? [{ to: "/app/programs", label: "أنواع البرامج" }] : []),
    ...(canViewAnnouncements ? [{ to: "/app/announcements", label: "الإعلانات" }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/audit-logs", label: "سجل التعديلات" }] : []),
    ...(canViewStudents ? [{ to: "/app/students", label: "الطلاب" }] : []),
    ...(canViewPlans ? [{ to: "/app/iep-plans", label: "الخطط الفردية" }] : []),
    ...(canViewMessages ? [{ to: "/app/messages", label: "الرسائل" }] : []),
    ...(canViewReports ? [{ to: "/app/reports", label: "التقارير" }] : []),
    ...(canViewFiles ? [{ to: "/app/files", label: "الملفات" }] : [])
  ];

  return (
    <div className="app-shell">
      <aside className="sidebar-panel">
        <div className="brand-block">
          <span className="eyebrow">معاك</span>
          <h1>لوحة التشغيل</h1>
          <p>واجهة تشغيل تفاعلية مرتبطة مباشرة بالنظام الخلفي الجاري تشغيله.</p>
        </div>

        {showSupervisorSchoolSwitcher ? (
          <div className="context-card">
            <span className="context-label">مدارس متعددة</span>
            <select
              className="context-select"
              onChange={(event) => setSchoolId(event.target.value)}
              value={schoolId}
            >
              {assignedSchools.map((school) => (
                <option key={school.id} value={school.id}>
                  {school.name_ar}
                </option>
              ))}
            </select>
            <small className="context-hint">
              {selectedSchool ? `البرنامج: ${selectedSchool.program_type ?? "-"}` : "اختر مدرسة من نطاقك الإشرافي."}
            </small>
          </div>
        ) : null}

        {showStaticSchoolInfo ? (
          <div className="context-card">
            <span className="context-label">رقم المدرسة</span>
            <strong>{currentSchoolCode}</strong>
            <small className="context-hint">{user?.school?.name_ar ?? "المدرسة الحالية"}</small>
          </div>
        ) : null}

        <nav className="nav-stack">
          {navItems.map((item) => (
            <NavLink
              key={item.to}
              className={({ isActive }) =>
                `nav-link${isActive ? " nav-link-active" : ""}`
              }
              to={item.to}
            >
              <span className="nav-link-content">
                <NavItemIcon icon={item.icon} />
                <span>{item.label}</span>
              </span>
            </NavLink>
          ))}
        </nav>

        {schoolPortalHref ? (
          <Link className="button button-secondary" to={schoolPortalHref}>
            العودة إلى بوابة المدرسة
          </Link>
        ) : null}

        <button className="button button-secondary" onClick={() => void logout()} type="button">
          تسجيل الخروج
        </button>
      </aside>

      <div className="content-shell">
        <header className="topbar">
          <div>
            <div className="eyebrow">المستخدم الحالي</div>
            <strong>{user?.full_name ?? "غير مسجل"}</strong>
          </div>
          <div className="topbar-meta">
            <span>{getRoleLabel(user?.role)}</span>
            {showSupervisorSchoolSwitcher ? <span>{selectedSchool?.name_ar ?? "مدارس متعددة"}</span> : null}
            <span>{user?.email ?? "-"}</span>
          </div>
        </header>

        <main className="content-panel">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
