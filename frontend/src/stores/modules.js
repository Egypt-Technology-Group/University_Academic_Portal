/**
 * Modules Pinia Store
 *
 * Synchronizes client-side module definitions and dynamic routing states
 * with backend modular plugin features and database activation flags.
 */

import { defineStore } from 'pinia'
import { modulesApi } from '../services/modulesApi'
import { moduleRegistry } from '../core/modules/moduleRegistry'

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
     * Check if a module is currently enabled.
     *
     * @returns {(id: string) => boolean}
     */
    isModuleEnabled: (state) => (id) => {
      if (!id) return false
      // If store is not yet initialized, check if registry has it, otherwise default to enabled or check state
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
     * Fetch active modules from backend GET /api/v1/modules.
     *
     * @param {boolean} [force=false]
     * @returns {Promise<Array<Object>>}
     */
    async fetchModules(force = false) {
      if (this.initialized && !force && this.modules.length > 0) {
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
        return items
      } catch (err) {
        console.error('[useModulesStore] Failed to fetch modules:', err)
        this.error = err?.message || 'Failed to fetch module registry status from server.'
        
        // Fallback: If network error and not initialized, enable all registered modules by default
        if (!this.initialized || this.enabledIds.length === 0) {
          const defaultIds = moduleRegistry.getAll().map((m) => m.id)
          this.enabledIds = defaultIds
          this.initialized = true
        }
        return this.modules
      } finally {
        this.loading = false
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

        return response
      } catch (err) {
        console.error(`[useModulesStore] Error toggling module ${id}:`, err)
        this.error = err?.message || 'Failed to toggle module.'

        // If backend returned a 409 dependency conflict
        if (err?.status === 409 || err?.response?.status === 409) {
          const conflictData = err.response?.data || err.context || {}
          this.conflictError = {
            id,
            message: conflictData.message || err.message,
            error: conflictData.error || 'dependency_conflict',
            context: conflictData.context || {},
          }
        }

        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Inspect module dependency graph and blocking dependents from backend.
     *
     * @param {string} id
     * @returns {Promise<Object>}
     */
    async checkDependencies(id) {
      try {
        return await modulesApi.getModuleDependencies(id)
      } catch (err) {
        console.error(`[useModulesStore] Failed to check dependencies for module ${id}:`, err)
        return {
          id,
          is_enabled: this.isModuleEnabled(id),
          ...moduleRegistry.validateDependencies(id, this.enabledIds),
        }
      }
    },
  },
})
