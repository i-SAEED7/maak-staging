import { forwardRef, type ButtonHTMLAttributes } from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "../../lib/utils";

const buttonVariants = cva(
  [
    "inline-flex items-center justify-center gap-2 font-bold",
    "rounded-2xl border-0 cursor-pointer",
    "transition-all duration-200 ease-out",
    "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2",
    "disabled:opacity-50 disabled:pointer-events-none",
    "active:scale-[0.97]",
  ],
  {
    variants: {
      variant: {
        primary: [
          "bg-gradient-to-l from-[var(--color-primary)] to-[var(--color-accent-strong)]",
          "text-white shadow-lg shadow-[rgba(56,41,114,0.2)]",
          "hover:shadow-xl hover:shadow-[rgba(56,41,114,0.28)] hover:-translate-y-0.5",
          "focus-visible:ring-[var(--color-accent-strong)]",
        ],
        secondary: [
          "border border-[rgba(32,140,170,0.16)]",
          "bg-white/50 backdrop-blur-sm",
          "text-[var(--color-accent-strong)]",
          "shadow-md shadow-[rgba(56,41,114,0.08)]",
          "hover:bg-white/70 hover:-translate-y-0.5",
          "focus-visible:ring-[var(--color-accent)]",
        ],
        ghost: [
          "bg-[rgba(112,48,160,0.08)]",
          "text-[var(--color-secondary)]",
          "hover:bg-[rgba(112,48,160,0.14)]",
          "focus-visible:ring-[var(--color-secondary)]",
        ],
        danger: [
          "bg-gradient-to-l from-rose-600 to-red-500",
          "text-white shadow-lg shadow-rose-500/20",
          "hover:shadow-xl hover:-translate-y-0.5",
          "focus-visible:ring-rose-500",
        ],
        link: [
          "bg-transparent underline underline-offset-4",
          "text-[var(--color-accent-strong)] font-bold",
          "hover:text-[var(--color-primary)]",
          "p-0 h-auto",
        ],
      },
      size: {
        sm: "text-sm px-3 py-2 min-h-[36px] rounded-xl",
        md: "text-base px-4 py-3 min-h-[44px]",
        lg: "text-lg px-6 py-4 min-h-[52px] rounded-2xl",
        icon: "w-10 h-10 p-0 rounded-full",
      },
    },
    defaultVariants: {
      variant: "primary",
      size: "md",
    },
  }
);

export type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> &
  VariantProps<typeof buttonVariants> & {
    loading?: boolean;
  };

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, loading, children, disabled, ...props }, ref) => {
    return (
      <button
        ref={ref}
        className={cn(buttonVariants({ variant, size }), className)}
        disabled={disabled || loading}
        {...props}
      >
        {loading ? (
          <span className="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" />
        ) : null}
        {children}
      </button>
    );
  }
);

Button.displayName = "Button";

export { buttonVariants };
