/**
 * Modules API Service
 *
 * Provides RESTful communication with backend Module Management endpoints.
 */

import apiClient from './api'

export const modulesApi = {
  /**
   * Fetch lightweight module manifest for application startup.
   */
  async getManifest() {
    const response = await apiClient.get('/modules/manifest')
    return response.data.data || response.data
  },

  /**
   * Fetch all registered modules with active status and dependency information.
   *
   * @param {Object} [params]
   * @returns {Promise<{ data: Array<Object>, meta: Object }>}
   */
  async getModules(params = {}) {
    const response = await apiClient.get('/modules', { params })
    return response.data
  },

  /**
   * Fetch dependency tree, blocking dependents, and resolution state for a specific module.
   *
   * @param {string} id
   * @returns {Promise<Object>}
   */
  async getModuleDependencies(id) {
    const response = await apiClient.get(`/modules/${id}/dependencies`)
    return response.data.data || response.data
  },

  /**
   * Toggle a module's enabled status, or explicitly enable/disable it.
   *
   * @param {string} id
   * @param {boolean|null} [explicitEnabled=null]
   * @returns {Promise<{ message: string, data: { id: string, is_enabled: boolean } }>}
   */
  async toggleModule(id, explicitEnabled = null) {
    const payload = explicitEnabled !== null && explicitEnabled !== undefined
      ? { enabled: explicitEnabled }
      : {}
    const response = await apiClient.patch(`/modules/${id}/toggle`, payload)
    return response.data
  },
}

export default modulesApi
