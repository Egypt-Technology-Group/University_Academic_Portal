/**
 * v-reveal Scroll Reveal Vue 3 Directive
 *
 * Provides performant viewport-entry reveal animations utilizing IntersectionObserver
 * and accessible prefers-reduced-motion fallback.
 *
 * Supported Animations:
 * - v-reveal (defaults to fade-up)
 * - v-reveal.fade-in / v-reveal="'fade-in'"
 * - v-reveal.slide-start / v-reveal="'slide-start'"
 * - v-reveal.slide-end / v-reveal="'slide-end'"
 * - v-reveal.zoom-in / v-reveal="'zoom-in'"
 * - v-reveal.scale-up / v-reveal="'scale-up'"
 *
 * Supported Delay Modifiers:
 * - v-reveal.delay-100, v-reveal.delay-200, v-reveal.delay-300,
 *   v-reveal.delay-400, v-reveal.delay-500, v-reveal.delay-600
 */

const ANIMATION_TYPES = [
  'fade-up',
  'fade-in',
  'slide-start',
  'slide-end',
  'zoom-in',
  'scale-up'
]

const DELAYS = [
  'delay-100',
  'delay-200',
  'delay-300',
  'delay-400',
  'delay-500',
  'delay-600'
]

export const vReveal = {
  mounted(el, binding) {
    // 1. Accessibility Check: prefers-reduced-motion
    const prefersReducedMotion = typeof window !== 'undefined' &&
      window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches

    if (prefersReducedMotion) {
      el.classList.add('reveal-active')
      return
    }

    // 2. Resolve animation variant (default: 'fade-up')
    let animation = 'fade-up'
    if (typeof binding.value === 'string' && ANIMATION_TYPES.includes(binding.value)) {
      animation = binding.value
    } else if (binding.value && typeof binding.value === 'object' && binding.value.animation && ANIMATION_TYPES.includes(binding.value.animation)) {
      animation = binding.value.animation
    } else if (binding.modifiers) {
      const foundInModifiers = ANIMATION_TYPES.find(type => binding.modifiers[type])
      if (foundInModifiers) {
        animation = foundInModifiers
      }
    }

    // 3. Add base reveal classes
    el.classList.add('reveal-init', `reveal-${animation}`)

    // 4. Resolve delay modifiers & attributes
    if (binding.modifiers) {
      Object.keys(binding.modifiers).forEach(mod => {
        if (DELAYS.includes(mod) || mod.startsWith('delay-')) {
          el.classList.add(mod)
        }
      })
    }

    if (typeof binding.value === 'string' && binding.value.startsWith('delay-')) {
      el.classList.add(binding.value)
    } else if (binding.value && typeof binding.value === 'object' && binding.value.delay) {
      const delayClass = typeof binding.value.delay === 'number'
        ? `delay-${binding.value.delay}`
        : binding.value.delay
      el.classList.add(delayClass)
    }

    // 5. Fallback for environments lacking IntersectionObserver
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
      el.classList.add('reveal-active')
      return
    }

    // 6. Observe element entry into viewport
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal-active')
          obs.unobserve(entry.target)
        }
      })
    }, {
      rootMargin: '0px 0px -40px 0px',
      threshold: 0.1
    })

    observer.observe(el)
    el._revealObserver = observer
  },

  unmounted(el) {
    if (el._revealObserver) {
      el._revealObserver.unobserve(el)
      el._revealObserver.disconnect()
      delete el._revealObserver
    }
  }
}

export default vReveal
