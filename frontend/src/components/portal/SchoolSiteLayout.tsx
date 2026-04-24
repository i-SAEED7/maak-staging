import { useEffect, useMemo, useState } from "react";
import { Link, NavLink, Navigate, Outlet, useNavigate, useParams } from "react-router-dom";
import { resolvePostLoginPath } from "../../lib/postLogin";
import {
  buildAccessibleSchools,
  resolveSchoolPath,
  translateRole,
  type SchoolSiteContextValue,
  type SchoolSiteStats
} from "../../lib/schoolSite";
import { getErrorMessage } from "../../services/api";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { useAuthStore } from "../../stores/authStore";

type SchoolSiteNavItem = {
  to: string;
  label: string;
};

export function SchoolSiteLayout() {
  const { schoolSlug } = useParams();
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const setSchoolId = useAuthStore((state) => state.setSchoolId);
  const logout = useAuthStore((state) => state.logout);
  const [school, setSchool] = useState<SchoolItem | null>(null);
  const [schoolStats, setSchoolStats] = useState<SchoolSiteStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const accessibleSchools = useMemo(() => buildAccessibleSchools(user), [user]);
  const matchedSchool = useMemo(
    () => accessibleSchools.find((item) => item.slug === schoolSlug || String(item.id) === schoolSlug) ?? null,
    [accessibleSchools, schoolSlug]
  );

  useEffect(() => {
    if (!user || !matchedSchool) {
      setLoading(false);
      return;
    }

    let isActive = true;

    setSchoolId(String(matchedSchool.id));
    setLoading(true);

    void Promise.all([schoolService.details(matchedSchool.id), schoolService.stats(matchedSchool.id)])
      .then(([schoolPayload, statsPayload]) => {
        if (!isActive) {
          return;
        }

        setSchool(schoolPayload);
        setSchoolStats(statsPayload);
        setError(null);
      })
      .catch((loadError) => {
        if (!isActive) {
          return;
        }

        setError(getErrorMessage(loadError));
      })
      .finally(() => {
        if (isActive) {
          setLoading(false);
        }
      });

    return () => {
      isActive = false;
    };
  }, [matchedSchool, setSchoolId, user]);

  if (!user) {
    return <Navigate replace to="/login" />;
  }

  if (!schoolSlug || !matchedSchool) {
    return <Navigate replace to={resolvePostLoginPath(user, { mode: user.is_central ? "central" : "school" })} />;
  }

  if (loading || !school) {
    return (
      <div className="portal-page-stack">
        <section className="portal-surface">
          {loading ? <div className="loading-box">جارٍ تجهيز موقع المدرسة...</div> : null}
          {error ? <div className="error-box">{error}</div> : null}
        </section>
      </div>
    );
  }

  const role = user.role;
  const currentSchoolId = matchedSchool.id;
  const currentSchoolSlug = school.slug ?? matchedSchool.slug ?? String(currentSchoolId);
  const schoolPath = resolveSchoolPath({ id: currentSchoolId, slug: currentSchoolSlug });
  const canAccessFiles = role !== "supervisor" && (permissions.includes("*") || permissions.includes("files.view"));
  const canUploadFiles = role !== "supervisor" && (permissions.includes("*") || permissions.includes("files.upload"));
  const canDeleteFiles = role !== "supervisor" && (permissions.includes("*") || permissions.includes("files.delete"));
  const canManageAnnouncements =
    permissions.includes("*") ||
    permissions.includes("announcements.create") ||
    permissions.includes("announcements.update");
  const canViewAnnouncements = canManageAnnouncements || permissions.includes("announcements.view_any");
  const canSendMessages = permissions.includes("*") || permissions.includes("messages.send");
  const canViewReports = permissions.includes("*") || permissions.includes("reports.school_summary") || permissions.includes("reports.student_summary");
  const canViewStudents = permissions.includes("*") || permissions.includes("students.view") || permissions.includes("students.view_any");
  const canViewPlans = permissions.includes("*") || permissions.includes("iep.view") || permissions.includes("iep.view_any");
  const navItems: SchoolSiteNavItem[] = [
    { to: schoolPath, label: "الرئيسية" },
    { to: `${schoolPath}/programs`, label: "البرامج" },
    ...(canAccessFiles ? [{ to: `${schoolPath}/files`, label: "الملفات" }] : []),
    ...(canViewAnnouncements ? [{ to: `${schoolPath}/announcements`, label: "الإعلانات" }] : []),
    { to: `${schoolPath}/services`, label: "الخدمات" },
    { to: `${schoolPath}/contact`, label: "تواصل معنا" }
  ];

  const contextValue: SchoolSiteContextValue = {
    school,
    schoolStats,
    currentSchoolId,
    currentSchoolSlug,
    schoolPath,
    user,
    permissions,
    role,
    accessibleSchools,
    canAccessFiles,
    canUploadFiles,
    canDeleteFiles,
    canManageAnnouncements,
    canViewAnnouncements,
    canSendMessages,
    canViewReports,
    canViewStudents,
    canViewPlans,
    isSupervisor: role === "supervisor",
    isPrincipal: role === "principal",
    isTeacher: role === "teacher",
    isParent: role === "parent"
  };

  return (
    <div className="portal-page-stack">
      <section className="portal-surface school-site-layout">
        <div className="school-site-head">
          <div className="school-site-intro">
            <span className="portal-eyebrow">موقع المدرسة</span>
            <h2>{school.name}</h2>
            <p>
              {school.stage ?? "مرحلة غير محددة"} | {school.program_type ?? "برنامج غير محدد"}
            </p>
            <div className="chip-row">
              <span className="chip">{translateRole(role)}</span>
              <span className="chip">{school.school_code ?? school.official_code ?? "-"}</span>
            </div>
          </div>

          <div className="portal-button-row">
            <Link className="portal-button portal-button-primary" to="/app">
              الدخول إلى النظام المركزي
            </Link>
            {role === "supervisor" && accessibleSchools.length > 1 ? (
              <Link className="portal-button portal-button-secondary" to="/select-school">
                تغيير المدرسة
              </Link>
            ) : null}
            <button
              className="portal-button portal-button-secondary"
              onClick={() => {
                void logout().then(() => navigate("/login", { replace: true }));
              }}
              type="button"
            >
              تسجيل الخروج
            </button>
          </div>
        </div>

        <div className="school-site-meta-grid">
          <article className="portal-stat-card">
            <span>كود المدرسة</span>
            <strong>{school.school_code ?? school.official_code ?? "-"}</strong>
          </article>
          <article className="portal-stat-card">
            <span>المدير</span>
            <strong>{school.principal?.full_name ?? "غير محدد"}</strong>
          </article>
          <article className="portal-stat-card">
            <span>المشرف</span>
            <strong>{school.supervisor?.full_name ?? "غير محدد"}</strong>
          </article>
        </div>

        <nav className="school-site-nav">
          {navItems.map((item) => (
            <NavLink
              key={item.to}
              className={({ isActive }) => `school-site-nav-link${isActive ? " is-active" : ""}`}
              end={item.to === schoolPath}
              to={item.to}
            >
              {item.label}
            </NavLink>
          ))}
        </nav>
      </section>

      {error ? <div className="error-box">{error}</div> : null}

      <Outlet context={contextValue} />
    </div>
  );
}
