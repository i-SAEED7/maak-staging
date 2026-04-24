export function hasPermission(
  permissions: string[] = [],
  required: string,
): boolean {
  return permissions.includes("*") || permissions.includes(required);
}

export function hasAnyPermission(
  permissions: string[] = [],
  requiredPermissions: string[] = [],
): boolean {
  return requiredPermissions.some((required) => hasPermission(permissions, required));
}
