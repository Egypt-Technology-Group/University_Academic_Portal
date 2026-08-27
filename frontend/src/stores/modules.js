/**
 * Modules Pinia Store
 *
 * Synchronizes client-side module definitions and dynamic routing states
 * with backend modular plugin features and verified entitlement status.
 */

import { defineStore } from 'pinia'
import { modulesApi } from '../services/modulesApi'
import { moduleRegistry } from '../core/modules/moduleRegistry'
import { apiCache } from '../services/apiCache'

const MANIFEST_CACHE_KEY = 'egyitech_modules_manifest_v1'

let manifestInFlightPromise = null
let adminModulesInFlightPromise = null

export const useModulesStore = defineStore('modules', {
  state: () => {
    let cachedEnabled = null
    let cachedTimestamp = null
    let hasValidCache = false

    try {
      const raw = localStorage.getItem(MANIFEST_CACHE_KEY)
      if (raw) {
        const parsed = JSON.parse(raw)
        if (Array.isArray(parsed?.enabledIds)) {
          cachedEnabled = parsed.enabledIds
          cachedTimestamp = Number(parsed.timestamp) || null
          hasValidCache = true
        }
      }
    } catch (e) {
      console.warn('[useModulesStore] Initial synchronous cache hydration failed:', e)
    }

    return {
      /** @type {Array<Object>} */
      modules: [],
      /** @type {Array<string>} */
      enabledIds: cachedEnabled || [],
      loading: false,
      error: null,
      conflictError: null,
      initialized: hasValidCache,
      lastFetched: cachedTimestamp,
    }
  },

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
      if (this.initialized && this.enabledIds.length > 0) {
        return
      }

      try {
        const raw = localStorage.getItem(MANIFEST_CACHE_KEY)
        if (raw) {
          const cached = JSON.parse(raw)
          if (Array.isArray(cached?.enabledIds)) {
            this.enabledIds = cached.enabledIds
            this.initialized = true
            this.lastFetched = Number(cached.timestamp) || null
          }
        }
      } catch (err) {
        console.warn('[useModulesStore] Cache hydration failed:', err)
      }
    },

    /**
     * Fetch lightweight module manifest asynchronously for startup / navbar.
     * Deduplicates in-flight promises so concurrent callers share a single request.
     *
     * @param {boolean} [force=false]
     * @returns {Promise<Array<string>>}
     */
    async fetchManifest(force = false) {
      if (this.initialized && !force && Date.now() - (this.lastFetched || 0) < 60000) {
        return this.enabledIds
      }

      if (manifestInFlightPromise) {
        return manifestInFlightPromise
      }

      this.loading = true
      this.error = null

      manifestInFlightPromise = (async () => {
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

          if (!this.initialized) {
            this.enabledIds = []
            this.initialized = true
          }

          return this.enabledIds
        } finally {
          this.loading = false
          manifestInFlightPromise = null
        }
      })()

      return manifestInFlightPromise
    },

    /**
     * Ensure module state is ready before route execution without duplicate blocking calls.
     */
    async ensureLoaded() {
      if (this.initialized && this.lastFetched) {
        // Run background non-blocking manifest freshness sync if cache is stale
        if (Date.now() - (this.lastFetched || 0) >= 60000) {
          this.fetchManifest()
        }
        return
      }

      this.hydrateFromCache()
      if (!this.initialized) {
        await this.fetchManifest()
      }
    },

    async fetchModules(force = false) {
      if (adminModulesInFlightPromise && !force) {
        return adminModulesInFlightPromise
      }

      adminModulesInFlightPromise = (async () => {
        try {
          const response = await modulesApi.getModules()
          this.modules = Array.isArray(response?.data) ? response.data : []
          const enabled = this.modules.filter((module) => module.is_enabled).map((module) => module.id)
          this.enabledIds = enabled
          this.initialized = true
          this.lastFetched = Date.now()
          localStorage.setItem(
            MANIFEST_CACHE_KEY,
            JSON.stringify({ enabledIds: enabled, timestamp: this.lastFetched })
          )
          return this.modules
        } finally {
          adminModulesInFlightPromise = null
        }
      })()

      return adminModulesInFlightPromise
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

        this.lastFetched = Date.now()
        apiCache.invalidate('public:')

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
