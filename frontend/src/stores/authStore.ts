import { create } from "zustand";
import { authService, type AuthUser } from "../services/authService";

const TOKEN_KEY = "maak_frontend_token";
const SCHOOL_KEY = "maak_frontend_school_id";
const USER_ROLE_KEY = "maak_frontend_user_role";

type AuthStatus = "idle" | "loading" | "ready";

type AuthStore = {
  token: string | null;
  schoolId: string;
  user: AuthUser | null;
  permissions: string[];
  status: AuthStatus;
  initialize: () => Promise<void>;
  login: (payload: { identifier: string; password: string }) => Promise<void>;
  loginCentral: (payload: { identifier: string; password: string }) => Promise<void>;
  loginSchool: (payload: { identifier: string; password: string; schoolCode: string }) => Promise<void>;
  logout: () => Promise<void>;
  setSchoolId: (value: string) => void;
};

function persistSchoolId(value: string) {
  window.localStorage.setItem(SCHOOL_KEY, value);
}

function persistUserRole(value: string | null) {
  if (!value) {
    window.localStorage.removeItem(USER_ROLE_KEY);
    return;
  }

  window.localStorage.setItem(USER_ROLE_KEY, value);
}

function clearSession() {
  window.localStorage.removeItem(TOKEN_KEY);
  window.localStorage.removeItem(SCHOOL_KEY);
  window.localStorage.removeItem(USER_ROLE_KEY);
}

function resetAuthState(set: (partial: Partial<AuthStore>) => void) {
  clearSession();
  set({
    token: null,
    user: null,
    permissions: [],
    schoolId: "",
    status: "ready"
  });
}

function resolveSchoolId(user: AuthUser, currentValue?: string) {
  if (user.role === "super_admin") {
    return "";
  }

  if (user.role === "supervisor") {
    const assignedSchools = user.assigned_schools ?? [];
    const currentMatch = currentValue
      ? assignedSchools.find((school) => String(school.id) === currentValue)
      : null;

    if (currentMatch) {
      return String(currentMatch.id);
    }

    if (assignedSchools.length > 0) {
      return String(assignedSchools[0].id);
    }

    return user.school_id ? String(user.school_id) : "";
  }

  return currentValue || String(user.school_id ?? "");
}

export const useAuthStore = create<AuthStore>((set, get) => ({
  token: window.localStorage.getItem(TOKEN_KEY),
  schoolId: window.localStorage.getItem(SCHOOL_KEY) ?? "",
  user: null,
  permissions: [],
  status: "idle",
  initialize: async () => {
    if (get().status === "loading") {
      return;
    }

    const token = get().token ?? window.localStorage.getItem(TOKEN_KEY);

    if (!token) {
      set({ status: "ready", user: null, permissions: [], token: null });
      return;
    }

    set({ status: "loading", token });

    try {
      const payload = await authService.me();
      const resolvedSchoolId = resolveSchoolId(payload.user, get().schoolId);

      persistSchoolId(resolvedSchoolId);
      persistUserRole(payload.user.role);

      set({
        user: payload.user,
        permissions: payload.permissions,
        token,
        schoolId: resolvedSchoolId,
        status: "ready"
      });
    } catch {
      clearSession();
      set({
        token: null,
        user: null,
        permissions: [],
        status: "ready"
      });
    }
  },
  login: async ({ identifier, password }) => {
    set({ status: "loading" });
    try {
      const payload = await authService.login(identifier, password);
      const resolvedSchoolId = resolveSchoolId(payload.user, get().schoolId);

      window.localStorage.setItem(TOKEN_KEY, payload.token);
      persistSchoolId(resolvedSchoolId);
      persistUserRole(payload.user.role);

      set({
        token: payload.token,
        schoolId: resolvedSchoolId,
        user: payload.user,
        permissions: payload.permissions,
        status: "ready"
      });
    } catch (error) {
      resetAuthState(set);
      throw error;
    }
  },
  loginCentral: async ({ identifier, password }) => {
    set({ status: "loading" });
    try {
      const payload = await authService.loginCentral(identifier, password);
      const resolvedSchoolId = resolveSchoolId(payload.user, "");

      window.localStorage.setItem(TOKEN_KEY, payload.token);
      persistSchoolId(resolvedSchoolId);
      persistUserRole(payload.user.role);

      set({
        token: payload.token,
        schoolId: resolvedSchoolId,
        user: payload.user,
        permissions: payload.permissions,
        status: "ready"
      });
    } catch (error) {
      resetAuthState(set);
      throw error;
    }
  },
  loginSchool: async ({ identifier, password, schoolCode }) => {
    set({ status: "loading" });
    try {
      const payload = await authService.loginSchool(identifier, password, schoolCode);
      const resolvedSchoolId = resolveSchoolId(payload.user, "");

      window.localStorage.setItem(TOKEN_KEY, payload.token);
      persistSchoolId(resolvedSchoolId);
      persistUserRole(payload.user.role);

      set({
        token: payload.token,
        schoolId: resolvedSchoolId,
        user: payload.user,
        permissions: payload.permissions,
        status: "ready"
      });
    } catch (error) {
      resetAuthState(set);
      throw error;
    }
  },
  logout: async () => {
    try {
      if (get().token) {
        await authService.logout();
      }
    } catch {
      // Keep local logout resilient even if the backend token is already invalid.
    }

    clearSession();
    set({
      token: null,
      user: null,
      permissions: [],
      schoolId: "",
      status: "ready"
    });
  },
  setSchoolId: (value) => {
    persistSchoolId(value);
    set({ schoolId: value });
  }
}));
