import type { FileItem } from "../../services/fileService";
import { useEffect, useState } from "react";

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
  const isTextPreview =
    previewMode === "frame" &&
    ((file.mime_type?.toLowerCase().startsWith("text/") ?? false) ||
      ["txt", "csv", "json", "xml", "html"].includes(file.extension?.toLowerCase() ?? ""));
  const [textContent, setTextContent] = useState<string | null>(null);
  const [textError, setTextError] = useState<string | null>(null);

  useEffect(() => {
    if (!isTextPreview) {
      setTextContent(null);
      setTextError(null);
      return;
    }

    let isActive = true;

    fetch(previewUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error("تعذر تحميل محتوى الملف النصي.");
        }

        return response.text();
      })
      .then((content) => {
        if (isActive) {
          setTextContent(content);
          setTextError(null);
        }
      })
      .catch(() => {
        if (isActive) {
          setTextError("تعذر عرض محتوى الملف النصي داخل المعاينة.");
        }
      });

    return () => {
      isActive = false;
    };
  }, [isTextPreview, previewUrl]);

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

      {isTextPreview ? (
        <pre className="file-preview-frame file-preview-text">
          {textError ?? textContent ?? "جارٍ تحميل محتوى الملف النصي..."}
        </pre>
      ) : null}

      {previewMode === "frame" && !isTextPreview ? (
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
