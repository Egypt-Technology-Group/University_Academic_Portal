import apiClient from '../../../services/api'

export const documentsApi = {
  // Public Endpoints
  async getDocuments(params = {}) {
    const response = await apiClient.get('/documents', { params })
    return response.data.data || response.data
  },

  async incrementDocumentDownload(id) {
    const response = await apiClient.post(`/documents/${id}/download`)
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
      },
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
      },
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
}

export default documentsApi

