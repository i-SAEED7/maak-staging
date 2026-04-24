import { apiClient } from "./api";

export type UserOption = {
  id: number;
  uuid: string;
  full_name: string;
  email: string | null;
  role: string;
  role_display_name_ar?: string | null;
  phone?: string | null;
  school_id?: number | null;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
    status?: string | null;
  } | null;
  assigned_schools?: Array<{
    id: number;
    name_ar: string;
    official_code?: string | null;
    stage?: string | null;
    program_type?: string | null;
    status?: string | null;
  }>;
  status: string;
};

export type UserListResponse = {
  data: UserOption[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export type RolePermission = {
  id: number;
  key: string;
  display_name_ar: string;
  module: string;
};

export type RoleSummary = {
  id: number;
  name: string;
  display_name_ar: string;
  description: string | null;
  permissions: RolePermission[];
};

export type PermissionModule = {
  module: string;
  permissions: Array<{
    id: number;
    key: string;
    display_name_ar: string;
  }>;
};

export type RoleUserPermissionTarget = {
  id: number;
  full_name: string;
  email: string | null;
  status: string;
  school?: {
    id: number;
    name_ar: string;
  } | null;
  assigned_schools?: Array<{
    id: number;
    name_ar: string;
  }>;
  effective_permission_keys: string[];
  direct_permission_overrides: {
    allow: string[];
    deny: string[];
  };
};

export type UserFormPayload = {
  full_name: string;
  email: string | null;
  phone: string | null;
  role: string;
  school_id: number | null;
  school_ids?: number[];
  password?: string;
  must_change_password?: boolean;
};

export const userService = {
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<UserOption[]>(
      `/api/v1/users${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 15),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    } satisfies UserListResponse;
  },
  listByRole: async (role: "principal" | "supervisor") => {
    const params = new URLSearchParams();
    params.set("filter[role]", role);
    params.set("filter[status]", "active");
    params.set("per_page", "100");

    const response = await apiClient.get<UserOption[]>(`/api/v1/users?${params.toString()}`);
    return response.data;
  },
  create: async (payload: UserFormPayload) => {
    const response = await apiClient.post<UserOption>("/api/v1/users", payload);
    return response.data;
  },
  update: async (id: number, payload: UserFormPayload) => {
    const response = await apiClient.put<UserOption>(`/api/v1/users/${id}`, payload);
    return response.data;
  },
  changeStatus: async (id: number, status: "active" | "inactive") => {
    const response = await apiClient.patch<UserOption>(`/api/v1/users/${id}/status`, { status });
    return response.data;
  },
  deactivate: async (id: number) => {
    const response = await apiClient.delete<UserOption>(`/api/v1/users/${id}`);
    return response.data;
  },
  listRoles: async () => {
    const response = await apiClient.get<RoleSummary[]>("/api/v1/access-control/roles");
    return response.data;
  },
  listPermissions: async () => {
    const response = await apiClient.get<PermissionModule[]>("/api/v1/access-control/permissions");
    return response.data;
  },
  listRoleUsers: async (roleId: number) => {
    const response = await apiClient.get<RoleUserPermissionTarget[]>(`/api/v1/access-control/roles/${roleId}/users`);
    return response.data;
  },
  updateRolePermissions: async (roleId: number, permissionKeys: string[]) => {
    const response = await apiClient.put<RoleSummary>(`/api/v1/access-control/roles/${roleId}/permissions`, {
      permission_keys: permissionKeys
    });
    return response.data;
  },
  updateUserPermissions: async (
    roleId: number,
    payload: {
      permission_keys: string[];
      user_ids?: number[];
      apply_to_all?: boolean;
    }
  ) => {
    const response = await apiClient.put<RoleUserPermissionTarget[]>(
      `/api/v1/access-control/roles/${roleId}/user-permissions`,
      payload
    );
    return response.data;
  }
};
