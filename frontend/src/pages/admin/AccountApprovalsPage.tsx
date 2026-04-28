import { useEffect, useMemo, useState } from "react";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import {
  accountApprovalService,
  type AccountApprovalItem,
  type AccountApprovalPayload
} from "../../services/accountApprovalService";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { useAuthStore } from "../../stores/authStore";

const STAGES = ["ابتدائي", "متوسط", "ثانوي", "متعدد المراحل"];
const ACCOUNT_TYPES = [
  { value: "parent", label: "ولي أمر" },
  { value: "teacher", label: "معلم" },
  { value: "principal", label: "مدير مدرسة" }
] as const;

function getAccountTypeLabel(value?: string | null) {
  return ACCOUNT_TYPES.find((item) => item.value === value)?.label ?? "غير محدد";
}

type ApprovalFormValues = {
  first_name: string;
  second_name: string;
  last_name: string;
  email: string;
  phone: string;
  account_type: string;
  stage: string;
  school_id: string;
};

function toFormValues(item: AccountApprovalItem): ApprovalFormValues {
  return {
    first_name: item.first_name,
    second_name: item.second_name ?? "",
    last_name: item.last_name,
    email: item.email,
    phone: item.phone,
    account_type: ACCOUNT_TYPES.some((type) => type.value === item.account_type) ? item.account_type : "",
    stage: item.stage,
    school_id: String(item.school_id)
  };
}

function toPayload(values: ApprovalFormValues): AccountApprovalPayload {
  return {
    first_name: values.first_name.trim(),
    second_name: values.second_name.trim() || null,
    last_name: values.last_name.trim(),
    email: values.email.trim(),
    phone: values.phone.trim(),
    account_type: values.account_type,
    stage: values.stage,
    school_id: Number(values.school_id)
  };
}

export function AccountApprovalsPage() {
  const user = useAuthStore((state) => state.user);
  const isSuperAdmin = user?.role === "super_admin";
  const [rows, setRows] = useState<AccountApprovalItem[]>([]);
  const [schools, setSchools] = useState<SchoolItem[]>([]);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [selectedRequest, setSelectedRequest] = useState<AccountApprovalItem | null>(null);
  const [formValues, setFormValues] = useState<ApprovalFormValues | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  const loadApprovals = async () => {
    setLoading(true);

    try {
      const payload = await accountApprovalService.list({
        per_page: 100,
        "filter[status]": "pending",
        "filter[search]": search.trim() || undefined
      });
      setRows(payload.data);
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    void loadApprovals();
  }, [isSuperAdmin, search]);

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    void schoolService
      .list({ perPage: 100 })
      .then((payload) => setSchools(payload.data))
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [isSuperAdmin]);

  const filteredSchools = useMemo(() => {
    if (!formValues?.stage) {
      return [];
    }

    return schools.filter((school) => school.stage === formValues.stage);
  }, [formValues?.stage, schools]);

  if (!isSuperAdmin) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب السوبر أدمن فقط.</div>
      </section>
    );
  }

  const columns: DataColumn<AccountApprovalItem>[] = [
    {
      key: "name",
      label: "الاسم الأول والأخير",
      render: (row) => `${row.first_name} ${row.last_name}`
    },
    {
      key: "account_type",
      label: "نوع الحساب",
      render: (row) => row.account_type_label ?? getAccountTypeLabel(row.account_type)
    },
    { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? "-" },
    {
      key: "details",
      label: "عرض البيانات",
      render: (row) => (
        <button
          className="button button-secondary"
          onClick={() => {
            setSelectedRequest(row);
            setFormValues(toFormValues(row));
            setError(null);
          }}
          type="button"
        >
          عرض البيانات
        </button>
      )
    },
    {
      key: "approve",
      label: "اعتماد",
      render: (row) => (
        <button
          className="button button-primary"
          disabled={!row.account_type}
          onClick={async () => {
            setSaving(true);
            setError(null);
            setSuccessMessage(null);

            try {
              await accountApprovalService.approve(row.id);
              setSuccessMessage(`تم اعتماد حساب: ${row.first_name} ${row.last_name}`);
              await loadApprovals();
            } catch (approveError) {
              setError(getErrorMessage(approveError));
            } finally {
              setSaving(false);
            }
          }}
          title={!row.account_type ? "يجب فتح الطلب واختيار نوع الحساب قبل الاعتماد." : undefined}
          type="button"
        >
          اعتماد
        </button>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">السوبر أدمن</span>
          <h2>اعتماد الحسابات</h2>
          <p className="section-description">
            مراجعة طلبات التسجيل الجديدة وتعديل بياناتها قبل تفعيل الحساب وربطه بالمدرسة.
          </p>
        </div>
      </div>

      <div className="filters-bar filters-bar-wide">
        <label className="field">
          <span>بحث</span>
          <input
            onChange={(event) => setSearch(event.target.value)}
            placeholder="ابحث بالاسم أو البريد أو المدرسة"
            value={search}
          />
        </label>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {successMessage ? <div className="info-box">{successMessage}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل طلبات الاعتماد...</div> : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد طلبات اعتماد معلقة." /> : null}

      {selectedRequest && formValues ? (
        <div className="modal-backdrop" role="presentation">
          <section aria-modal="true" className="modal-card" role="dialog">
            <div className="page-header">
              <div>
                <span className="eyebrow">بيانات الطلب</span>
                <h3>{selectedRequest.full_name}</h3>
              </div>
              <button className="button button-ghost" onClick={() => setSelectedRequest(null)} type="button">
                إغلاق
              </button>
            </div>

            <form
              className="page-stack"
              onSubmit={async (event) => {
                event.preventDefault();
                setSaving(true);
                setError(null);
                setSuccessMessage(null);

                try {
                  const updated = await accountApprovalService.update(selectedRequest.id, toPayload(formValues));
                  setSelectedRequest(updated);
                  setFormValues(toFormValues(updated));
                  setSuccessMessage("تم تحديث بيانات طلب الاعتماد.");
                  await loadApprovals();
                } catch (saveError) {
                  setError(getErrorMessage(saveError));
                } finally {
                  setSaving(false);
                }
              }}
            >
              <div className="grid-three">
                <label className="field">
                  <span>الاسم الأول</span>
                  <input
                    onChange={(event) => setFormValues((current) => current ? { ...current, first_name: event.target.value } : current)}
                    required
                    value={formValues.first_name}
                  />
                </label>
                <label className="field">
                  <span>الاسم الثاني</span>
                  <input
                    onChange={(event) => setFormValues((current) => current ? { ...current, second_name: event.target.value } : current)}
                    value={formValues.second_name}
                  />
                </label>
                <label className="field">
                  <span>الاسم الأخير</span>
                  <input
                    onChange={(event) => setFormValues((current) => current ? { ...current, last_name: event.target.value } : current)}
                    required
                    value={formValues.last_name}
                  />
                </label>
              </div>

              <div className="grid-two">
                <label className="field">
                  <span>البريد الإلكتروني</span>
                  <input
                    onChange={(event) => setFormValues((current) => current ? { ...current, email: event.target.value } : current)}
                    required
                    type="email"
                    value={formValues.email}
                  />
                </label>
                <label className="field">
                  <span>رقم الجوال</span>
                  <input
                    onChange={(event) => setFormValues((current) => current ? { ...current, phone: event.target.value } : current)}
                    required
                    value={formValues.phone}
                  />
                </label>
              </div>

              <div className="grid-two">
                <label className="field">
                  <span>نوع الحساب</span>
                  <select
                    onChange={(event) =>
                      setFormValues((current) =>
                        current
                          ? {
                              ...current,
                              account_type: event.target.value
                            }
                          : current
                      )
                    }
                    required
                    value={formValues.account_type}
                  >
                    <option value="">اختر نوع الحساب</option>
                    {ACCOUNT_TYPES.map((accountType) => (
                      <option key={accountType.value} value={accountType.value}>
                        {accountType.label}
                      </option>
                    ))}
                  </select>
                </label>
                <label className="field">
                  <span>المرحلة</span>
                  <select
                    onChange={(event) =>
                      setFormValues((current) =>
                        current ? { ...current, stage: event.target.value, school_id: "" } : current
                      )
                    }
                    value={formValues.stage}
                  >
                    {STAGES.map((stage) => (
                      <option key={stage} value={stage}>
                        {stage}
                      </option>
                    ))}
                  </select>
                </label>
                <label className="field">
                  <span>المدرسة</span>
                  <select
                    onChange={(event) => setFormValues((current) => current ? { ...current, school_id: event.target.value } : current)}
                    required
                    value={formValues.school_id}
                  >
                    <option value="">اختر المدرسة</option>
                    {filteredSchools.map((school) => (
                      <option key={school.id} value={school.id}>
                        {school.name}
                      </option>
                    ))}
                  </select>
                </label>
              </div>

              <div className="button-row">
                <button className="button button-secondary" disabled={saving} type="submit">
                  حفظ تعديل البيانات
                </button>
                <button
                  className="button button-primary"
                  disabled={saving}
                  onClick={async () => {
                    setSaving(true);
                    setError(null);
                    setSuccessMessage(null);

                    try {
                      if (!formValues.account_type) {
                        setError("يجب اختيار نوع الحساب قبل الاعتماد.");
                        return;
                      }

                      await accountApprovalService.update(selectedRequest.id, toPayload(formValues));
                      await accountApprovalService.approve(selectedRequest.id);
                      setSuccessMessage(`تم اعتماد حساب: ${formValues.first_name} ${formValues.last_name}`);
                      setSelectedRequest(null);
                      await loadApprovals();
                    } catch (approveError) {
                      setError(getErrorMessage(approveError));
                    } finally {
                      setSaving(false);
                    }
                  }}
                  type="button"
                >
                  اعتماد الحساب
                </button>
              </div>
            </form>
          </section>
        </div>
      ) : null}
    </section>
  );
}
