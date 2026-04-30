import { useState } from "react";
import { Link, NavLink, Outlet, useNavigate } from "react-router-dom";
import {
  Bell,
  BarChart3,
  FileText,
  GraduationCap,
  LayoutDashboard,
  LogOut,
  Megaphone,
  MessageSquare,
  NotebookTabs,
  Quote,
  RotateCcw,
  School,
  Archive,
  FolderKanban,
  UserCheck,
  Users,
  Menu,
  X
} from "lucide-react";
import { cn } from "../../lib/utils";
import { useAuthStore } from "../../stores/authStore";
import { getRoleLabel } from "../../lib/uiText";
import { ThemeToggle } from "../ui/ThemeToggle";

type NavItem = {
  to: string;
  label: string;
  icon: "dashboard" | "school" | "users" | "approvals" | "programs" | "quotes" | "announcements" | "audit" | "students" | "iep" | "messages" | "reports" | "files" | "teacherPortfolio";
};

const navIcons: Record<NavItem["icon"], typeof LayoutDashboard> = {
  dashboard: LayoutDashboard,
  school: School,
  users: Users,
  approvals: UserCheck,
  programs: NotebookTabs,
  quotes: Quote,
  announcements: Megaphone,
  audit: Bell,
  students: GraduationCap,
  iep: FileText,
  messages: MessageSquare,
  reports: BarChart3,
  files: Archive,
  teacherPortfolio: FolderKanban
};

function NavItemIcon({ icon }: { icon: NavItem["icon"] }) {
  const Icon = navIcons[icon];

  return (
    <span aria-hidden="true" className="nav-link-icon">
      <Icon size={18} strokeWidth={1.9} />
    </span>
  );
}

export function AppShell() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const schoolId = useAuthStore((state) => state.schoolId);
  const setSchoolId = useAuthStore((state) => state.setSchoolId);
  const logout = useAuthStore((state) => state.logout);
  const navigate = useNavigate();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
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
  const canViewTeacherPortfolio =
    normalizedRole === "teacher" &&
    (hasPermission("teacher_portfolios.view") || hasPermission("portfolios.view") || hasPermission("portfolios.view_any"));
  const currentSchoolName = showSupervisorSchoolSwitcher
    ? selectedSchool?.name_ar ?? "-"
    : user?.school?.name_ar ?? "-";
  const currentPortalSchool = showSupervisorSchoolSwitcher
    ? assignedSchools.find((school) => String(school.id) === schoolId) ?? null
    : user?.school ?? null;
  const schoolPortalHref = currentPortalSchool
    ? `/schools/${currentPortalSchool.slug ?? currentPortalSchool.id}`
    : null;
  const navItems: NavItem[] = [
    { to: "/app", label: "الواجهة", icon: "dashboard" },
    ...(normalizedRole === "super_admin" ? [{ to: "/app/schools", label: "المدارس", icon: "school" as const }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/users", label: "الحسابات والصلاحيات", icon: "users" as const }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/account-approvals", label: "اعتماد الحسابات", icon: "approvals" as const }] : []),
    ...(["super_admin", "admin"].includes(normalizedRole) ? [{ to: "/app/programs", label: "أنواع البرامج", icon: "programs" as const }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/inspirational-quotes", label: "العبارات الملهمة", icon: "quotes" as const }] : []),
    ...(canViewAnnouncements ? [{ to: "/app/announcements", label: "الإعلانات", icon: "announcements" as const }] : []),
    ...(normalizedRole === "super_admin" ? [{ to: "/app/audit-logs", label: "سجل التعديلات", icon: "audit" as const }] : []),
    ...(canViewStudents ? [{ to: "/app/students", label: "الطلاب", icon: "students" as const }] : []),
    ...(canViewPlans ? [{ to: "/app/iep-plans", label: "الخطط الفردية", icon: "iep" as const }] : []),
    ...(canViewMessages ? [{ to: "/app/messages", label: "الرسائل", icon: "messages" as const }] : []),
    ...(canViewReports ? [{ to: "/app/reports", label: "التقارير", icon: "reports" as const }] : []),
    ...(canViewFiles ? [{ to: "/app/files", label: "الملفات", icon: "files" as const }] : []),
    ...(canViewTeacherPortfolio ? [{ to: "/app/teacher-portfolio", label: "ملف إنجاز المعلم", icon: "teacherPortfolio" as const }] : [])
  ];

  return (
    <div className="app-shell relative">
      {/* Mobile Toggle Button */}
      <button 
        className="md:hidden fixed top-4 right-4 z-50 p-2 bg-white rounded-lg shadow-md border border-[rgba(123,97,58,0.15)]"
        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
      >
        {isMobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
      </button>

      {/* Backdrop for mobile */}
      {isMobileMenuOpen && (
        <div 
          className="fixed inset-0 bg-black/50 z-30 md:hidden"
          onClick={() => setIsMobileMenuOpen(false)}
        />
      )}

      <aside className={cn(
        "sidebar-panel z-40 bg-[rgba(255,252,247,0.95)] backdrop-blur-md transition-transform duration-300 md:translate-x-0",
        isMobileMenuOpen ? "translate-x-0 fixed inset-y-0 right-0 w-[280px] shadow-2xl" : "translate-x-full fixed md:sticky md:block"
      )}>
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
            <span className="context-label">المدرسة</span>
            <strong>{currentSchoolName}</strong>
            <small className="context-hint">لا يظهر رقم المدرسة إلا للسوبر أدمن.</small>
          </div>
        ) : null}

        <nav className="nav-stack">
          {navItems.map((item) => (
            <NavLink
              key={item.to}
              className={({ isActive }) =>
                cn("nav-link", isActive && "nav-link-active")
              }
              onClick={() => setIsMobileMenuOpen(false)}
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
            <RotateCcw size={18} />
            العودة إلى بوابة المدرسة
          </Link>
        ) : null}

        <div className="flex items-center gap-3" style={{ display: 'flex', gap: '10px' }}>
          <ThemeToggle />
          <button
            className="button button-secondary"
            onClick={async () => {
              await logout();
              navigate("/?login=1", { replace: true });
            }}
            style={{ flex: 1 }}
            type="button"
          >
            <LogOut size={18} />
            تسجيل الخروج
          </button>
        </div>
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
