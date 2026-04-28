import { apiClient } from "./api";

export type FileItem = {
  id: number;
  school_id?: number | null;
  original_name: string;
  mime_type?: string | null;
  extension?: string | null;
  category: string;
  visibility: string;
  is_sensitive: boolean;
  size_bytes: number;
  uploaded_at: string | null;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
  } | null;
  uploader?: {
    id: number;
    full_name: string;
    email: string | null;
  } | null;
};

export type FileListResponse = {
  data: FileItem[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export const fileService = {
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<FileItem[]>(
      `/api/v1/files${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 15),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    } satisfies FileListResponse;
  },
  upload: async (formData: FormData) => {
    const response = await apiClient.upload<FileItem>("/api/v1/files", formData);
    return response.data;
  },
  temporaryLink: async (id: number, expiresInMinutes: number) => {
    const response = await apiClient.post<{
      file: FileItem;
      temporary_link: { url: string; preview_url?: string; expires_at: string };
    }>(`/api/v1/files/${id}/temporary-link`, {
      expires_in_minutes: expiresInMinutes
    });

    return response.data;
  },
  delete: async (id: number) => {
    await apiClient.delete(`/api/v1/files/${id}`);
  }
};
