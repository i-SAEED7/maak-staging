import { type ReactNode } from "react";
import * as DialogPrimitive from "@radix-ui/react-dialog";
import { X } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { cn } from "../../lib/utils";

type ModalProps = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description?: string;
  children: ReactNode;
  narrow?: boolean;
  className?: string;
};

export function Modal({
  open,
  onOpenChange,
  title,
  description,
  children,
  narrow = false,
  className,
}: ModalProps) {
  return (
    <DialogPrimitive.Root open={open} onOpenChange={onOpenChange}>
      <AnimatePresence>
        {open ? (
          <DialogPrimitive.Portal forceMount>
            <DialogPrimitive.Overlay asChild>
              <motion.div
                className={cn(
                  "fixed inset-0 z-[1000]",
                  "grid place-items-center p-6",
                  "bg-[rgba(21,68,90,0.34)] backdrop-blur-md"
                )}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.2 }}
              >
                <DialogPrimitive.Content asChild>
                  <motion.div
                    className={cn(
                      "w-full max-h-[min(88vh,860px)] overflow-auto",
                      "border border-[rgba(56,41,114,0.12)]",
                      "rounded-3xl bg-[#fffdf8] backdrop-blur-2xl",
                      "shadow-2xl shadow-[rgba(56,41,114,0.2)]",
                      "p-7",
                      narrow ? "max-w-[560px]" : "max-w-[920px]",
                      className
                    )}
                    style={{ direction: "rtl" }}
                    initial={{ opacity: 0, scale: 0.95, y: 12 }}
                    animate={{ opacity: 1, scale: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.97, y: 8 }}
                    transition={{ type: "spring" as const, damping: 28, stiffness: 380 }}
                  >
                    {/* Header */}
                    <div className="flex items-start justify-between gap-4 mb-5">
                      <div className="grid gap-1.5">
                        <DialogPrimitive.Title className="text-xl font-bold text-[var(--color-title)] m-0">
                          {title}
                        </DialogPrimitive.Title>
                        {description ? (
                          <DialogPrimitive.Description className="text-sm text-[var(--color-body)] m-0">
                            {description}
                          </DialogPrimitive.Description>
                        ) : null}
                      </div>

                      <DialogPrimitive.Close asChild>
                        <button
                          className={cn(
                            "w-10 h-10 flex-shrink-0",
                            "inline-flex items-center justify-center",
                            "rounded-full border-0",
                            "bg-[rgba(21,68,90,0.08)]",
                            "text-[var(--color-title)]",
                            "cursor-pointer transition-colors",
                            "hover:bg-[rgba(21,68,90,0.14)]",
                            "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent-strong)]"
                          )}
                          aria-label="إغلاق"
                        >
                          <X size={18} />
                        </button>
                      </DialogPrimitive.Close>
                    </div>

                    {/* Body */}
                    {children}
                  </motion.div>
                </DialogPrimitive.Content>
              </motion.div>
            </DialogPrimitive.Overlay>
          </DialogPrimitive.Portal>
        ) : null}
      </AnimatePresence>
    </DialogPrimitive.Root>
  );
}
