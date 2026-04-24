import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { AnnouncementSlider } from "../../components/portal/AnnouncementSlider";
import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { SchoolMapPanel } from "../../components/portal/SchoolMapPanel";
import {
  heroSlides,
  inspirationalQuotes,
  portalCards,
  programDetails
} from "../../lib/portalContent";
import { getErrorMessage } from "../../services/api";
import { portalService, type PortalProgram, type PortalSchool, type PortalStatistics } from "../../services/portalService";

export function HomePage() {
  const [programs, setPrograms] = useState<PortalProgram[]>([]);
  const [schools, setSchools] = useState<PortalSchool[]>([]);
  const [statistics, setStatistics] = useState<PortalStatistics | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    setLoading(true);

    Promise.all([portalService.programs(), portalService.schools(), portalService.statistics()])
      .then(([programsPayload, schoolsPayload, statisticsPayload]) => {
        setPrograms(programsPayload);
        setSchools(schoolsPayload);
        setStatistics(statisticsPayload);
        setError(null);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="portal-page-stack">
      <section className="portal-hero">
        <AnnouncementSlider slides={heroSlides} />
      </section>

      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="البرامج"
          title="البرامج التعليمية الحالية"
          description="عرض سريع للبرامج الأساسية المعتمدة حاليًا داخل البوابة."
        />

        <div className="portal-grid portal-grid-two">
          {programs.map((program) => {
            const programPath = program.code === "yasir_learning" ? "/programs/yasir" : "/programs/adhd";
            const fallbackContent =
              program.code === "yasir_learning" ? programDetails.yasir : programDetails.adhd;

            return (
              <article className="portal-card" key={program.id}>
                <span className="portal-chip">{program.code === "yasir_learning" ? "يسير" : "فرط الحركة"}</span>
                <h3>{program.name_ar}</h3>
                <p>{program.description ?? fallbackContent.subtitle}</p>
                <Link className="portal-button portal-button-primary" to={programPath}>
                  للمزيد
                </Link>
              </article>
            );
          })}
        </div>
      </section>

      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="الرؤية"
          title="عبارات ملهمة"
          description="مختارات تعبّر عن التوجه نحو تعليم نوعي أكثر شمولًا وتمكينًا."
        />

        <div className="portal-grid portal-grid-two">
          {inspirationalQuotes.map((quote) => (
            <article className="portal-quote-card" key={quote.source}>
              <p>{quote.text}</p>
              <strong>{quote.source}</strong>
            </article>
          ))}
        </div>
      </section>

      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="الخريطة"
          title="خريطة المدارس"
          description="عرض بصري أولي لمواقع المدارس على امتداد مدينة جدة."
        />

        {loading ? <div className="loading-box">جارٍ تحميل الخريطة...</div> : null}
        {error ? <div className="error-box">{error}</div> : null}
        {!loading && !error ? <SchoolMapPanel compact schools={schools} /> : null}
      </section>

      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="أقسام رئيسية"
          title="استكشف البوابة"
          description="وصول سريع إلى الصفحات الرئيسية داخل البوابة."
        />

        <div className="portal-grid portal-grid-four">
          {portalCards.map((card) => (
            <Link className="portal-card portal-card-link" key={card.to} to={card.to}>
              <h3>{card.title}</h3>
              <p>{card.text}</p>
            </Link>
          ))}
        </div>
      </section>

      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="إحصائيات"
          title="أرقام مختصرة"
          description="ملخص رقمي سريع لنطاق الخدمات والمدارس والبرامج."
        />

        {loading ? <div className="loading-box">جارٍ تحميل الإحصائيات...</div> : null}
        {!loading && statistics ? (
          <div className="portal-grid portal-grid-four">
            <article className="portal-stat-card">
              <span>المدارس</span>
              <strong>{statistics.schools_count}</strong>
            </article>
            <article className="portal-stat-card">
              <span>البرامج</span>
              <strong>{statistics.programs_count}</strong>
            </article>
            <article className="portal-stat-card">
              <span>الطلاب</span>
              <strong>{statistics.students_count}</strong>
            </article>
            <article className="portal-stat-card">
              <span>المعلمون</span>
              <strong>{statistics.teachers_count}</strong>
            </article>
          </div>
        ) : null}
      </section>
    </div>
  );
}
