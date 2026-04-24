export type LoginRequest = {
  identifier: string;
  password: string;
};

export type LoginResponse = {
  token: string;
  user: {
    id: number;
    uuid: string;
    full_name: string;
    role: string;
    school_id: number | null;
  };
  permissions: string[];
};

export type SchoolPayload = {
  name_ar: string;
  region: string;
  city: string;
  district?: string;
  address?: string;
  phone?: string;
  email?: string;
  latitude?: number;
  longitude?: number;
  storage_quota_mb?: number;
};

export type StudentPayload = {
  school_id: number;
  education_program_id?: number;
  disability_category_id?: number;
  primary_teacher_user_id?: number;
  first_name: string;
  father_name?: string;
  family_name: string;
  gender: "male" | "female";
  birth_date?: string;
  grade_level?: string;
  classroom?: string;
  student_number?: string;
};
