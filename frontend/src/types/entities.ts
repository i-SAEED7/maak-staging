export type SchoolEntity = {
  id: number;
  uuid: string;
  name_ar: string;
  region: string;
  city: string;
  status: string;
};

export type UserEntity = {
  id: number;
  uuid: string;
  full_name: string;
  role: string;
  school_id: number | null;
  status: string;
};

export type StudentEntity = {
  id: number;
  uuid: string;
  full_name: string;
  school_id: number;
  enrollment_status: string;
};

export type IepPlanEntity = {
  id: number;
  uuid: string;
  student_id: number;
  status: string;
  current_version_number: number;
  title: string;
};

export type NotificationEntity = {
  id: number;
  title: string;
  body: string;
  read_at?: string | null;
};

export type MessageEntity = {
  id: number;
  thread_key: string;
  subject?: string;
  body?: string;
};
