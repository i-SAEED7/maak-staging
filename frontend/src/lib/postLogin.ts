import type { AuthUser } from "../services/authService";

type ResolvePostLoginOptions = {
  mode?: "auto" | "school" | "central";
};

function resolveAvailableSchools(user: AuthUser) {
  const schools: Array<{ id: number; slug?: string | null }> = [];

  if (user.school && user.school_id) {
    schools.push({
      id: user.school_id,
      slug: user.school.slug ?? null
    });
  }

  for (const school of user.assigned_schools ?? []) {
    schools.push({
      id: school.id,
      slug: school.slug ?? null
    });
  }

  return schools.filter((school, index, list) => list.findIndex((item) => item.id === school.id) === index);
}

function toSchoolPath(school: { id: number; slug?: string | null }) {
  return `/schools/${school.slug ?? school.id}`;
}

export function resolvePostLoginPath(user: AuthUser | null | undefined, options: ResolvePostLoginOptions = {}) {
  const mode = options.mode ?? "auto";

  if (!user) {
    return "/login";
  }

  const schools = resolveAvailableSchools(user);

  if (mode === "central") {
    return "/app";
  }

  if (mode === "school") {
    if (schools.length > 1) {
      return "/select-school";
    }

    if (schools.length === 1) {
      return toSchoolPath(schools[0]);
    }

    if (user.school_id) {
      return toSchoolPath({
        id: user.school_id,
        slug: user.school?.slug ?? null
      });
    }

    return "/login";
  }

  if (user.role === "super_admin" || user.role === "admin") {
    return "/app";
  }

  if (user.role === "supervisor") {
    if (schools.length > 1) {
      return "/select-school";
    }

    if (schools.length === 1) {
      return toSchoolPath(schools[0]);
    }
  }

  if (user.school_id) {
    return toSchoolPath({
      id: user.school_id,
      slug: user.school?.slug ?? null
    });
  }

  return "/app";
}
