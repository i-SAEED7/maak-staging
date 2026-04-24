import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { programDetails, resolveProgramSlugFromName } from "../../lib/portalContent";
import { useSchoolSite } from "../../lib/schoolSite";
import {
  educationProgramService,
  type EducationProgramOption
} from "../../services/educationProgramService";
import { getErrorMessage } from "../../services/api";

export function SchoolProgramsPage() {
  const { school, schoolPath } = useSchoolSite();
  const [programs, setPrograms] = useState<EducationProgramOption[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const currentProgramSlug = resolveProgramSlugFromName(school.program_type);
  const currentProgramContent: (typeof programDetails)[keyof typeof programDetails] | null =
    currentProgramSlug ? programDetails[currentProgramSlug] : null;

  useEffect(() => {
    let isActive = true;

    void educationProgramService
      .list()
      .then((payload) => {
        if (!isActive) {
          return;
        }

        setPrograms(payload);
        setError(null);
      })
      .catch((loadError) => {
        if (!isActive) {
          return;
        }

        setError(getErrorMessage(loadError));
      })
      .finally(() => {
        if (isActive) {
          setLoading(false);
        }
      });

    return () => {
      isActive = false;
    };
  }, []);

  return (
    <>
      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">برامج المدرسة</span>
          <h2>البرنامج المعتمد في {school.name}</h2>
          <p>
            تعرض هذه الصفحة البرنامج الحالي للمدرسة، مع قائمة البرامج التعليمية النشطة داخل المنصة،
            ويمكنك فتح الصفحة التفصيلية لكل برنامج من البطاقات أدناه.
          </p>
        </div>

        <article className="portal-card">
          <span className="portal-chip">البرنامج الحالي</span>
          <h3>{school.program_type ?? "برنامج غير محدد"}</h3>
          <p>
            {currentProgramContent?.overview ??
              "تم ربط هذه المدرسة ببرنامج تعليمي داخل النظام، وسيظهر وصفه التفصيلي هنا عند توفره."}
          </p>

          {currentProgramContent?.goals?.length ? (
            <div className="detail-list">
              {currentProgramContent.goals.map((goal) => (
                <div className="detail-list-item" key={goal}>
                  <strong>{goal}</strong>
                </div>
              ))}
            </div>
          ) : null}

          {currentProgramContent ? (
            <div className="portal-button-row">
              <Link
                className="portal-button portal-button-primary"
                to={`${schoolPath}/programs/${currentProgramContent.slug}`}
              >
                عرض الصفحة التفصيلية
              </Link>
            </div>
          ) : null}
        </article>
      </section>

      <section className="portal-surface page-stack">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">البرامج النشطة</span>
          <h2>جميع البرامج المتاحة</h2>
        </div>

        {error ? <div className="error-box">{error}</div> : null}
        {loading ? <div className="loading-box">جارٍ تحميل البرامج...</div> : null}

        {!loading ? (
          <div className="portal-grid portal-grid-two">
            {programs.map((program) => {
              const isCurrent = program.name_ar === school.program_type;
              const slug = resolveProgramSlugFromName(program.name_ar);

              return (
                <article className="portal-card" key={program.id}>
                  <span className="portal-chip">{isCurrent ? "البرنامج الحالي" : "برنامج متاح"}</span>
                  <h3>{program.name_ar}</h3>
                  <p>
                    {isCurrent
                      ? "هذا هو البرنامج المسجل حاليًا على هذه المدرسة داخل النظام."
                      : "برنامج تعليمي نشط داخل المنصة ويمكن ربطه بمدارس أخرى حسب سياسات الإدارة."}
                  </p>

                  {slug ? (
                    <div className="portal-button-row">
                      <Link className="portal-button portal-button-secondary" to={`${schoolPath}/programs/${slug}`}>
                        عرض التفاصيل
                      </Link>
                    </div>
                  ) : (
                    <small className="field-hint">سيتم توفير الصفحة التفصيلية لهذا البرنامج لاحقًا.</small>
                  )}
                </article>
              );
            })}
          </div>
        ) : null}
      </section>
    </>
  );
}
