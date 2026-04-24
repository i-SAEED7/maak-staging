import { useEffect, useState } from "react";
import { Navigate, useSearchParams } from "react-router-dom";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { FilePreviewPanel } from "../../components/common/FilePreviewPanel";
import { FileUploader } from "../../components/common/FileUploader";
import { getErrorMessage } from "../../services/api";
import { fileService, type FileItem } from "../../services/fileService";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import { useAuthStore } from "../../stores/authStore";

function translateFileCategory(category?: string | null) {
  const labels: Record<string, string> = {
    general: "عام",
    iep_attachment: "مرفق خطة",
    portfolio: "ملف إنجاز",
    student_report: "تقرير طالب",
    supervision: "إشراف تربوي"
  };

  if (!category) {
    return "-";
  }

  return labels[category] ?? "تصنيف ملف";
}

function translateFileVisibility(visibility?: string | null) {
  const labels: Record<string, string> = {
    private: "خاص",
    school: "مدرسي",
    public: "عام"
  };

  if (!visibility) {
    return "-";
  }

  return labels[visibility] ?? "مستوى وصول";
}

function FileActions({
  file,
  onChanged,
  onPreview,
  onDownload
}: {
  file: FileItem;
  onChanged: () => void;
  onPreview: (file: FileItem) => void;
  onDownload: (file: FileItem) => void;
}) {
  return (
    <div className="button-row">
      <button className="button button-secondary" onClick={() => onPreview(file)} type="button">
        معاينة
      </button>
      <button
        className="button button-secondary"
        onClick={() => onDownload(file)}
        type="button"
      >
        تنزيل
      </button>
      <button
        className="button button-ghost"
        onClick={async () => {
          await fileService.delete(file.id);
          onChanged();
        }}
        type="button"
      >
        حذف
      </button>
    </div>
  );
}

export function FilesPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const user = useAuthStore((state) => state.user);
  const permissions = useAuthStore((state) => state.permissions);
  const isSuperAdmin = user?.role === "super_admin";
  const canViewFiles = permissions.includes("*") || permissions.includes("files.view");

  if (user?.role === "supervisor" || !canViewFiles) {
    return <Navigate replace to="/app" />;
  }
  const [rows, setRows] = useState<FileItem[]>([]);
  const [schools, setSchools] = useState<SchoolItem[]>([]);
  const [schoolFilter, setSchoolFilter] = useState(searchParams.get("schoolId") ?? "");
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [previewState, setPreviewState] = useState<{
    file: FileItem;
    url: string;
    expiresAt?: string | null;
  } | null>(null);

  const loadFiles = async () => {
    setLoading(true);

    try {
      const payload = await fileService.list({
        "filter[school_id]": schoolFilter || undefined,
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
    void loadFiles();
  }, [schoolFilter, search]);

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    void schoolService
      .list({ perPage: 100 })
      .then((payload) => setSchools(payload.data))
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [isSuperAdmin]);

  const openPreview = async (file: FileItem) => {
    try {
      const payload = await fileService.temporaryLink(file.id, 30);
      setPreviewState({
        file,
        url: payload.temporary_link.url,
        expiresAt: payload.temporary_link.expires_at
      });
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    }
  };

  const downloadFile = async (file: FileItem) => {
    try {
      const payload = await fileService.temporaryLink(file.id, 30);
      window.open(payload.temporary_link.url, "_blank", "noopener");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    }
  };

  const columns: DataColumn<FileItem>[] = [
    { key: "name", label: "الاسم", render: (row) => row.original_name },
    { key: "school", label: "المدرسة", render: (row) => row.school?.name_ar ?? "-" },
    { key: "category", label: "الفئة", render: (row) => translateFileCategory(row.category) },
    { key: "uploader", label: "اسم رافع الملف", render: (row) => row.uploader?.full_name ?? "-" },
    { key: "visibility", label: "الظهور", render: (row) => translateFileVisibility(row.visibility) },
    { key: "size", label: "الحجم", render: (row) => `${row.size_bytes} بايت` },
    { key: "sensitive", label: "حساس", render: (row) => (row.is_sensitive ? "نعم" : "لا") },
    {
      key: "uploaded",
      label: "الرفع",
      render: (row) => (row.uploaded_at ? new Date(row.uploaded_at).toLocaleString("ar-SA") : "-")
    },
    {
      key: "actions",
      label: "إجراء",
      render: (row) => (
        <FileActions
          file={row}
          onChanged={() => void loadFiles()}
          onDownload={(file) => void downloadFile(file)}
          onPreview={(file) => void openPreview(file)}
        />
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">الملفات</span>
          <h2>الملفات</h2>
        </div>
      </div>

      <div className="filters-bar filters-bar-wide">
        {isSuperAdmin ? (
          <label className="field">
            <span>فلترة حسب المدرسة</span>
            <select
              onChange={(event) => {
                const value = event.target.value;
                setSchoolFilter(value);
                setSearchParams((current) => {
                  const next = new URLSearchParams(current);

                  if (value) {
                    next.set("schoolId", value);
                  } else {
                    next.delete("schoolId");
                  }

                  return next;
                });
              }}
              value={schoolFilter}
            >
              <option value="">كل المدارس</option>
              {schools.map((school) => (
                <option key={school.id} value={school.id}>
                  {school.name}
                </option>
              ))}
            </select>
          </label>
        ) : null}

        <label className="field">
          <span>بحث</span>
          <input
            onChange={(event) => setSearch(event.target.value)}
            placeholder="ابحث باسم الملف أو المدرسة أو الرافع"
            value={search}
          />
        </label>
      </div>

      <section className="surface-card">
        <h3>رفع ملف جديد</h3>
        <FileUploader
          onUploaded={async (formData) => {
            await fileService.upload(formData);
            await loadFiles();
          }}
        />
      </section>

      {error ? <div className="error-box">{error}</div> : null}
      {loading ? <div className="loading-box">جارٍ تحميل الملفات...</div> : null}
      {previewState ? (
        <FilePreviewPanel
          expiresAt={previewState.expiresAt}
          file={previewState.file}
          onClose={() => setPreviewState(null)}
          onDownload={() => void downloadFile(previewState.file)}
          previewUrl={previewState.url}
        />
      ) : null}
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد ملفات حالية." /> : null}
    </section>
  );
}
