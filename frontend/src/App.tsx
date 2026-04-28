import { useEffect } from "react";
import { RouterProvider } from "react-router-dom";
import { router } from "./router";
import { useAuthStore } from "./stores/authStore";

export function App() {
  useEffect(() => {
    void useAuthStore.getState().initialize();
  }, []);

  return <RouterProvider router={router} />;
}
