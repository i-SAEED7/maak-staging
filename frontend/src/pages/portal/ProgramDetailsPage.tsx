import { Link, Navigate, useParams } from "react-router-dom";
import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { programDetails } from "../../lib/portalContent";

export function ProgramDetailsPage() {
  const { programSlug } = useParams();

  if (!programSlug || !(programSlug in programDetails)) {
    return <Navigate replace to="/" />;
  }

  const content = programDetails[programSlug as keyof typeof programDetails];

  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="البرامج"
          title={content.title}
          description={content.subtitle}
        />

        <div className="portal-grid portal-grid-two">
          {content.points.map((point) => (
            <article className="portal-card" key={point}>
              <p>{point}</p>
            </article>
          ))}
        </div>

        <div className="portal-button-row">
          <Link className="portal-button portal-button-primary" to="/login">
            تسجيل الدخول
          </Link>
          <Link className="portal-button portal-button-secondary" to="/">
            العودة إلى الرئيسية
          </Link>
        </div>
      </section>
    </div>
  );
}
