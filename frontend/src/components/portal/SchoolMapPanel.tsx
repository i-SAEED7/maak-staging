import { useMemo, useState } from "react";
import type { PortalSchool } from "../../services/portalService";

type SchoolMapPanelProps = {
  schools: PortalSchool[];
  compact?: boolean;
};

type NormalizedSchool = PortalSchool & {
  x: number;
  y: number;
};

function normalizeSchools(schools: PortalSchool[]): NormalizedSchool[] {
  const withCoordinates = schools.filter(
    (school) => school.location_lat !== null && school.location_lng !== null
  );

  if (withCoordinates.length === 0) {
    return schools.map((school, index) => ({
      ...school,
      x: 20 + (index % 4) * 18,
      y: 22 + Math.floor(index / 4) * 18
    }));
  }

  const latitudes = withCoordinates.map((school) => Number(school.location_lat));
  const longitudes = withCoordinates.map((school) => Number(school.location_lng));
  const minLat = Math.min(...latitudes);
  const maxLat = Math.max(...latitudes);
  const minLng = Math.min(...longitudes);
  const maxLng = Math.max(...longitudes);

  return schools.map((school, index) => {
    if (school.location_lat === null || school.location_lng === null) {
      return {
        ...school,
        x: 18 + (index % 4) * 18,
        y: 20 + Math.floor(index / 4) * 18
      };
    }

    const x =
      maxLng === minLng
        ? 50
        : 12 + ((Number(school.location_lng) - minLng) / (maxLng - minLng)) * 76;
    const y =
      maxLat === minLat
        ? 50
        : 12 + ((maxLat - Number(school.location_lat)) / (maxLat - minLat)) * 70;

    return {
      ...school,
      x,
      y
    };
  });
}

export function SchoolMapPanel({ schools, compact = false }: SchoolMapPanelProps) {
  const normalizedSchools = useMemo(() => normalizeSchools(schools), [schools]);
  const [activeSchoolId, setActiveSchoolId] = useState<number | null>(normalizedSchools[0]?.id ?? null);

  const activeSchool =
    normalizedSchools.find((school) => school.id === activeSchoolId) ?? normalizedSchools[0] ?? null;

  return (
    <div className={`portal-map-card${compact ? " is-compact" : ""}`}>
      <div className="portal-map-stage">
        <div className="portal-map-shape" />
        <div className="portal-map-grid" />
        {normalizedSchools.map((school) => (
          <button
            key={school.id}
            className={`portal-map-marker${school.id === activeSchool?.id ? " is-active" : ""}`}
            onClick={() => setActiveSchoolId(school.id)}
            style={{ left: `${school.x}%`, top: `${school.y}%` }}
            title={school.name_ar}
            type="button"
          >
            <span />
          </button>
        ))}
      </div>

      <div className="portal-map-sidebar">
        <div>
          <span className="portal-map-label">خريطة جدة</span>
          <h3>مواقع المدارس</h3>
          <p>استعراض بصري مبسط للمدارس المرتبطة بالقسم داخل مدينة جدة.</p>
        </div>

        {activeSchool ? (
          <div className="portal-map-school">
            <strong>{activeSchool.name_ar}</strong>
            <p>
              {activeSchool.program_type ?? "برنامج غير محدد"} | {activeSchool.stage ?? "مرحلة غير محددة"}
            </p>
            <small>{activeSchool.address ?? activeSchool.city ?? "جدة"}</small>
          </div>
        ) : null}

        <div className="portal-map-list">
          {normalizedSchools.slice(0, compact ? 4 : normalizedSchools.length).map((school) => (
            <button
              key={school.id}
              className={`portal-map-list-item${school.id === activeSchool?.id ? " is-active" : ""}`}
              onClick={() => setActiveSchoolId(school.id)}
              type="button"
            >
              <span>{school.name_ar}</span>
              <small>{school.program_type ?? "برنامج غير محدد"}</small>
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}
