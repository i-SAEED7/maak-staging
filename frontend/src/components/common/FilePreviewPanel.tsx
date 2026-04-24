import type { FileItem } from "../../services/fileService";

type FilePreviewPanelProps = {
  file: FileItem;
  previewUrl: string;
  expiresAt?: string | null;
  onClose: () => void;
  onDownload: () => void;
};

function resolvePreviewMode(file: FileItem) {
  const mime = file.mime_type?.toLowerCase() ?? "";
  const extension = file.extension?.toLowerCase() ?? "";

  if (mime.startsWith("image/")) {
    return "image";
  }

  if (mime.startsWith("video/")) {
    return "video";
  }

  if (mime.startsWith("audio/")) {
    return "audio";
  }

  if (
    mime === "application/pdf" ||
    mime.startsWith("text/") ||
    mime.includes("json") ||
    mime.includes("xml") ||
    ["pdf", "txt", "json", "xml", "csv", "html"].includes(extension)
  ) {
    return "frame";
  }

  return "unsupported";
}

export function FilePreviewPanel({
  file,
  previewUrl,
  expiresAt,
  onClose,
  onDownload
}: FilePreviewPanelProps) {
  const previewMode = resolvePreviewMode(file);

  return (
    <section className="surface-card page-stack file-preview-panel">
      <div className="page-header">
        <div>
          <span className="eyebrow">Preview</span>
          <h3>معاينة الملف</h3>
          <p className="section-description">
            {file.original_name}
            {expiresAt ? ` | ينتهي الرابط: ${new Date(expiresAt).toLocaleString("ar-SA")}` : ""}
          </p>
        </div>

        <div className="button-row">
          <button className="button button-secondary" onClick={onDownload} type="button">
            تنزيل
          </button>
          <button className="button button-ghost" onClick={onClose} type="button">
            إغلاق المعاينة
          </button>
        </div>
      </div>

      {previewMode === "image" ? (
        <div className="file-preview-frame file-preview-image-wrapper">
          <img alt={file.original_name} className="file-preview-image" src={previewUrl} />
        </div>
      ) : null}

      {previewMode === "video" ? (
        <div className="file-preview-frame">
          <video className="file-preview-video" controls src={previewUrl} />
        </div>
      ) : null}

      {previewMode === "audio" ? (
        <div className="file-preview-audio">
          <audio controls src={previewUrl} />
        </div>
      ) : null}

      {previewMode === "frame" ? (
        <iframe className="file-preview-frame" src={previewUrl} title={`معاينة ${file.original_name}`} />
      ) : null}

      {previewMode === "unsupported" ? (
        <div className="info-box">
          هذا النوع من الملفات لا يدعم المعاينة المباشرة داخل النظام حاليًا. يمكنك تنزيل الملف من
          الزر أعلاه.
        </div>
      ) : null}
    </section>
  );
}
