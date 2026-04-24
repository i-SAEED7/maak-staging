import { useEffect, useState } from "react";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  educationProgramService,
  type EducationProgramOption
} from "../../services/educationProgramService";
import { useAuthStore } from "../../stores/authStore";

type ProgramFormValues = {
  name_ar: string;
  code: string;
  is_active: boolean;
};

const defaultFormValues: ProgramFormValues = {
  name_ar: "",
  code: "",
  is_active: true
};

export function ProgramsPage() {
  const user = useAuthStore((state) => state.user);
  const canManagePrograms = ["super_admin", "admin"].includes(user?.role ?? "");
  const [rows, setRows] = useState<EducationProgramOption[]>([]);
  const [editingProgram, setEditingProgram] = useState<EducationProgramOption | null>(null);
  const [formValues, setFormValues] = useState<ProgramFormValues>(defaultFormValues);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  const loadPrograms = async () => {
    setLoading(true);

    try {
      setRows(await educationProgramService.list({ includeInactive: true }));
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!canManagePrograms) {
      return;
    }

    void loadPrograms();
  }, [canManagePrograms]);

  if (!canManagePrograms) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب الأدمن والسوبر أدمن فقط.</div>
      </section>
    );
  }

  const columns: DataColumn<EducationProgramOption>[] = [
    { key: "name", label: "اسم البرنامج", render: (row) => row.name_ar },
    { key: "code", label: "الكود", render: (row) => row.code },
    {
      key: "status",
      label: "الحالة",
      render: (row) => (
        <span className={`status-pill${row.is_active ? "" : " status-pill-inactive"}`}>
          {row.is_active ? "نشط" : "غير نشط"}
        </span>
      )
    },
    {
      key: "actions",
      label: "الإجراءات",
      render: (row) => (
        <div className="button-row compact-actions">
          <button
            className="button button-secondary"
            onClick={() => {
              setEditingProgram(row);
              setFormValues({
                name_ar: row.name_ar,
                code: row.code,
                is_active: row.is_active ?? true
              });
            }}
            type="button"
          >
            تعديل
          </button>
          {row.is_active ? (
            <button
              className="button button-ghost"
              onClick={async () => {
                await educationProgramService.deactivate(row.id);
                setSuccessMessage(`تم تعطيل البرنامج: ${row.name_ar}`);
                await loadPrograms();
              }}
              type="button"
            >
              تعطيل
            </button>
          ) : null}
        </div>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">البرامج</span>
          <h2>إدارة أنواع البرامج</h2>
          <p className="section-description">
            يمكنك إضافة البرامج التعليمية الجديدة أو تعديلها دون الحاجة إلى تعديل الكود يدويًا.
          </p>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {successMessage ? <div className="info-box">{successMessage}</div> : null}

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">البيانات</span>
            <h3>البرامج الحالية</h3>
          </div>
        </div>

        {loading ? <div className="loading-box">جارٍ تحميل البرامج...</div> : null}
        {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد برامج حالية." /> : null}
      </section>

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">{editingProgram ? "تعديل" : "إنشاء"}</span>
            <h3>{editingProgram ? `تعديل البرنامج: ${editingProgram.name_ar}` : "إضافة برنامج جديد"}</h3>
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
              const payload = {
                name_ar: formValues.name_ar.trim(),
                code: formValues.code.trim() || undefined,
                is_active: formValues.is_active
              };

              if (editingProgram) {
                await educationProgramService.update(editingProgram.id, payload);
                setSuccessMessage("تم تحديث البرنامج بنجاح.");
              } else {
                await educationProgramService.create(payload);
                setSuccessMessage("تم إنشاء البرنامج بنجاح.");
              }

              setEditingProgram(null);
              setFormValues(defaultFormValues);
              await loadPrograms();
            } catch (saveError) {
              setError(getErrorMessage(saveError));
            } finally {
              setSaving(false);
            }
          }}
        >
          <div className="grid-two">
            <label className="field">
              <span>اسم البرنامج</span>
              <input
                onChange={(event) => setFormValues((current) => ({ ...current, name_ar: event.target.value }))}
                required
                value={formValues.name_ar}
              />
            </label>

            <label className="field">
              <span>الكود</span>
              <input
                onChange={(event) => setFormValues((current) => ({ ...current, code: event.target.value }))}
                placeholder="سيتم توليده تلقائيًا إن تُرك فارغًا"
                value={formValues.code}
              />
            </label>
          </div>

          <label className="checkbox-row">
            <input
              checked={formValues.is_active}
              onChange={(event) => setFormValues((current) => ({ ...current, is_active: event.target.checked }))}
              type="checkbox"
            />
            <span>البرنامج نشط وقابل للاختيار</span>
          </label>

          <div className="button-row">
            <button className="button button-primary" disabled={saving} type="submit">
              {saving ? "جارٍ الحفظ..." : editingProgram ? "حفظ التعديلات" : "إنشاء البرنامج"}
            </button>
            <button
              className="button button-ghost"
              onClick={() => {
                setEditingProgram(null);
                setFormValues(defaultFormValues);
                setError(null);
              }}
              type="button"
            >
              إلغاء
            </button>
          </div>
        </form>
      </section>
    </section>
  );
}
