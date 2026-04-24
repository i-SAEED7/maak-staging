import { apiClient } from "./api";

export type PortalProgram = {
  id: number;
  code: string;
  name_ar: string;
  description?: string | null;
};

export type PortalSchool = {
  id: number;
  name_ar: string;
  school_code?: string | null;
  slug?: string | null;
  official_code: string | null;
  stage: string | null;
  program_type: string | null;
  gender: string | null;
  city: string | null;
  address: string | null;
  location_lat: number | null;
  location_lng: number | null;
};

export type PortalStatistics = {
  schools_count: number;
  programs_count: number;
  students_count: number;
  teachers_count: number;
  program_breakdown: Array<{
    program_type: string | null;
    schools_count: number;
  }>;
};

export const portalService = {
  programs: async () => {
    const response = await apiClient.get<PortalProgram[]>("/api/portal/programs", false);
    return response.data;
  },
  schools: async () => {
    const response = await apiClient.get<PortalSchool[]>("/api/portal/schools", false);
    return response.data;
  },
  statistics: async () => {
    const response = await apiClient.get<PortalStatistics>("/api/portal/statistics", false);
    return response.data;
  }
};
