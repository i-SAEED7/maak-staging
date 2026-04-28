// Placeholder route guard helpers.

export function canAccessRoute(
  role: string | null,
  path: string,
  routeMap: Record<string, string[]> = {},
) {
  if (path === "/login" || path === "/register" || path === "/reset-password" || path === "/unauthorized") {
    return true;
  }

  if (!role) {
    return false;
  }

  const allowed = [...(routeMap.authenticated ?? []), ...(routeMap[role] ?? [])];

  return allowed.some((routePath) => path.startsWith(routePath));
}
