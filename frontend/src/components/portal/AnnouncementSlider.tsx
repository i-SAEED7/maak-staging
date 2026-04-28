import { useEffect, useState } from "react";
import { Link } from "react-router-dom";

type AnnouncementSlide = {
  title: string;
  text: string;
};

type AnnouncementSliderProps = {
  slides: AnnouncementSlide[];
};

export function AnnouncementSlider({ slides }: AnnouncementSliderProps) {
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (slides.length <= 1) {
      return;
    }

    const timer = window.setInterval(() => {
      setCurrentIndex((current) => (current + 1) % slides.length);
    }, 5000);

    return () => window.clearInterval(timer);
  }, [slides.length]);

  if (slides.length === 0) {
    return null;
  }

  const slide = slides[currentIndex];

  return (
    <section className="portal-hero-slider">
      <div className="portal-hero-copy">
        <span className="portal-hero-label">بوابة معاك</span>
        <h1>{slide.title}</h1>
        <p>{slide.text}</p>
        <div className="portal-hero-actions">
          <Link className="portal-hero-action" to="/services">
            استكشف النظام
          </Link>
          <Link className="portal-hero-action" to="/map">
            الخريطة التفاعلية
          </Link>
        </div>
      </div>

      <div className="portal-slider-controls">
        {slides.map((item, index) => (
          <button
            key={item.title}
            aria-label={`الانتقال إلى الشريحة ${index + 1}`}
            className={`portal-slider-dot${index === currentIndex ? " is-active" : ""}`}
            onClick={() => setCurrentIndex(index)}
            type="button"
          />
        ))}
      </div>

      <div className="portal-hero-scroll-hint" aria-hidden="true">
        <span>مرر للأسفل لاكتشاف المزيد</span>
        <span className="portal-hero-scroll-arrow">↓</span>
      </div>
    </section>
  );
}
