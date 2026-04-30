import { forwardRef, type HTMLAttributes, type ReactNode } from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "../../lib/utils";

const cardVariants = cva(
  [
    "rounded-3xl border transition-all duration-200",
    "backdrop-blur-lg",
  ],
  {
    variants: {
      variant: {
        glass: [
          "border-[rgba(56,41,114,0.12)]",
          "bg-[var(--glass-bg)]",
          "shadow-[var(--shadow-soft)]",
        ],
        solid: [
          "border-[rgba(56,41,114,0.08)]",
          "bg-white",
          "shadow-lg shadow-[rgba(56,41,114,0.1)]",
        ],
        gradient: [
          "border-transparent",
          "bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-accent-strong)]",
          "text-white",
          "shadow-xl shadow-[rgba(56,41,114,0.2)]",
        ],
        outlined: [
          "border-[rgba(56,41,114,0.15)]",
          "bg-transparent",
          "shadow-none",
        ],
      },
      interactive: {
        true: [
          "cursor-pointer",
          "hover:-translate-y-1 hover:shadow-xl",
          "active:scale-[0.98]",
        ],
        false: [],
      },
      padding: {
        none: "p-0",
        sm: "p-4",
        md: "p-6",
        lg: "p-8",
      },
    },
    defaultVariants: {
      variant: "glass",
      interactive: false,
      padding: "md",
    },
  }
);

export type CardProps = HTMLAttributes<HTMLDivElement> &
  VariantProps<typeof cardVariants>;

export const Card = forwardRef<HTMLDivElement, CardProps>(
  ({ className, variant, interactive, padding, ...props }, ref) => (
    <div
      ref={ref}
      className={cn(cardVariants({ variant, interactive, padding }), className)}
      {...props}
    />
  )
);

Card.displayName = "Card";

/* ---------- Card sub-components ---------- */

export function CardHeader({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>) {
  return (
    <div className={cn("flex items-start justify-between gap-3 mb-4", className)} {...props}>
      {children}
    </div>
  );
}

export function CardTitle({
  className,
  children,
  as: Tag = "h3",
  ...props
}: HTMLAttributes<HTMLHeadingElement> & { as?: "h2" | "h3" | "h4" }) {
  return (
    <Tag className={cn("text-lg font-bold text-[var(--color-title)] m-0", className)} {...props}>
      {children}
    </Tag>
  );
}

export function CardDescription({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLParagraphElement>) {
  return (
    <p className={cn("text-sm text-[var(--color-body)] m-0", className)} {...props}>
      {children}
    </p>
  );
}

export function CardContent({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>) {
  return (
    <div className={cn("grid gap-4", className)} {...props}>
      {children}
    </div>
  );
}

export function CardFooter({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>) {
  return (
    <div className={cn("flex items-center gap-3 mt-4 pt-4 border-t border-[rgba(56,41,114,0.08)]", className)} {...props}>
      {children}
    </div>
  );
}

/* ---------- Stat Card ---------- */

type StatCardProps = {
  label: string;
  value: string | number;
  icon?: ReactNode;
  trend?: "up" | "down" | "neutral";
  className?: string;
};

export function StatCard({ label, value, icon, trend, className }: StatCardProps) {
  return (
    <Card className={cn("group", className)} padding="md">
      <div className="flex items-center gap-3 mb-2">
        {icon ? (
          <span
            className={cn(
              "inline-flex items-center justify-center w-10 h-10 rounded-xl",
              "bg-[rgba(32,140,170,0.1)] text-[var(--color-accent-strong)]",
              "transition-transform duration-200 group-hover:scale-110"
            )}
          >
            {icon}
          </span>
        ) : null}

        <span className="text-sm text-[var(--color-body)]">{label}</span>
      </div>

      <strong className="block text-2xl font-extrabold text-[var(--color-title)]">
        {value}
      </strong>
    </Card>
  );
}

export { cardVariants };
