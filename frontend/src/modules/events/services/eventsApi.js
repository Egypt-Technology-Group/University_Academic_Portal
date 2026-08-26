import apiClient from '../../../services/api'

export const eventsApi = {
  // Public Endpoints
  async getEvents(params = {}) {
    const response = await apiClient.get('/events', { params })
    return response.data.data || response.data
  },

  async registerEvent(eventId, attendeeData) {
    const response = await apiClient.post(`/events/${eventId}/register`, attendeeData)
    return response.data.data || response.data
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
}

export default eventsApi
