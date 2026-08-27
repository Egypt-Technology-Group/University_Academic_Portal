---
noteId: "f5359c20a20a11f199338946b8fe1c28"
tags: []

---

# Task 5 Brief: Homepage & Layout Motion Integration

## Goal
Integrate the motion system into `HomeView.vue`, `Navbar.vue`, and `Footer.vue` using `v-reveal` directives, staggered delays, micro-interactions, and animated counters.

## Target Files
- `frontend/src/views/HomeView.vue` (modify)
- `frontend/src/components/layout/Navbar.vue` (modify)
- `frontend/src/components/layout/Footer.vue` (modify)

## Requirements
1. **`HomeView.vue`**:
   - Hero Section:
     - Apply smooth entrance to Hero content: Badge (`v-reveal.fade-up`), Title (`v-reveal.fade-up.delay-100`), Subtitle (`v-reveal.fade-up.delay-200`), CTA buttons (`v-reveal.fade-up.delay-300`).
     - Right visual floating card: `v-reveal.zoom-in.delay-200` with subtle `animate-float` or `group-hover:scale-105`.
   - Statistics Section:
     - Wrap stat items with `v-reveal.fade-up` and staggered delays (`delay-100`, `delay-200`, `delay-300`...).
     - Create a small inline counter component or use `useAnimatedCounter` for the statistics values so they count up when scrolled into view.
   - Featured Colleges Section:
     - Title/Header: `v-reveal.fade-up`
     - College Cards: `v-reveal.fade-up` with staggered delay (`:class="'delay-' + ((index + 1) * 100)"` or static delay classes).
   - Degree Programs Section:
     - Program Cards: `v-reveal.fade-up` with staggered delay.
   - Features / Institutional Highlights:
     - Feature Cards: `v-reveal.fade-up` with staggered delays.
   - Latest News & Events Section:
     - News Cards / Events: `v-reveal.fade-up` with staggered delays.
   - CTA / Admissions Banner:
     - Banner content: `v-reveal.zoom-in`.

2. **`Navbar.vue`**:
   - Add scroll detection to dynamically enhance navbar styling on scroll (e.g. elevated shadow `shadow-md` and `bg-white/95 backdrop-blur-md`).
   - Add smooth transitions on mobile menu drawer opening/closing.

3. **`Footer.vue`**:
   - Add `v-reveal.fade-in` to footer content columns.
   - Enhance social links and newsletter button with hover micro-interactions.

4. **Verification**:
   - Run `npm run build` in `frontend/` to confirm 0 errors.

5. **Report**:
   - Write report to `.superpowers/sdd/2026-08-27-website-animations/task-5-report.md`.
