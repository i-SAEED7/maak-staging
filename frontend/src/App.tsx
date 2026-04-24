import { useEffect } from "react";
import { RouterProvider } from "react-router-dom";
import { router } from "./router";
import { useAuthStore } from "./stores/authStore";

function isPublicAuthRoute(pathname: string) {
  return pathname === "/login"
    || pathname === "/login/school"
    || pathname === "/login/central";
}

export function App() {
  useEffect(() => {
    if (isPublicAuthRoute(window.location.pathname)) {
      return;
    }

    void useAuthStore.getState().initialize();
  }, []);

  return <RouterProvider router={router} />;
}
