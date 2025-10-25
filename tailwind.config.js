/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: "class",

  theme: {
    extend: {
      colors: {
        brand: {
          50:  '#ECF9FF',
          100: '#D6F1FF',
          200: '#AEE2FF',
          300: '#7FD2FF',
          400: '#52C4FF',
          500: '#26BBFF', // tu azul institucional
          600: '#1499D6',
          700: '#0F78A8',
          800: '#0D5B80',
          900: '#0A4260',
        },
        ink: {
          50:  '#2B263B',
          100: '#252038',
          200: '#201A2F', // tu 900 original
          300: '#191826',
          400: '#15141F',
          500: '#111115', // tu 800 original
          600: '#0D0D12',
          700: '#09090D', // cercano a tu 700 negro profundo
          800: '#050507',
          900: '#000000',
        },
        neutral: {
          50:  '#F4F4F5',
          100: '#E9E9EA',
          200: '#D6D6D8',
          300: '#B5B5B8',
          400: '#A1A1A1', // tu 400
          500: '#848282', // tu 500
          600: '#6A696B',
          700: '#535256',
          800: '#3F3E43',
          900: '#2E2D33',
        },
        accent: {
          50:  '#F4F4E8',
          100: '#E5E5CC',
          200: '#CFCF9E',
          300: '#B9B96F',
          400: '#A3A34E',
          500: '#8F8F2F',
          600: '#6F6F22',
          700: '#4F4F18',
          800: '#2E2E0C',
          900: '#0F0F02', // tu 900
        },
        // Estados útiles
        success: {
          100: '#DCFCE7', 500: '#22C55E', 700: '#15803D',
        },
        warning: {
          100: '#FEF3C7', 500: '#F59E0B', 700: '#B45309',
        },
        danger: {
          100: '#FEE2E2', 500: '#EF4444', 700: '#B91C1C',
        },
      },
      boxShadow: {
        soft: '0 6px 24px rgba(0,0,0,0.18)',
        inset: 'inset 0 1px 0 rgba(255,255,255,0.05)',
      },
      borderRadius: {
        xl: '0.9rem',
        '2xl': '1.25rem',
      },
    },
  },
  plugins: [],
};
