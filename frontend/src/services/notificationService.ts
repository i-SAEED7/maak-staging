import { apiClient } from "./api";

export type NotificationItem = {
  id: number;
  type: string;
  title: string;
  body: string;
  created_by_user_id: number | null;
  creator?: {
    id: number;
    full_name: string;
    email: string | null;
    role: string | null;
  } | null;
  recipient?: {
    id: number;
    full_name: string;
    email: string | null;
    role: string | null;
  } | null;
  school?: {
    id: number;
    name_ar: string;
  } | null;
  school_name?: string | null;
  teacher_name?: string | null;
  entity_type?: string | null;
  entity_id?: number | null;
  thread_key?: string | null;
  action_url?: string | null;
  action_label?: string | null;
  sent_at: string | null;
  read_at: string | null;
};

export const notificationService = {
  list: async () => {
    const response = await apiClient.get<NotificationItem[]>("/api/v1/notifications");
    return response.data;
  },
  markRead: async (id: number) => {
    await apiClient.post(`/api/v1/notifications/${id}/read`, {});
  },
  markAllRead: async () => {
    await apiClient.post("/api/v1/notifications/read-all", {});
  }
};
