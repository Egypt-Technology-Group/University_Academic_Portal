---
noteId: "7258f040a20a11f199338946b8fe1c28"
tags: []

---

# Task 3 Brief: Animated Number Counter Composable & Stat Cards

## Goal
Create `useAnimatedCounter.js` composable and integrate it into `MetricStatCard.vue` and `KpiCard.vue` so numerical metrics animate smoothly with easing upon entering the viewport.

## Target Files
- `frontend/src/composables/useAnimatedCounter.js` (create)
- `frontend/src/components/ui/MetricStatCard.vue` (modify)
- `frontend/src/components/ui/KpiCard.vue` (modify)

## Requirements
1. **`useAnimatedCounter.js`**:
   - Accepts `targetValue` (ref, getter, or primitive String/Number) and `duration` (default ~1400ms).
   - Robust number parsing:
     - Handles prefixes and suffixes (e.g. `96.8%`, `+15,000`, `120+`, `EGP 25,000`, `$4,500`).
     - Preserves decimal precision (e.g. `96.8%` animates decimals smoothly, `120` animates integers).
     - Formats numbers with thousands separators if original string had commas.
   - Smooth `requestAnimationFrame` interpolation with `easeOutCubic: 1 - Math.pow(1 - t, 3)`.
   - Uses `IntersectionObserver` with fallback if SSR/no observer.
   - Reduced-motion detection: if reduced motion enabled, immediately returns targetValue without count animation.
   - Returns `{ displayValue, elementRef }`.

2. **`MetricStatCard.vue` & `KpiCard.vue`**:
   - Bind `ref="elementRef"` to the root or value container element.
   - Use `useAnimatedCounter(toRef(props, 'value'))` (or computed target) so display updates smoothly when props change or enter viewport.
   - Render `displayValue` in place of raw `value`.
   - Add hover lift and micro-interaction classes (`hover-lift`, `transition-all duration-300`).

3. **Verification**:
   - Run `npm run build` in `frontend/` to verify zero errors.

4. **Report**:
   - Write report to `.superpowers/sdd/2026-08-27-website-animations/task-3-report.md`.
