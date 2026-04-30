import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiClient, type ApiEnvelope } from "../services/api";

/* ------------------------------------------------------------------ */
/*  Generic hooks that can be used with any API endpoint               */
/* ------------------------------------------------------------------ */

/**
 * Fetch a paginated list from any endpoint.
 * Automatically handles caching, refetching, and stale data.
 */
export function useApiList<T>(
  key: string[],
  path: string,
  params?: Record<string, string | number | boolean | undefined>,
  enabled = true
) {
  const query = params
    ? Object.entries(params)
        .filter(([, v]) => v !== undefined && v !== "")
        .map(([k, v]) => `${k}=${encodeURIComponent(String(v))}`)
        .join("&")
    : "";

  const fullPath = query ? `${path}?${query}` : path;

  return useQuery<ApiEnvelope<T>>({
    queryKey: [...key, params],
    queryFn: () => apiClient.get<T>(fullPath),
    enabled,
  });
}

/**
 * Fetch a single resource by ID.
 */
export function useApiDetail<T>(
  key: string[],
  path: string,
  enabled = true
) {
  return useQuery<ApiEnvelope<T>>({
    queryKey: key,
    queryFn: () => apiClient.get<T>(path),
    enabled,
  });
}

/**
 * Create a resource (POST) with automatic cache invalidation.
 */
export function useApiCreate<TInput, TOutput = unknown>(
  path: string,
  invalidateKeys: string[][]
) {
  const queryClient = useQueryClient();

  return useMutation<ApiEnvelope<TOutput>, Error, TInput>({
    mutationFn: (data: TInput) => apiClient.post<TOutput>(path, data),
    onSuccess: () => {
      invalidateKeys.forEach((key) => {
        queryClient.invalidateQueries({ queryKey: key });
      });
    },
  });
}

/**
 * Update a resource (PUT) with automatic cache invalidation.
 */
export function useApiUpdate<TInput, TOutput = unknown>(
  path: string,
  invalidateKeys: string[][]
) {
  const queryClient = useQueryClient();

  return useMutation<ApiEnvelope<TOutput>, Error, TInput>({
    mutationFn: (data: TInput) => apiClient.put<TOutput>(path, data),
    onSuccess: () => {
      invalidateKeys.forEach((key) => {
        queryClient.invalidateQueries({ queryKey: key });
      });
    },
  });
}

/**
 * Delete a resource with automatic cache invalidation.
 */
export function useApiDelete<TOutput = unknown>(
  path: string,
  invalidateKeys: string[][]
) {
  const queryClient = useQueryClient();

  return useMutation<ApiEnvelope<TOutput>, Error, void>({
    mutationFn: () => apiClient.delete<TOutput>(path),
    onSuccess: () => {
      invalidateKeys.forEach((key) => {
        queryClient.invalidateQueries({ queryKey: key });
      });
    },
  });
}
