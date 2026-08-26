import axios from 'axios'
import { normalizeError, ApiError } from '../utils/errorHandler'

// Import modular API services
import { academicStructureApi } from '../modules/academic-structure/services/academicStructureApi'
import { admissionsApi } from '../modules/admissions/services/admissionsApi'
import { cmsApi } from '../modules/cms/services/cmsApi'
import { eventsApi } from '../modules/events/services/eventsApi'
import { documentsApi } from '../modules/documents/services/documentsApi'
import { academicServicesApi } from '../modules/academic-services/services/academicServicesApi'
import { resultsApi } from '../modules/results/services/resultsApi'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || import.meta.env.api || 'http://127.0.0.1:8000/api/v1',
  timeout: 15000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Request Interceptor to dynamically set Accept-Language, X-Locale and Authorization
apiClient.interceptors.request.use((config) => {
  const currentLocale = localStorage.getItem('egyitech_locale') || 'ar'
  config.headers['Accept-Language'] = currentLocale
  config.headers['X-Locale'] = currentLocale

  const token = localStorage.getItem('egyitech_auth_token')
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
}, (error) => {
  return Promise.reject(error)
})

// Response Interceptor for handling token expiration and normalizing all API errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    const currentLocale = localStorage.getItem('egyitech_locale') || 'ar'
    const normalized = normalizeError(error, currentLocale)

    if (normalized.status === 401) {
      const url = error.config?.url || ''
      if (!url.includes('/auth/login')) {
        localStorage.removeItem('egyitech_auth_token')
        localStorage.removeItem('egyitech_auth_user')
        if (window.location.pathname.startsWith('/admin') && window.location.pathname !== '/admin/login') {
          window.location.href = '/admin/login'
        }
      }
    }
    return Promise.reject(normalized)
  }
)

// Translation helper utility for objects that contain { ar: '...', en: '...' } or strings
export function getTranslated(field, locale = 'ar') {
  if (!field) return ''
  if (typeof field === 'string') return field
  if (typeof field === 'object') {
    return field[locale] || field.ar || field.en || Object.values(field)[0] || ''
  }
  return String(field)
}

export {
  formatStandardDate,
  formatStandardDateTime,
  formatStandardTime,
  formatTimeRange,
  getLocalizedMonth,
  getLocalizedDay,
  formatRelativeTime
} from '../utils/dateFormat'

// Core API Methods (Auth, Site Settings, Admin Dashboard, Audit Logs)
const coreApi = {
  // Authentication
  async login({ email, password }) {
    const response = await apiClient.post('/auth/login', { email, password })
    return response.data
  },

  async getAuthUser() {
    const response = await apiClient.get('/auth/me')
    return response.data?.user || response.data?.data || response.data
  },

  async logout() {
    await apiClient.post('/auth/logout')
    return { success: true }
  },

  // Admin Dashboard & Statistics
  async getAdminStats() {
    const response = await apiClient.get('/admin/stats')
    return response.data.data || response.data
  },

  // Dynamic Site Settings API
  async getPublicSettings() {
    const response = await apiClient.get('/settings')
    return response.data.settings || response.data
  },

  async getAdminSettings() {
    const response = await apiClient.get('/admin/settings')
    return response.data.settings || response.data
  },

  async updateAdminSettings(settingsArray) {
    const response = await apiClient.post('/admin/settings', { settings: settingsArray })
    return response.data
  },

  async updateSingleSetting(key, { value, group = 'general', is_public = true }) {
    const response = await apiClient.patch(`/admin/settings/${key}`, { value, group, is_public })
    return response.data
  },

  async resetAdminSettings() {
    const response = await apiClient.post('/admin/settings/reset')
    return response.data
  },

  // Enterprise Audit Trail & Compliance Log Endpoints
  async getAuditLogs(params = {}) {
    const response = await apiClient.get('/admin/audit-logs', { params })
    return response.data
  },

  async getAuditLog(id) {
    const response = await apiClient.get(`/admin/audit-logs/${id}`)
    return response.data.data || response.data
  },

  async verifyAuditIntegrity(params = {}) {
    const response = await apiClient.get('/admin/audit-logs/integrity', { params })
    return response.data
  },

  getAuditExportUrl(params = {}) {
    const query = new URLSearchParams(params).toString()
    const baseUrl = apiClient.defaults.baseURL || 'http://localhost:8000/api/v1'
    return `${baseUrl}/admin/audit-logs/export${query ? '?' + query : ''}`
  }
}

// Unified API Gateway aggregating Core and 7 Modular Services
export const api = {
  ...coreApi,
  ...academicStructureApi,
  ...admissionsApi,
  ...cmsApi,
  ...eventsApi,
  ...documentsApi,
  ...academicServicesApi,
  ...resultsApi,
}

// Re-export modular API services individually
export {
  academicStructureApi,
  admissionsApi,
  cmsApi,
  eventsApi,
  documentsApi,
  academicServicesApi,
  resultsApi,
}

export default apiClient



