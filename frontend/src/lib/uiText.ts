const ROLE_LABELS: Record<string, string> = {
  super_admin: "المشرف العام",
  admin: "الإداري العام",
  supervisor: "المشرف التربوي",
  principal: "مدير المدرسة",
  teacher: "المعلم",
  parent: "ولي الأمر"
};

const PERMISSION_MODULE_LABELS: Record<string, string> = {
  announcements: "الإعلانات",
  audit_logs: "سجل العمليات",
  files: "الملفات",
  iep: "الخطط الفردية",
  inspirational_quotes: "العبارات الملهمة",
  messages: "الرسائل",
  notifications: "الإشعارات",
  portfolios: "ملفات الإنجاز",
  reference_data: "البيانات المرجعية",
  reports: "التقارير",
  schools: "المدارس",
  student_reports: "التقارير الدورية",
  students: "الطلاب",
  supervision: "الإشراف التربوي",
  teacher_assignments: "إسناد الطلاب للمعلمين",
  teacher_portfolios: "ملف إنجاز المعلم",
  users: "المستخدمون"
};

const NOTIFICATION_TYPE_LABELS: Record<string, string> = {
  announcement: "إعلان",
  general: "إشعار عام",
  iep_plan: "خطة فردية",
  message: "رسالة",
  message_thread: "سلسلة رسائل",
  system: "إشعار نظامي"
};

const AUDIT_TARGET_LABELS: Record<string, string> = {
  Announcement: "إعلان",
  AuditLog: "سجل عملية",
  EducationProgram: "برنامج تعليمي",
  File: "ملف",
  IepPlan: "خطة فردية",
  InspirationalQuote: "عبارة ملهمة",
  Message: "رسالة",
  MessageThread: "سلسلة رسائل",
  Notification: "إشعار",
  School: "مدرسة",
  Student: "طالب",
  StudentReport: "تقرير طالب",
  SupervisorVisit: "زيارة إشرافية",
  AccountApprovalRequest: "طلب اعتماد حساب",
  User: "مستخدم"
};

export function getRoleLabel(role?: string | null) {
  if (!role) {
    return "-";
  }

  return ROLE_LABELS[role] ?? "حساب";
}

export function getPermissionModuleLabel(moduleKey: string) {
  return PERMISSION_MODULE_LABELS[moduleKey] ?? "مجموعة صلاحيات";
}

export function getNotificationTypeLabel(type?: string | null) {
  if (!type) {
    return "-";
  }

  return NOTIFICATION_TYPE_LABELS[type] ?? "إشعار";
}

export function getAuditTargetLabel(targetType?: string | null) {
  if (!targetType) {
    return "-";
  }

  const normalizedTargetType = targetType.split("\\").pop() ?? targetType;

  return AUDIT_TARGET_LABELS[normalizedTargetType] ?? "عنصر نظامي";
}
