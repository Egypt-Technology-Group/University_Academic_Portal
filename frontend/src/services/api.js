import axios from 'axios'
import { normalizeError, ApiError } from '../utils/errorHandler'

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
      localStorage.removeItem('egyitech_auth_token')
      localStorage.removeItem('egyitech_auth_user')
      if (window.location.pathname.startsWith('/admin') && window.location.pathname !== '/admin/login') {
        window.location.href = '/admin/login'
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

// API Service Methods
export const api = {
  // Colleges
  async getColleges() {
    const response = await apiClient.get('/colleges')
    return response.data.data || response.data
  },

  async getCollege(slug) {
    const response = await apiClient.get(`/colleges/${slug}`)
    return response.data.data || response.data
  },

  // Departments
  async getDepartments(params = {}) {
    const response = await apiClient.get('/departments', { params })
    return response.data.data || response.data
  },

  // Programs
  async getPrograms(params = {}) {
    const response = await apiClient.get('/programs', { params })
    return response.data.data || response.data
  },

  async getProgram(slug) {
    const response = await apiClient.get(`/programs/${slug}`)
    return response.data.data || response.data
  },

  // Faculty
  async getFaculty(params = {}) {
    const response = await apiClient.get('/faculty', { params })
    return response.data.data || response.data
  },

  // News
  async getNews(params = {}) {
    const response = await apiClient.get('/news', { params })
    return response.data.data || response.data
  },

  async getNewsArticle(slug) {
    const response = await apiClient.get(`/news/${slug}`)
    return {
      article: response.data.data || response.data,
      related: response.data.related_articles || []
    }
  },

  // Events
  async getEvents(params = {}) {
    const response = await apiClient.get('/events', { params })
    return response.data.data || response.data
  },

  async registerEvent(eventId, attendeeData) {
    const response = await apiClient.post(`/events/${eventId}/register`, attendeeData)
    return response.data.data || response.data
  },

  // Announcements
  async getAnnouncements(params = {}) {
    const response = await apiClient.get('/announcements', { params })
    return response.data.data || response.data
  },

  // Documents
  async getDocuments(params = {}) {
    const response = await apiClient.get('/documents', { params })
    return response.data.data || response.data
  },

  async incrementDocumentDownload(id) {
    const response = await apiClient.post(`/documents/${id}/download`)
    return response.data
  },

  // Admissions
  async getActiveCycle() {
    const response = await apiClient.get('/admissions/active-cycle')
    return response.data
  },

  async submitApplication(formData) {
    const response = await apiClient.post('/admissions/apply', formData)
    return response.data.data || response.data
  },

  async trackApplication({ application_number, national_id, email }) {
    const response = await apiClient.post('/admissions/track', {
      application_number,
      national_id,
      email,
    })
    return response.data.data || response.data
  },

  // Student Portal
  async inquireStudentResults({ student_id_number, academic_term_id }) {
    const response = await apiClient.post('/student-portal/results', {
      student_id_number,
      academic_term_id,
    })
    return response.data
  },

  async simulateStudentRegistration({ student_id_number, selected_courses }) {
    const response = await apiClient.post('/student-portal/simulate-registration', {
      student_id_number,
      selected_courses,
    })
    return response.data?.data || response.data
  },

  // Authentication
  async login({ email, password }) {
    const response = await apiClient.post('/auth/login', { email, password })
    return response.data
  },

  async getAuthUser() {
    const response = await apiClient.get('/auth/me')
    return response.data.data || response.data
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

  // Admin Admissions Management
  async getAdminApplications(params = {}) {
    const response = await apiClient.get('/admin/applications', { params })
    return response.data.data || response.data
  },

  async updateApplicationStatus(applicationId, payload = {}) {
    const response = await apiClient.patch(`/admin/applications/${applicationId}/status`, payload)
    return response.data.data || response.data
  },

  async verifyDocument(applicationId, documentId, payload = {}) {
    const response = await apiClient.post(`/admin/applications/${applicationId}/documents/${documentId}/verify`, payload)
    return response.data.data || response.data
  },

  async requestMissingDocuments(applicationId, payload = {}) {
    const response = await apiClient.post(`/admin/applications/${applicationId}/request-missing-docs`, payload)
    return response.data.data || response.data
  },

  // Admin CMS: News Management
  async createNews(formData) {
    const response = await apiClient.post('/admin/news', formData)
    return response.data.data || response.data
  },

  async updateNews(id, formData) {
    const response = await apiClient.patch(`/admin/news/${id}`, formData)
    return response.data.data || response.data
  },

  async deleteNews(id) {
    const response = await apiClient.delete(`/admin/news/${id}`)
    return response.data
  },

  // Admin CMS: Announcements Management
  async createAnnouncement(formData) {
    const response = await apiClient.post('/admin/announcements', formData)
    return response.data.data || response.data
  },

  async updateAnnouncement(id, formData) {
    const response = await apiClient.patch(`/admin/announcements/${id}`, formData)
    return response.data.data || response.data
  },

  async deleteAnnouncement(id) {
    const response = await apiClient.delete(`/admin/announcements/${id}`)
    return response.data
  },

  // Admin Events Management
  async createEvent(formData) {
    const response = await apiClient.post('/admin/events', formData)
    return response.data.data || response.data
  },

  async updateEvent(id, formData) {
    const response = await apiClient.patch(`/admin/events/${id}`, formData)
    return response.data.data || response.data
  },

  async deleteEvent(id) {
    const response = await apiClient.delete(`/admin/events/${id}`)
    return response.data
  },

  // Admin Documents Repository Management
  async createDocument(formData, onProgress = null) {
    const isMultipart = formData instanceof FormData
    const headers = isMultipart ? { 'Content-Type': 'multipart/form-data' } : {}
    const config = {
      headers,
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(percent)
        }
      }
    }
    const response = await apiClient.post('/admin/documents', formData, config)
    return response.data.data || response.data
  },

  async updateDocument(id, formData, onProgress = null) {
    const isMultipart = formData instanceof FormData
    const headers = isMultipart ? { 'Content-Type': 'multipart/form-data' } : {}
    const config = {
      headers,
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(percent)
        }
      }
    }
    const response = await apiClient.post(`/admin/documents/${id}`, formData, config)
    return response.data.data || response.data
  },

  async toggleArchiveDocument(id) {
    const response = await apiClient.post(`/admin/documents/${id}/toggle-archive`)
    return response.data.data || response.data
  },

  async deleteDocument(id) {
    const response = await apiClient.delete(`/admin/documents/${id}`)
    return response.data
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

  // Academic & Student Services API
  async getStudentRequests(params = {}) {
    const response = await apiClient.get('/admin/student-requests', { params })
    return response.data.data || response.data
  },

  async updateStudentRequestStatus(id, { status, admin_notes, handled_by }) {
    const response = await apiClient.patch(`/admin/student-requests/${id}/status`, { status, admin_notes, handled_by })
    return response.data.data || response.data
  },

  async submitStudentRequest(data) {
    const response = await apiClient.post('/student-services/apply', data)
    return response.data.data || response.data
  },

  async getOfficialStatements(params = {}) {
    const response = await apiClient.get('/admin/official-statements', { params })
    return response.data.data || response.data
  },

  async issueOfficialStatement(data) {
    let payload = data
    let config = {}
    if (data.document instanceof File) {
      payload = new FormData()
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
    }
    const response = await apiClient.post('/admin/official-statements/issue', payload, config)
    return response.data.data || response.data
  },

  async verifyOfficialStatement(code, hash = '') {
    const response = await apiClient.get('/verify-statement', { params: { code, hash } })
    return response.data
  },

  async getExamSchedules(params = {}) {
    const response = await apiClient.get('/exam-schedules', { params })
    return response.data.data || response.data
  },

  async storeExamSchedule(data) {
    let payload = data
    let config = {}
    if (data.timetable_document instanceof File) {
      payload = new FormData()
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
    }
    const response = await apiClient.post('/admin/exam-schedules', payload, config)
    return response.data.data || response.data
  },

  async updateExamSchedule(id, data) {
    let payload = data
    let config = {}
    if (data.timetable_document instanceof File) {
      payload = new FormData()
      payload.append('_method', 'PATCH')
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
      const response = await apiClient.post(`/admin/exam-schedules/${id}`, payload, config)
      return response.data.data || response.data
    }
    const response = await apiClient.patch(`/admin/exam-schedules/${id}`, payload)
    return response.data.data || response.data
  },

  async deleteExamSchedule(id) {
    const response = await apiClient.delete(`/admin/exam-schedules/${id}`)
    return response.data
  },

  async deleteStudentRequest(id) {
    const response = await apiClient.delete(`/admin/student-requests/${id}`)
    return response.data
  },

  // ----------------------------------------------------
  // Academic Structure Admin CRUD Methods
  // ----------------------------------------------------
  async createCollege(data) {
    const response = await apiClient.post('/admin/colleges', data)
    return response.data.data || response.data
  },

  async updateCollege(id, data) {
    const response = await apiClient.patch(`/admin/colleges/${id}`, data)
    return response.data.data || response.data
  },

  async deleteCollege(id) {
    const response = await apiClient.delete(`/admin/colleges/${id}`)
    return response.data
  },

  async createDepartment(data) {
    const response = await apiClient.post('/admin/departments', data)
    return response.data.data || response.data
  },

  async updateDepartment(id, data) {
    const response = await apiClient.patch(`/admin/departments/${id}`, data)
    return response.data.data || response.data
  },

  async deleteDepartment(id) {
    const response = await apiClient.delete(`/admin/departments/${id}`)
    return response.data
  },

  async createProgram(data) {
    let payload = data
    let config = {}
    if (data.study_plan_document instanceof File) {
      payload = new FormData()
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
    }
    const response = await apiClient.post('/admin/programs', payload, config)
    return response.data.data || response.data
  },

  async updateProgram(id, data) {
    let payload = data
    let config = {}
    if (data.study_plan_document instanceof File) {
      payload = new FormData()
      payload.append('_method', 'PATCH')
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
      const response = await apiClient.post(`/admin/programs/${id}`, payload, config)
      return response.data.data || response.data
    }
    const response = await apiClient.patch(`/admin/programs/${id}`, payload)
    return response.data.data || response.data
  },

  async deleteProgram(id) {
    const response = await apiClient.delete(`/admin/programs/${id}`)
    return response.data
  },

  // ----------------------------------------------------
  // Faculty & Researchers Admin CRUD Methods
  // ----------------------------------------------------
  async createFaculty(data) {
    let payload = data
    let config = {}
    if (data.cv_file instanceof File) {
      payload = new FormData()
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
    }
    const response = await apiClient.post('/admin/faculty', payload, config)
    return response.data.data || response.data
  },

  async updateFaculty(id, data) {
    let payload = data
    let config = {}
    if (data.cv_file instanceof File) {
      payload = new FormData()
      payload.append('_method', 'PATCH')
      Object.entries(data).forEach(([key, val]) => {
        if (val !== undefined && val !== null) payload.append(key, val)
      })
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
      const response = await apiClient.post(`/admin/faculty/${id}`, payload, config)
      return response.data.data || response.data
    }
    const response = await apiClient.patch(`/admin/faculty/${id}`, payload)
    return response.data.data || response.data
  },

  async deleteFaculty(id) {
    const response = await apiClient.delete(`/admin/faculty/${id}`)
    return response.data
  },

  // ----------------------------------------------------
  // Enterprise Audit Trail & Compliance Log Endpoints
  // ----------------------------------------------------
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

export default apiClient


