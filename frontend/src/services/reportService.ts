import { apiClient } from "./api";

export type SchoolSummaryReport = {
  school: {
    id: number;
    name_ar: string;
    official_code?: string | null;
    stage?: string | null;
    program_type?: string | null;
    gender?: string | null;
    city?: string | null;
    status?: string | null;
  };
  overview: Record<string, number>;
  leadership: {
    principal?: {
      id: number;
      full_name: string;
      email: string;
    } | null;
    supervisors?: Array<{
      id: number;
      full_name: string;
      email: string;
    }>;
  };
  breakdowns: Record<string, Record<string, number>>;
};

export type ComparisonReportRow = {
  school_id: number;
  official_code?: string | null;
  school_name: string;
  stage?: string | null;
  program_type?: string | null;
  status?: string | null;
  students_count: number;
  active_students_count: number;
  teachers_count: number;
  iep_plans_count: number;
  approved_iep_plans_count: number;
  messages_count: number;
  notifications_count: number;
  files_count?: number;
  student_teacher_ratio?: number | null;
};

export type PivotReport = {
  dimension: string;
  school_ids: number[];
  rows: Array<{
    label: string;
    value: number;
  }>;
};

export type StudentSummaryReport = {
  student: {
    id: number;
    full_name: string;
    student_number?: string | null;
    grade_level?: string | null;
    classroom?: string | null;
    enrollment_status?: string | null;
  };
  school: {
    id?: number | null;
    name_ar?: string | null;
  };
  education: {
    academic_year?: string | null;
    program?: string | null;
    disability_category?: string | null;
    primary_teacher?: string | null;
  };
  guardians: Array<{
    id: number;
    parent_user_id: number;
    parent_name?: string | null;
    relationship?: string | null;
    is_primary: boolean;
  }>;
  iep: {
    plans_count: number;
    counts_by_status: Record<string, number>;
    latest_plan?: {
      id: number;
      title: string;
      status: string;
      current_version_number?: number | null;
      updated_at?: string | null;
    } | null;
  };
  activity: {
    portfolio_items_count: number;
    student_reports_count: number;
  };
};

export const reportService = {
  schoolSummary: async (schoolId: string | number) => {
    const response = await apiClient.get<SchoolSummaryReport>(`/api/v1/reports/schools/${schoolId}/summary`);
    return response.data;
  },
  studentSummary: async (studentId: string | number) => {
    const response = await apiClient.get<StudentSummaryReport>(`/api/v1/reports/students/${studentId}/summary`);
    return response.data;
  },
  comparison: async (schoolIds: Array<string | number> = []) => {
    const params = new URLSearchParams();

    if (schoolIds.length) {
      params.set("school_ids", schoolIds.join(","));
    }

    const response = await apiClient.get<ComparisonReportRow[]>(
      `/api/v1/reports/comparison${params.toString() ? `?${params.toString()}` : ""}`
    );

    return response.data;
  },
  pivot: async (dimension: string, schoolIds: Array<string | number> = []) => {
    const params = new URLSearchParams();
    params.set("dimension", dimension);

    if (schoolIds.length) {
      params.set("school_ids", schoolIds.join(","));
    }

    const response = await apiClient.get<PivotReport>(`/api/v1/reports/pivot?${params.toString()}`);
    return response.data;
  }
};
