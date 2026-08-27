---
noteId: "22232fa0a20a11f199338946b8fe1c28"
tags: []

---

# Elegant & Professional Academic Motion System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement a comprehensive, lightweight, GPU-accelerated animation system across the academic portal (scroll reveals, animated metric counters, interactive card/button micro-interactions, and smooth transitions).

**Architecture:** Custom native Vue 3 `v-reveal` directive using `IntersectionObserver`, `requestAnimationFrame` animated number counter composable, refined Tailwind keyframes & modern CSS transitions respecting `prefers-reduced-motion`.

**Tech Stack:** Vue 3 (Composition API), Tailwind CSS 3, Modern CSS, Vite.

## Global Constraints
- Strictly GPU-accelerated properties (`transform`, `opacity`, `filter`).
- Built-in `@media (prefers-reduced-motion: reduce)` support.
- Fully compatible with bidirectional layout (RTL Arabic / LTR English).
- Zero heavy third-party animation dependencies.

---

### Task 1: Create `v-reveal` Scroll-Observer Directive

**Files:**
- Create: `frontend/src/directives/vReveal.js`
- Modify: `frontend/src/main.js`

**Interfaces:**
- Consumes: Native DOM element binding and modifiers (`fade-up`, `fade-in`, `slide-start`, `slide-end`, `zoom-in`, `delay-100`...`delay-600`).
- Produces: `v-reveal` directive registered globally in Vue.

- [ ] **Step 1: Create the `vReveal.js` directive**
```javascript
// frontend/src/directives/vReveal.js
const isReducedMotion = () => {
  return typeof window !== 'undefined' && window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

export const vReveal = {
  mounted(el, binding) {
    if (isReducedMotion()) {
      el.classList.add('reveal-active')
      return
    }

    const { modifiers, value } = binding
    const animationClass = Object.keys(modifiers).find(m => 
      ['fade-up', 'fade-in', 'slide-start', 'slide-end', 'zoom-in', 'scale-up'].includes(m)
    ) || (typeof value === 'string' ? value : 'fade-up')

    const delayModifier = Object.keys(modifiers).find(m => m.startsWith('delay-'))
    if (delayModifier) {
      el.classList.add(delayModifier)
    }

    el.classList.add('reveal-init', `reveal-${animationClass}`)

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal-active')
            obs.unobserve(entry.target)
          }
        })
      },
      {
        root: null,
        rootMargin: '0px 0px -40px 0px',
        threshold: 0.1
      }
    )

    el._revealObserver = observer
    observer.observe(el)
  },
  unmounted(el) {
    if (el._revealObserver) {
      el._revealObserver.disconnect()
      delete el._revealObserver
    }
  }
}
```

- [ ] **Step 2: Register directive in `frontend/src/main.js`**
Add `import { vReveal } from './directives/vReveal'` and `app.directive('reveal', vReveal)`.

- [ ] **Step 3: Verification**
Verify `frontend/src/main.js` compiles without errors.

---

### Task 2: Core Animation Utilities & Keyframes in Tailwind & CSS

**Files:**
- Modify: `frontend/tailwind.config.js`
- Modify: `frontend/src/style.css`

**Interfaces:**
- Consumes: Tailwind config and custom CSS classes.
- Produces: Global reveal classes (`reveal-init`, `reveal-active`, `reveal-fade-up`, etc.), stagger delays, and hover keyframes.

- [ ] **Step 1: Extend Tailwind animation keyframes in `frontend/tailwind.config.js`**
Add `shimmer`, `float-slow`, `pulse-subtle` and custom cubic bezier easing.

- [ ] **Step 2: Add CSS Motion & Reveal classes to `frontend/src/style.css`**
Add styles for `.reveal-init`, `.reveal-active`, `.reveal-fade-up`, `.reveal-slide-start`, `.reveal-zoom-in`, `.delay-100` through `.delay-600`, and `prefers-reduced-motion`.

- [ ] **Step 3: Verification**
Verify CSS compiles cleanly.

---

### Task 3: Animated Number Counter Composable & Stat Cards

**Files:**
- Create: `frontend/src/composables/useAnimatedCounter.js`
- Modify: `frontend/src/components/ui/MetricStatCard.vue`
- Modify: `frontend/src/components/ui/KpiCard.vue`

**Interfaces:**
- Consumes: Numeric or formatted string value (e.g. `96.8%`, `15,000+`).
- Produces: Reactive animated display text triggered when element is visible.

- [ ] **Step 1: Create `frontend/src/composables/useAnimatedCounter.js`**
```javascript
// frontend/src/composables/useAnimatedCounter.js
import { ref, onMounted } from 'vue'

export function useAnimatedCounter(targetValue, duration = 1600) {
  const displayValue = ref('')
  const elementRef = ref(null)
  
  const parseValue = (val) => {
    if (typeof val === 'number') return { num: val, prefix: '', suffix: '', decimals: Number.isInteger(val) ? 0 : 1 }
    const str = String(val || '')
    const match = str.match(/^([^\d.-]*)([\d,.]+)(.*)$/)
    if (!match) return { num: 0, prefix: '', suffix: str, decimals: 0 }
    
    const prefix = match[1]
    const numStr = match[2].replace(/,/g, '')
    const suffix = match[3]
    const num = parseFloat(numStr) || 0
    const hasDot = numStr.includes('.')
    const decimals = hasDot ? (numStr.split('.')[1] || '').length : 0
    return { num, prefix, suffix, decimals }
  }

  const animate = () => {
    const { num, prefix, suffix, decimals } = parseValue(targetValue)
    if (num === 0) {
      displayValue.value = targetValue
      return
    }

    const startTime = performance.now()
    const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3)

    const step = (currentTime) => {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)
      const currentNum = (easeOutCubic(progress) * num).toFixed(decimals)
      displayValue.value = `${prefix}${currentNum}${suffix}`

      if (progress < 1) {
        requestAnimationFrame(step)
      } else {
        displayValue.value = String(targetValue)
      }
    }

    requestAnimationFrame(step)
  }

  onMounted(() => {
    if (typeof window !== 'undefined' && 'IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animate()
            obs.disconnect()
          }
        })
      }, { threshold: 0.2 })

      if (elementRef.value) {
        observer.observe(elementRef.value)
      } else {
        animate()
      }
    } else {
      displayValue.value = String(targetValue)
    }
  })

  return { displayValue, elementRef }
}
```

- [ ] **Step 2: Integrate into `MetricStatCard.vue` and `KpiCard.vue`**
Update components to mount the counter observer and smoothly render the count-up animation.

- [ ] **Step 3: Verification**
Verify stat cards render properly.

---

### Task 4: UI Micro-Interactions on Primitives

**Files:**
- Modify: `frontend/src/components/ui/Button.vue`
- Modify: `frontend/src/components/ui/Card.vue`
- Modify: `frontend/src/components/ui/Modal.vue`
- Modify: `frontend/src/components/ui/Badge.vue`

**Interfaces:**
- Consumes: Standard UI properties and slots.
- Produces: Enhanced interactive hover lifts, active press downscale, smooth modal zooms and badge pulses.

- [ ] **Step 1: Update `Button.vue`**
Add active scale transitions (`active:scale-[0.98] transition-all duration-200`) and subtle shine/glow on hover for gold/primary variants.

- [ ] **Step 2: Update `Card.vue`**
Add smooth hover transitions (`transition-all duration-300 hover:shadow-academic-lg hover:-translate-y-1`).

- [ ] **Step 3: Update `Modal.vue`**
Add refined transition classes with backdrop-filter blur and scale entry (`transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1)`).

- [ ] **Step 4: Update `Badge.vue`**
Add subtle transitions and dot pulse animation when active.

---

### Task 5: Homepage & Layout Motion Integration

**Files:**
- Modify: `frontend/src/views/HomeView.vue`
- Modify: `frontend/src/components/layout/Navbar.vue`
- Modify: `frontend/src/components/layout/Footer.vue`

**Interfaces:**
- Consumes: `v-reveal` directives, counter composables, and micro-interaction classes.
- Produces: Animated Hero cascade, smooth scroll-revealed program & news cards, and interactive navbar scrolling.

- [ ] **Step 1: Enhance `HomeView.vue` with `v-reveal` & staggered delays**
Add `v-reveal` to Hero elements, Statistics counter section, Academic Programs grid, Features, News & Events cards.

- [ ] **Step 2: Enhance `Navbar.vue`**
Add smooth scroll-based shadow / backdrop blur elevation and animated active link underlines.

- [ ] **Step 3: Enhance `Footer.vue`**
Add subtle scroll reveal and link hover transitions.

---

### Task 6: End-to-End Build & Verification

**Files:**
- Run build in `frontend/`

- [ ] **Step 1: Run Vite production build**
`npm run build` in `frontend/` to confirm complete zero-error compilation.

- [ ] **Step 2: Verify bundle output & responsiveness**
Check that output bundle is clean and all assets build with 0 errors.
