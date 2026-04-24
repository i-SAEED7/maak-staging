import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";

export function AboutPage() {
  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="عن القسم"
          title="بوابة رسمية لقسم ذوي الإعاقة"
          description="تعريف مختصر بالدور التنظيمي والتقني للبوابة داخل منظومة التعليم بمحافظة جدة."
        />

        <div className="portal-grid portal-grid-two">
          <article className="portal-card">
            <h3>الرؤية</h3>
            <p>
              توفير بوابة حديثة وهادئة المظهر، تعكس هوية رسمية، وتقدم المعلومات والخدمات العامة
              والبرامج التعليمية بأسلوب واضح وقابل للتوسع.
            </p>
          </article>
          <article className="portal-card">
            <h3>الرسالة</h3>
            <p>
              فصل البوابة العامة عن نظام المدرسة الداخلي، مع الحفاظ على النواة التشغيلية للنظام
              الحالي، وتمهيد الطريق لمواقع مدارس مستقلة مستقبلًا.
            </p>
          </article>
        </div>
      </section>

      <section className="portal-surface">
        <div className="portal-grid portal-grid-three">
          <article className="portal-card">
            <h3>هوية رسمية</h3>
            <p>تصميم حديث مناسب لجهة تعليمية حكومية، بألوان متوازنة وتجربة استخدام هادئة.</p>
          </article>
          <article className="portal-card">
            <h3>ربط ذكي</h3>
            <p>تسجيل دخول موحد وتوجيه تلقائي للمستخدم نحو المدرسة أو النظام المركزي بحسب نطاقه.</p>
          </article>
          <article className="portal-card">
            <h3>مرونة مستقبلية</h3>
            <p>البوابة قابلة للتوسع لاحقًا لإضافة مواقع المدارس والقوالب الخاصة بها دون إعادة بناء النواة.</p>
          </article>
        </div>
      </section>
    </div>
  );
}
