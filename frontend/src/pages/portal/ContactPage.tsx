import { useState } from "react";
import { PortalSectionHeading } from "../../components/portal/PortalSectionHeading";
import { contactCards } from "../../lib/portalContent";

export function ContactPage() {
  const [sent, setSent] = useState(false);

  return (
    <div className="portal-page-stack">
      <section className="portal-surface">
        <PortalSectionHeading
          eyebrow="تواصل معنا"
          title="قنوات التواصل"
          description="يمكنك استخدام بيانات التواصل أو إرسال رسالة مباشرة عبر النموذج التالي."
        />

        <div className="portal-grid portal-grid-three">
          {contactCards.map((card) => (
            <article className="portal-card" key={card.title}>
              <h3>{card.title}</h3>
              <p>{card.value}</p>
            </article>
          ))}
        </div>
      </section>

      <section className="portal-surface">
        <div className="portal-grid portal-grid-two">
          <form
            className="portal-form"
            onSubmit={(event) => {
              event.preventDefault();
              setSent(true);
            }}
          >
            <label className="field">
              <span>الاسم</span>
              <input placeholder="الاسم الكامل" required />
            </label>
            <label className="field">
              <span>البريد الإلكتروني</span>
              <input placeholder="name@example.com" required type="email" />
            </label>
            <label className="field">
              <span>الرسالة</span>
              <textarea placeholder="اكتب رسالتك هنا" required rows={6} />
            </label>
            <button className="portal-button portal-button-primary" type="submit">
              إرسال الرسالة
            </button>
            {sent ? <div className="info-box">تم تجهيز نموذج التواصل مبدئيًا وسيتم ربطه تشغيليًا لاحقًا.</div> : null}
          </form>

          <article className="portal-card portal-contact-note">
            <h3>ملاحظات</h3>
            <p>
              هذه الصفحة مهيأة الآن كواجهة رسمية متناسقة مع البوابة. ويمكن لاحقًا ربطها بمسار
              بريد إلكتروني أو بنظام تذاكر داخلي دون إعادة تصميم الصفحة.
            </p>
          </article>
        </div>
      </section>
    </div>
  );
}
