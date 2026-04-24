import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { useSchoolSite } from "../../lib/schoolSite";
import { announcementService, type AnnouncementItem } from "../../services/announcementService";
import { getErrorMessage } from "../../services/api";

type SchoolQuickLink = {
  to: string;
  title: string;
  text: string;
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
  const [activeSlideIndex, setActiveSlideIndex] = useState(0);

  const quickLinks = useMemo(() => buildQuickLinks(context), [context]);
  const sliderAnnouncements = useMemo(
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
    setActiveSlideIndex(0);
  }, [sliderAnnouncements.length]);

  useEffect(() => {
    if (sliderAnnouncements.length <= 1) {
      return;
    }

    const intervalId = window.setInterval(() => {
      setActiveSlideIndex((current) => (current + 1) % sliderAnnouncements.length);
    }, 5000);

    return () => {
      window.clearInterval(intervalId);
    };
  }, [sliderAnnouncements.length]);

  const activeSlide = sliderAnnouncements[activeSlideIndex] ?? sliderAnnouncements[0];

  return (
    <>
      <section className="portal-surface page-stack school-announcement-slider">
        <div className="page-header">
          <div className="portal-section-heading">
            <span className="portal-eyebrow">المستجدات</span>
            <h2>شريط الإعلانات المدرسي</h2>
          </div>

          {canViewAnnouncements ? (
            <Link className="portal-button portal-button-secondary" to={`${schoolPath}/announcements`}>
              جميع الإعلانات
            </Link>
          ) : null}
        </div>

        {announcementError ? <div className="error-box">{announcementError}</div> : null}
        {loadingAnnouncements ? <div className="loading-box">جارٍ تحميل الشريط الإعلاني...</div> : null}

        {!loadingAnnouncements && activeSlide ? (
          <div className="school-slider-surface">
            <div className="school-slider-content">
              <span className="portal-chip">
                {activeSlide.is_all_schools ? "إعلان عام" : activeSlide.school?.name_ar ?? school.name}
              </span>
              <h3>{activeSlide.title}</h3>
              <p>{activeSlide.body}</p>

              {activeSlide.id ? (
                <div className="portal-button-row">
                  <Link
                    className="portal-button portal-button-primary"
                    to={`${schoolPath}/announcements/${activeSlide.id}`}
                  >
                    عرض الإعلان
                  </Link>
                </div>
              ) : null}
            </div>

            {sliderAnnouncements.length > 1 ? (
              <div className="school-slider-dots" role="tablist" aria-label="التنقل بين الإعلانات">
                {sliderAnnouncements.map((slide, index) => (
                  <button
                    aria-label={`الانتقال إلى الإعلان ${index + 1}`}
                    className={`school-slider-dot${index === activeSlideIndex ? " is-active" : ""}`}
                    key={slide.id}
                    onClick={() => setActiveSlideIndex(index)}
                    type="button"
                  />
                ))}
              </div>
            ) : null}
          </div>
        ) : null}
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
          <article className="portal-stat-card">
            <span>كود المدرسة</span>
            <strong>{school.school_code ?? school.official_code ?? "-"}</strong>
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
