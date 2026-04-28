import { apiClient } from "./api";

export type AccountApprovalItem = {
  id: number;
  uuid: string;
  first_name: string;
  second_name?: string | null;
  last_name: string;
  full_name: string;
  email: string;
  phone: string;
  account_type: string;
  account_type_label?: string;
  stage: string;
  school_id: number;
  status: "pending" | "approved" | string;
  created_at?: string | null;
  approved_at?: string | null;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
  } | null;
  created_user?: {
    id: number;
    full_name: string;
    email: string | null;
  } | null;
  approved_by?: {
    id: number;
    full_name: string;
    email: string | null;
  } | null;
};

export type AccountApprovalPayload = {
  first_name: string;
  second_name?: string | null;
  last_name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  phone: string;
  account_type: string;
  stage: string;
  school_id: number;
};

export const accountApprovalService = {
  register: async (payload: AccountApprovalPayload & { password: string; password_confirmation: string }) => {
    const response = await apiClient.post<AccountApprovalItem>("/api/v1/account-approval-requests", payload, false);
    return response.data;
  },
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<AccountApprovalItem[]>(
      `/api/v1/account-approvals${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 20),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    };
  },
  update: async (id: number, payload: AccountApprovalPayload) => {
    const response = await apiClient.put<AccountApprovalItem>(`/api/v1/account-approvals/${id}`, payload);
    return response.data;
  },
  approve: async (id: number) => {
    const response = await apiClient.post<AccountApprovalItem>(`/api/v1/account-approvals/${id}/approve`, {});
    return response.data;
  }
};
