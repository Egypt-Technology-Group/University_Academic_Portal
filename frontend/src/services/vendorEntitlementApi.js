import apiClient from './api'

export const vendorEntitlementApi = {
  /**
   * Fetch current cryptographic entitlement status and licensed modules list.
   */
  async getStatus() {
    const response = await apiClient.get('/vendor/entitlement/status')
    return response.data.data || response.data
  },

  /**
   * Cryptographically verify a vendor license package before applying.
   */
  async verifyPackage(packageData) {
    const response = await apiClient.post('/vendor/entitlement/verify', packageData)
    return response.data.data || response.data
  },

  /**
   * Apply a signed vendor license package to activate entitled modules.
   */
  async applyLicense(packageData) {
    const response = await apiClient.post('/vendor/entitlement/apply', packageData)
    return response.data.data || response.data
  },
}
