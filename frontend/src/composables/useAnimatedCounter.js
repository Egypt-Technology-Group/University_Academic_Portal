import { ref, watch, onMounted, onUnmounted, unref } from 'vue'

/**
 * Parses a string or number into prefix, numeric value, suffix, decimal precision, and comma formatting.
 * Examples:
 *   "96.8%"      => { prefix: "", targetNum: 96.8, suffix: "%", decimals: 1, hasCommas: false }
 *   "+15,000"    => { prefix: "+", targetNum: 15000, suffix: "", decimals: 0, hasCommas: true }
 *   "120+"       => { prefix: "", targetNum: 120, suffix: "+", decimals: 0, hasCommas: false }
 *   "EGP 25,000" => { prefix: "EGP ", targetNum: 25000, suffix: "", decimals: 0, hasCommas: true }
 *   "$4,500.50"  => { prefix: "$", targetNum: 4500.5, suffix: "", decimals: 2, hasCommas: true }
 *   14           => { prefix: "", targetNum: 14, suffix: "", decimals: 0, hasCommas: false }
 */
export function parseCounterValue(raw) {
  if (raw === null || raw === undefined) {
    return null
  }

  const str = String(raw).trim()
  if (!str) return null

  // Regex captures:
  // 1: prefix (non-digits before number)
  // 2: number body with optional commas and decimals
  // 3: suffix (any trailing characters)
  const regex = /^([^\d]*?)(\d[\d,]*(?:\.\d+)?)(.*)$/s
  const match = str.match(regex)

  if (!match) {
    const numOnly = parseFloat(str.replace(/,/g, ''))
    if (!isNaN(numOnly)) {
      return {
        prefix: '',
        targetNum: numOnly,
        suffix: '',
        decimals: str.includes('.') ? str.split('.')[1].length : 0,
        hasCommas: str.includes(','),
        original: str
      }
    }
    return null
  }

  const prefix = match[1]
  const numStr = match[2]
  const suffix = match[3]

  const cleanNumStr = numStr.replace(/,/g, '')
  const targetNum = parseFloat(cleanNumStr)

  if (isNaN(targetNum)) {
    return null
  }

  const decimals = cleanNumStr.includes('.') ? cleanNumStr.split('.')[1].length : 0
  const hasCommas = numStr.includes(',')

  return {
    prefix,
    targetNum,
    suffix,
    decimals,
    hasCommas,
    original: str
  }
}

/**
 * Formats a number with specified decimal precision, commas, prefix, and suffix.
 */
export function formatCounterNumber(num, { prefix = '', suffix = '', decimals = 0, hasCommas = false } = {}) {
  let numStr
  if (decimals > 0) {
    numStr = Number(num).toFixed(decimals)
  } else {
    numStr = Math.round(Number(num)).toString()
  }

  if (hasCommas) {
    const parts = numStr.split('.')
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    numStr = parts.join('.')
  }

  return `${prefix}${numStr}${suffix}`
}

/**
 * useAnimatedCounter Composable
 * 
 * Animates numerical display values with smooth easing (easeOutCubic)
 * triggered upon entering the viewport via IntersectionObserver.
 * 
 * @param {import('vue').Ref<string|number> | Function | string | number} targetValue - Source value
 * @param {number | Object} optionsOrDuration - Duration in ms or options object
 * @returns {{ displayValue: import('vue').Ref<string>, elementRef: import('vue').Ref<HTMLElement|null>, isAnimating: import('vue').Ref<boolean>, startAnimation: Function }}
 */
export function useAnimatedCounter(targetValue, optionsOrDuration = {}) {
  const options = typeof optionsOrDuration === 'number'
    ? { duration: optionsOrDuration }
    : (optionsOrDuration || {})

  const {
    duration = 1400,
    delay = 0,
    startValue = 0,
    immediate = false,
    threshold = 0.15,
    rootMargin = '0px 0px -40px 0px'
  } = options

  const elementRef = ref(null)
  const isAnimating = ref(false)
  const displayValue = ref('')

  let observer = null
  let animationFrameId = null
  let hasTriggered = false
  let currentNumericValue = startValue

  function getRawValue() {
    if (typeof targetValue === 'function') {
      return targetValue()
    }
    return unref(targetValue)
  }

  // Initialize displayValue
  const initialRaw = getRawValue()
  const initialMeta = parseCounterValue(initialRaw)
  if (initialMeta) {
    displayValue.value = formatCounterNumber(startValue, initialMeta)
  } else {
    displayValue.value = initialRaw !== null && initialRaw !== undefined ? String(initialRaw) : ''
  }

  /**
   * Run the animation frame loop from `from` to `to`.
   */
  function startAnimation(from = currentNumericValue, customTo = null) {
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId)
      animationFrameId = null
    }

    const raw = getRawValue()
    const meta = parseCounterValue(raw)

    if (!meta) {
      displayValue.value = raw !== null && raw !== undefined ? String(raw) : ''
      isAnimating.value = false
      return
    }

    const targetNum = customTo !== null ? customTo : meta.targetNum

    if (duration <= 0) {
      currentNumericValue = targetNum
      displayValue.value = formatCounterNumber(targetNum, meta)
      isAnimating.value = false
      return
    }

    const startTime = performance.now()
    const startNum = from
    const change = targetNum - startNum
    isAnimating.value = true

    function step(currentTime) {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)
      // easeOutCubic: 1 - Math.pow(1 - progress, 3)
      const easedProgress = 1 - Math.pow(1 - progress, 3)

      currentNumericValue = startNum + change * easedProgress
      displayValue.value = formatCounterNumber(currentNumericValue, meta)

      if (progress < 1) {
        animationFrameId = requestAnimationFrame(step)
      } else {
        currentNumericValue = targetNum
        displayValue.value = formatCounterNumber(targetNum, meta)
        animationFrameId = null
        isAnimating.value = false
      }
    }

    animationFrameId = requestAnimationFrame(step)
  }

  /**
   * Set up IntersectionObserver when mounted.
   */
  onMounted(() => {
    if (immediate || typeof window === 'undefined' || !window.IntersectionObserver) {
      hasTriggered = true
      startAnimation(startValue)
      return
    }

    if (elementRef.value) {
      observer = new IntersectionObserver((entries) => {
        const entry = entries[0]
        const rect = entry ? entry.target.getBoundingClientRect() : null
        const inView = entry && entry.isIntersecting && rect && rect.top <= window.innerHeight - 20 && rect.bottom >= 20

        if (inView) {
          if (!hasTriggered) {
            hasTriggered = true
            const initialRaw = getRawValue()
            const initialMeta = parseCounterValue(initialRaw)
            if (initialMeta) {
              displayValue.value = formatCounterNumber(startValue, initialMeta)
            }
            if (delay > 0) {
              setTimeout(() => startAnimation(startValue), delay)
            } else {
              startAnimation(startValue)
            }
          }
        } else {
          hasTriggered = false
        }
      }, {
        threshold,
        rootMargin
      })

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          if (elementRef.value && observer) {
            const rect = elementRef.value.getBoundingClientRect()
            if (rect.top <= window.innerHeight - 20 && rect.bottom >= 20 && !hasTriggered) {
              hasTriggered = true
              startAnimation(startValue)
            }
            observer.observe(elementRef.value)
          }
        })
      })
    } else {
      hasTriggered = true
      startAnimation(startValue)
    }
  })

  /**
   * Watch for targetValue changes and re-animate if already triggered.
   */
  watch(
    () => getRawValue(),
    (newVal, oldVal) => {
      if (newVal === oldVal) return

      const meta = parseCounterValue(newVal)
      if (!meta) {
        displayValue.value = newVal !== null && newVal !== undefined ? String(newVal) : ''
        return
      }

      if (hasTriggered || immediate) {
        startAnimation(currentNumericValue, meta.targetNum)
      } else {
        displayValue.value = formatCounterNumber(currentNumericValue, meta)
      }
    }
  )

  /**
   * Cleanup on unmounted.
   */
  onUnmounted(() => {
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId)
      animationFrameId = null
    }
    if (observer) {
      observer.disconnect()
      observer = null
    }
  })

  return {
    displayValue,
    elementRef,
    isAnimating,
    startAnimation
  }
}

export default useAnimatedCounter
