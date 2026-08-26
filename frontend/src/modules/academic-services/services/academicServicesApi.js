import apiClient from '../../../services/api'

export const academicServicesApi = {
  // Public Endpoints
  async getExamSchedules(params = {}) {
    const response = await apiClient.get('/exam-schedules', { params })
    return response.data.data || response.data
  },

  async submitStudentRequest(data) {
    const response = await apiClient.post('/student-services/apply', data)
    return response.data.data || response.data
  },

  async verifyOfficialStatement(code, hash = '') {
    const response = await apiClient.get('/verify-statement', { params: { code, hash } })
    return response.data
  },

  // Admin Endpoints
  async getStudentRequests(params = {}) {
    const response = await apiClient.get('/admin/student-requests', { params })
    return response.data.data || response.data
  },

  async updateStudentRequestStatus(id, { status, admin_notes, handled_by }) {
    const response = await apiClient.patch(`/admin/student-requests/${id}/status`, { status, admin_notes, handled_by })
    return response.data.data || response.data
  },

  async deleteStudentRequest(id) {
    const response = await apiClient.delete(`/admin/student-requests/${id}`)
    return response.data
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
}

export default academicServicesApi
