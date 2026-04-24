import { apiClient } from "./api";

export type StudentSummary = {
  id: number;
  full_name: string;
  school_id?: number | null;
  student_number: string | null;
  grade_level: string | null;
  classroom: string | null;
  enrollment_status: string;
  school?: {
    id: number;
    name_ar: string;
    stage?: string | null;
    status?: string | null;
  } | null;
  primary_teacher?: {
    full_name?: string | null;
  } | null;
  education_program?: {
    id: number;
    name_ar: string;
  } | null;
};

export type StudentListResponse = {
  data: StudentSummary[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export type StudentDetails = StudentSummary & {
  first_name?: string | null;
  family_name?: string | null;
  school_id?: number | null;
  education_program_id?: number | null;
  gender?: string | null;
  birth_date?: string | null;
  guardians?: Array<{
    id: number;
    parent_name: string;
    relationship: string;
    is_primary: boolean;
  }>;
  academic_year?: {
    id: number;
    name_ar: string;
  } | null;
  education_program?: {
    id: number;
    name_ar: string;
  } | null;
  disability_category?: {
    id: number;
    name_ar: string;
  } | null;
};

export const studentService = {
  list: async (filters: Record<string, string | number | undefined> = {}) => {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== "") {
        params.set(key, String(value));
      }
    });

    const response = await apiClient.get<StudentSummary[]>(
      `/api/v1/students${params.toString() ? `?${params.toString()}` : ""}`
    );

    return {
      data: response.data,
      meta: {
        page: Number(response.meta?.page ?? 1),
        per_page: Number(response.meta?.per_page ?? 15),
        total: Number(response.meta?.total ?? response.data.length),
        last_page: Number(response.meta?.last_page ?? 1)
      }
    } satisfies StudentListResponse;
  },
  details: async (id: number | string) => {
    const response = await apiClient.get<StudentDetails>(`/api/v1/students/${id}`);
    return response.data;
  },
  create: async (payload: {
    school_id?: number | null;
    education_program_id?: number | null;
    first_name: string;
    family_name: string;
    student_number?: string | null;
    gender: "male" | "female";
    grade_level?: string | null;
    classroom?: string | null;
  }) => {
    const response = await apiClient.post<StudentDetails>("/api/v1/students", payload);
    return response.data;
  },
  update: async (
    id: number | string,
    payload: {
      school_id?: number | null;
      education_program_id?: number | null;
      first_name?: string;
      family_name?: string;
      gender?: "male" | "female";
      grade_level?: string | null;
      classroom?: string | null;
    }
  ) => {
    const response = await apiClient.put<StudentDetails>(`/api/v1/students/${id}`, payload);
    return response.data;
  }
};
