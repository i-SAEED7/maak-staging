import { forwardRef, type InputHTMLAttributes, type ReactNode } from "react";
import { cn } from "../../lib/utils";

export type InputProps = InputHTMLAttributes<HTMLInputElement> & {
  label?: string;
  hint?: string;
  error?: string;
  icon?: ReactNode;
};

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ className, label, hint, error, icon, id, ...props }, ref) => {
    const inputId = id || `input-${label?.replace(/\s+/g, "-")}`;

    return (
      <div className="grid gap-2">
        {label ? (
          <label
            htmlFor={inputId}
            className="text-sm font-semibold text-[var(--color-body)]"
          >
            {label}
          </label>
        ) : null}

        <div className="relative">
          {icon ? (
            <span className="absolute top-1/2 right-3 -translate-y-1/2 text-[var(--color-body)] opacity-50 pointer-events-none">
              {icon}
            </span>
          ) : null}

          <input
            ref={ref}
            id={inputId}
            className={cn(
              "w-full min-h-[46px] px-4 py-3 rounded-2xl",
              "border border-[rgba(56,41,114,0.12)]",
              "bg-white/70 backdrop-blur-sm",
              "text-[var(--color-title)]",
              "placeholder:text-[var(--color-body)] placeholder:opacity-50",
              "transition-all duration-200",
              "focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-strong)] focus:ring-offset-1",
              "focus:border-[var(--color-accent-strong)]",
              "hover:border-[rgba(56,41,114,0.22)]",
              "disabled:opacity-50 disabled:cursor-not-allowed",
              error && "border-rose-400 focus:ring-rose-400",
              icon && "pr-10",
              className
            )}
            aria-invalid={error ? "true" : undefined}
            aria-describedby={error ? `${inputId}-error` : hint ? `${inputId}-hint` : undefined}
            dir={props.dir || "auto"}
            {...props}
          />
        </div>

        {error ? (
          <p id={`${inputId}-error`} className="text-sm text-rose-500 font-medium" role="alert">
            {error}
          </p>
        ) : hint ? (
          <p id={`${inputId}-hint`} className="text-sm text-[var(--color-body)] opacity-70">
            {hint}
          </p>
        ) : null}
      </div>
    );
  }
);

Input.displayName = "Input";
