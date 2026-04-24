import { apiClient } from "./api";

export type AuditLogItem = {
  id: number;
  action: string;
  target_type: string | null;
  target_id: number | null;
  method: string | null;
  endpoint: string | null;
  created_at: string;
  actor?: {
    id: number;
    full_name: string;
    email: string | null;
    role?: string | null;
  } | null;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
  } | null;
};

export type AuditLogListResponse = {
  data: AuditLogItem[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export const auditLogService = {
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<AuditLogItem[]>(
      `/api/v1/audit-logs${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 20),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    } satisfies AuditLogListResponse;
  }
};
