import { apiClient } from "./api";

export type IepPlanSummary = {
  id: number;
  title: string;
  status: string;
  current_version_number: number | null;
  current_user_acknowledged_at?: string | null;
  school_id?: number | null;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
    program_type?: string | null;
  } | null;
  goals?: Array<unknown>;
  summary?: string | null;
  strengths?: string | null;
  needs?: string | null;
  accommodations?: string[] | null;
  start_date?: string | null;
  end_date?: string | null;
  student?: {
    id?: number;
    full_name?: string | null;
    student_number?: string | null;
    grade_level?: string | null;
    classroom?: string | null;
    education_program?: {
      id: number;
      name_ar: string;
    } | null;
    school?: {
      id: number;
      name_ar: string;
      stage?: string | null;
    } | null;
  } | null;
  teacher?: {
    id?: number;
    full_name?: string | null;
  } | null;
};

export type IepGoalPayload = {
  domain: string;
  goal_text: string;
  measurement_method?: string | null;
  baseline_value?: string | null;
  target_value?: string | null;
  due_date?: string | null;
  sort_order?: number;
};

export type IepPlanPayload = {
  title: string;
  start_date?: string | null;
  end_date?: string | null;
  summary?: string | null;
  strengths?: string | null;
  needs?: string | null;
  accommodations?: string[] | null;
  goals?: IepGoalPayload[];
};

export type IepPlanListResponse = {
  data: IepPlanSummary[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export const iepPlanService = {
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<IepPlanSummary[]>(
      `/api/v1/iep-plans${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 15),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    } satisfies IepPlanListResponse;
  },
  details: async (id: number | string) => {
    const response = await apiClient.get<IepPlanSummary>(`/api/v1/iep-plans/${id}`);
    return response.data;
  },
  create: async (
    payload: IepPlanPayload & {
      student_id: number;
      academic_year_id?: number | null;
    }
  ) => {
    const response = await apiClient.post<IepPlanSummary>("/api/v1/iep-plans", payload);
    return response.data;
  },
  update: async (id: number | string, payload: IepPlanPayload) => {
    const response = await apiClient.put<IepPlanSummary>(`/api/v1/iep-plans/${id}`, payload);
    return response.data;
  },
  submit: async (id: number | string, payload: { notes?: string } = {}) => {
    const response = await apiClient.post<IepPlanSummary>(`/api/v1/iep-plans/${id}/submit`, payload);
    return response.data;
  },
  delete: async (id: number | string) => {
    const response = await apiClient.delete<{ id: number; deleted_at: string }>(`/api/v1/iep-plans/${id}`);
    return response.data;
  },
  principalApprove: async (id: number | string, payload: { notes?: string } = {}) => {
    const response = await apiClient.post<IepPlanSummary>(`/api/v1/iep-plans/${id}/principal-approve`, payload);
    return response.data;
  },
  acknowledge: async (id: number | string, payload: { notes?: string } = {}) => {
    const response = await apiClient.post<IepPlanSummary>(`/api/v1/iep-plans/${id}/acknowledge`, payload);
    return response.data;
  }
};
