type PortalSectionHeadingProps = {
  eyebrow: string;
  title: string;
  description?: string;
};

export function PortalSectionHeading({ eyebrow, title, description }: PortalSectionHeadingProps) {
  return (
    <div className="portal-section-heading">
      <span className="portal-eyebrow">{eyebrow}</span>
      <h2>{title}</h2>
      {description ? <p>{description}</p> : null}
    </div>
  );
}
