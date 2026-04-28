import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { Sparkles } from "lucide-react";
import { useSchoolSite } from "../../lib/schoolSite";
import { announcementService, type AnnouncementItem } from "../../services/announcementService";
import { getErrorMessage } from "../../services/api";
import {
  inspirationalQuoteService,
  type InspirationalQuote
} from "../../services/inspirationalQuoteService";

type SchoolQuickLink = {
  to: string;
  title: string;
  text: string;
};

const defaultInspirationalQuote: InspirationalQuote = {
  id: 0,
  title: "عبارة سمو ولي العهد الأمير محمد بن سلمان حفظه الله",
  body:
    "وسنمكّن أبنائنا من ذوي الإعاقة من الحصول على فرص عمل مناسبة وتعليم يضمن استقلاليتهم واندماجهم بوصفهم عناصر فاعلة في المجتمع، كما سنمدهم بكل التسهيلات والأدوات التي تساعدهم على تحقيق النجاح",
  is_active: true,
  sort_order: 0
};

function buildQuickLinks(context: ReturnType<typeof useSchoolSite>): SchoolQuickLink[] {
  const links: SchoolQuickLink[] = [
    {
      to: `${context.schoolPath}/programs`,
      title: "برامج المدرسة",
      text: "الاطلاع على البرنامج المعتمد في المدرسة وبقية البرامج التعليمية النشطة."
    },
    {
      to: "/app/messages",
      title: "الرسائل",
      text: "الانتقال مباشرة إلى الرسائل الداخلية المرتبطة بالمدرسة الحالية."
    },
    {
      to: `${context.schoolPath}/services`,
      title: "الخدمات",
      text: "الدخول إلى الخدمات المرتبطة بعملك داخل المدرسة والنظام المركزي."
    }
  ];

  if (context.canViewAnnouncements) {
    links.splice(1, 0, {
      to: `${context.schoolPath}/announcements`,
      title: "الإعلانات",
      text: "متابعة الإعلانات الموجهة لهذه المدرسة حسب دور المستخدم الحالي."
    });
  }

  if (context.canAccessFiles) {
    links.push({
      to: `${context.schoolPath}/files`,
      title: "ملفات المدرسة",
      text: "استعراض الملفات المشتركة داخل المدرسة وتنزيلها أو رفعها حسب الصلاحية."
    });
  }

  return links;
}

function formatTickerTitle(title: string) {
  return title.trim().split(/\s+/).slice(0, 4).join(" ");
}

export function SchoolGatewayPage() {
  const context = useSchoolSite();
  const {
    school,
    schoolStats,
    schoolPath,
    canViewAnnouncements,
    isParent,
    canViewStudents,
    canViewPlans
  } = context;
  const [announcements, setAnnouncements] = useState<AnnouncementItem[]>([]);
  const [loadingAnnouncements, setLoadingAnnouncements] = useState(canViewAnnouncements);
  const [announcementError, setAnnouncementError] = useState<string | null>(null);
  const [quotes, setQuotes] = useState<InspirationalQuote[]>([defaultInspirationalQuote]);
  const [activeQuoteIndex, setActiveQuoteIndex] = useState(0);

  const quickLinks = useMemo(() => buildQuickLinks(context), [context]);
  const tickerAnnouncements = useMemo(
    () =>
      announcements.length
        ? announcements.slice(0, 5)
        : [
            {
              id: 0,
              title: `مرحبًا بكم في ${school.name}`,
              body: "هذه الصفحة تمثل المدخل الوظيفي لموقع المدرسة، وتعرض أهم الخدمات والإعلانات الحالية.",
              is_all_schools: false,
              status: "active",
              target_audience: "general",
              published_at: null,
              school: {
                id: school.id,
                name_ar: school.name
              }
            } satisfies AnnouncementItem
          ],
    [announcements, school.id, school.name]
  );

  useEffect(() => {
    if (!canViewAnnouncements) {
      setAnnouncements([]);
      setLoadingAnnouncements(false);
      return;
    }

    let isActive = true;
    setLoadingAnnouncements(true);

    void announcementService
      .list()
      .then((payload) => {
        if (!isActive) {
          return;
        }

        const filtered = payload.filter(
          (announcement) => announcement.is_all_schools || announcement.school?.id === school.id
        );

        setAnnouncements(filtered.slice(0, 3));
        setAnnouncementError(null);
      })
      .catch((loadError) => {
        if (!isActive) {
          return;
        }

        setAnnouncementError(getErrorMessage(loadError));
      })
      .finally(() => {
        if (isActive) {
          setLoadingAnnouncements(false);
        }
      });

    return () => {
      isActive = false;
    };
  }, [canViewAnnouncements, school.id]);

  useEffect(() => {
    let isActive = true;

    void inspirationalQuoteService
      .publicList()
      .then((payload) => {
        if (!isActive) {
          return;
        }

        setQuotes(payload.length ? payload : [defaultInspirationalQuote]);
        setActiveQuoteIndex(0);
      })
      .catch(() => {
        if (isActive) {
          setQuotes([defaultInspirationalQuote]);
        }
      });

    return () => {
      isActive = false;
    };
  }, []);

  useEffect(() => {
    if (quotes.length <= 1) {
      return;
    }

    const intervalId = window.setInterval(() => {
      setActiveQuoteIndex((current) => (current + 1) % quotes.length);
    }, 6500);

    return () => {
      window.clearInterval(intervalId);
    };
  }, [quotes.length]);

  const activeQuote = quotes[activeQuoteIndex] ?? defaultInspirationalQuote;

  return (
    <>
      <section className="school-gateway-ticker" aria-label="شريط الإعلانات المدرسي">
        {announcementError ? <span className="school-gateway-ticker-error">{announcementError}</span> : null}
        {loadingAnnouncements ? <span className="school-gateway-ticker-error">جارٍ تحميل الإعلانات...</span> : null}
        {!loadingAnnouncements ? (
          <div className="school-gateway-ticker-track">
            {[...tickerAnnouncements, ...tickerAnnouncements].map((announcement, index) => (
              <span className="school-gateway-ticker-item" key={`${announcement.id}-${index}`}>
                <Sparkles aria-hidden="true" size={18} />
                <strong>{formatTickerTitle(announcement.title)}</strong>
                <span>:</span>
                <span>{announcement.body}</span>
              </span>
            ))}
          </div>
        ) : null}
      </section>

      <section className="portal-surface page-stack inspirational-section">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">عبارات ملهمة</span>
          <h2>إضاءات ملهمة</h2>
        </div>

        <div className="inspirational-slide">
          <span className="portal-chip">عبارات ملهمة</span>
          <h3>{activeQuote.title}</h3>
          <p>{activeQuote.body}</p>

          {quotes.length > 1 ? (
            <div className="school-slider-dots" role="tablist" aria-label="التنقل بين العبارات الملهمة">
              {quotes.map((quote, index) => (
                <button
                  aria-label={`الانتقال إلى العبارة ${index + 1}`}
                  className={`school-slider-dot${index === activeQuoteIndex ? " is-active" : ""}`}
                  key={quote.id}
                  onClick={() => setActiveQuoteIndex(index)}
                  type="button"
                />
              ))}
            </div>
          ) : null}
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">الصفحة الرئيسية</span>
          <h2>{school.name}</h2>
          <p>
            هذه الصفحة تمثل المدخل الوظيفي لموقع المدرسة. ستجد فيها نظرة سريعة على المدرسة،
            والوصول السريع إلى البرامج والخدمات والملفات والإعلانات.
          </p>
        </div>

        <div className="portal-grid portal-grid-four">
          <article className="portal-stat-card">
            <span>عدد الطلاب</span>
            <strong>{schoolStats?.students_count ?? 0}</strong>
          </article>
          <article className="portal-stat-card">
            <span>عدد المعلمين</span>
            <strong>{schoolStats?.teachers_count ?? 0}</strong>
          </article>
          <article className="portal-stat-card">
            <span>نوع البرنامج</span>
            <strong>{school.program_type ?? "-"}</strong>
          </article>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">الوصول السريع</span>
          <h2>روابط المدرسة الأساسية</h2>
        </div>

        <div className="portal-grid portal-grid-two">
          {quickLinks.map((item) => (
            <Link className="portal-card portal-card-link" key={item.to} to={item.to}>
              <h3>{item.title}</h3>
              <p>{item.text}</p>
            </Link>
          ))}
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">الدخول إلى الخدمات</span>
          <h2>اختصارات الدور الحالي</h2>
        </div>

        <div className="portal-grid portal-grid-three">
          {canViewStudents ? (
            <Link className="portal-card portal-card-link" to="/app/students">
              <h3>{isParent ? "أبنائي" : "الطلاب"}</h3>
              <p>
                {isParent
                  ? "استعراض بيانات الأبناء المرتبطين بهذه المدرسة داخل النظام المركزي."
                  : "استعراض الطلاب المرتبطين بهذه المدرسة داخل النظام المركزي."}
              </p>
            </Link>
          ) : null}

          {canViewPlans ? (
            <Link className="portal-card portal-card-link" to="/app/iep-plans">
              <h3>الخطط الفردية</h3>
              <p>الدخول إلى الخطط الفردية داخل نفس المدرسة مع الحفاظ على نطاق المدرسة الحالي.</p>
            </Link>
          ) : null}

          <Link className="portal-card portal-card-link" to="/app/messages">
            <h3>الرسائل</h3>
            <p>الانتقال إلى الرسائل الداخلية مع بقاء سياق المدرسة الحالية محفوظًا.</p>
          </Link>
        </div>

        <div className="portal-button-row">
          <Link className="portal-button portal-button-primary" to="/app">
            الدخول إلى النظام المركزي
          </Link>
          <Link className="portal-button portal-button-secondary" to={`${schoolPath}/services`}>
            استعراض جميع الخدمات
          </Link>
        </div>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">آخر المستجدات</span>
          <h2>الإعلانات الأخيرة</h2>
        </div>

        {announcementError ? <div className="error-box">{announcementError}</div> : null}
        {loadingAnnouncements ? <div className="loading-box">جارٍ تحميل الإعلانات...</div> : null}

        {!loadingAnnouncements ? (
          announcements.length ? (
            <div className="portal-grid portal-grid-three">
              {announcements.map((announcement) => (
                <Link className="portal-card portal-card-link" key={announcement.id} to={`${schoolPath}/announcements/${announcement.id}`}>
                  <span className="portal-chip">
                    {announcement.is_all_schools ? "عام" : announcement.school?.name_ar ?? "إعلان مدرسي"}
                  </span>
                  <h3>{announcement.title}</h3>
                  <p>{announcement.body}</p>
                </Link>
              ))}
            </div>
          ) : (
            <div className="info-box">لا توجد إعلانات مفعلة حاليًا لهذه المدرسة.</div>
          )
        ) : null}

        <div className="portal-button-row">
          <Link className="portal-button portal-button-secondary" to={`${schoolPath}/announcements`}>
            الانتقال إلى صفحة الإعلانات
          </Link>
        </div>
      </section>
    </>
  );
}
