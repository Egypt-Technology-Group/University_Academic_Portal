import { defineStore } from 'pinia'
import i18n from '../i18n'

export const useLocaleStore = defineStore('locale', {
  state: () => ({
    currentLocale: localStorage.getItem('egyitech_locale') || 'ar',
  }),

  getters: {
    isRtl: (state) => state.currentLocale === 'ar',
    dir: (state) => (state.currentLocale === 'ar' ? 'rtl' : 'ltr'),
    locale: (state) => state.currentLocale,
  },

  actions: {
    setLocale(newLocale) {
      if (['ar', 'en'].includes(newLocale)) {
        this.currentLocale = newLocale
        i18n.global.locale.value = newLocale
        localStorage.setItem('egyitech_locale', newLocale)
        this.applyDomAttributes()
      }
    },

    toggleLocale() {
      const targetLocale = this.currentLocale === 'ar' ? 'en' : 'ar'
      this.setLocale(targetLocale)
    },

    initLocale() {
      const saved = localStorage.getItem('egyitech_locale') || 'ar'
      this.setLocale(saved)
    },

    applyDomAttributes() {
      const htmlEl = document.documentElement
      htmlEl.setAttribute('lang', this.currentLocale)
      htmlEl.setAttribute('dir', this.currentLocale === 'ar' ? 'rtl' : 'ltr')
      if (this.currentLocale === 'ar') {
        document.body.classList.remove('font-english')
        document.body.classList.add('font-sans')
      } else {
        document.body.classList.remove('font-arabic')
        document.body.classList.add('font-english')
      }
    },
  },
})
