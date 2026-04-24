import { apiClient } from "./api";

export type DashboardSummary = {
  schools_count: number;
  students_count: number;
  programs_count: number;
  teachers_count: number;
  supervisors_count: number;
  principals_count: number;
  context_school_name?: string | null;
  context_school_code?: string | null;
  context_principal_name?: string | null;
  active_filters?: {
    school_id?: string | number | null;
    program_type?: string | null;
  };
  map_placeholder: {
    enabled: boolean;
    message: string;
  };
};

export const dashboardService = {
  summary: async (filters: { school_id?: string; program_type?: string } = {}) => {
    const params = new URLSearchParams();

    if (filters.school_id) {
      params.set("school_id", filters.school_id);
    }

    if (filters.program_type) {
      params.set("program_type", filters.program_type);
    }

    const response = await apiClient.get<DashboardSummary>(
      `/api/v1/dashboard/summary${params.toString() ? `?${params.toString()}` : ""}`
    );
    return response.data;
  }
};
