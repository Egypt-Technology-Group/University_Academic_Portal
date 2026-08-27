# Elegant & Professional Academic Motion System Design

**Date:** 2026-08-27  
**Status:** Approved  
**Target:** Frontend Academic Portal (`frontend/`)

## 1. Overview
This specification outlines the architecture, implementation details, and component enhancements for an elegant, high-performance animation and motion system across the University Academic Portal. The system ensures 60fps GPU-accelerated motion, seamless RTL/LTR bilingual support, accessibility compliance (`prefers-reduced-motion`), and zero external library bloat.

---

## 2. Core Motion Architecture

### 2.1 Scroll Reveal Directive (`v-reveal`)
* **File Location:** `frontend/src/directives/vReveal.js` & registered in `frontend/src/main.js`
* **Mechanism:** Single shared or lightweight `IntersectionObserver` monitoring elements in the DOM.
* **Modifiers / Configurations:**
  * Directions: `fade-up` (default), `fade-in`, `slide-start`, `slide-end`, `zoom-in`, `scale-up`
  * Delays: `.delay-100`, `.delay-200`, `.delay-300`, `.delay-500` for staggered children grids
  * Threshold: Default `0.1` (element triggers when 10% visible)
  * Once: Automatically unobserves after triggering once to free resources
* **Accessibility:** If `window.matchMedia('(prefers-reduced-motion: reduce)').matches` is true, reveal classes are applied instantly without transition delays or transforms.

### 2.2 Global CSS & Tailwind Animations
* **File Locations:** `frontend/src/style.css` and `frontend/tailwind.config.js`
* **GPU Transitions:** Utilize `transform` and `opacity` with cubic-bezier easing (`cubic-bezier(0.16, 1, 0.3, 1)`).
* **Styles added:**
  * Reveal base state classes (`.reveal-init`, `.reveal-active`)
  * Stagger delay utility classes (`.delay-100` to `.delay-600`)
  * Micro-interaction classes (card hover lift, button click active scale feedback, ambient floating and gradient shimmer).

---

## 3. Component Enhancements

### 3.1 Animated Number Counter (`useAnimatedCounter` / `MetricStatCard.vue` & `KpiCard.vue`)
* **File Location:** `frontend/src/composables/useAnimatedCounter.js`
* **Behavior:** Animates numerical values from 0 up to target when element enters viewport.
* **Formatting:** Preserves non-numeric affixes (e.g. `96.8%`, `15,000+`, `120+`).

### 3.2 Homepage & Hero Visuals (`HomeView.vue`)
* Staggered entrance of Hero Badge, Title, Subtitle, and Call-to-Action buttons.
* Ambient background decorative glows with gentle slow breathing pulse.
* Features, Academic Programs, News, and Events cards reveal smoothly on scroll with staggered delays.

### 3.3 Layout & UI Polish (`Navbar.vue`, `Footer.vue`, `Button.vue`, `Card.vue`, `Modal.vue`)
* **Navbar:** Smooth shadow & background elevation on scroll.
* **Buttons:** Subtle active press downscale (`active:scale-[0.98]`), smooth hover glow.
* **Cards:** Academic elevation lift (`hover:-translate-y-1 hover:shadow-academic-lg`).
* **Modals & Dialogs:** Smooth backdrop fade and modal scale-up.

---

## 4. Accessibility & Performance Guardrails
* All animations strictly respect `prefers-reduced-motion`.
* Animation durations are kept crisp (150ms to 600ms) to ensure professionalism without delaying user interactions.
* No layout-triggering properties (`top`, `left`, `width`, `height`, `margin`) are animated.
