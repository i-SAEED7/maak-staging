import { type ReactNode } from "react";
import { motion, type Variants } from "framer-motion";
import { cn } from "../../lib/utils";

/* ---------- Page transition wrapper ---------- */

const pageVariants: Variants = {
  initial: { opacity: 0, y: 10 },
  animate: { opacity: 1, y: 0 },
  exit: { opacity: 0, y: -6 },
};

type PageTransitionProps = {
  children: ReactNode;
  className?: string;
};

export function PageTransition({ children, className }: PageTransitionProps) {
  return (
    <motion.div
      variants={pageVariants}
      initial="initial"
      animate="animate"
      exit="exit"
      transition={{ duration: 0.3, ease: "easeOut" }}
      className={cn("grid gap-5", className)}
    >
      {children}
    </motion.div>
  );
}

/* ---------- Stagger children ---------- */

const containerVariants: Variants = {
  initial: {},
  animate: {
    transition: { staggerChildren: 0.06 },
  },
};

const childVariants: Variants = {
  initial: { opacity: 0, y: 14 },
  animate: { opacity: 1, y: 0, transition: { duration: 0.35, ease: "easeOut" } },
};

type StaggerListProps = {
  children: ReactNode;
  className?: string;
};

export function StaggerList({ children, className }: StaggerListProps) {
  return (
    <motion.div
      variants={containerVariants}
      initial="initial"
      animate="animate"
      className={cn("grid gap-4", className)}
    >
      {children}
    </motion.div>
  );
}

type StaggerItemProps = {
  children: ReactNode;
  className?: string;
};

export function StaggerItem({ children, className }: StaggerItemProps) {
  return (
    <motion.div variants={childVariants} className={className}>
      {children}
    </motion.div>
  );
}

/* ---------- Fade In ---------- */

type FadeInProps = {
  children: ReactNode;
  delay?: number;
  direction?: "up" | "down" | "left" | "right";
  className?: string;
};

const directionMap = {
  up: { y: 16 },
  down: { y: -16 },
  left: { x: 16 },
  right: { x: -16 },
} as const;

export function FadeIn({
  children,
  delay = 0,
  direction = "up",
  className,
}: FadeInProps) {
  return (
    <motion.div
      initial={{ opacity: 0, ...directionMap[direction] }}
      animate={{ opacity: 1, x: 0, y: 0 }}
      transition={{ duration: 0.4, delay, ease: "easeOut" }}
      className={className}
    >
      {children}
    </motion.div>
  );
}

/* ---------- Skeleton loader ---------- */

type SkeletonProps = {
  className?: string;
};

export function Skeleton({ className }: SkeletonProps) {
  return (
    <div
      className={cn(
        "rounded-2xl bg-gradient-to-l from-gray-200 via-gray-100 to-gray-200",
        "bg-[length:200%_100%] animate-shimmer",
        className
      )}
    />
  );
}

/* ---------- Empty state ---------- */

type EmptyStateProps = {
  icon?: ReactNode;
  title: string;
  description?: string;
  action?: ReactNode;
  className?: string;
};

export function EmptyState({ icon, title, description, action, className }: EmptyStateProps) {
  return (
    <FadeIn className={cn("grid place-items-center gap-4 py-12 text-center", className)}>
      {icon ? (
        <span className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[rgba(32,140,170,0.1)] text-[var(--color-accent-strong)]">
          {icon}
        </span>
      ) : null}
      <h3 className="text-lg font-bold text-[var(--color-title)] m-0">{title}</h3>
      {description ? (
        <p className="text-sm text-[var(--color-body)] max-w-md m-0">{description}</p>
      ) : null}
      {action}
    </FadeIn>
  );
}
