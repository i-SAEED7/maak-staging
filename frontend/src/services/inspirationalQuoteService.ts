import { apiClient } from "./api";

export type InspirationalQuote = {
  id: number;
  uuid?: string;
  title: string;
  body: string;
  is_active: boolean;
  sort_order: number;
  created_at?: string | null;
  updated_at?: string | null;
};

export const inspirationalQuoteService = {
  publicList: async () => {
    const response = await apiClient.get<InspirationalQuote[]>("/api/portal/inspirational-quotes", false);
    return response.data;
  },
  list: async () => {
    const response = await apiClient.get<InspirationalQuote[]>("/api/v1/inspirational-quotes");
    return response.data;
  },
  create: async (payload: {
    title: string;
    body: string;
    is_active: boolean;
    sort_order: number;
  }) => {
    const response = await apiClient.post<InspirationalQuote>("/api/v1/inspirational-quotes", payload);
    return response.data;
  },
  update: async (
    id: number | string,
    payload: Partial<Pick<InspirationalQuote, "title" | "body" | "is_active" | "sort_order">>
  ) => {
    const response = await apiClient.put<InspirationalQuote>(`/api/v1/inspirational-quotes/${id}`, payload);
    return response.data;
  },
  deactivate: async (id: number | string) => {
    const response = await apiClient.delete<InspirationalQuote>(`/api/v1/inspirational-quotes/${id}`);
    return response.data;
  }
};
