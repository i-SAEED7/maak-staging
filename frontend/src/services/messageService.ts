import { apiClient } from "./api";

export type MessageThread = {
  thread_key: string;
  school?: {
    id: number;
    name_ar: string;
    program_type?: string | null;
  } | null;
  subject: string | null;
  latest_sender_name: string | null;
  recipient_names?: string[];
  read_status: string;
  thread_status: string;
  latest_message_excerpt: string | null;
  latest_message_at: string | null;
  message_count: number;
  unread_count: number;
};

export type MessageThreadDetails = {
  thread_key: string;
  subject: string | null;
  participants: Array<{
    id: number;
    full_name: string;
    email: string | null;
    role: string | null;
  }>;
  message_count: number;
};

export type ThreadMessage = {
  id: number;
  school?: {
    id: number;
    name_ar: string;
    program_type?: string | null;
  } | null;
  subject: string | null;
  body: string;
  created_at: string | null;
  sender?: {
    id: number;
    full_name: string;
    role: string | null;
  } | null;
  recipients?: Array<{
    recipient_user_id: number;
    recipient_name?: string | null;
    recipient_role?: string | null;
    read_at?: string | null;
  }>;
};

export type MessageRecipientOption = {
  id: number;
  full_name: string;
  role: string | null;
  role_display_name_ar?: string | null;
  school_id?: number | null;
};

export const messageService = {
  listThreads: async () => {
    const response = await apiClient.get<MessageThread[]>("/api/v1/messages");
    return response.data;
  },
  thread: async (threadKey: string) => {
    const response = await apiClient.get<ThreadMessage[]>(`/api/v1/messages/thread/${threadKey}`);
    return {
      messages: response.data,
      thread: response.meta?.thread as MessageThreadDetails | undefined
    };
  },
  send: async (payload: {
    school_id: number;
    program_type?: string;
    recipient_ids: number[];
    subject: string;
    body: string;
  }) => {
    const response = await apiClient.post<ThreadMessage>("/api/v1/messages", payload);
    return response.data;
  },
  recipients: async (schoolId: number | string) => {
    const response = await apiClient.get<MessageRecipientOption[]>(
      `/api/v1/messages/recipients?school_id=${schoolId}`
    );
    return response.data;
  }
};
