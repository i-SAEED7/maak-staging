import { useEffect, useState } from "react";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  inspirationalQuoteService,
  type InspirationalQuote
} from "../../services/inspirationalQuoteService";
import { useAuthStore } from "../../stores/authStore";

type QuoteFormValues = {
  title: string;
  body: string;
  is_active: boolean;
  sort_order: number;
};

const defaultFormValues: QuoteFormValues = {
  title: "",
  body: "",
  is_active: true,
  sort_order: 0
};

export function InspirationalQuotesPage() {
  const user = useAuthStore((state) => state.user);
  const [quotes, setQuotes] = useState<InspirationalQuote[]>([]);
  const [formValues, setFormValues] = useState<QuoteFormValues>(defaultFormValues);
  const [editingQuote, setEditingQuote] = useState<InspirationalQuote | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const canManage = user?.role === "super_admin";

  const loadQuotes = async () => {
    setLoading(true);

    try {
      setQuotes(await inspirationalQuoteService.list());
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (canManage) {
      void loadQuotes();
    }
  }, [canManage]);

  if (!canManage) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب السوبر أدمن فقط.</div>
      </section>
    );
  }

  const resetForm = () => {
    setEditingQuote(null);
    setFormValues(defaultFormValues);
    setError(null);
  };

  const columns: DataColumn<InspirationalQuote>[] = [
    { key: "title", label: "عنوان العبارة", render: (row) => row.title },
    {
      key: "body",
      label: "نص العبارة",
      render: (row) => <span className="table-truncate">{row.body}</span>
    },
    { key: "sort_order", label: "ترتيب العرض", render: (row) => row.sort_order },
    {
      key: "status",
      label: "الحالة",
      render: (row) => (
        <span className={`status-pill${row.is_active ? "" : " status-pill-inactive"}`}>
          {row.is_active ? "مفعلة" : "غير مفعلة"}
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
              setEditingQuote(row);
              setFormValues({
                title: row.title,
                body: row.body,
                is_active: row.is_active,
                sort_order: row.sort_order
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
                await inspirationalQuoteService.deactivate(row.id);
                setSuccessMessage("تم تعطيل العبارة الملهمة.");
                await loadQuotes();
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
          <span className="eyebrow">العبارات الملهمة</span>
          <h2>إدارة العبارات الملهمة</h2>
          <p className="section-description">
            تظهر العبارات المفعلة في الواجهة المدرسية، وإذا لم توجد عبارات مفعلة تظهر العبارة الافتراضية.
          </p>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {successMessage ? <div className="info-box">{successMessage}</div> : null}

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">القائمة</span>
            <h3>العبارات الحالية</h3>
          </div>
        </div>

        {loading ? <div className="loading-box">جارٍ تحميل العبارات...</div> : null}
        {!loading ? <DataTable columns={columns} rows={quotes} emptyMessage="لا توجد عبارات ملهمة بعد." /> : null}
      </section>

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">{editingQuote ? "تعديل" : "إنشاء"}</span>
            <h3>{editingQuote ? "تعديل عبارة ملهمة" : "إضافة عبارة ملهمة"}</h3>
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
                title: formValues.title.trim(),
                body: formValues.body.trim(),
                is_active: formValues.is_active,
                sort_order: Number(formValues.sort_order) || 0
              };

              if (editingQuote) {
                await inspirationalQuoteService.update(editingQuote.id, payload);
                setSuccessMessage("تم تحديث العبارة الملهمة.");
              } else {
                await inspirationalQuoteService.create(payload);
                setSuccessMessage("تم إنشاء العبارة الملهمة.");
              }

              resetForm();
              await loadQuotes();
            } catch (saveError) {
              setError(getErrorMessage(saveError));
            } finally {
              setSaving(false);
            }
          }}
        >
          <div className="grid-two">
            <label className="field">
              <span>عنوان العبارة</span>
              <input
                onChange={(event) => setFormValues((current) => ({ ...current, title: event.target.value }))}
                required
                value={formValues.title}
              />
            </label>

            <label className="field">
              <span>ترتيب العرض</span>
              <input
                min={0}
                onChange={(event) =>
                  setFormValues((current) => ({ ...current, sort_order: Number(event.target.value) }))
                }
                type="number"
                value={formValues.sort_order}
              />
            </label>
          </div>

          <label className="field">
            <span>نص العبارة</span>
            <textarea
              onChange={(event) => setFormValues((current) => ({ ...current, body: event.target.value }))}
              required
              rows={5}
              value={formValues.body}
            />
          </label>

          <label className="checkbox-row">
            <input
              checked={formValues.is_active}
              onChange={(event) => setFormValues((current) => ({ ...current, is_active: event.target.checked }))}
              type="checkbox"
            />
            <span>العبارة مفعلة وتظهر في الواجهة المدرسية</span>
          </label>

          <div className="button-row">
            <button className="button button-primary" disabled={saving} type="submit">
              {saving ? "جارٍ الحفظ..." : editingQuote ? "حفظ التعديلات" : "إنشاء العبارة"}
            </button>
            <button className="button button-ghost" onClick={resetForm} type="button">
              إلغاء
            </button>
          </div>
        </form>
      </section>
    </section>
  );
}
