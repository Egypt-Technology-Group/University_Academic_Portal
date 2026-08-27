/**
 * v-reveal Scroll Reveal Vue 3 Directive
 *
 * Standard, modern scroll-reveal animation system.
 * Elements reveal smoothly when entering the viewport from any scroll direction,
 * and re-arm when exiting to replay naturally on subsequent scrolls.
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

let sharedObserver = null

function getSharedObserver() {
  if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
    return null
  }

  if (!sharedObserver) {
    sharedObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const rect = entry.target.getBoundingClientRect()
          const winHeight = window.innerHeight

          // In viewport threshold: element is visible on screen
          if (entry.isIntersecting && rect.top <= winHeight - 30 && rect.bottom >= 10) {
            entry.target.classList.add('reveal-active')
          } else if (rect.top > winHeight + 20 || rect.bottom < -40) {
            // Out of viewport: silently reset so animation replays upon re-entering
            entry.target.classList.remove('reveal-active')
          }
        })
      },
      {
        root: null,
        rootMargin: '40px 0px 40px 0px',
        threshold: [0, 0.1, 0.2]
      }
    )
  }

  return sharedObserver
}

export const vReveal = {
  mounted(el, binding) {
    // 1. Resolve animation variant (default: 'fade-up')
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
      const foundInModifiers = ANIMATION_TYPES.find((type) => binding.modifiers[type])
      if (foundInModifiers) {
        animation = foundInModifiers
      }
    }

    // 2. Add base reveal classes
    el.classList.add('reveal-init', `reveal-${animation}`)

    // 3. Resolve delay modifiers, classes, and inline styles
    if (binding.modifiers) {
      Object.keys(binding.modifiers).forEach((mod) => {
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

    // 4. Fallback for environments lacking IntersectionObserver
    const observer = getSharedObserver()
    if (!observer) {
      el.classList.add('reveal-active')
      return
    }

    // 5. Initial viewport check and continuous observation
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        const rect = el.getBoundingClientRect()
        if (rect.top <= window.innerHeight - 30 && rect.bottom >= 10) {
          el.classList.add('reveal-active')
        }
        observer.observe(el)
      })
    })
  },

  unmounted(el) {
    if (sharedObserver) {
      sharedObserver.unobserve(el)
    }
  }
}

export default vReveal
