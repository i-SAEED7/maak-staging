import { apiClient } from "./api";

export type AuthUser = {
  id: number;
  full_name: string;
  username?: string | null;
  email: string | null;
  role: string;
  is_central?: boolean;
  school_id: number | null;
  school?: {
    id: number;
    name_ar: string;
    school_code?: string | null;
    slug?: string | null;
    stage?: string | null;
    status?: string | null;
  } | null;
  assigned_schools?: Array<{
    id: number;
    name_ar: string;
    school_code?: string | null;
    slug?: string | null;
    official_code?: string | null;
    stage?: string | null;
    program_type?: string | null;
    status?: string | null;
  }>;
};

export type LoginResponse = {
  token: string;
  user: AuthUser;
  permissions: string[];
};

export type MeResponse = {
  user: AuthUser;
  permissions: string[];
};

export const authService = {
  login: async (identifier: string, password: string) => {
    const response = await apiClient.post<LoginResponse>(
      "/api/login",
      { identifier, password },
      false
    );

    return response.data;
  },
  forgotPassword: async (email: string) => {
    const response = await apiClient.post<null>("/api/forgot-password", { email }, false);
    return response.message;
  },
  resetPassword: async (payload: {
    email: string;
    token: string;
    password: string;
    password_confirmation: string;
  }) => {
    const response = await apiClient.post<null>("/api/reset-password", payload, false);
    return response.message;
  },
  me: async () => {
    const response = await apiClient.get<MeResponse>("/api/v1/auth/me");
    return response.data;
  },
  logout: async () => {
    await apiClient.post("/api/v1/auth/logout", {});
  }
};
