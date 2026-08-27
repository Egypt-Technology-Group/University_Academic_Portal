/**
 * v-reveal Scroll Reveal Vue 3 Directive
 *
 * Provides performant, reliable viewport-entry animations utilizing IntersectionObserver.
 * Ensures animations ONLY trigger when elements genuinely enter the user's viewport on scroll.
 */

const ANIMATION_TYPES = [
  'fade-up',
  'fade-down',
  'fade-in',
  'slide-start',
  'slide-end',
  'zoom-in',
  'scale-up'
]

const DELAYS = [
  'delay-75',
  'delay-100',
  'delay-150',
  'delay-200',
  'delay-250',
  'delay-300',
  'delay-350',
  'delay-400',
  'delay-500',
  'delay-600',
  'delay-700',
  'delay-800'
]

function isReducedMotion() {
  return (
    typeof window !== 'undefined' &&
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches
  )
}

export const vReveal = {
  mounted(el, binding) {
    // 1. Respect accessibility: prefers-reduced-motion
    if (isReducedMotion()) {
      el.classList.add('reveal-active')
      return
    }

    // 2. Resolve animation variant (default: 'fade-up')
    let animation = 'fade-up'
    if (typeof binding.value === 'string' && ANIMATION_TYPES.includes(binding.value)) {
      animation = binding.value
    } else if (
      binding.value &&
      typeof binding.value === 'object' &&
      binding.value.animation &&
      ANIMATION_TYPES.includes(binding.value.animation)
    ) {
      animation = binding.value.animation
    } else if (binding.modifiers) {
      const foundInModifiers = ANIMATION_TYPES.find(type => binding.modifiers[type])
      if (foundInModifiers) {
        animation = foundInModifiers
      }
    }

    // 3. Add base reveal classes
    el.classList.add('reveal-init', `reveal-${animation}`)

    // 4. Resolve delay modifiers, classes, and inline styles
    if (binding.modifiers) {
      Object.keys(binding.modifiers).forEach(mod => {
        if (DELAYS.includes(mod) || mod.startsWith('delay-')) {
          el.classList.add(mod)
        }
      })
    }

    if (typeof binding.value === 'string' && binding.value.startsWith('delay-')) {
      el.classList.add(binding.value)
    } else if (binding.value && typeof binding.value === 'object' && binding.value.delay !== undefined) {
      if (typeof binding.value.delay === 'number') {
        el.style.transitionDelay = `${binding.value.delay}ms`
      } else {
        el.classList.add(binding.value.delay)
      }
    }

    // 5. Fallback for environments lacking IntersectionObserver
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
      el.classList.add('reveal-active')
      return
    }

    // 6. Observe element entry into viewport
    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            // Element entered viewport: activate transition and unobserve
            entry.target.classList.add('reveal-active')
            obs.unobserve(entry.target)
          }
        })
      },
      {
        root: null,
        // Bottom margin ensures elements don't trigger until scrolled well into view
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.12
      }
    )

    el._revealObserver = observer

    // Use requestAnimationFrame so layout calculations settle after dynamic data loads
    requestAnimationFrame(() => {
      if (el._revealObserver) {
        el._revealObserver.observe(el)
      }
    })
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
