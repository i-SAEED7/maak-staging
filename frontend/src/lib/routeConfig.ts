export const routeConfig = {
  public: ["/login", "/forgot-password", "/unauthorized"],
  authenticated: ["/notifications", "/messages"],
  super_admin: ["/admin", "/admin/schools", "/admin/users", "/admin/reports", "/admin/audit-logs", "/admin/settings"],
  admin: ["/admin", "/admin/schools", "/admin/users", "/admin/reports"],
  supervisor: ["/supervisor", "/supervisor/schools", "/supervisor/visits", "/supervisor/iep-plans", "/supervisor/reports"],
  principal: ["/principal", "/principal/users", "/principal/students", "/principal/iep-plans", "/principal/reports"],
  teacher: ["/teacher", "/teacher/students", "/teacher/iep-plans", "/teacher/student-reports", "/teacher/portfolio", "/teacher/messages"],
  parent: ["/parent", "/parent/children", "/parent/reports", "/parent/messages", "/parent/notifications"],
};
