/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Dynamic Academic Primary Navy
        navy: {
          50: '#f0f4f9',
          100: '#d9e4f0',
          200: '#b4cae2',
          300: '#87abd0',
          400: '#5c8bbc',
          500: '#3c6fa8',
          600: '#2b578c',
          700: '#234470',
          800: 'var(--color-navy-800, #1a365d)',
          900: 'var(--color-navy-900, #0f3460)',
          950: 'var(--color-navy-950, #0a2540)',
        },
        primary: {
          50: '#f0f5fa',
          100: '#e1ebf4',
          200: '#c3d8e9',
          300: '#94bbdb',
          400: '#5e98c9',
          500: '#387bb7',
          600: '#26609b',
          700: '#1f4e7e',
          800: 'var(--color-navy-800, #1a365d)',
          900: 'var(--color-navy-900, #0f3460)',
          950: 'var(--color-navy-950, #0a2540)',
        },
        // Dynamic Secondary Academic Gold
        gold: {
          50: '#fbf8ea',
          100: '#f5efc8',
          200: '#ebdE90',
          300: '#dec754',
          400: 'var(--color-gold-400, #d4af37)',
          500: 'var(--color-gold-500, #c59b27)',
          600: '#a87a1d',
          700: '#865b19',
          800: '#6f491b',
          900: '#5d3d1b',
          950: '#37200c',
        },
        // Dynamic Accent Emerald
        emerald: {
          50: '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: 'var(--color-emerald-600, #059669)',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
          950: '#022c22',
        },
        // Slate / Neutral Surfaces
        slate: {
          50: '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          900: '#0f172a',
          950: '#020617',
        }
      },
      fontFamily: {
        sans: ['Cairo', 'Inter', 'system-ui', 'sans-serif'],
        arabic: ['Cairo', 'system-ui', 'sans-serif'],
        english: ['Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'academic': '0 4px 20px -2px rgba(10, 37, 64, 0.08), 0 2px 6px -1px rgba(10, 37, 64, 0.04)',
        'academic-lg': '0 10px 30px -4px rgba(10, 37, 64, 0.12), 0 4px 12px -2px rgba(10, 37, 64, 0.06)',
        'gold-glow': '0 0 25px -3px rgba(212, 175, 55, 0.35)',
        'navy-glow': '0 0 30px -4px rgba(15, 52, 96, 0.45)',
      },
      transitionTimingFunction: {
        'academic-spring': 'cubic-bezier(0.16, 1, 0.3, 1)',
        'bounce-subtle': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
      },
      animation: {
        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'float': 'float 6s ease-in-out infinite',
        'float-slow': 'float-slow 8s ease-in-out infinite',
        'shimmer': 'shimmer 2.5s infinite linear',
        'pulse-subtle': 'pulse-subtle 3s ease-in-out infinite',
        'glow-pulse': 'glow-pulse 3s ease-in-out infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-8px)' },
        },
        'float-slow': {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-12px)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-200% 0' },
          '100%': { backgroundPosition: '200% 0' },
        },
        'pulse-subtle': {
          '0%, 100%': { opacity: '1', transform: 'scale(1)' },
          '50%': { opacity: '0.85', transform: 'scale(1.03)' },
        },
        'glow-pulse': {
          '0%, 100%': { filter: 'drop-shadow(0 0 15px rgba(212, 175, 55, 0.3))' },
          '50%': { filter: 'drop-shadow(0 0 25px rgba(212, 175, 55, 0.6))' },
        },
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
