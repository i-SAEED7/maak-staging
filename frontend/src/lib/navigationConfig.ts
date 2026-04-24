export const navigationConfig = {
  super_admin: [
    { label: "لوحة التحكم", path: "/admin" },
    { label: "المدارس", path: "/admin/schools" },
    { label: "المستخدمون", path: "/admin/users" },
    { label: "التقارير", path: "/admin/reports" },
    { label: "سجل العمليات", path: "/admin/audit-logs" },
    { label: "الإعدادات", path: "/admin/settings" },
  ],
  supervisor: [
    { label: "لوحة التحكم", path: "/supervisor" },
    { label: "الزيارات", path: "/supervisor/visits" },
    { label: "الخطط الفردية", path: "/supervisor/iep-plans" },
    { label: "التقارير", path: "/supervisor/reports" },
  ],
  principal: [
    { label: "لوحة التحكم", path: "/principal" },
    { label: "الطلاب", path: "/principal/students" },
    { label: "الخطط الفردية", path: "/principal/iep-plans" },
    { label: "التقارير", path: "/principal/reports" },
  ],
  teacher: [
    { label: "لوحة التحكم", path: "/teacher" },
    { label: "الطلاب", path: "/teacher/students" },
    { label: "التقارير الدورية", path: "/teacher/student-reports" },
    { label: "ملف الإنجاز", path: "/teacher/portfolio" },
    { label: "الرسائل", path: "/teacher/messages" },
  ],
  parent: [
    { label: "لوحة التحكم", path: "/parent" },
    { label: "الأبناء", path: "/parent/children" },
    { label: "التقارير", path: "/parent/reports" },
    { label: "الرسائل", path: "/parent/messages" },
    { label: "الإشعارات", path: "/parent/notifications" },
  ],
};
