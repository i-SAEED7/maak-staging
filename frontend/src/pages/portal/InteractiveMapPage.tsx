import { useEffect, useState } from "react";
import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { SchoolMapPanel } from "../../components/portal/SchoolMapPanel";
import { getErrorMessage } from "../../services/api";
import { portalService, type PortalSchool } from "../../services/portalService";

export function InteractiveMapPage() {
  const [schools, setSchools] = useState<PortalSchool[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);

    portalService
      .schools()
      .then((payload) => {
        setSchools(payload);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="الخريطة"
          title="الخريطة التفاعلية"
          description="استعراض مواقع المدارس المعتمدة داخل جدة بطريقة مرئية مبسطة."
        />

        {loading ? <div className="loading-box">جارٍ تحميل بيانات المدارس...</div> : null}
        {error ? <div className="error-box">{error}</div> : null}
        {!loading && !error ? <SchoolMapPanel schools={schools} /> : null}
      </section>
    </div>
  );
}
