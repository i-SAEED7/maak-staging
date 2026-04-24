export function buildClassKey(gradeLevel?: string | null, classroom?: string | null) {
  return encodeURIComponent(`${gradeLevel ?? "غير محدد"}||${classroom ?? "غير محدد"}`);
}

export function parseClassKey(classKey?: string) {
  const decoded = decodeURIComponent(classKey ?? "");
  const [gradeLevel = "غير محدد", classroom = "غير محدد"] = decoded.split("||");

  return {
    gradeLevel,
    classroom
  };
}

export function buildClassLabel(gradeLevel?: string | null, classroom?: string | null) {
  return `الصف: ${gradeLevel ?? "غير محدد"} | الفصل: ${classroom ?? "غير محدد"}`;
}
