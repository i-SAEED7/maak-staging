import { createBrowserRouter, Navigate } from "react-router-dom";
import { lazy, Suspense, type ReactNode } from "react";
import { AppShell } from "./components/layout/AppShell";
import { PortalLayout } from "./components/portal/PortalLayout";
import { SchoolSiteLayout } from "./components/portal/SchoolSiteLayout";
import { resolvePostLoginPath } from "./lib/postLogin";
import { useAuthStore } from "./stores/authStore";

/* ---------- Lazy-loaded pages (code splitting) ---------- */

// Admin pages
const AuditLogsPage = lazy(() => import("./pages/admin/AuditLogsPage").then(m => ({ default: m.AuditLogsPage })));
const AccountApprovalsPage = lazy(() => import("./pages/admin/AccountApprovalsPage").then(m => ({ default: m.AccountApprovalsPage })));
const ProgramsPage = lazy(() => import("./pages/admin/ProgramsPage").then(m => ({ default: m.ProgramsPage })));
const InspirationalQuotesPage = lazy(() => import("./pages/admin/InspirationalQuotesPage").then(m => ({ default: m.InspirationalQuotesPage })));
const SchoolsPage = lazy(() => import("./pages/admin/SchoolsPage").then(m => ({ default: m.SchoolsPage })));
const UsersPage = lazy(() => import("./pages/admin/UsersPage").then(m => ({ default: m.UsersPage })));

// Auth pages
const RegisterPage = lazy(() => import("./pages/auth/RegisterPage").then(m => ({ default: m.RegisterPage })));
const ResetPasswordPage = lazy(() => import("./pages/auth/ResetPasswordPage").then(m => ({ default: m.ResetPasswordPage })));

// Portal pages
const AboutPage = lazy(() => import("./pages/portal/AboutPage").then(m => ({ default: m.AboutPage })));
const ContactPage = lazy(() => import("./pages/portal/ContactPage").then(m => ({ default: m.ContactPage })));
const HomePage = lazy(() => import("./pages/portal/HomePage").then(m => ({ default: m.HomePage })));
const InteractiveMapPage = lazy(() => import("./pages/portal/InteractiveMapPage").then(m => ({ default: m.InteractiveMapPage })));
const ProgramDetailsPage = lazy(() => import("./pages/portal/ProgramDetailsPage").then(m => ({ default: m.ProgramDetailsPage })));
const SchoolAnnouncementsPage = lazy(() => import("./pages/portal/SchoolAnnouncementsPage").then(m => ({ default: m.SchoolAnnouncementsPage })));
const SchoolContactPage = lazy(() => import("./pages/portal/SchoolContactPage").then(m => ({ default: m.SchoolContactPage })));
const SchoolFilesPage = lazy(() => import("./pages/portal/SchoolFilesPage").then(m => ({ default: m.SchoolFilesPage })));
const SchoolGatewayPage = lazy(() => import("./pages/portal/SchoolGatewayPage").then(m => ({ default: m.SchoolGatewayPage })));
const SchoolProgramDetailsPage = lazy(() => import("./pages/portal/SchoolProgramDetailsPage").then(m => ({ default: m.SchoolProgramDetailsPage })));
const SchoolProgramsPage = lazy(() => import("./pages/portal/SchoolProgramsPage").then(m => ({ default: m.SchoolProgramsPage })));
const SchoolSelectionPage = lazy(() => import("./pages/portal/SchoolSelectionPage").then(m => ({ default: m.SchoolSelectionPage })));
const SchoolServicesPage = lazy(() => import("./pages/portal/SchoolServicesPage").then(m => ({ default: m.SchoolServicesPage })));
const ServicesPage = lazy(() => import("./pages/portal/ServicesPage").then(m => ({ default: m.ServicesPage })));
const StatisticsPage = lazy(() => import("./pages/portal/StatisticsPage").then(m => ({ default: m.StatisticsPage })));
const Test3DPage = lazy(() => import("./pages/portal/Test3DPage").then(m => ({ default: m.Test3DPage })));

// Shared pages
const AnnouncementDetailsPage = lazy(() => import("./pages/shared/AnnouncementDetailsPage").then(m => ({ default: m.AnnouncementDetailsPage })));
const AnnouncementsPage = lazy(() => import("./pages/shared/AnnouncementsPage").then(m => ({ default: m.AnnouncementsPage })));
const DashboardPage = lazy(() => import("./pages/shared/DashboardPage").then(m => ({ default: m.DashboardPage })));
const FilesPage = lazy(() => import("./pages/shared/FilesPage").then(m => ({ default: m.FilesPage })));
const IepPlanCreatePage = lazy(() => import("./pages/shared/IepPlanCreatePage").then(m => ({ default: m.IepPlanCreatePage })));
const IepPlanDetailsPage = lazy(() => import("./pages/shared/IepPlanDetailsPage").then(m => ({ default: m.IepPlanDetailsPage })));
const IepPlanEditPage = lazy(() => import("./pages/shared/IepPlanEditPage").then(m => ({ default: m.IepPlanEditPage })));
const IepPlansPage = lazy(() => import("./pages/shared/IepPlansPage").then(m => ({ default: m.IepPlansPage })));
const MessagesPage = lazy(() => import("./pages/shared/MessagesPage").then(m => ({ default: m.MessagesPage })));
const NotificationsPage = lazy(() => import("./pages/shared/NotificationsPage").then(m => ({ default: m.NotificationsPage })));
const ReportsPage = lazy(() => import("./pages/shared/ReportsPage").then(m => ({ default: m.ReportsPage })));
const SupervisorIepClassTeachersPage = lazy(() => import("./pages/shared/SupervisorIepClassTeachersPage").then(m => ({ default: m.SupervisorIepClassTeachersPage })));
const SupervisorIepProgramClassesPage = lazy(() => import("./pages/shared/SupervisorIepProgramClassesPage").then(m => ({ default: m.SupervisorIepProgramClassesPage })));
const SupervisorIepSchoolProgramsPage = lazy(() => import("./pages/shared/SupervisorIepSchoolProgramsPage").then(m => ({ default: m.SupervisorIepSchoolProgramsPage })));
const SchoolDetailsPage = lazy(() => import("./pages/shared/SchoolDetailsPage").then(m => ({ default: m.SchoolDetailsPage })));
const StudentEditPage = lazy(() => import("./pages/shared/StudentEditPage").then(m => ({ default: m.StudentEditPage })));
const StudentDetailsPage = lazy(() => import("./pages/shared/StudentDetailsPage").then(m => ({ default: m.StudentDetailsPage })));
const StudentsPage = lazy(() => import("./pages/shared/StudentsPage").then(m => ({ default: m.StudentsPage })));
const SupervisorIepTeacherStudentsPage = lazy(() => import("./pages/shared/SupervisorIepTeacherStudentsPage").then(m => ({ default: m.SupervisorIepTeacherStudentsPage })));

// Teacher pages
const PortfolioPage = lazy(() => import("./pages/teacher/PortfolioPage").then(m => ({ default: m.PortfolioPage })));

/* ---------- Suspense wrapper ---------- */

function LazyPage({ children }: { children: ReactNode }) {
  return (
    <Suspense fallback={<div className="fullscreen-message">جارٍ تحميل الصفحة...</div>}>
      {children}
    </Suspense>
  );
}

function ProtectedAppLayout() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);

  if (status !== "ready") {
    return <div className="fullscreen-message">جارٍ تجهيز الجلسة...</div>;
  }

  if (!token || !user) {
    return <Navigate replace to="/?login=1" />;
  }

  return <AppShell />;
}

function ProtectedPortalPage({ children }: { children: ReactNode }) {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);

  if (status !== "ready") {
    return <div className="fullscreen-message">جارٍ تجهيز الجلسة...</div>;
  }

  if (!token || !user) {
    return <Navigate replace to="/?login=1" />;
  }

  return children;
}

function NotFoundRedirect() {
  const user = useAuthStore((state) => state.user);

  return <Navigate replace to={user ? resolvePostLoginPath(user) : "/"} />;
}

export const router = createBrowserRouter([
  {
    path: "/",
    element: <PortalLayout />,
    children: [
      { index: true, element: <LazyPage><HomePage /></LazyPage> },
      { path: "about", element: <LazyPage><AboutPage /></LazyPage> },
      { path: "services", element: <LazyPage><ServicesPage /></LazyPage> },
      { path: "programs/:programSlug", element: <LazyPage><ProgramDetailsPage /></LazyPage> },
      { path: "statistics", element: <LazyPage><StatisticsPage /></LazyPage> },
      { path: "interactive-map", element: <LazyPage><InteractiveMapPage /></LazyPage> },
      { path: "contact", element: <LazyPage><ContactPage /></LazyPage> },
      { path: "test-3d", element: <LazyPage><Test3DPage /></LazyPage> },
      { path: "login", element: <Navigate replace to="/?login=1" /> },
      { path: "register", element: <LazyPage><RegisterPage /></LazyPage> },
      { path: "reset-password", element: <LazyPage><ResetPasswordPage /></LazyPage> },
      {
        path: "select-school",
        element: (
          <ProtectedPortalPage>
            <LazyPage><SchoolSelectionPage /></LazyPage>
          </ProtectedPortalPage>
        )
      },
      {
        path: "schools/:schoolSlug",
        element: (
          <ProtectedPortalPage>
            <SchoolSiteLayout />
          </ProtectedPortalPage>
        ),
        children: [
          { index: true, element: <LazyPage><SchoolGatewayPage /></LazyPage> },
          { path: "programs", element: <LazyPage><SchoolProgramsPage /></LazyPage> },
          { path: "programs/:programSlug", element: <LazyPage><SchoolProgramDetailsPage /></LazyPage> },
          { path: "files", element: <LazyPage><SchoolFilesPage /></LazyPage> },
          { path: "announcements", element: <LazyPage><SchoolAnnouncementsPage /></LazyPage> },
          { path: "announcements/:announcementId", element: <LazyPage><AnnouncementDetailsPage /></LazyPage> },
          { path: "services", element: <LazyPage><SchoolServicesPage /></LazyPage> },
          { path: "contact", element: <LazyPage><SchoolContactPage /></LazyPage> }
        ]
      }
    ]
  },
  {
    path: "/app",
    element: <ProtectedAppLayout />,
    children: [
      { index: true, element: <LazyPage><DashboardPage /></LazyPage> },
      { path: "schools", element: <LazyPage><SchoolsPage /></LazyPage> },
      { path: "schools/:schoolId", element: <LazyPage><SchoolDetailsPage /></LazyPage> },
      { path: "programs", element: <LazyPage><ProgramsPage /></LazyPage> },
      { path: "inspirational-quotes", element: <LazyPage><InspirationalQuotesPage /></LazyPage> },
      { path: "users", element: <LazyPage><UsersPage /></LazyPage> },
      { path: "account-approvals", element: <LazyPage><AccountApprovalsPage /></LazyPage> },
      { path: "audit-logs", element: <LazyPage><AuditLogsPage /></LazyPage> },
      { path: "announcements", element: <LazyPage><AnnouncementsPage /></LazyPage> },
      { path: "announcements/:announcementId", element: <LazyPage><AnnouncementDetailsPage /></LazyPage> },
      { path: "students", element: <LazyPage><StudentsPage /></LazyPage> },
      { path: "students/:studentId", element: <LazyPage><StudentDetailsPage /></LazyPage> },
      { path: "students/:studentId/edit", element: <LazyPage><StudentEditPage /></LazyPage> },
      { path: "iep-plans", element: <LazyPage><IepPlansPage /></LazyPage> },
      { path: "iep-plans/create", element: <LazyPage><IepPlanCreatePage /></LazyPage> },
      { path: "iep-plans/schools/:schoolId/programs", element: <LazyPage><SupervisorIepSchoolProgramsPage /></LazyPage> },
      { path: "iep-plans/schools/:schoolId/programs/:programId/classes", element: <LazyPage><SupervisorIepProgramClassesPage /></LazyPage> },
      {
        path: "iep-plans/schools/:schoolId/programs/:programId/classes/:classKey/teachers",
        element: <LazyPage><SupervisorIepClassTeachersPage /></LazyPage>
      },
      {
        path: "iep-plans/schools/:schoolId/programs/:programId/classes/:classKey/teachers/:teacherId/plans",
        element: <LazyPage><SupervisorIepTeacherStudentsPage /></LazyPage>
      },
      { path: "iep-plans/:planId", element: <LazyPage><IepPlanDetailsPage /></LazyPage> },
      { path: "iep-plans/:planId/edit", element: <LazyPage><IepPlanEditPage /></LazyPage> },
      { path: "messages", element: <LazyPage><MessagesPage /></LazyPage> },
      { path: "messages/:threadKey", element: <LazyPage><MessagesPage /></LazyPage> },
      { path: "notifications", element: <LazyPage><NotificationsPage /></LazyPage> },
      { path: "reports", element: <LazyPage><ReportsPage /></LazyPage> },
      { path: "files", element: <LazyPage><FilesPage /></LazyPage> },
      { path: "teacher-portfolio", element: <LazyPage><PortfolioPage /></LazyPage> }
    ]
  },
  {
    path: "*",
    element: <NotFoundRedirect />
  }
]);
