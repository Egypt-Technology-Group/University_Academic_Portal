---
noteId: "54cc8be0a20a11f199338946b8fe1c28"
tags: []

---

# Task 2 Brief: Core Animation Utilities & Keyframes in Tailwind & CSS

## Goal
Add comprehensive animation utilities, keyframes, transitions, and reveal styles into Tailwind and CSS.

## Target Files
- `frontend/tailwind.config.js` (modify)
- `frontend/src/style.css` (modify)

## Requirements
1. **`tailwind.config.js`**:
   - Add animation keyframes and helpers:
     - `shimmer`: `0% { background-position: -200% 0 }`, `100% { background-position: 200% 0 }`
     - `float`: `0%, 100% { transform: translateY(0) }`, `50% { transform: translateY(-8px) }`
     - `float-slow`: `0%, 100% { transform: translateY(0) }`, `50% { transform: translateY(-12px) }`
     - `pulse-subtle`: `0%, 100% { opacity: 1, transform: scale(1) }`, `50% { opacity: 0.85, transform: scale(1.03) }`
     - `glow-pulse`: `0%, 100% { filter: drop-shadow(0 0 15px rgba(212,175,55,0.3)) }`, `50% { filter: drop-shadow(0 0 25px rgba(212,175,55,0.6)) }`
   - Add transition timing functions:
     - `'academic-spring': 'cubic-bezier(0.16, 1, 0.3, 1)'`
     - `'bounce-subtle': 'cubic-bezier(0.34, 1.56, 0.64, 1)'`

2. **`src/style.css`**:
   - Add Reveal Classes:
     - `.reveal-init`: `opacity: 0; transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1); will-change: transform, opacity;`
     - `.reveal-fade-up`: `transform: translateY(24px);`
     - `.reveal-fade-in`: `transform: translateY(0);`
     - `.reveal-slide-start`: `[dir="ltr"] & { transform: translateX(-30px); } [dir="rtl"] & { transform: translateX(30px); }`
     - `.reveal-slide-end`: `[dir="ltr"] & { transform: translateX(30px); } [dir="rtl"] & { transform: translateX(-30px); }`
     - `.reveal-zoom-in`: `transform: scale(0.92);`
     - `.reveal-scale-up`: `transform: scale(0.95) translateY(16px);`
     - `.reveal-active`: `opacity: 1 !important; transform: translateY(0) translateX(0) scale(1) !important;`
   - Add Stagger Delays:
     - `.delay-100 { transition-delay: 100ms; }`
     - `.delay-200 { transition-delay: 200ms; }`
     - `.delay-300 { transition-delay: 300ms; }`
     - `.delay-400 { transition-delay: 400ms; }`
     - `.delay-500 { transition-delay: 500ms; }`
     - `.delay-600 { transition-delay: 600ms; }`
   - Micro-interaction & Hover helpers:
     - `.hover-lift`: `transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);`
     - `.hover-lift:hover`: `transform: translateY(-4px); box-shadow: 0 12px 30px -4px rgba(10, 37, 64, 0.15);`
     - `.card-interactive`: subtle border and shadow transition on hover.
     - `.btn-press`: `active:scale-[0.98] transition-transform duration-150`
   - Accessibility guard:
     - `@media (prefers-reduced-motion: reduce) { .reveal-init { opacity: 1 !important; transform: none !important; transition: none !important; } }`
3. **Verification**:
   - Run `npm run build` in `frontend` to verify clean build.
4. **Report**:
   - Write report to `.superpowers/sdd/2026-08-27-website-animations/task-2-report.md`.
