import { Link } from "react-router-dom";
import { useSchoolSite } from "../../lib/schoolSite";

type ServiceCard = {
  to: string;
  title: string;
  text: string;
};

function buildServiceCards(context: ReturnType<typeof useSchoolSite>): ServiceCard[] {
  const cards: ServiceCard[] = [];

  if (context.canViewStudents && !context.isParent) {
    cards.push({
      to: "/app/students",
      title: "الطلاب",
      text: "استعراض الطلاب وإدارة بياناتهم ضمن نطاق المدرسة الحالية."
    });
  }

  if (context.canViewPlans) {
    cards.push({
      to: "/app/iep-plans",
      title: "الخطط الفردية",
      text: "الدخول إلى الخطط الفردية الخاصة بهذه المدرسة بحسب صلاحيات الدور الحالي."
    });
  }

  if (context.canSendMessages) {
    cards.push({
      to: "/app/messages",
      title: "الرسائل",
      text: "إرسال الرسائل الداخلية ومتابعة المحادثات المرتبطة بالمدرسة."
    });
  }

  if (context.canViewReports) {
    cards.push({
      to: "/app/reports",
      title: "التقارير",
      text: context.isParent
        ? "استعراض تقارير الابن ومتابعة التقدم داخل المدرسة."
        : "استعراض ملخصات المدرسة أو التقارير المرتبطة بالدور الحالي."
    });
  }

  if (context.canAccessFiles) {
    cards.push({
      to: `${context.schoolPath}/files`,
      title: "ملفات المدرسة",
      text: "الوصول إلى الملفات من داخل موقع المدرسة دون مغادرة السياق الحالي."
    });
  }

  if (context.canManageAnnouncements) {
    cards.push({
      to: "/app/announcements",
      title: "إدارة الإعلانات",
      text: "إنشاء الإعلانات أو تعديلها على مستوى المدرسة من النظام المركزي."
    });
  }

  return cards;
}

export function SchoolServicesPage() {
  const context = useSchoolSite();
  const cards = buildServiceCards(context);

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">الخدمات</span>
          <h2>الخدمات المتاحة داخل المدرسة</h2>
          <p>
            تمثل هذه الصفحة طبقة توجيه وظيفية بين موقع المدرسة وبين النظام المركزي، مع الحفاظ على
            صلاحيات الدور الحالي وسياق المدرسة المختارة.
          </p>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-grid portal-grid-two">
          {cards.map((card) => (
            <Link className="portal-card portal-card-link" key={card.to} to={card.to}>
              <h3>{card.title}</h3>
              <p>{card.text}</p>
            </Link>
          ))}
        </div>
      </section>
    </>
  );
}
