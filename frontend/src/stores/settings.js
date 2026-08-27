import { defineStore } from 'pinia'
import { api, getTranslated } from '../services/api'
import { defaultSettings } from '../services/defaultSettings'

let publicSettingsInFlightPromise = null

export const useSettingsStore = defineStore('settings', {
  state: () => {
    let cached = null
    try {
      const stored = localStorage.getItem('egyitech_site_settings')
      if (stored) {
        cached = JSON.parse(stored)
      }
    } catch (e) {
      console.warn('Failed to parse cached settings from localStorage', e)
    }

    return {
      settings: cached || { ...defaultSettings },
      isLoading: false,
      isSaving: false,
      error: null,
      lastUpdated: null,
    }
  },

  getters: {
    // Branding & Identity (Backend-sourced)
    siteIdentity: (state) => state.settings.site_identity || {},
    siteName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.name, locale),
    siteShortName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.short_name, locale),
    siteSlogan: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.slogan, locale),
    siteLogoUrl: (state) => state.settings.site_identity?.logo_url || '',
    siteFaviconUrl: (state) => state.settings.site_identity?.favicon_url || '',

    // Theme & Visual Identity (Bootstrap/Backend-customizable)
    themeColors: (state) => state.settings.theme_colors || defaultSettings.theme_colors,
    primaryColor: (state) => state.settings.theme_colors?.primary_color || '#0A2540',
    secondaryGold: (state) => state.settings.theme_colors?.secondary_gold || '#C59B27',
    accentEmerald: (state) => state.settings.theme_colors?.accent_emerald || '#059669',
    headerStyle: (state) => state.settings.theme_colors?.header_style || 'classic',

    // President Message & Leadership (Backend-sourced)
    presidentMessage: (state) => state.settings.president_message || {},
    presidentName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.name, locale),
    presidentTitle: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.title, locale),
    presidentAvatar: (state) => state.settings.president_message?.avatar_url || '',
    presidentQuote: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.quote, locale),
    presidentFullMessage: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.message, locale),

    // Hero Slider (Backend-sourced)
    heroSlider: (state) => state.settings.hero_slider?.slides || [],

    // Contact Information (Backend-sourced)
    contactInfo: (state) => state.settings.contact_info || {},
    hotline: (state) => state.settings.contact_info?.hotline || '',
    phone: (state) => state.settings.contact_info?.phone || '',
    email: (state) => state.settings.contact_info?.email || '',
    admissionsEmail: (state) => state.settings.contact_info?.admissions_email || '',
    address: (state) => (locale = 'ar') =>
      getTranslated(state.settings.contact_info?.address, locale),
    workingHours: (state) => (locale = 'ar') =>
      getTranslated(state.settings.contact_info?.working_hours, locale),

    // Social Channels (Backend-sourced)
    socialLinks: (state) => state.settings.social_links || {},

    // Footer (Backend-sourced)
    footerInfo: (state) => state.settings.footer_info || {},
    footerAbout: (state) => (locale = 'ar') =>
      getTranslated(state.settings.footer_info?.about_text, locale),
    footerCopyright: (state) => (locale = 'ar') =>
      getTranslated(state.settings.footer_info?.copyright_text, locale),

    // Top Urgent Announcement Bar (Backend-sourced)
    topAnnouncement: (state) => state.settings.top_announcement_bar || {},
    isTopAnnouncementActive: (state) => Boolean(state.settings.top_announcement_bar?.is_enabled),
    topAnnouncementText: (state) => (locale = 'ar') =>
      getTranslated(state.settings.top_announcement_bar?.text, locale),
    topAnnouncementLink: (state) => state.settings.top_announcement_bar?.link_url || '/admissions',

    // Site Statistics & Counters (Backend-sourced)
    siteStatistics: (state) => state.settings.site_statistics || { title: { ar: '', en: '' }, subtitle: { ar: '', en: '' }, items: [] },
    statisticsTitle: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_statistics?.title, locale),
    statisticsSubtitle: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_statistics?.subtitle, locale),
    activeStatisticsItems: (state) => {
      const stats = state.settings.site_statistics?.items || []
      return [...stats]
        .filter((item) => item.is_active !== false)
        .sort((a, b) => (Number(a.order) || 0) - (Number(b.order) || 0))
    },
  },

  actions: {
    /**
     * Fetch public settings from backend API with in-flight deduplication.
     */
    async fetchPublicSettings(force = false) {
      if (!force && this.lastUpdated && Date.now() - this.lastUpdated < 60000) {
        return this.settings
      }

      if (publicSettingsInFlightPromise && !force) {
        return publicSettingsInFlightPromise
      }

      this.isLoading = true
      this.error = null

      publicSettingsInFlightPromise = (async () => {
        try {
          const fetched = await api.getPublicSettings()
          if (fetched && Object.keys(fetched).length > 0) {
            this.settings = {
              ...this.settings,
              ...fetched,
            }
            this.lastUpdated = Date.now()
            localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
            this.applyThemeToCssVariables()
          }
          return this.settings
        } catch (err) {
          console.warn('Could not fetch remote site settings:', err.message)
          return this.settings
        } finally {
          this.isLoading = false
          publicSettingsInFlightPromise = null
        }
      })()

      return publicSettingsInFlightPromise
    },

    /**
     * Fetch full settings (including admin details) for Admin Dashboard.
     */
    async fetchAdminSettings() {
      this.isLoading = true
      this.error = null
      try {
        const adminData = await api.getAdminSettings()
        if (adminData) {
          // Flatten into key-value map
          const flat = {}
          for (const key in adminData) {
            flat[key] = adminData[key].value !== undefined ? adminData[key].value : adminData[key]
          }
          this.settings = {
            ...this.settings,
            ...flat,
          }
          localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
          this.applyThemeToCssVariables()
        }
        return this.settings
      } catch (err) {
        console.warn('Failed to fetch admin settings from backend:', err.message)
        return this.settings
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Save dynamic settings changes to backend.
     */
    async saveSettings(updatedSettings) {
      this.isSaving = true
      this.error = null
      try {
        const settingsPayload = Object.keys(updatedSettings).map((key) => ({
          key,
          value: updatedSettings[key],
          group: this.resolveGroup(key),
          is_public: true,
        }))

        await api.updateAdminSettings(settingsPayload)

        this.settings = {
          ...this.settings,
          ...updatedSettings,
        }
        localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
        this.lastUpdated = new Date().toISOString()
        this.applyThemeToCssVariables()
        return true
      } catch (err) {
        this.error = err.message || 'فشل حفظ الإعدادات'
        throw err
      } finally {
        this.isSaving = false
      }
    },

    /**
     * Save single setting by key.
     */
    async saveSingleSetting(key, value) {
      this.isSaving = true
      try {
        await api.updateSingleSetting(key, {
          value,
          group: this.resolveGroup(key),
          is_public: true,
        })
        this.settings[key] = value
        localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
        this.applyThemeToCssVariables()
        return true
      } catch (err) {
        console.warn(`Failed to update setting ${key}:`, err.message)
        throw err
      } finally {
        this.isSaving = false
      }
    },

    /**
     * Reset all site settings to factory seed defaults via Backend.
     */
    async resetSettings() {
      this.isSaving = true
      try {
        await api.resetAdminSettings()
        await this.fetchAdminSettings()
        return true
      } catch (err) {
        console.warn('Reset API failed:', err.message)
        throw err
      } finally {
        this.isSaving = false
      }
    },

    /**
     * Injects custom CSS custom variables into the document for dynamic branding and theme updates.
     */
    applyThemeToCssVariables() {
      const colors = this.themeColors
      const root = document.documentElement

      if (colors) {
        if (colors.primary_color) {
          root.style.setProperty('--color-navy-950', colors.primary_color)
          // Compute slightly brighter shades for gradients and hover states
          root.style.setProperty('--color-navy-900', colors.primary_hover || colors.primary_color)
          root.style.setProperty('--color-navy-800', colors.primary_hover || colors.primary_color)
        }
        if (colors.secondary_gold) {
          root.style.setProperty('--color-gold-500', colors.secondary_gold)
          root.style.setProperty('--color-gold-400', colors.secondary_gold_light || colors.secondary_gold)
        }
        if (colors.accent_emerald) {
          root.style.setProperty('--color-emerald-600', colors.accent_emerald)
        }

        // Apply dynamic font families
        if (colors.font_family_ar) {
          root.style.setProperty('--font-family-ar', `"${colors.font_family_ar}", Cairo, system-ui, sans-serif`)
        }
        if (colors.font_family_en) {
          root.style.setProperty('--font-family-en', `"${colors.font_family_en}", Inter, system-ui, sans-serif`)
        }
      }

      // Inject custom CSS block if configured
      const customCss = this.settings.custom_css?.css_code
      let styleTag = document.getElementById('egyitech-custom-css')
      if (customCss) {
        if (!styleTag) {
          styleTag = document.createElement('style')
          styleTag.id = 'egyitech-custom-css'
          document.head.appendChild(styleTag)
        }
        styleTag.innerHTML = customCss
      } else if (styleTag) {
        styleTag.innerHTML = ''
      }

      // Update favicon if custom one exists
      if (this.siteFaviconUrl) {
        let link = document.querySelector("link[rel~='icon']")
        if (!link) {
          link = document.createElement('link')
          link.rel = 'icon'
          document.head.appendChild(link)
        }
        link.href = this.siteFaviconUrl
      }
    },

    resolveGroup(key) {
      const groupMap = {
        site_identity: 'branding',
        theme_colors: 'theme',
        president_message: 'president',
        hero_slider: 'hero',
        contact_info: 'contact',
        social_links: 'social',
        footer_info: 'footer',
        top_announcement_bar: 'general',
        site_statistics: 'statistics',
      }
      return groupMap[key] || 'general'
    },
  },
})

