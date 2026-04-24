import { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { FilePreviewPanel } from "../../components/common/FilePreviewPanel";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { FileUploader } from "../../components/common/FileUploader";
import { useSchoolSite } from "../../lib/schoolSite";
import { getErrorMessage } from "../../services/api";
import { fileService, type FileItem } from "../../services/fileService";

export function SchoolFilesPage() {
  const { school, schoolPath, currentSchoolId, canAccessFiles, canUploadFiles, canDeleteFiles } = useSchoolSite();
  const [rows, setRows] = useState<FileItem[]>([]);
  const [searchDraft, setSearchDraft] = useState("");
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(canAccessFiles);
  const [error, setError] = useState<string | null>(null);
  const [previewState, setPreviewState] = useState<{
    file: FileItem;
    url: string;
    expiresAt?: string | null;
  } | null>(null);

  async function loadFiles() {
    if (!canAccessFiles) {
      return;
    }

    setLoading(true);

    try {
      const payload = await fileService.list({
        "filter[school_id]": currentSchoolId,
        "filter[search]": search.trim() || undefined
      });
      setRows(payload.data);
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    void loadFiles();
  }, [canAccessFiles, currentSchoolId, search]);

  async function previewFile(file: FileItem) {
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
  }

  async function downloadFile(file: FileItem) {
    try {
      const payload = await fileService.temporaryLink(file.id, 30);
      window.open(payload.temporary_link.url, "_blank", "noopener");
      setError(null);
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    }
  }

  const columns: DataColumn<FileItem>[] = [
    { key: "name", label: "اسم الملف", render: (row) => row.original_name },
    { key: "category", label: "الفئة", render: (row) => row.category },
    { key: "uploader", label: "رافع الملف", render: (row) => row.uploader?.full_name ?? "-" },
    { key: "visibility", label: "الظهور", render: (row) => row.visibility },
    {
      key: "uploaded",
      label: "تاريخ الرفع",
      render: (row) => (row.uploaded_at ? new Date(row.uploaded_at).toLocaleString("ar-SA") : "-")
    },
    {
      key: "actions",
      label: "الإجراء",
      render: (row) => (
        <div className="button-row compact-actions">
          <button
            className="button button-secondary"
            onClick={() => void previewFile(row)}
            type="button"
          >
            معاينة
          </button>
          <button className="button button-secondary" onClick={() => void downloadFile(row)} type="button">
            تنزيل
          </button>
          {canDeleteFiles ? (
            <button
              className="button button-ghost"
              onClick={async () => {
                if (!window.confirm(`هل تريد حذف الملف: ${row.original_name}؟`)) {
                  return;
                }

                await fileService.delete(row.id);
                await loadFiles();
              }}
              type="button"
            >
              حذف
            </button>
          ) : null}
        </div>
      )
    }
  ];

  if (!canAccessFiles) {
    return <Navigate replace to={schoolPath} />;
  }

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">ملفات المدرسة</span>
          <h2>ملفات {school.name}</h2>
          <p>
            يتم هنا استعراض الملفات المرتبطة بالمدرسة الحالية فقط. أي رفع جديد سيُربط تلقائيًا بسياق
            هذه المدرسة.
          </p>
        </div>

        <div className="filters-bar">
          <label className="field">
            <span>بحث</span>
            <input
              onChange={(event) => setSearchDraft(event.target.value)}
              placeholder="ابحث باسم الملف أو الرافع"
              value={searchDraft}
            />
          </label>

          <div className="button-row filters-actions">
            <button className="button button-secondary" onClick={() => setSearch(searchDraft)} type="button">
              بحث
            </button>
          </div>
        </div>
      </section>

      {canUploadFiles ? (
        <section className="portal-surface page-stack">
          <div className="portal-section-heading">
            <span className="portal-eyebrow">رفع ملف</span>
            <h2>إضافة ملف جديد</h2>
          </div>

          <FileUploader
            onUploaded={async (formData) => {
              await fileService.upload(formData);
              await loadFiles();
            }}
          />
        </section>
      ) : null}

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
      {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد ملفات متاحة لهذه المدرسة." /> : null}
    </>
  );
}
