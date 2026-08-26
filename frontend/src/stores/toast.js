import { defineStore } from 'pinia'

let nextToastId = 1

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: [],
    position: 'top-end', // 'top-end' | 'top-start' | 'bottom-end' | 'bottom-start' | 'top-center' | 'bottom-center'
    maxToasts: 5,
  }),

  actions: {
    /**
     * Add a new toast notification
     * @param {Object} options
     * @param {string} options.title - Notification title
     * @param {string} options.message - Notification message description
     * @param {'success'|'error'|'warning'|'info'|'loading'} [options.variant='info'] - Variant type
     * @param {number} [options.duration=4000] - Duration in ms (0 for persistent)
     * @param {boolean} [options.dismissible=true] - Can user close it
     * @param {string} [options.actionLabel] - Optional button label
     * @param {Function} [options.onAction] - Action callback
     * @param {boolean} [options.deduplicate=true] - Prevent duplicate message spam
     * @returns {number|string} Toast ID
     */
    show(options = {}) {
      const {
        title = '',
        message = '',
        variant = 'info',
        duration = 4500,
        dismissible = true,
        actionLabel = '',
        onAction = null,
        deduplicate = true,
        id = null,
      } = typeof options === 'string' ? { message: options } : options

      if (deduplicate && message) {
        const existing = this.toasts.find(
          (t) => t.message === message && t.title === title && t.variant === variant
        )
        if (existing) {
          existing.count = (existing.count || 1) + 1
          existing.timer = this.resetTimer(existing.id, existing.duration)
          return existing.id
        }
      }

      const toastId = id || nextToastId++

      const toast = {
        id: toastId,
        title,
        message,
        variant,
        duration,
        dismissible,
        actionLabel,
        onAction,
        count: 1,
        createdAt: Date.now(),
        progress: 100,
        timeoutId: null,
      }

      // Maintain max queue size
      if (this.toasts.length >= this.maxToasts) {
        const oldest = this.toasts.shift()
        if (oldest && oldest.timeoutId) clearTimeout(oldest.timeoutId)
      }

      this.toasts.push(toast)

      if (duration > 0 && variant !== 'loading') {
        toast.timeoutId = setTimeout(() => {
          this.dismiss(toastId)
        }, duration)
      }

      return toastId
    },

    success(message, title = '', options = {}) {
      return this.show({ message, title, variant: 'success', ...options })
    },

    error(message, title = '', options = {}) {
      return this.show({ message, title, variant: 'error', duration: 6000, ...options })
    },

    warning(message, title = '', options = {}) {
      return this.show({ message, title, variant: 'warning', duration: 5000, ...options })
    },

    info(message, title = '', options = {}) {
      return this.show({ message, title, variant: 'info', ...options })
    },

    loading(message, title = '', options = {}) {
      return this.show({ message, title, variant: 'loading', duration: 0, dismissible: false, ...options })
    },

    update(id, options = {}) {
      const toast = this.toasts.find((t) => t.id === id)
      if (!toast) return
      if (toast.timeoutId) clearTimeout(toast.timeoutId)

      Object.assign(toast, options)

      if (toast.duration > 0 && toast.variant !== 'loading') {
        toast.timeoutId = setTimeout(() => {
          this.dismiss(id)
        }, toast.duration)
      }
    },

    dismiss(id) {
      const idx = this.toasts.findIndex((t) => t.id === id)
      if (idx !== -1) {
        const [removed] = this.toasts.splice(idx, 1)
        if (removed && removed.timeoutId) clearTimeout(removed.timeoutId)
      }
    },

    clear() {
      this.toasts.forEach((t) => {
        if (t.timeoutId) clearTimeout(t.timeoutId)
      })
      this.toasts = []
    },

    resetTimer(id, duration) {
      const toast = this.toasts.find((t) => t.id === id)
      if (toast && duration > 0) {
        if (toast.timeoutId) clearTimeout(toast.timeoutId)
        toast.timeoutId = setTimeout(() => {
          this.dismiss(id)
        }, duration)
      }
    },
  },
})
