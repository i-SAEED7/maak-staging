import { Link, Navigate, useParams } from "react-router-dom";
import { programDetails } from "../../lib/portalContent";
import { useSchoolSite } from "../../lib/schoolSite";

export function SchoolProgramDetailsPage() {
  const { programSlug } = useParams();
  const { school, schoolPath } = useSchoolSite();

  if (!programSlug || !(programSlug in programDetails)) {
    return <Navigate replace to={`${schoolPath}/programs`} />;
  }

  const content = programDetails[programSlug as keyof typeof programDetails];

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="page-header">
          <div className="portal-section-heading">
            <span className="portal-eyebrow">تفاصيل البرنامج</span>
            <h2>{content.title}</h2>
            <p>{content.subtitle}</p>
          </div>

          <div className="portal-button-row">
            <Link className="portal-button portal-button-secondary" to={`${schoolPath}/programs`}>
              العودة إلى البرامج
            </Link>
          </div>
        </div>

        <div className="portal-grid portal-grid-two">
          <article className="portal-card">
            <span className="portal-chip">الوصف العام</span>
            <h3>نظرة عامة</h3>
            <p>{content.overview}</p>
          </article>

          <article className="portal-card">
            <span className="portal-chip">المدرسة الحالية</span>
            <h3>{school.name}</h3>
            <p>
              المرحلة: {school.stage ?? "-"}
              <br />
              البرنامج المعتمد: {school.program_type ?? "-"}
            </p>
          </article>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-grid portal-grid-three">
          <article className="portal-card">
            <h3>أهداف البرنامج</h3>
            <div className="detail-list">
              {content.goals.map((goal) => (
                <div className="detail-list-item" key={goal}>
                  <strong>{goal}</strong>
                </div>
              ))}
            </div>
          </article>

          <article className="portal-card">
            <h3>الخدمات المرتبطة</h3>
            <div className="detail-list">
              {content.services.map((service) => (
                <div className="detail-list-item" key={service}>
                  <strong>{service}</strong>
                </div>
              ))}
            </div>
          </article>

          <article className="portal-card">
            <h3>الفئات المستفيدة</h3>
            <div className="detail-list">
              {content.beneficiaries.map((beneficiary) => (
                <div className="detail-list-item" key={beneficiary}>
                  <strong>{beneficiary}</strong>
                </div>
              ))}
            </div>
          </article>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">التطبيق داخل المدرسة</span>
          <h2>كيف يُستخدم هذا البرنامج داخل {school.name}</h2>
        </div>

        <div className="portal-grid portal-grid-two">
          {content.schoolUseCases.map((item) => (
            <article className="portal-card" key={item}>
              <p>{item}</p>
            </article>
          ))}
        </div>
      </section>
    </>
  );
}
