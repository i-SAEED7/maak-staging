export type Role =
  | "super_admin"
  | "admin"
  | "supervisor"
  | "principal"
  | "teacher"
  | "parent";

export type SchoolStatus = "active" | "inactive";

export type IepPlanStatus =
  | "draft"
  | "pending_principal_review"
  | "pending_supervisor_review"
  | "approved"
  | "rejected"
  | "archived";
