export type ApiEnvelope<T> = {
  success: boolean;
  message: string;
  data: T;
  meta?: Record<string, unknown>;
};

export class ApiError extends Error {
  payload?: unknown;

  constructor(message: string, payload?: unknown) {
    super(message);
    this.name = "ApiError";
    this.payload = payload;
  }
}

type RequestOptions = {
  method?: string;
  body?: BodyInit | null;
  headers?: HeadersInit;
  useAuth?: boolean;
};

const TOKEN_KEY = "maak_frontend_token";
const SCHOOL_KEY = "maak_frontend_school_id";
const USER_ROLE_KEY = "maak_frontend_user_role";
const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL ?? "").trim().replace(/\/$/, "");

function resolveRequestUrl(path: string) {
  if (!API_BASE_URL || /^https?:\/\//i.test(path)) {
    return path;
  }

  return `${API_BASE_URL}${path}`;
}

function readToken() {
  return window.localStorage.getItem(TOKEN_KEY);
}

function readSchoolId() {
  return window.localStorage.getItem(SCHOOL_KEY);
}

function readUserRole() {
  return window.localStorage.getItem(USER_ROLE_KEY);
}

async function request<T>(path: string, options: RequestOptions = {}): Promise<ApiEnvelope<T>> {
  const { method = "GET", body = null, headers = {}, useAuth = true } = options;
  const isFormData = body instanceof FormData;
  const token = readToken();
  const schoolId = readSchoolId();
  const userRole = readUserRole();
  const requestUrl = resolveRequestUrl(path);

  const response = await fetch(requestUrl, {
    method,
    body,
    headers: {
      Accept: "application/json",
      ...(useAuth && token ? { Authorization: `Bearer ${token}` } : {}),
      ...(schoolId && userRole !== "super_admin" ? { "X-School-Id": schoolId } : {}),
      ...(!isFormData && body ? { "Content-Type": "application/json" } : {}),
      ...headers
    }
  });

  const rawText = await response.text();
  const payload = rawText ? (JSON.parse(rawText) as ApiEnvelope<T>) : null;

  if (!response.ok || !payload) {
    const message =
      (payload && typeof payload === "object" && "message" in payload && typeof payload.message === "string"
        ? payload.message
        : "حدث خطأ أثناء التواصل مع الخادم");
    throw new ApiError(message, payload);
  }

  return payload;
}

export const apiClient = {
  get: <T>(path: string, useAuth = true) => request<T>(path, { method: "GET", useAuth }),
  post: <T>(path: string, data?: unknown, useAuth = true) =>
    request<T>(path, {
      method: "POST",
      body: data === undefined ? null : JSON.stringify(data),
      useAuth
    }),
  patch: <T>(path: string, data?: unknown) =>
    request<T>(path, {
      method: "PATCH",
      body: data === undefined ? null : JSON.stringify(data)
    }),
  put: <T>(path: string, data?: unknown) =>
    request<T>(path, {
      method: "PUT",
      body: data === undefined ? null : JSON.stringify(data)
    }),
  delete: <T>(path: string) => request<T>(path, { method: "DELETE" }),
  upload: <T>(path: string, formData: FormData) =>
    request<T>(path, {
      method: "POST",
      body: formData
    })
};

export function getErrorMessage(error: unknown) {
  if (error instanceof ApiError) {
    return error.message;
  }

  if (error instanceof Error) {
    return error.message;
  }

  return "حدث خطأ غير متوقع";
}
