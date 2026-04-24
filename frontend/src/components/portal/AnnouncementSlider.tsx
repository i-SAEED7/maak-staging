import { useEffect, useState } from "react";

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
        <span className="portal-hero-label">قسم ذوي الإعاقة</span>
        <h1>{slide.title}</h1>
        <p>{slide.text}</p>
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
    </section>
  );
}
