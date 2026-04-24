import { apiClient } from "./api";

export type EducationProgramOption = {
  id: number;
  code: string;
  name_ar: string;
  is_active?: boolean;
};

export const educationProgramService = {
  list: async (options: { includeInactive?: boolean } = {}) => {
    const params = new URLSearchParams();

    if (options.includeInactive) {
      params.set("include_inactive", "1");
    }

    const response = await apiClient.get<EducationProgramOption[]>(
      `/api/v1/education-programs${params.toString() ? `?${params.toString()}` : ""}`
    );
    return response.data;
  },
  create: async (payload: { name_ar: string; code?: string | null; is_active?: boolean }) => {
    const response = await apiClient.post<EducationProgramOption>("/api/v1/education-programs", payload);
    return response.data;
  },
  update: async (
    id: number | string,
    payload: { name_ar?: string; code?: string | null; is_active?: boolean }
  ) => {
    const response = await apiClient.put<EducationProgramOption>(`/api/v1/education-programs/${id}`, payload);
    return response.data;
  },
  deactivate: async (id: number | string) => {
    const response = await apiClient.delete<EducationProgramOption>(`/api/v1/education-programs/${id}`);
    return response.data;
  }
};
