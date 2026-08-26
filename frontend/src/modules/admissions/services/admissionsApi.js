import apiClient from '../../../services/api'

export const admissionsApi = {
  // Public Endpoints
  async getActiveCycle() {
    const response = await apiClient.get('/admissions/active-cycle')
    return response.data
  },

  async submitApplication(payload) {
    let requestPayload = payload
    let config = {}

    // Support FormData if file attachments are sent
    if (payload instanceof FormData) {
      requestPayload = payload
      config = { headers: { 'Content-Type': 'multipart/form-data' } }
    }

    const response = await apiClient.post('/admissions/apply', requestPayload, config)
    return response.data.data || response.data
  },

  async trackApplication(params) {
    const response = await apiClient.post('/admissions/track', params)
    return response.data.data || response.data
  },

  // Admin Endpoints
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
}

export default admissionsApi
