import { useOutletContext } from "react-router-dom";
import type { AuthUser } from "../services/authService";
import type { SchoolItem } from "../services/schoolService";

export type SchoolSiteStats = {
  students_count: number;
  teachers_count: number;
  iep_plans_count: number;
};

export type SchoolSiteAccessibleSchool = {
  id: number;
  name_ar: string;
  school_code?: string | null;
  slug?: string | null;
  stage?: string | null;
  program_type?: string | null;
};

export type SchoolSiteContextValue = {
  school: SchoolItem;
  schoolStats: SchoolSiteStats | null;
  currentSchoolId: number;
  currentSchoolSlug: string;
  schoolPath: string;
  user: AuthUser;
  permissions: string[];
  role: string;
  accessibleSchools: SchoolSiteAccessibleSchool[];
  canAccessFiles: boolean;
  canUploadFiles: boolean;
  canDeleteFiles: boolean;
  canManageAnnouncements: boolean;
  canViewAnnouncements: boolean;
  canSendMessages: boolean;
  canViewReports: boolean;
  canViewStudents: boolean;
  canViewPlans: boolean;
  isSupervisor: boolean;
  isPrincipal: boolean;
  isTeacher: boolean;
  isParent: boolean;
};

export function buildAccessibleSchools(user: AuthUser | null | undefined): SchoolSiteAccessibleSchool[] {
  if (!user) {
    return [];
  }

  const schools: SchoolSiteAccessibleSchool[] = [];

  if (user.school && user.school_id) {
    schools.push({
      id: user.school_id,
      name_ar: user.school.name_ar,
      school_code: user.school.school_code,
      slug: user.school.slug,
      stage: user.school.stage
    });
  }

  for (const school of user.assigned_schools ?? []) {
    schools.push({
      id: school.id,
      name_ar: school.name_ar,
      school_code: school.school_code,
      slug: school.slug,
      stage: school.stage,
      program_type: school.program_type
    });
  }

  return schools.filter((school, index, list) => list.findIndex((item) => item.id === school.id) === index);
}

export function translateRole(role: string | undefined) {
  const labels: Record<string, string> = {
    teacher: "المعلم",
    principal: "مدير المدرسة",
    parent: "ولي الأمر",
    supervisor: "المشرف التربوي",
    admin: "الإدارة المركزية",
    super_admin: "السوبر أدمن"
  };

  if (!role) {
    return "مستخدم النظام";
  }

  return labels[role] ?? role;
}

export function resolveSchoolPath(school: { id: number; slug?: string | null }) {
  return `/schools/${school.slug ?? school.id}`;
}

export function useSchoolSite() {
  return useOutletContext<SchoolSiteContextValue>();
}
