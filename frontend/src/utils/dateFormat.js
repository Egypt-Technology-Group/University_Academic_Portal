/**
 * Centralized Date, Time, and Range Formatting Utilities
 * Standardized across EgyiTech Academic Platform
 */

/**
 * Format date in localized standard style: "DD MMM YYYY" (e.g., "15 Oct 2025" or "١٥ أكتوبر ٢٠٢٥")
 * @param {string|number|Date} dateVal - Input date string or timestamp
 * @param {string} locale - 'ar' or 'en'
 * @param {object} customOptions - Optional Intl.DateTimeFormat options
 * @returns {string}
 */
export const formatStandardDate = (dateVal, locale = 'ar', customOptions = {}) => {
  if (!dateVal) return ''
  try {
    const d = new Date(dateVal)
    if (isNaN(d.getTime())) return String(dateVal)

    const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)
    const activeLocale = isArabic ? 'ar-EG' : 'en-US'

    const defaultOptions = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      ...customOptions,
    }

    return d.toLocaleDateString(activeLocale, defaultOptions)
  } catch (e) {
    return String(dateVal)
  }
}

/**
 * Format full date & time localized: "DD MMM YYYY, HH:mm"
 * @param {string|number|Date} dateVal
 * @param {string} locale
 * @returns {string}
 */
export const formatStandardDateTime = (dateVal, locale = 'ar') => {
  if (!dateVal) return ''
  try {
    const d = new Date(dateVal)
    if (isNaN(d.getTime())) return String(dateVal)

    const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)
    const activeLocale = isArabic ? 'ar-EG' : 'en-US'

    return d.toLocaleString(activeLocale, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
    })
  } catch (e) {
    return String(dateVal)
  }
}

/**
 * Format localized Time: "HH:mm AM/PM" or 24h
 * @param {string} timeStr - "10:00:00" or ISO string
 * @param {string} locale
 * @returns {string}
 */
export const formatStandardTime = (timeStr, locale = 'ar') => {
  if (!timeStr) return ''
  try {
    if (timeStr.includes(':') && !timeStr.includes('T')) {
      const parts = timeStr.split(':')
      const h = parseInt(parts[0], 10)
      const m = parts[1] || '00'
      const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)
      const period = h >= 12 ? (isArabic ? 'م' : 'PM') : (isArabic ? 'ص' : 'AM')
      const formattedHour = h % 12 || 12
      return `${formattedHour}:${m} ${period}`
    }
    const d = new Date(timeStr)
    const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)
    return d.toLocaleTimeString(isArabic ? 'ar-EG' : 'en-US', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
    })
  } catch (e) {
    return String(timeStr)
  }
}

/**
 * Format localized Time Range: "09:00 AM - 12:00 PM"
 * @param {string} startTime
 * @param {string} endTime
 * @param {string} locale
 * @returns {string}
 */
export const formatTimeRange = (startTime, endTime, locale = 'ar') => {
  if (!startTime) return ''
  const start = formatStandardTime(startTime, locale)
  if (!endTime) return start
  const end = formatStandardTime(endTime, locale)
  return `${start} - ${end}`
}

/**
 * Get localized Month Name (short)
 * @param {string|Date} dateVal
 * @param {string} locale
 * @returns {string}
 */
export const getLocalizedMonth = (dateVal, locale = 'ar') => {
  if (!dateVal) return ''
  try {
    const d = new Date(dateVal)
    const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)
    return d.toLocaleDateString(isArabic ? 'ar-EG' : 'en-US', { month: 'short' })
  } catch {
    return ''
  }
}

/**
 * Get Numeric Day
 * @param {string|Date} dateVal
 * @returns {number|string}
 */
export const getLocalizedDay = (dateVal) => {
  if (!dateVal) return ''
  try {
    const d = new Date(dateVal)
    return d.getDate()
  } catch {
    return ''
  }
}

/**
 * Format relative time (e.g., "5 mins ago" / "منذ ٥ دقائق")
 * @param {string|number|Date} dateVal
 * @param {string} locale
 * @returns {string}
 */
export const formatRelativeTime = (dateVal, locale = 'ar') => {
  if (!dateVal) return ''
  try {
    const d = new Date(dateVal)
    const now = new Date()
    const diffSec = Math.floor((now.getTime() - d.getTime()) / 1000)
    const isArabic = locale === 'ar' || (typeof locale === 'boolean' && locale)

    if (diffSec < 60) {
      return isArabic ? 'الآن' : 'Just now'
    }
    const diffMin = Math.floor(diffSec / 60)
    if (diffMin < 60) {
      return isArabic ? `منذ ${diffMin} دقيقة` : `${diffMin}m ago`
    }
    const diffHours = Math.floor(diffMin / 60)
    if (diffHours < 24) {
      return isArabic ? `منذ ${diffHours} ساعة` : `${diffHours}h ago`
    }
    const diffDays = Math.floor(diffHours / 24)
    if (diffDays < 7) {
      return isArabic ? `منذ ${diffDays} يوم` : `${diffDays}d ago`
    }
    return formatStandardDate(dateVal, locale)
  } catch {
    return formatStandardDate(dateVal, locale)
  }
}
