import { defineStore } from 'pinia'
import { api, getTranslated } from '../services/api'
import { defaultSettings } from '../services/defaultSettings'

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
      settings: cached || defaultSettings,
      isLoading: false,
      isSaving: false,
      error: null,
      lastUpdated: null,
    }
  },

  getters: {
    // Branding & Identity
    siteIdentity: (state) => state.settings.site_identity || defaultSettings.site_identity,
    siteName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.name, locale) || 'جامعة إيجي تك للتكنولوجيا والعلوم التطبيقية',
    siteShortName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.short_name, locale) || 'إيجي تك',
    siteSlogan: (state) => (locale = 'ar') =>
      getTranslated(state.settings.site_identity?.slogan, locale) || '',
    siteLogoUrl: (state) => state.settings.site_identity?.logo_url || '',
    siteFaviconUrl: (state) => state.settings.site_identity?.favicon_url || '',

    // Theme & Visual Identity
    themeColors: (state) => state.settings.theme_colors || defaultSettings.theme_colors,
    primaryColor: (state) => state.settings.theme_colors?.primary_color || '#0A2540',
    secondaryGold: (state) => state.settings.theme_colors?.secondary_gold || '#C59B27',
    accentEmerald: (state) => state.settings.theme_colors?.accent_emerald || '#059669',
    headerStyle: (state) => state.settings.theme_colors?.header_style || 'classic',

    // President Message & Leadership
    presidentMessage: (state) => state.settings.president_message || defaultSettings.president_message,
    presidentName: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.name, locale) || 'أ.د. عصام النجار',
    presidentTitle: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.title, locale) || 'رئيس الجامعة',
    presidentAvatar: (state) =>
      state.settings.president_message?.avatar_url ||
      'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    presidentQuote: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.quote, locale) || '',
    presidentFullMessage: (state) => (locale = 'ar') =>
      getTranslated(state.settings.president_message?.message, locale) || '',

    // Hero Slider
    heroSlider: (state) => state.settings.hero_slider?.slides || defaultSettings.hero_slider.slides,

    // Contact Information
    contactInfo: (state) => state.settings.contact_info || defaultSettings.contact_info,
    hotline: (state) => state.settings.contact_info?.hotline || '19850',
    phone: (state) => state.settings.contact_info?.phone || '+20 2 2456 7890',
    email: (state) => state.settings.contact_info?.email || 'info@egyitech.edu.eg',
    admissionsEmail: (state) => state.settings.contact_info?.admissions_email || 'admissions@university.edu.eg',
    address: (state) => (locale = 'ar') =>
      getTranslated(state.settings.contact_info?.address, locale) || '',
    workingHours: (state) => (locale = 'ar') =>
      getTranslated(state.settings.contact_info?.working_hours, locale) || '',

    // Social Channels
    socialLinks: (state) => state.settings.social_links || defaultSettings.social_links,

    // Footer
    footerInfo: (state) => state.settings.footer_info || defaultSettings.footer_info,
    footerAbout: (state) => (locale = 'ar') =>
      getTranslated(state.settings.footer_info?.about_text, locale) || '',
    footerCopyright: (state) => (locale = 'ar') =>
      getTranslated(state.settings.footer_info?.copyright_text, locale) || '',

    // Top Urgent Announcement Bar
    topAnnouncement: (state) => state.settings.top_announcement_bar || defaultSettings.top_announcement_bar,
    isTopAnnouncementActive: (state) => Boolean(state.settings.top_announcement_bar?.is_enabled),
    topAnnouncementText: (state) => (locale = 'ar') =>
      getTranslated(state.settings.top_announcement_bar?.text, locale) || '',
    topAnnouncementLink: (state) => state.settings.top_announcement_bar?.link_url || '/admissions',
  },

  actions: {
    /**
     * Fetch public settings from backend API.
     */
    async fetchPublicSettings() {
      this.isLoading = true
      this.error = null
      try {
        const fetched = await api.getPublicSettings()
        if (fetched && Object.keys(fetched).length > 0) {
          this.settings = {
            ...this.settings,
            ...fetched,
          }
          localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
          this.applyThemeToCssVariables()
        }
        return this.settings
      } catch (err) {
        console.warn('Could not fetch remote site settings, using default/cached:', err.message)
        return this.settings
      } finally {
        this.isLoading = false
      }
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
        this.settings[key] = value
        localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
        this.applyThemeToCssVariables()
        return true
      } finally {
        this.isSaving = false
      }
    },

    /**
     * Reset all site settings to factory seed defaults.
     */
    async resetSettings() {
      this.isSaving = true
      try {
        await api.resetAdminSettings()
        this.settings = { ...defaultSettings }
        localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
        this.applyThemeToCssVariables()
        return true
      } catch (err) {
        console.warn('Reset API failed, resetting local store:', err.message)
        this.settings = { ...defaultSettings }
        localStorage.setItem('egyitech_site_settings', JSON.stringify(this.settings))
        this.applyThemeToCssVariables()
        return true
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
      }
      return groupMap[key] || 'general'
    },
  },
})
