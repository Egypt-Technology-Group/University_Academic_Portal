import { createI18n } from 'vue-i18n'
import ar from './ar.json'
import en from './en.json'

const savedLocale = localStorage.getItem('egyitech_locale') || 'ar'

const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: {
    ar,
    en,
  },
})

export default i18n
