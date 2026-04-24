import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { serviceHighlights } from "../../lib/portalContent";

export function ServicesPage() {
  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="الخدمات"
          title="الخدمات الرئيسية داخل البوابة"
          description="مجموعة خدمات تعريفية وتشغيلية تربط المستخدمين بالمنصة وفق دورهم ونطاقهم."
        />

        <div className="portal-grid portal-grid-two">
          {serviceHighlights.map((service) => (
            <article className="portal-card" key={service.title}>
              <h3>{service.title}</h3>
              <p>{service.text}</p>
            </article>
          ))}
        </div>
      </section>
    </div>
  );
}
