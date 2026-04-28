import { Navigate, useNavigate } from "react-router-dom";
import { useAuthStore } from "../../stores/authStore";

export function SchoolSelectionPage() {
  const navigate = useNavigate();
  const user = useAuthStore((state) => state.user);
  const setSchoolId = useAuthStore((state) => state.setSchoolId);
  const assignedSchools = user?.assigned_schools ?? [];

  if (!user) {
    return <Navigate replace to="/login" />;
  }

  if (user.role !== "supervisor") {
    return <Navigate replace to="/app" />;
  }

  if (assignedSchools.length <= 1) {
    return <Navigate replace to={assignedSchools[0] ? `/schools/${assignedSchools[0].slug ?? assignedSchools[0].id}` : "/app"} />;
  }

  return (
    <div className="portal-page-stack">
      <section className="portal-surface portal-auth-surface">
        <div className="portal-section-heading">
          <span className="portal-eyebrow">مدارس متعددة</span>
          <h2>اختر المدرسة</h2>
          <p>تم ربط حسابك بعدة مدارس. اختر المدرسة التي تريد الدخول إليها الآن.</p>
        </div>

        <div className="portal-grid portal-grid-two">
          {assignedSchools.map((school) => (
            <button
              className="portal-card portal-school-card"
              key={school.id}
              onClick={() => {
                setSchoolId(String(school.id));
                navigate(`/schools/${school.slug ?? school.id}`);
              }}
              type="button"
            >
              <span className="portal-chip">{school.program_type ?? "برنامج"}</span>
              <h3>{school.name_ar}</h3>
              <p>{school.stage ?? "مرحلة غير محددة"}</p>
            </button>
          ))}
        </div>
      </section>
    </div>
  );
}
