import { createBrowserRouter, Navigate } from "react-router-dom";
import type { ReactNode } from "react";
import { AppShell } from "./components/layout/AppShell";
import { PortalLayout } from "./components/portal/PortalLayout";
import { SchoolSiteLayout } from "./components/portal/SchoolSiteLayout";
import { AuditLogsPage } from "./pages/admin/AuditLogsPage";
import { ProgramsPage } from "./pages/admin/ProgramsPage";
import { AnnouncementDetailsPage } from "./pages/shared/AnnouncementDetailsPage";
import { AnnouncementsPage } from "./pages/shared/AnnouncementsPage";
import { CentralLoginPage } from "./pages/auth/CentralLoginPage";
import { LoginPage } from "./pages/auth/LoginPage";
import { SchoolLoginPage } from "./pages/auth/SchoolLoginPage";
import { SchoolsPage } from "./pages/admin/SchoolsPage";
import { AboutPage } from "./pages/portal/AboutPage";
import { ContactPage } from "./pages/portal/ContactPage";
import { HomePage } from "./pages/portal/HomePage";
import { InteractiveMapPage } from "./pages/portal/InteractiveMapPage";
import { ProgramDetailsPage } from "./pages/portal/ProgramDetailsPage";
import { SchoolAnnouncementsPage } from "./pages/portal/SchoolAnnouncementsPage";
import { SchoolContactPage } from "./pages/portal/SchoolContactPage";
import { SchoolFilesPage } from "./pages/portal/SchoolFilesPage";
import { SchoolGatewayPage } from "./pages/portal/SchoolGatewayPage";
import { SchoolProgramDetailsPage } from "./pages/portal/SchoolProgramDetailsPage";
import { SchoolProgramsPage } from "./pages/portal/SchoolProgramsPage";
import { SchoolSelectionPage } from "./pages/portal/SchoolSelectionPage";
import { SchoolServicesPage } from "./pages/portal/SchoolServicesPage";
import { ServicesPage } from "./pages/portal/ServicesPage";
import { StatisticsPage } from "./pages/portal/StatisticsPage";
import { DashboardPage } from "./pages/shared/DashboardPage";
import { FilesPage } from "./pages/shared/FilesPage";
import { IepPlanCreatePage } from "./pages/shared/IepPlanCreatePage";
import { IepPlanDetailsPage } from "./pages/shared/IepPlanDetailsPage";
import { IepPlanEditPage } from "./pages/shared/IepPlanEditPage";
import { IepPlansPage } from "./pages/shared/IepPlansPage";
import { MessagesPage } from "./pages/shared/MessagesPage";
import { NotificationsPage } from "./pages/shared/NotificationsPage";
import { ReportsPage } from "./pages/shared/ReportsPage";
import { SupervisorIepClassTeachersPage } from "./pages/shared/SupervisorIepClassTeachersPage";
import { SupervisorIepProgramClassesPage } from "./pages/shared/SupervisorIepProgramClassesPage";
import { SupervisorIepSchoolProgramsPage } from "./pages/shared/SupervisorIepSchoolProgramsPage";
import { SchoolDetailsPage } from "./pages/shared/SchoolDetailsPage";
import { StudentEditPage } from "./pages/shared/StudentEditPage";
import { StudentDetailsPage } from "./pages/shared/StudentDetailsPage";
import { StudentsPage } from "./pages/shared/StudentsPage";
import { SupervisorIepTeacherStudentsPage } from "./pages/shared/SupervisorIepTeacherStudentsPage";
import { UsersPage } from "./pages/admin/UsersPage";
import { resolvePostLoginPath } from "./lib/postLogin";
import { useAuthStore } from "./stores/authStore";

function ProtectedAppLayout() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const status = useAuthStore((state) => state.status);

  if (status !== "ready") {
    return <div className="fullscreen-message">جارٍ تجهيز الجلسة...</div>;
  }

  if (!token || !user) {
    return <Navigate replace to="/login" />;
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
    return <Navigate replace to="/login" />;
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
      { index: true, element: <HomePage /> },
      { path: "about", element: <AboutPage /> },
      { path: "services", element: <ServicesPage /> },
      { path: "programs/:programSlug", element: <ProgramDetailsPage /> },
      { path: "statistics", element: <StatisticsPage /> },
      { path: "interactive-map", element: <InteractiveMapPage /> },
      { path: "contact", element: <ContactPage /> },
      { path: "login", element: <LoginPage /> },
      { path: "login/school", element: <SchoolLoginPage /> },
      { path: "login/central", element: <CentralLoginPage /> },
      {
        path: "select-school",
        element: (
          <ProtectedPortalPage>
            <SchoolSelectionPage />
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
          { index: true, element: <SchoolGatewayPage /> },
          { path: "programs", element: <SchoolProgramsPage /> },
          { path: "programs/:programSlug", element: <SchoolProgramDetailsPage /> },
          { path: "files", element: <SchoolFilesPage /> },
          { path: "announcements", element: <SchoolAnnouncementsPage /> },
          { path: "announcements/:announcementId", element: <AnnouncementDetailsPage /> },
          { path: "services", element: <SchoolServicesPage /> },
          { path: "contact", element: <SchoolContactPage /> }
        ]
      }
    ]
  },
  {
    path: "/app",
    element: <ProtectedAppLayout />,
    children: [
      { index: true, element: <DashboardPage /> },
      { path: "schools", element: <SchoolsPage /> },
      { path: "schools/:schoolId", element: <SchoolDetailsPage /> },
      { path: "programs", element: <ProgramsPage /> },
      { path: "users", element: <UsersPage /> },
      { path: "audit-logs", element: <AuditLogsPage /> },
      { path: "announcements", element: <AnnouncementsPage /> },
      { path: "announcements/:announcementId", element: <AnnouncementDetailsPage /> },
      { path: "students", element: <StudentsPage /> },
      { path: "students/:studentId", element: <StudentDetailsPage /> },
      { path: "students/:studentId/edit", element: <StudentEditPage /> },
      { path: "iep-plans", element: <IepPlansPage /> },
      { path: "iep-plans/create", element: <IepPlanCreatePage /> },
      { path: "iep-plans/schools/:schoolId/programs", element: <SupervisorIepSchoolProgramsPage /> },
      { path: "iep-plans/schools/:schoolId/programs/:programId/classes", element: <SupervisorIepProgramClassesPage /> },
      {
        path: "iep-plans/schools/:schoolId/programs/:programId/classes/:classKey/teachers",
        element: <SupervisorIepClassTeachersPage />
      },
      {
        path: "iep-plans/schools/:schoolId/programs/:programId/classes/:classKey/teachers/:teacherId/plans",
        element: <SupervisorIepTeacherStudentsPage />
      },
      { path: "iep-plans/:planId", element: <IepPlanDetailsPage /> },
      { path: "iep-plans/:planId/edit", element: <IepPlanEditPage /> },
      { path: "messages", element: <MessagesPage /> },
      { path: "messages/:threadKey", element: <MessagesPage /> },
      { path: "notifications", element: <NotificationsPage /> },
      { path: "reports", element: <ReportsPage /> },
      { path: "files", element: <FilesPage /> }
    ]
  },
  {
    path: "*",
    element: <NotFoundRedirect />
  }
]);
