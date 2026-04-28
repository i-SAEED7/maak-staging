import { useEffect, useState } from "react";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { educationProgramService, type EducationProgramOption } from "../../services/educationProgramService";
import {
  schoolService,
  type PaginatedMeta,
  type SchoolFormPayload,
  type SchoolItem
} from "../../services/schoolService";
import { userService, type UserOption } from "../../services/userService";
import { useAuthStore } from "../../stores/authStore";

type SchoolFormValues = {
  name: string;
  stage: string;
  program_types: string[];
  location_lat: string;
  location_lng: string;
  principal_id: string;
  supervisor_id: string;
};

const STAGE_OPTIONS = ["ابتدائي", "متوسط", "ثانوي", "متعدد المراحل"];
const GENDER_OPTIONS = ["", "بنين", "بنات", "غير محدد"];

const defaultFormValues: SchoolFormValues = {
  name: "",
  stage: STAGE_OPTIONS[0],
  program_types: [],
  location_lat: "",
  location_lng: "",
  principal_id: "",
  supervisor_id: ""
};

const initialMeta: PaginatedMeta = {
  page: 1,
  per_page: 10,
  total: 0,
  last_page: 1
};

function toFormValues(school: SchoolItem): SchoolFormValues {
  const programTypes = school.program_types?.length
    ? school.program_types
    : (school.program_type ?? "")
        .split(/،|,/)
        .map((program) => program.trim())
        .filter(Boolean);

  return {
    name: school.name ?? "",
    stage: school.stage ?? STAGE_OPTIONS[0],
    program_types: programTypes,
    location_lat: school.location_lat === null ? "" : String(school.location_lat),
    location_lng: school.location_lng === null ? "" : String(school.location_lng),
    principal_id: school.principal_id === null ? "" : String(school.principal_id),
    supervisor_id: school.supervisor_id === null ? "" : String(school.supervisor_id)
  };
}

function toPayload(values: SchoolFormValues): SchoolFormPayload {
  return {
    name: values.name.trim(),
    stage: values.stage,
    program_type: values.program_types[0] ?? "",
    program_types: values.program_types,
    location_lat: values.location_lat.trim() === "" ? null : values.location_lat.trim(),
    location_lng: values.location_lng.trim() === "" ? null : values.location_lng.trim(),
    principal_id: values.principal_id ? Number(values.principal_id) : null,
    supervisor_id: values.supervisor_id ? Number(values.supervisor_id) : null
  };
}

function buildPageNumbers(currentPage: number, lastPage: number) {
  const pages: number[] = [];
  const start = Math.max(1, currentPage - 2);
  const end = Math.min(lastPage, currentPage + 2);

  for (let page = start; page <= end; page += 1) {
    pages.push(page);
  }

  return pages;
}

export function SchoolsPage() {
  const user = useAuthStore((state) => state.user);
  const isSuperAdmin = user?.role === "super_admin";
  const [rows, setRows] = useState<SchoolItem[]>([]);
  const [meta, setMeta] = useState<PaginatedMeta>(initialMeta);
  const [page, setPage] = useState(1);
  const [searchInput, setSearchInput] = useState("");
  const [searchQuery, setSearchQuery] = useState("");
  const [stageFilter, setStageFilter] = useState("");
  const [genderFilter, setGenderFilter] = useState("");
  const [principalOptions, setPrincipalOptions] = useState<UserOption[]>([]);
  const [supervisorOptions, setSupervisorOptions] = useState<UserOption[]>([]);
  const [programOptions, setProgramOptions] = useState<EducationProgramOption[]>([]);
  const [formValues, setFormValues] = useState<SchoolFormValues>(defaultFormValues);
  const [editingSchool, setEditingSchool] = useState<SchoolItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [optionsLoading, setOptionsLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [tableError, setTableError] = useState<string | null>(null);
  const [formError, setFormError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const buildDefaultFormValues = () => ({
    ...defaultFormValues,
    program_types: programOptions[0]?.name_ar ? [programOptions[0].name_ar] : []
  });

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    const loadOptions = async () => {
      setOptionsLoading(true);

      try {
        const [principals, supervisors, programs] = await Promise.all([
          userService.listByRole("principal"),
          userService.listByRole("supervisor"),
          educationProgramService.list()
        ]);

        setPrincipalOptions(principals);
        setSupervisorOptions(supervisors);
        setProgramOptions(programs);
        setFormValues((current) => ({
          ...current,
          program_types: current.program_types.length ? current.program_types : programs[0]?.name_ar ? [programs[0].name_ar] : []
        }));
      } catch (error) {
        setFormError(getErrorMessage(error));
      } finally {
        setOptionsLoading(false);
      }
    };

    void loadOptions();
  }, [isSuperAdmin]);

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    const loadSchools = async () => {
      setLoading(true);
      setTableError(null);

      try {
        const payload = await schoolService.list({
          name: searchQuery,
          stage: stageFilter || undefined,
          gender: genderFilter || undefined,
          page,
          perPage: 10
        });

        setRows(payload.data);
        setMeta(payload.meta);
      } catch (error) {
        setTableError(getErrorMessage(error));
      } finally {
        setLoading(false);
      }
    };

    void loadSchools();
  }, [genderFilter, isSuperAdmin, page, searchQuery, stageFilter]);

  if (!isSuperAdmin) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب السوبر أدمن فقط.</div>
      </section>
    );
  }

  const columns: DataColumn<SchoolItem>[] = [
    {
      key: "name",
      label: "اسم المدرسة",
      render: (row) => (
        <div className="table-primary-cell">
          <strong>{row.name}</strong>
          <span className="table-subtext">الكود: {row.official_code}</span>
        </div>
      )
    },
    {
      key: "program_type",
      label: "البرنامج",
      render: (row) => row.program_type ?? "-"
    },
    {
      key: "stage",
      label: "المرحلة",
      render: (row) => row.stage ?? "-"
    },
    {
      key: "status",
      label: "الحالة",
      render: (row) => (
        <span className={`status-pill${row.status === "inactive" ? " status-pill-inactive" : ""}`}>
          {row.status === "active" ? "نشطة" : "غير نشطة"}
        </span>
      )
    },
    {
      key: "teachers_count",
      label: "عدد المعلمين",
      render: (row) => row.teachers_count
    },
    {
      key: "students_count",
      label: "عدد الطلاب",
      render: (row) => row.students_count
    },
    {
      key: "actions",
      label: "الإجراءات",
      render: (row) => (
        <div className="button-row compact-actions">
          <button
            className="button button-secondary"
            onClick={() => {
              setEditingSchool(row);
              setFormValues(toFormValues(row));
              setFormError(null);
              setSuccessMessage(null);
            }}
            type="button"
          >
            تعديل
          </button>
          <button
            className={`button ${row.status === "active" ? "button-ghost" : "button-primary"}`}
            onClick={async () => {
              setFormError(null);
              setSuccessMessage(null);

              try {
                if (row.status === "active") {
                  await schoolService.deactivate(row.id);
                  setSuccessMessage(`تم تعطيل المدرسة: ${row.name}`);
                } else {
                  await schoolService.update(row.id, {
                    ...toPayload(toFormValues(row)),
                    status: "active"
                  });
                  setSuccessMessage(`تم تفعيل المدرسة: ${row.name}`);
                }

                const payload = await schoolService.list({
                  name: searchQuery,
                  stage: stageFilter || undefined,
                  gender: genderFilter || undefined,
                  page,
                  perPage: 10
                });

                setRows(payload.data);
                setMeta(payload.meta);
              } catch (error) {
                setFormError(getErrorMessage(error));
              }
            }}
            type="button"
          >
            {row.status === "active" ? "تعطيل" : "تفعيل"}
          </button>
        </div>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">المشرف العام</span>
          <h2>إدارة المدارس</h2>
          <p className="section-description">
            إدارة المدارس الأساسية وربط المدير والمشرف عبر معرف الحساب فقط، مع حفظ
            الإحداثيات للاستخدام المستقبلي في الخريطة.
          </p>
        </div>
        <button
          className="button button-primary"
          onClick={() => {
            setEditingSchool(null);
            setFormValues(buildDefaultFormValues());
            setFormError(null);
            setSuccessMessage(null);
          }}
          type="button"
        >
          إضافة مدرسة
        </button>
      </div>

      {tableError ? <div className="error-box">{tableError}</div> : null}
      {formError ? <div className="error-box">{formError}</div> : null}
      {successMessage ? <div className="info-box">{successMessage}</div> : null}

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">الجدول</span>
            <h3>استعراض المدارس</h3>
          </div>
          <div className="table-summary">
            <span>الإجمالي: {meta.total}</span>
            <span>الصفحة: {meta.page} / {meta.last_page}</span>
          </div>
        </div>

        <form
          className="filters-bar"
          onSubmit={(event) => {
            event.preventDefault();
            setPage(1);
            setSearchQuery(searchInput.trim());
          }}
        >
          <label className="field">
            <span>اسم المدرسة</span>
            <input
              onChange={(event) => setSearchInput(event.target.value)}
              placeholder="ابحث عن مدرسة..."
              value={searchInput}
            />
          </label>

          <label className="field">
            <span>المرحلة</span>
            <select onChange={(event) => setStageFilter(event.target.value)} value={stageFilter}>
              <option value="">كل المراحل</option>
              {STAGE_OPTIONS.map((option) => (
                <option key={option} value={option}>
                  {option}
                </option>
              ))}
            </select>
          </label>

          <label className="field">
            <span>الجنس</span>
            <select onChange={(event) => setGenderFilter(event.target.value)} value={genderFilter}>
              <option value="">كل الأنواع</option>
              {GENDER_OPTIONS.filter(Boolean).map((option) => (
                <option key={option} value={option}>
                  {option}
                </option>
              ))}
            </select>
          </label>

          <div className="button-row filters-actions">
            <button className="button button-secondary" type="submit">
              تطبيق البحث
            </button>
            <button
              className="button button-ghost"
              onClick={() => {
                setSearchInput("");
                setSearchQuery("");
                setStageFilter("");
                setGenderFilter("");
                setPage(1);
              }}
              type="button"
            >
              إعادة تعيين
            </button>
          </div>
        </form>

        {loading ? <div className="loading-box">جارٍ تحميل المدارس...</div> : null}
        {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد مدارس مطابقة." /> : null}

        <div className="pagination-bar">
          <button
            className="button button-secondary"
            disabled={meta.page <= 1}
            onClick={() => setPage((current) => Math.max(1, current - 1))}
            type="button"
          >
            السابق
          </button>

          <div className="pagination-pages">
            {buildPageNumbers(meta.page, meta.last_page).map((pageNumber) => (
              <button
                className={`button ${pageNumber === meta.page ? "button-primary" : "button-secondary"}`}
                key={pageNumber}
                onClick={() => setPage(pageNumber)}
                type="button"
              >
                {pageNumber}
              </button>
            ))}
          </div>

          <button
            className="button button-secondary"
            disabled={meta.page >= meta.last_page}
            onClick={() => setPage((current) => Math.min(meta.last_page, current + 1))}
            type="button"
          >
            التالي
          </button>
        </div>
      </section>

      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">{editingSchool ? "تعديل" : "إنشاء"}</span>
            <h3>{editingSchool ? `تعديل مدرسة: ${editingSchool.name}` : "إضافة مدرسة جديدة"}</h3>
          </div>
        </div>

        <form
          className="page-stack"
          onSubmit={async (event) => {
            event.preventDefault();
            setSubmitting(true);
            setFormError(null);
            setSuccessMessage(null);

            try {
              if (formValues.program_types.length === 0) {
                setFormError("يجب اختيار برنامج واحد على الأقل.");
                setSubmitting(false);
                return;
              }

              const payload = toPayload(formValues);

              if (editingSchool) {
                await schoolService.update(editingSchool.id, payload);
                setSuccessMessage("تم تحديث المدرسة بنجاح.");
              } else {
                await schoolService.create(payload);
                setSuccessMessage("تم إنشاء المدرسة بنجاح.");
              }

              setEditingSchool(null);
              setFormValues(buildDefaultFormValues());
              setPage(1);

              const refreshed = await schoolService.list({
                name: searchQuery,
                stage: stageFilter || undefined,
                gender: genderFilter || undefined,
                page: 1,
                perPage: 10
              });

              setRows(refreshed.data);
              setMeta(refreshed.meta);
            } catch (error) {
              setFormError(getErrorMessage(error));
            } finally {
              setSubmitting(false);
            }
          }}
        >
          <div className="grid-two">
            <label className="field">
              <span>اسم المدرسة</span>
              <input
                onChange={(event) => setFormValues((current) => ({ ...current, name: event.target.value }))}
                required
                value={formValues.name}
              />
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>المرحلة</span>
              <select
                onChange={(event) => setFormValues((current) => ({ ...current, stage: event.target.value }))}
                value={formValues.stage}
              >
                {STAGE_OPTIONS.map((option) => (
                  <option key={option} value={option}>
                    {option}
                  </option>
                ))}
              </select>
            </label>

            <label className="field">
              <span>نوع البرنامج</span>
              <div className="selection-list selection-list-compact">
                {programOptions.map((option) => {
                  const isChecked = formValues.program_types.includes(option.name_ar);

                  return (
                    <label className="selection-option" key={option.id}>
                      <input
                        checked={isChecked}
                        onChange={(event) => {
                          setFormValues((current) => ({
                            ...current,
                            program_types: event.target.checked
                              ? Array.from(new Set([...current.program_types, option.name_ar]))
                              : current.program_types.filter((programName) => programName !== option.name_ar)
                          }));
                        }}
                        type="checkbox"
                      />
                      <span>{option.name_ar}</span>
                    </label>
                  );
                })}
              </div>
              <small className="field-hint">يمكن اختيار أكثر من برنامج للمدرسة الواحدة.</small>
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>خط العرض</span>
              <input
                onChange={(event) =>
                  setFormValues((current) => ({ ...current, location_lat: event.target.value }))
                }
                placeholder="24.7135517"
                value={formValues.location_lat}
              />
            </label>

            <label className="field">
              <span>خط الطول</span>
              <input
                onChange={(event) =>
                  setFormValues((current) => ({ ...current, location_lng: event.target.value }))
                }
                placeholder="46.6752957"
                value={formValues.location_lng}
              />
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>مدير المدرسة</span>
              <select
                disabled={optionsLoading}
                onChange={(event) =>
                  setFormValues((current) => ({ ...current, principal_id: event.target.value }))
                }
                value={formValues.principal_id}
              >
                <option value="">اختر مدير المدرسة</option>
                {principalOptions.map((option) => (
                  <option key={option.id} value={option.id}>
                    {option.full_name}
                  </option>
                ))}
              </select>
            </label>

            <label className="field">
              <span>المشرف التربوي</span>
              <select
                disabled={optionsLoading}
                onChange={(event) =>
                  setFormValues((current) => ({ ...current, supervisor_id: event.target.value }))
                }
                value={formValues.supervisor_id}
              >
                <option value="">اختر المشرف التربوي</option>
                {supervisorOptions.map((option) => (
                  <option key={option.id} value={option.id}>
                    {option.full_name}
                  </option>
                ))}
              </select>
            </label>
          </div>

          <div className="button-row">
            <button className="button button-primary" disabled={submitting} type="submit">
              {submitting ? "جارٍ الحفظ..." : editingSchool ? "حفظ التعديلات" : "إنشاء المدرسة"}
            </button>
            <button
              className="button button-ghost"
              onClick={() => {
                setEditingSchool(null);
                setFormValues(buildDefaultFormValues());
                setFormError(null);
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
