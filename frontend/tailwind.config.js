/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,jsx,ts,tsx}"
  ],
  darkMode: "class",
  corePlugins: {
    preflight: false
  },
  theme: {
    extend: {
      fontFamily: {
        sans: ["MaakFont", "IBM Plex Sans Arabic", "Tahoma", "sans-serif"],
        display: ["MaakFont", "IBM Plex Sans Arabic", "sans-serif"],
      },
      colors: {
        maak: {
          primary: "#382972",
          secondary: "#7030A0",
          accent: "#208CAA",
          "accent-strong": "#1673A4",
          cyan: "#0DCFDA",
          sky: "#2BADDF",
          violet: "#9338FF",
          lavender: "#757BBB",
          deep: "#003944",
          title: "#351375",
          body: "#797979",
          bg: "#E5E5E5",
        }
      },
      boxShadow: {
        soft: "0 24px 70px rgba(56, 41, 114, 0.14)",
        glass: "0 20px 42px rgba(21, 68, 90, 0.08)",
      },
      borderRadius: {
        "4xl": "2rem",
      },
      keyframes: {
        shimmer: {
          "0%": { backgroundPosition: "200% 0" },
          "100%": { backgroundPosition: "-200% 0" },
        },
        "fade-in": {
          "0%": { opacity: 0, transform: "translateY(8px)" },
          "100%": { opacity: 1, transform: "translateY(0)" },
        },
        "slide-up": {
          "0%": { opacity: 0, transform: "translateY(20px)" },
          "100%": { opacity: 1, transform: "translateY(0)" },
        },
        "scale-in": {
          "0%": { opacity: 0, transform: "scale(0.95)" },
          "100%": { opacity: 1, transform: "scale(1)" },
        },
      },
      animation: {
        shimmer: "shimmer 1.5s ease-in-out infinite",
        "fade-in": "fade-in 0.3s ease-out",
        "slide-up": "slide-up 0.4s ease-out",
        "scale-in": "scale-in 0.2s ease-out",
      },
    }
  },
  plugins: []
};
