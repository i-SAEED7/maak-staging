/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,jsx,ts,tsx}"
  ],
  corePlugins: {
    preflight: false
  },
  theme: {
    extend: {
      fontFamily: {
        sans: ["IBM Plex Sans Arabic", "Tahoma", "sans-serif"]
      },
      colors: {
        maak: {
          primary: "#07a869",
          secondary: "#3d7eb9",
          accent: "#0da9a6",
          dark: "#15445a",
          warm: "#c1b489",
          gray: "#c2c1c1"
        }
      },
      boxShadow: {
        soft: "0 18px 42px rgba(21, 68, 90, 0.10)"
      }
    }
  },
  plugins: []
};
