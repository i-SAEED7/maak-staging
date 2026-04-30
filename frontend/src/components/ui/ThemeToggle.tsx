import { type ReactNode, useState, useEffect, useCallback } from "react";
import { Sun, Moon } from "lucide-react";
import { cn } from "../../lib/utils";

const THEME_KEY = "maak_theme";

type Theme = "light" | "dark";

function getStoredTheme(): Theme {
  try {
    const stored = window.localStorage.getItem(THEME_KEY);
    if (stored === "dark" || stored === "light") return stored;
  } catch {
    // localStorage unavailable
  }
  return "light";
}

export function useTheme() {
  const [theme, setThemeState] = useState<Theme>(getStoredTheme);

  const setTheme = useCallback((newTheme: Theme) => {
    setThemeState(newTheme);
    try {
      window.localStorage.setItem(THEME_KEY, newTheme);
    } catch {
      // localStorage unavailable
    }

    if (newTheme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  }, []);

  const toggle = useCallback(() => {
    setTheme(theme === "dark" ? "light" : "dark");
  }, [theme, setTheme]);

  // Sync on mount
  useEffect(() => {
    if (theme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  }, [theme]);

  return { theme, setTheme, toggle, isDark: theme === "dark" };
}

/* ---------- Toggle button ---------- */

type ThemeToggleProps = {
  className?: string;
};

export function ThemeToggle({ className }: ThemeToggleProps) {
  const { isDark, toggle } = useTheme();

  return (
    <button
      type="button"
      onClick={toggle}
      className={cn(
        "w-10 h-10 rounded-full border-0 cursor-pointer",
        "inline-flex items-center justify-center",
        "bg-[rgba(56,41,114,0.08)] text-[var(--color-title)]",
        "transition-all duration-200",
        "hover:bg-[rgba(56,41,114,0.14)] hover:scale-105",
        "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent-strong)]",
        className
      )}
      aria-label={isDark ? "تفعيل الوضع الفاتح" : "تفعيل الوضع الداكن"}
    >
      {isDark ? <Sun size={18} /> : <Moon size={18} />}
    </button>
  );
}
