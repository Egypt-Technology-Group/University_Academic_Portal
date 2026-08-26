import apiClient from '../../../services/api'

export const cmsApi = {
  // Public Endpoints
  async getNews(params = {}) {
    const response = await apiClient.get('/news', { params })
    return response.data.data || response.data
  },

  async getNewsArticle(slug) {
    const response = await apiClient.get(`/news/${slug}`)
    return {
      article: response.data.data || response.data,
      related: response.data.related_articles || [],
    }
  },

  async getAnnouncements(params = {}) {
    const response = await apiClient.get('/announcements', { params })
    return response.data.data || response.data
  },

  // Admin CMS Management
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
}

export default cmsApi
