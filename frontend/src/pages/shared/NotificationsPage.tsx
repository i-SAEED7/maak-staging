import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useAuthStore } from "../../stores/authStore";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getNotificationTypeLabel } from "../../lib/uiText";
import { getErrorMessage } from "../../services/api";
import { notificationService, type NotificationItem } from "../../services/notificationService";

function NotificationActions({
  notification,
  onChanged
}: {
  notification: NotificationItem;
  onChanged: () => Promise<void>;
}) {
  return (
    <div className="button-row compact-actions">
      {notification.entity_type === "iep_plan" && notification.entity_id ? (
        <Link className="button button-secondary" to={`/app/iep-plans/${notification.entity_id}`}>
          عرض الخطة
        </Link>
      ) : notification.action_url ? (
        <Link className="button button-secondary" to={notification.action_url}>
          {notification.action_label ?? "عرض"}
        </Link>
      ) : null}

      {!notification.read_at ? (
        <button
          className="button button-ghost"
          onClick={() => void notificationService.markRead(notification.id).then(onChanged)}
          type="button"
        >
          تحديد كمقروء
        </button>
      ) : null}
    </div>
  );
}

export function NotificationsPage() {
  const user = useAuthStore((state) => state.user);
  const isSuperAdmin = user?.role === "super_admin";
  const isSupervisor = user?.role === "supervisor";
  const [rows, setRows] = useState<NotificationItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const columns: DataColumn<NotificationItem>[] = isSuperAdmin
    ? [
        { key: "type", label: "نوع الإشعار", render: (row) => getNotificationTypeLabel(row.type) },
        { key: "creator", label: "من أنشأه", render: (row) => row.creator?.full_name ?? "-" },
        { key: "recipient", label: "لمن موجه", render: (row) => row.recipient?.full_name ?? "-" },
        { key: "status", label: "حالة القراءة", render: (row) => (row.read_at ? "مقروء" : "غير مقروء") },
        {
          key: "sent",
          label: "التاريخ",
          render: (row) => (row.sent_at ? new Date(row.sent_at).toLocaleString("ar-SA") : "-")
        }
      ]
    : [
        { key: "title", label: "العنوان", render: (row) => row.title },
        { key: "type", label: "النوع", render: (row) => getNotificationTypeLabel(row.type) },
        { key: "school", label: "المدرسة", render: (row) => row.school_name ?? row.school?.name_ar ?? "-" },
        { key: "teacher", label: "المعلم", render: (row) => row.teacher_name ?? "-" },
        {
          key: "sent",
          label: "التاريخ",
          render: (row) => (row.sent_at ? new Date(row.sent_at).toLocaleString("ar-SA") : "-")
        },
        { key: "status", label: "الحالة", render: (row) => (row.read_at ? "مقروء" : "غير مقروء") },
        {
          key: "actions",
          label: "الإجراء",
          render: (row) => <NotificationActions notification={row} onChanged={load} />
        }
      ];

  async function load() {
    setLoading(true);

    try {
      setRows(await notificationService.list());
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    void load();
  }, []);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الإشعارات</span>
          <h2>الإشعارات</h2>
        </div>
        {!isSuperAdmin ? (
          <button
            className="button button-secondary"
            onClick={() => void notificationService.markAllRead().then(load)}
            type="button"
          >
            تعليم الكل كمقروء
          </button>
        ) : null}
      </div>

      {isSupervisor ? (
        <div className="info-box">
          تظهر لك هنا الإشعارات المرتبطة بمدارسك المسندة، مع إمكانية فتح الخطة مباشرة أو تعليم الإشعار كمقروء.
        </div>
      ) : null}

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الإشعارات...</div> : null}
      {!loading ? (
        <DataTable
          columns={columns}
          rows={rows}
          emptyMessage={isSuperAdmin ? "لا توجد إشعارات حالية على مستوى النظام." : "لا توجد إشعارات حالية."}
        />
      ) : null}
    </section>
  );
}
