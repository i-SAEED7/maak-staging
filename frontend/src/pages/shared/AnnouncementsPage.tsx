import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  announcementService,
  type AnnouncementItem,
  type AnnouncementPayload
} from "../../services/announcementService";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { useAuthStore } from "../../stores/authStore";

const audienceLabels: Record<AnnouncementItem["target_audience"], string> = {
  teacher: "معلم",
  principal: "مدير",
  supervisor: "مشرف",
  parent: "ولي أمر",
  general: "عامة"
};

const defaultForm: AnnouncementPayload = {
  title: "",
  body: "",
  target_audience: "general",
  is_all_schools: false,
  school_id: null,
  status: "active"
};

type SchoolOption = {
  id: number;
  name: string;
};

export function AnnouncementsPage() {
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const role = user?.role ?? "";
  const isSuperAdmin = role === "super_admin";
  const canManage = permissions.includes("*") || permissions.includes("announcements.create") || permissions.includes("announcements.update");
  const [rows, setRows] = useState<AnnouncementItem[]>([]);
  const [schools, setSchools] = useState<SchoolOption[]>([]);
  const [form, setForm] = useState<AnnouncementPayload>(defaultForm);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  const availableSchools = useMemo(() => {
    if (isSuperAdmin) {
      return schools;
    }

    if (user?.school?.id && user.school.name_ar) {
      return [{ id: user.school.id, name: user.school.name_ar }];
    }

    return [];
  }, [isSuperAdmin, schools, user?.school]);

  const columns: DataColumn<AnnouncementItem>[] = [
    {
      key: "title",
      label: "العنوان",
      render: (row) => (
        <Link className="inline-link" to={`/app/announcements/${row.id}`}>
          {row.title}
        </Link>
      )
    },
    {
      key: "audience",
      label: "الفئة المستهدفة",
      render: (row) => audienceLabels[row.target_audience] ?? row.target_audience
    },
    {
      key: "school",
      label: "المدرسة المستهدفة",
      render: (row) => (row.is_all_schools ? "جميع المدارس" : row.school?.name_ar ?? "مدرسة غير محددة")
    },
    { key: "status", label: "الحالة", render: (row) => (row.status === "active" ? "نشط" : "غير نشط") },
    { key: "creator", label: "أنشأه", render: (row) => row.creator?.full_name ?? "-" },
    ...(canManage
      ? [
          {
            key: "actions",
            label: "الإجراء",
            render: (row: AnnouncementItem) => (
              <div className="button-row compact-actions">
                <button
                  className="button button-secondary"
                  onClick={() => {
                    setEditingId(row.id);
                    setForm({
                      title: row.title,
                      body: row.body,
                      target_audience: row.target_audience,
                      is_all_schools: row.is_all_schools,
                      school_id: row.school?.id ?? null,
                      status: row.status
                    });
                    setSuccessMessage(null);
                    setError(null);
                  }}
                  type="button"
                >
                  تعديل
                </button>
                <button
                  className="button button-ghost"
                  onClick={() => {
                    if (!window.confirm(`هل تريد حذف الإعلان: ${row.title}؟`)) {
                      return;
                    }

                    void announcementService.delete(row.id).then(() => {
                      setRows((current) => current.filter((item) => item.id !== row.id));
                    });
                  }}
                  type="button"
                >
                  حذف
                </button>
              </div>
            )
          }
        ]
      : [])
  ];

  async function load() {
    setLoading(true);

    try {
      const [announcements, schoolPayload] = await Promise.all([
        announcementService.list(),
        isSuperAdmin ? schoolService.list({ perPage: 100 }) : Promise.resolve({ data: [] as SchoolItem[] })
      ]);

      setRows(announcements);
      if (isSuperAdmin) {
        setSchools(schoolPayload.data.map((school) => ({ id: school.id, name: school.name })));
      }
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

  useEffect(() => {
    if (!isSuperAdmin && availableSchools[0]?.id) {
      setForm((current) => ({
        ...current,
        school_id: current.school_id ?? availableSchools[0].id,
        is_all_schools: false
      }));
    }
  }, [availableSchools, isSuperAdmin]);

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الإعلانات</span>
          <h2>الإعلانات</h2>
        </div>
      </div>

      {canManage ? (
      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">{editingId ? "تعديل" : "إنشاء"}</span>
            <h3>{editingId ? "تعديل إعلان" : "إنشاء إعلان"}</h3>
          </div>
        </div>

        <form
          className="page-stack"
          onSubmit={async (event) => {
            event.preventDefault();
            setSaving(true);
            setError(null);
            setSuccessMessage(null);

            try {
              const payload: AnnouncementPayload = {
                ...form,
                school_id: form.is_all_schools ? null : form.school_id ? Number(form.school_id) : null
              };

              if (editingId) {
                await announcementService.update(editingId, payload);
                setSuccessMessage("تم تحديث الإعلان بنجاح.");
              } else {
                await announcementService.create(payload);
                setSuccessMessage("تم إنشاء الإعلان بنجاح.");
              }

              setEditingId(null);
              setForm({
                ...defaultForm,
                school_id: !isSuperAdmin ? availableSchools[0]?.id ?? null : null
              });
              await load();
            } catch (saveError) {
              setError(getErrorMessage(saveError));
            } finally {
              setSaving(false);
            }
          }}
        >
          <div className="grid-two">
            <label className="field">
              <span>العنوان</span>
              <input
                onChange={(event) => setForm((current) => ({ ...current, title: event.target.value }))}
                required
                value={form.title}
              />
            </label>

            <label className="field">
              <span>الفئة المستهدفة</span>
              <select
                onChange={(event) =>
                  setForm((current) => ({
                    ...current,
                    target_audience: event.target.value as AnnouncementItem["target_audience"]
                  }))
                }
                value={form.target_audience}
              >
                {Object.entries(audienceLabels).map(([value, label]) => (
                  <option key={value} value={value}>
                    {label}
                  </option>
                ))}
              </select>
            </label>
          </div>

          <div className="grid-two">
            {isSuperAdmin ? (
              <label className="field">
                <span>المدرسة المستهدفة</span>
                <select
                  disabled={form.is_all_schools}
                  onChange={(event) =>
                    setForm((current) => ({
                      ...current,
                      school_id: event.target.value ? Number(event.target.value) : null
                    }))
                  }
                  value={form.school_id ?? ""}
                >
                  <option value="">اختر مدرسة</option>
                  {availableSchools.map((school) => (
                    <option key={school.id} value={school.id}>
                      {school.name}
                    </option>
                  ))}
                </select>
              </label>
            ) : (
              <label className="field">
                <span>المدرسة المستهدفة</span>
                <input disabled value={availableSchools[0]?.name ?? "مدرستك الحالية"} />
              </label>
            )}

            <label className="field">
              <span>الحالة</span>
              <select
                onChange={(event) =>
                  setForm((current) => ({
                    ...current,
                    status: event.target.value as "active" | "inactive"
                  }))
                }
                value={form.status ?? "active"}
              >
                <option value="active">نشط</option>
                <option value="inactive">غير نشط</option>
              </select>
            </label>
          </div>

          {isSuperAdmin ? (
            <label className="checkbox-row">
              <input
                checked={Boolean(form.is_all_schools)}
                onChange={(event) =>
                  setForm((current) => ({
                    ...current,
                    is_all_schools: event.target.checked,
                    school_id: event.target.checked ? null : current.school_id
                  }))
                }
                type="checkbox"
              />
              <span>استهداف جميع المدارس</span>
            </label>
          ) : null}

          <label className="field">
            <span>نص الإعلان</span>
            <textarea
              onChange={(event) => setForm((current) => ({ ...current, body: event.target.value }))}
              required
              rows={5}
              value={form.body}
            />
          </label>

          <div className="button-row">
            <button className="button button-primary" disabled={saving} type="submit">
              {saving ? "جارٍ الحفظ..." : editingId ? "حفظ التعديل" : "إنشاء الإعلان"}
            </button>
            {editingId ? (
              <button
                className="button button-secondary"
                onClick={() => {
                  setEditingId(null);
                  setForm({
                    ...defaultForm,
                    school_id: !isSuperAdmin ? availableSchools[0]?.id ?? null : null
                  });
                }}
                type="button"
              >
                إلغاء التعديل
              </button>
            ) : null}
          </div>
        </form>
      </section>
      ) : null}

      {successMessage ? <div className="info-box">{successMessage}</div> : null}
      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الإعلانات...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد إعلانات حالية." /> : null}
    </section>
  );
}
