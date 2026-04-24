import { useEffect, useMemo, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  messageService,
  type MessageRecipientOption,
  type MessageThread,
  type MessageThreadDetails,
  type ThreadMessage
} from "../../services/messageService";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { useAuthStore } from "../../stores/authStore";

const supervisorColumns: DataColumn<MessageThread>[] = [
  { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? "-" },
  { key: "sender", label: "المرسل", render: (row) => row.latest_sender_name ?? "-" },
  {
    key: "recipients",
    label: "المستقبل",
    render: (row) => row.recipient_names?.join("، ") ?? "-"
  },
  { key: "subject", label: "الموضوع", render: (row) => row.subject ?? "-" },
  {
    key: "updated",
    label: "آخر تحديث",
    render: (row) => (row.latest_message_at ? new Date(row.latest_message_at).toLocaleString("ar-SA") : "-")
  },
  { key: "read_status", label: "القراءة", render: (row) => row.read_status },
  { key: "thread_status", label: "الحالة", render: (row) => row.thread_status }
];

const defaultThreadColumns: DataColumn<MessageThread>[] = [
  { key: "subject", label: "الموضوع", render: (row) => row.subject ?? "-" },
  { key: "sender", label: "آخر مرسل", render: (row) => row.latest_sender_name ?? "-" },
  { key: "excerpt", label: "الملخص", render: (row) => row.latest_message_excerpt ?? "-" },
  { key: "count", label: "عدد الرسائل", render: (row) => row.message_count },
  { key: "unread", label: "غير مقروءة", render: (row) => row.unread_count },
  {
    key: "updated",
    label: "آخر تحديث",
    render: (row) => (row.latest_message_at ? new Date(row.latest_message_at).toLocaleString("ar-SA") : "-")
  }
];

export function MessagesPage() {
  const { threadKey } = useParams();
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const persistedSchoolId = useAuthStore((state) => state.schoolId);
  const isSuperAdmin = user?.role === "super_admin";
  const isSupervisor = user?.role === "supervisor";
  const canCompose = Boolean(user) && permissionsForMessages(user?.role, permissions);
  const [rows, setRows] = useState<MessageThread[]>([]);
  const [recipients, setRecipients] = useState<MessageRecipientOption[]>([]);
  const [availableSchools, setAvailableSchools] = useState<Array<{ id: number; name_ar: string; program_type?: string | null }>>([]);
  const [selectedSchoolId, setSelectedSchoolId] = useState(persistedSchoolId || "");
  const [selectedProgramType, setSelectedProgramType] = useState("");
  const [selectedRecipientId, setSelectedRecipientId] = useState("");
  const [subject, setSubject] = useState("");
  const [body, setBody] = useState("");
  const [showComposer, setShowComposer] = useState(false);
  const [thread, setThread] = useState<MessageThreadDetails | null>(null);
  const [threadMessages, setThreadMessages] = useState<ThreadMessage[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  const selectedSchool = availableSchools.find((school) => String(school.id) === selectedSchoolId) ?? null;
  const programOptions = useMemo(() => {
    const values = new Set(
      availableSchools
        .map((school) => school.program_type)
        .filter((value): value is string => Boolean(value))
    );

    return Array.from(values);
  }, [availableSchools]);

  useEffect(() => {
    const role = user?.role ?? "";

    if (!user) {
      setAvailableSchools([]);
      return;
    }

    if (role === "super_admin") {
      void schoolService
        .list({ perPage: 100 })
        .then((payload) => {
          const schools = payload.data.map((school: SchoolItem) => ({
            id: school.id,
            name_ar: school.name,
            program_type: school.program_type
          }));
          setAvailableSchools(schools);
          setSelectedSchoolId((current) => current || String(schools[0]?.id ?? ""));
        })
        .catch((loadError) => setError(getErrorMessage(loadError)));

      return;
    }

    const schools = (user.assigned_schools?.length
      ? user.assigned_schools
      : user.school
        ? [user.school]
        : []
    ).map((school) => ({
      id: school.id,
      name_ar: school.name_ar,
      program_type:
        typeof school === "object" && school !== null && "program_type" in school
          ? (school.program_type as string | null | undefined) ?? null
          : null
    }));

    setAvailableSchools(schools);
    setSelectedSchoolId((current) => current || persistedSchoolId || String(schools[0]?.id ?? ""));
  }, [persistedSchoolId, user]);

  const loadThreads = async () => {
    setLoading(true);

    try {
      setRows(await messageService.listThreads());
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!selectedSchoolId && availableSchools.length > 0) {
      setSelectedSchoolId(String(availableSchools[0].id));
      return;
    }

    if (selectedSchool?.program_type) {
      setSelectedProgramType(selectedSchool.program_type);
    }
  }, [availableSchools, selectedSchool, selectedSchoolId]);

  useEffect(() => {
    void loadThreads();
  }, []);

  useEffect(() => {
    if (!canCompose || !selectedSchoolId) {
      setRecipients([]);
      setSelectedRecipientId("");
      return;
    }

    setSelectedRecipientId("");

    void messageService
      .recipients(selectedSchoolId)
      .then((payload) => {
        setRecipients(payload.filter((recipient) => recipient.id !== user?.id));
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [canCompose, selectedSchoolId, user?.id]);

  const openThread = async (nextThreadKey: string, updateRoute = true) => {
    try {
      const payload = await messageService.thread(nextThreadKey);
      setThread(payload.thread ?? null);
      setThreadMessages(payload.messages);
      setError(null);
      if (updateRoute) {
        navigate(`/app/messages/${nextThreadKey}`);
      }
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    }
  };

  useEffect(() => {
    if (!threadKey) {
      setThread(null);
      setThreadMessages([]);
      return;
    }

    void openThread(threadKey, false);
  }, [threadKey]);

  const columns: DataColumn<MessageThread>[] =
    isSuperAdmin || isSupervisor
      ? [
          ...supervisorColumns,
          {
            key: "actions",
            label: "الإجراء",
            render: (row) => (
              <button className="button button-secondary" onClick={() => void openThread(row.thread_key)} type="button">
                عرض السلسلة
              </button>
            )
          }
        ]
      : [
          ...defaultThreadColumns,
          {
            key: "actions",
            label: "الإجراء",
            render: (row) => (
              <button className="button button-secondary" onClick={() => void openThread(row.thread_key)} type="button">
                فتح الرسالة
              </button>
            )
          }
        ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الرسائل</span>
          <h2>الرسائل</h2>
        </div>
        {canCompose ? (
          <button
            className="button button-primary"
            onClick={() => setShowComposer((current) => !current)}
            type="button"
          >
            {showComposer ? "إخفاء النموذج" : "إنشاء رسالة"}
          </button>
        ) : null}
      </div>

      {showComposer && canCompose ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">إنشاء</span>
              <h3>إنشاء رسالة جديدة</h3>
            </div>
          </div>

          <form
            className="page-stack"
            onSubmit={async (event) => {
              event.preventDefault();

              if (!selectedSchoolId || !selectedRecipientId) {
                setError("يجب اختيار المدرسة والمستلم قبل الإرسال.");
                return;
              }

              if (!window.confirm("هل تريد إرسال الرسالة الآن؟")) {
                return;
              }

              setSubmitting(true);

              try {
                await messageService.send({
                  school_id: Number(selectedSchoolId),
                  program_type: isSupervisor ? selectedProgramType : undefined,
                  recipient_ids: [Number(selectedRecipientId)],
                  subject,
                  body
                });

                setSubject("");
                setBody("");
                setSelectedRecipientId("");
                setShowComposer(false);
                setSuccessMessage("تم إرسال الرسالة وإشعار المستلم بنجاح.");
                setError(null);
                await loadThreads();
              } catch (submitError) {
                setError(getErrorMessage(submitError));
              } finally {
                setSubmitting(false);
              }
            }}
          >
            <div className="grid-two">
              <label className="field">
                <span>المدرسة</span>
                <select
                  onChange={(event) => {
                    setSelectedSchoolId(event.target.value);
                    setSuccessMessage(null);
                    setError(null);
                  }}
                  disabled={!isSuperAdmin && availableSchools.length === 1}
                  value={selectedSchoolId}
                >
                  <option value="">اختر المدرسة</option>
                  {availableSchools.map((school) => (
                    <option key={school.id} value={school.id}>
                      {school.name_ar}
                    </option>
                  ))}
                </select>
              </label>

              {isSupervisor ? (
                <label className="field">
                  <span>نوع البرنامج</span>
                  <select
                    onChange={(event) => setSelectedProgramType(event.target.value)}
                    value={selectedProgramType}
                  >
                    {selectedSchool?.program_type ? (
                      <option value={selectedSchool.program_type}>{selectedSchool.program_type}</option>
                    ) : (
                      <>
                        <option value="">اختر البرنامج</option>
                        {programOptions.map((program) => (
                          <option key={program} value={program}>
                            {program}
                          </option>
                        ))}
                      </>
                    )}
                  </select>
                </label>
              ) : null}
            </div>

            <div className="grid-two">
              <label className="field">
                <span>المستلم</span>
                <select
                  onChange={(event) => setSelectedRecipientId(event.target.value)}
                  value={selectedRecipientId}
                >
                  <option value="">اختر المستلم</option>
                  {recipients.map((recipient) => (
                    <option key={recipient.id} value={recipient.id}>
                      {recipient.full_name} - {recipient.role_display_name_ar ?? recipient.role}
                    </option>
                  ))}
                </select>
                {selectedSchoolId && recipients.length === 0 ? (
                  <small className="field-hint">لا يوجد مستلمون متاحون داخل المدرسة المختارة.</small>
                ) : null}
              </label>

              <label className="field">
                <span>الموضوع</span>
                <input onChange={(event) => setSubject(event.target.value)} required value={subject} />
              </label>
            </div>

            <label className="field">
              <span>نص الرسالة</span>
              <textarea onChange={(event) => setBody(event.target.value)} required rows={5} value={body} />
            </label>

            <div className="button-row">
              <button className="button button-primary" disabled={submitting} type="submit">
                {submitting ? "جارٍ الإرسال..." : "تأكيد الإرسال"}
              </button>
            </div>
          </form>
        </section>
      ) : null}

      {successMessage ? <div className="info-box">{successMessage}</div> : null}
      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الرسائل...</div> : null}
      {!loading ? (
        <DataTable
          columns={columns}
          rows={rows}
          emptyMessage={
            isSuperAdmin
              ? "لا توجد رسائل حالية على مستوى النظام."
              : isSupervisor
                ? "لا توجد رسائل داخل نطاقك الإشرافي."
                : "لا توجد محادثات حالية."
          }
        />
      ) : null}

      {thread ? (
        <section className="surface-card page-stack">
          <div className="page-header">
            <div>
              <span className="eyebrow">سلسلة الرسائل</span>
              <h3>{thread.subject ?? "سلسلة رسائل"}</h3>
            </div>
            <button className="button button-ghost" onClick={() => navigate("/app/messages")} type="button">
              إغلاق الرسالة
            </button>
          </div>

          <div className="info-box">
            المشاركون: {thread.participants.map((participant) => participant.full_name).join("، ")}
          </div>

          <div className="detail-list">
            {threadMessages.map((message) => (
              <article className="detail-list-item" key={message.id}>
                <strong>{message.sender?.full_name ?? "مرسل غير معروف"}</strong>
                <div className="detail-paragraph">{message.body}</div>
                <small>
                  {message.school?.name_ar ?? "-"} |{" "}
                  {message.created_at ? new Date(message.created_at).toLocaleString("ar-SA") : "-"}
                </small>
              </article>
            ))}
          </div>
        </section>
      ) : null}
    </section>
  );
}

function permissionsForMessages(role: string | undefined, permissions: string[]) {
  if (!role) {
    return false;
  }

  return permissions.includes("*") || permissions.includes("messages.send");
}
