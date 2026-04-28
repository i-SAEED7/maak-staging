import { useEffect, useState } from "react";
import { getAuditTargetLabel, getRoleLabel } from "../../lib/uiText";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { auditLogService, type AuditLogItem } from "../../services/auditLogService";
import { useAuthStore } from "../../stores/authStore";

const actionLabels: Record<string, string> = {
  create: "إنشاء",
  update: "تعديل",
  delete: "حذف",
  upload: "رفع",
  send: "إرسال"
};

export function AuditLogsPage() {
  const user = useAuthStore((state) => state.user);
  const isSuperAdmin = user?.role === "super_admin";
  const [rows, setRows] = useState<AuditLogItem[]>([]);
  const [search, setSearch] = useState("");
  const [actionFilter, setActionFilter] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedActor, setSelectedActor] = useState<AuditLogItem["actor"] | null>(null);

  const columns: DataColumn<AuditLogItem>[] = [
    {
      key: "actor",
      label: "من قام بالعملية",
      render: (row) =>
        row.actor ? (
          <button className="link-button" onClick={() => setSelectedActor(row.actor ?? null)} type="button">
            {row.actor.full_name}
          </button>
        ) : (
          "-"
        )
    },
    { key: "action", label: "نوع العملية", render: (row) => actionLabels[row.action] ?? row.action },
    {
      key: "target",
      label: "العنصر المتأثر",
      render: (row) => `${getAuditTargetLabel(row.target_type)}${row.target_id ? ` #${row.target_id}` : ""}`
    },
    { key: "date", label: "التاريخ والوقت", render: (row) => new Date(row.created_at).toLocaleString("ar-SA") }
  ];

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    setLoading(true);

    void auditLogService
      .list({
        per_page: 100,
        "filter[search]": search.trim() || undefined,
        "filter[action]": actionFilter || undefined
      })
      .then((payload) => setRows(payload.data))
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, [actionFilter, isSuperAdmin, search]);

  if (!isSuperAdmin) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب السوبر أدمن فقط.</div>
      </section>
    );
  }

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">المشرف العام</span>
          <h2>سجل التعديلات</h2>
        </div>
      </div>

      <div className="filters-bar filters-bar-wide">
        <label className="field">
          <span>بحث</span>
          <input
            onChange={(event) => setSearch(event.target.value)}
            placeholder="ابحث بالمستخدم أو المسار أو العنصر"
            value={search}
          />
        </label>

        <label className="field">
          <span>نوع العملية</span>
          <select onChange={(event) => setActionFilter(event.target.value)} value={actionFilter}>
            <option value="">الكل</option>
            <option value="create">إنشاء</option>
            <option value="update">تعديل</option>
            <option value="delete">حذف</option>
            <option value="upload">رفع</option>
            <option value="send">إرسال</option>
          </select>
        </label>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل سجل التعديلات...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد عمليات مسجلة بعد." /> : null}

      {selectedActor ? (
        <div className="modal-backdrop" role="presentation">
          <section aria-modal="true" className="modal-card modal-card-narrow" role="dialog">
            <div className="page-header">
              <div>
                <span className="eyebrow">بيانات الحساب</span>
                <h3>{selectedActor.full_name}</h3>
              </div>
              <button className="button button-ghost" onClick={() => setSelectedActor(null)} type="button">
                إغلاق
              </button>
            </div>
            <div className="details-grid">
              <div>
                <span className="detail-label">الاسم</span>
                <strong>{selectedActor.full_name}</strong>
              </div>
              <div>
                <span className="detail-label">البريد</span>
                <strong>{selectedActor.email ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الدور</span>
                <strong>{getRoleLabel(selectedActor.role)}</strong>
              </div>
            </div>
          </section>
        </div>
      ) : null}
    </section>
  );
}
