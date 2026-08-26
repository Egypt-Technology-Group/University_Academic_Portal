import apiClient from '../../../services/api'

export const academicStructureApi = {
  // Public Endpoints
  async getColleges() {
    const response = await apiClient.get('/colleges')
    return response.data.data || response.data
  },

  async getCollege(slug) {
    const response = await apiClient.get(`/colleges/${slug}`)
    return response.data.data || response.data
  },

  async getDepartments(params = {}) {
    const response = await apiClient.get('/departments', { params })
    return response.data.data || response.data
  },

  async getPrograms(params = {}) {
    const response = await apiClient.get('/programs', { params })
    return response.data.data || response.data
  },

  async getProgram(slug) {
    const response = await apiClient.get(`/programs/${slug}`)
    return response.data.data || response.data
  },

  async getFaculty(params = {}) {
    const response = await apiClient.get('/faculty', { params })
    return response.data.data || response.data
  },

  // Admin Endpoints
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
}

export default academicStructureApi
