import { apiClient } from "./api";

export type AnnouncementItem = {
  id: number;
  title: string;
  body: string;
  target_audience: "teacher" | "principal" | "supervisor" | "parent" | "general";
  is_all_schools: boolean;
  status: "active" | "inactive";
  published_at: string | null;
  school?: {
    id: number;
    name_ar: string;
    school_code?: string | null;
  } | null;
  creator?: {
    id: number;
    full_name: string;
    role: string | null;
  } | null;
  views?: Array<{
    id: number;
    viewer_name?: string | null;
    viewer_role?: string | null;
    viewed_at?: string | null;
  }>;
};

export type AnnouncementPayload = {
  title: string;
  body: string;
  target_audience: AnnouncementItem["target_audience"];
  is_all_schools?: boolean;
  school_id?: number | null;
  status?: "active" | "inactive";
};

export const announcementService = {
  list: async () => {
    const response = await apiClient.get<AnnouncementItem[]>("/api/v1/announcements");
    return response.data;
  },
  details: async (id: number | string) => {
    const response = await apiClient.get<AnnouncementItem>(`/api/v1/announcements/${id}`);
    return response.data;
  },
  create: async (payload: AnnouncementPayload) => {
    const response = await apiClient.post<AnnouncementItem>("/api/v1/announcements", payload);
    return response.data;
  },
  update: async (id: number | string, payload: Partial<AnnouncementPayload>) => {
    const response = await apiClient.put<AnnouncementItem>(`/api/v1/announcements/${id}`, payload);
    return response.data;
  },
  delete: async (id: number | string) => {
    await apiClient.delete(`/api/v1/announcements/${id}`);
  }
};
