import { apiClient } from "./api";

export type SchoolUserOption = {
  id: number;
  full_name: string;
  email: string | null;
};

export type SchoolItem = {
  id: number;
  name: string;
  school_code: string;
  slug?: string | null;
  official_code: string;
  stage: string | null;
  program_type: string | null;
  gender?: string | null;
  city: string | null;
  address: string | null;
  location_lat: number | null;
  location_lng: number | null;
  principal_id: number | null;
  supervisor_id: number | null;
  status: "active" | "inactive";
  teachers_count: number;
  students_count: number;
  principal: SchoolUserOption | null;
  supervisor: SchoolUserOption | null;
};

export type SchoolFormPayload = {
  name: string;
  stage: string;
  program_type: string;
  location_lat: string | number | null;
  location_lng: string | number | null;
  principal_id: number | null;
  supervisor_id: number | null;
  status?: "active" | "inactive";
};

export type PaginatedMeta = {
  page: number;
  per_page: number;
  total: number;
  last_page: number;
};

export type SchoolListFilters = {
  search?: string;
  name?: string;
  stage?: string;
  gender?: string;
  page?: number;
  perPage?: number;
};

export const schoolService = {
  list: async (filters: SchoolListFilters = {}) => {
    const params = new URLSearchParams();

    if (filters.search) {
      params.set("filter[search]", filters.search);
    }

    if (filters.name) {
      params.set("filter[name]", filters.name);
    }

    if (filters.stage) {
      params.set("filter[stage]", filters.stage);
    }

    if (filters.gender) {
      params.set("filter[gender]", filters.gender);
    }

    params.set("page", String(filters.page ?? 1));
    params.set("per_page", String(filters.perPage ?? 10));

    const queryString = params.toString();
    const response = await apiClient.get<SchoolItem[]>(
      `/api/v1/schools${queryString ? `?${queryString}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? filters.perPage ?? 10),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      } satisfies PaginatedMeta
    };
  },
  create: async (payload: SchoolFormPayload) => {
    const response = await apiClient.post<SchoolItem>("/api/v1/schools", payload);
    return response.data;
  },
  details: async (id: string | number) => {
    const response = await apiClient.get<SchoolItem>(`/api/v1/schools/${id}`);
    return response.data;
  },
  update: async (id: string | number, payload: SchoolFormPayload) => {
    const response = await apiClient.put<SchoolItem>(`/api/v1/schools/${id}`, payload);
    return response.data;
  },
  deactivate: async (id: string | number) => {
    const response = await apiClient.delete<SchoolItem>(`/api/v1/schools/${id}`);
    return response.data;
  },
  stats: async (id: string | number) => {
    const response = await apiClient.get<{
      students_count: number;
      teachers_count: number;
      iep_plans_count: number;
    }>(`/api/v1/schools/${id}/stats`);

    return response.data;
  }
};
