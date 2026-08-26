/**
 * Modules Pinia Store
 *
 * Synchronizes client-side module definitions and dynamic routing states
 * with backend modular plugin features and verified entitlement status.
 */

import { defineStore } from 'pinia'
import { modulesApi } from '../services/modulesApi'
import { moduleRegistry } from '../core/modules/moduleRegistry'

const MANIFEST_CACHE_KEY = 'egyitech_modules_manifest_v1'

export const useModulesStore = defineStore('modules', {
  state: () => ({
    /** @type {Array<Object>} */
    modules: [],
    /** @type {Array<string>} */
    enabledIds: [],
    loading: false,
    error: null,
    conflictError: null,
    initialized: false,
    lastFetched: null,
  }),

  getters: {
    /**
     * Return all registered modules merged with backend runtime status.
     */
    allModules: (state) => {
      const registered = moduleRegistry.getAll()
      return registered.map((reg) => {
        const backendInfo = state.modules.find((m) => m.id === reg.id)
        return {
          ...reg,
          ...(backendInfo || {}),
          is_enabled: state.enabledIds.includes(reg.id),
        }
      })
    },

    /**
     * Return only enabled modules.
     */
    enabledModuleList: (state) => {
      return moduleRegistry.getEnabled(state.enabledIds)
    },

    /**
     * Check if a module is currently enabled and verified.
     *
     * @returns {(id: string) => boolean}
     */
    isModuleEnabled: (state) => (id) => {
      if (!id) return false
      return state.enabledIds.includes(id)
    },

    /**
     * Get module definition by ID.
     *
     * @returns {(id: string) => Object|null}
     */
    getModule: (state) => (id) => {
      if (!id) return null
      const backendInfo = state.modules.find((m) => m.id === id)
      const registered = moduleRegistry.get(id)
      if (!registered && !backendInfo) return null

      return {
        ...(registered || {}),
        ...(backendInfo || {}),
        is_enabled: state.enabledIds.includes(id),
      }
    },

    /**
     * Get filtered navigation items for a given section ('public' or 'admin').
     *
     * @returns {(section?: 'public'|'admin') => Array<Object>}
     */
    getNavItems: (state) => (section = 'public') => {
      return moduleRegistry.getNavItems(state.enabledIds, section)
    },

    /**
     * Check if dependencies for a given module ID are satisfied.
     *
     * @returns {(id: string) => { valid: boolean, missingDependencies: string[] }}
     */
    canEnableModule: (state) => (id) => {
      return moduleRegistry.validateDependencies(id, state.enabledIds)
    },
  },

  actions: {
    /**
     * Instantly hydrate state from local storage cache during startup.
     */
    hydrateFromCache() {
      try {
        const raw = localStorage.getItem(MANIFEST_CACHE_KEY)
        if (raw) {
          const cached = JSON.parse(raw)
          if (Array.isArray(cached?.enabledIds)) {
            this.enabledIds = cached.enabledIds
            this.initialized = true
          }
        }
      } catch (err) {
        console.warn('[useModulesStore] Cache hydration failed:', err)
      }
    },

    /**
     * Fetch lightweight module manifest asynchronously for startup / navbar.
     *
     * @param {boolean} [force=false]
     * @returns {Promise<Array<string>>}
     */
    async fetchManifest(force = false) {
      if (this.initialized && !force && Date.now() - (this.lastFetched || 0) < 60000) {
        return this.enabledIds
      }

      this.loading = true
      this.error = null

      try {
        const manifest = await modulesApi.getManifest()
        const enabled = Array.isArray(manifest?.enabled_ids) ? manifest.enabled_ids : []

        this.enabledIds = enabled
        this.initialized = true
        this.lastFetched = Date.now()

        // Cache verified manifest state locally
        try {
          localStorage.setItem(
            MANIFEST_CACHE_KEY,
            JSON.stringify({ enabledIds: enabled, timestamp: Date.now() })
          )
        } catch (e) {
        }

        return enabled
      } catch (err) {
        console.warn('[useModulesStore] Failed to fetch manifest:', err)
        this.error = err?.message || 'Failed to fetch module manifest.'

        // If no cache was hydrated, strictly remain fail-closed ([])
        if (!this.initialized) {
          this.enabledIds = []
          this.initialized = true
        }

        return this.enabledIds
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch full module details for the Module Management control plane.
     *
     * @param {boolean} [force=false]
     * @returns {Promise<Array<Object>>}
     */
    async fetchModules(force = false) {
      if (this.modules.length > 0 && !force) {
        return this.modules
      }

      this.loading = true
      this.error = null

      try {
        const response = await modulesApi.getModules()
        const items = Array.isArray(response?.data) ? response.data : []

        this.modules = items
        this.enabledIds = items
          .filter((item) => item.is_enabled === true || item.is_enabled === 1)
          .map((item) => item.id)

        this.initialized = true
        this.lastFetched = Date.now()

        try {
          localStorage.setItem(
            MANIFEST_CACHE_KEY,
            JSON.stringify({ enabledIds: this.enabledIds, timestamp: Date.now() })
          )
        } catch (e) {
        }

        return items
      } catch (err) {
        console.error('[useModulesStore] Failed to fetch modules list:', err)
        this.error = err?.message || 'Failed to fetch module registry status from server.'
        return this.modules
      } finally {
        this.loading = false
      }
    },

    /**
     * Ensure module state is loaded before route execution.
     */
    async ensureLoaded() {
      if (this.initialized) return
      this.hydrateFromCache()
      if (!this.initialized) {
        await this.fetchManifest()
      }
    },

    /**
     * Toggle or explicitly set module active status.
     *
     * @param {string} id
     * @param {boolean|null} [explicitState=null]
     * @returns {Promise<Object>}
     */
    async toggleModule(id, explicitState = null) {
      this.loading = true
      this.error = null
      this.conflictError = null

      try {
        const response = await modulesApi.toggleModule(id, explicitState)
        const isEnabled = response?.data?.is_enabled ?? !this.enabledIds.includes(id)

        if (isEnabled) {
          if (!this.enabledIds.includes(id)) {
            this.enabledIds.push(id)
          }
        } else {
          this.enabledIds = this.enabledIds.filter((moduleId) => moduleId !== id)
        }

        // Update module item in state list
        const modIdx = this.modules.findIndex((m) => m.id === id)
        if (modIdx !== -1) {
          this.modules[modIdx].is_enabled = isEnabled
        }

        try {
          localStorage.setItem(
            MANIFEST_CACHE_KEY,
            JSON.stringify({ enabledIds: this.enabledIds, timestamp: Date.now() })
          )
        } catch (e) {
        }

        return response
      } catch (err) {
        console.error(`[useModulesStore] Error toggling module ${id}:`, err)
        this.error = err?.message || 'Failed to toggle module.'

        if (err?.status === 409 || err?.response?.status === 409) {
          const conflictData = err.response?.data || err.context || {}
          this.conflictError = {
            id,
            message: conflictData.message || err.message,
            error: conflictData.error || 'dependency_conflict',
            blocking_dependents: conflictData.blocking_dependents || [],
            missing_dependencies: conflictData.missing_dependencies || [],
          }
        }

        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Clear error state.
     */
    clearErrors() {
      this.error = null
      this.conflictError = null
    },
  },
})
