---
noteId: "2dc84bb0a20a11f199338946b8fe1c28"
tags: []

---

# Task 1 Brief: Create `v-reveal` Scroll-Observer Directive

## Goal
Create a native, lightweight Vue 3 custom directive `v-reveal` utilizing `IntersectionObserver` that adds scroll reveal animations to any DOM element.

## Target Files
- `frontend/src/directives/vReveal.js` (create)
- `frontend/src/main.js` (modify to register directive globally)

## Requirements
1. **Directive Logic (`vReveal.js`)**:
   - Detect `prefers-reduced-motion: reduce`. If enabled, immediately add `reveal-active` class and return without observing.
   - Parse modifiers and value:
     - Animation classes: `fade-up` (default), `fade-in`, `slide-start`, `slide-end`, `zoom-in`, `scale-up`.
     - Delay modifiers: `delay-100`, `delay-200`, `delay-300`, `delay-400`, `delay-500`, `delay-600`. Add modifier class to element if present.
   - Add base classes: `reveal-init` and `reveal-${animationClass}`.
   - Use `IntersectionObserver` with rootMargin `'0px 0px -40px 0px'` and threshold `0.1`.
   - On intersect: add `reveal-active` and `obs.unobserve(entry.target)`.
   - On `unmounted`: disconnect observer and clean up.
2. **Global Registration (`main.js`)**:
   - Import `vReveal` from `./directives/vReveal` and register via `app.directive('reveal', vReveal)`.
3. **Report**:
   - Write completion report to `.superpowers/sdd/2026-08-27-website-animations/task-1-report.md`.
