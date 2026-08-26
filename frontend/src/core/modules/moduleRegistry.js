/**
 * Module Registry - Central Micro-Module Engine
 *
 * Manages client-side module definitions, route registrations,
 * dependency validations, and dynamic navigation filtering.
 */

import { validateModuleDefinition, normalizeModuleDefinition } from './types.js'

class ModuleRegistry {
  constructor() {
    /** @type {Map<string, import('./types').ModuleDefinition>} */
    this._modules = new Map()
  }

  /**
   * Register a new micro-module into the registry.
   *
   * @param {Object} moduleDefinition
   * @returns {import('./types').ModuleDefinition}
   */
  register(moduleDefinition) {
    const validation = validateModuleDefinition(moduleDefinition)
    if (!validation.valid) {
      console.error(`[ModuleRegistry] Validation failed for module "${moduleDefinition?.id}":`, validation.errors)
      throw new Error(`[ModuleRegistry] Invalid module definition: ${validation.errors.join(', ')}`)
    }

    const normalized = normalizeModuleDefinition(moduleDefinition)
    this._modules.set(normalized.id, normalized)
    return normalized
  }

  /**
   * Register multiple modules at once.
   *
   * @param {Array<Object>} definitions
   * @returns {Array<import('./types').ModuleDefinition>}
   */
  registerAll(definitions) {
    if (!Array.isArray(definitions)) return []
    return definitions.map((def) => this.register(def))
  }

  /**
   * Retrieve a module definition by ID.
   *
   * @param {string} id
   * @returns {import('./types').ModuleDefinition|null}
   */
  get(id) {
    if (!id) return null
    return this._modules.get(String(id).trim()) || null
  }

  /**
   * Check if a module ID is registered.
   *
   * @param {string} id
   * @returns {boolean}
   */
  has(id) {
    if (!id) return false
    return this._modules.has(String(id).trim())
  }

  /**
   * Return all registered module definitions.
   *
   * @returns {Array<import('./types').ModuleDefinition>}
   */
  getAll() {
    return Array.from(this._modules.values())
  }

  /**
   * Return only module definitions that are currently enabled.
   *
   * @param {string[]|Set<string>} enabledIds
   * @returns {Array<import('./types').ModuleDefinition>}
   */
  getEnabled(enabledIds = []) {
    const idSet = enabledIds instanceof Set ? enabledIds : new Set(enabledIds || [])
    return this.getAll().filter((mod) => idSet.has(mod.id))
  }

  /**
   * Get filtered navigation items for a specific section (e.g. 'public' or 'admin')
   * based on currently enabled module IDs.
   *
   * @param {string[]|Set<string>} enabledIds
   * @param {'public'|'admin'} section
   * @returns {Array<Object>}
   */
  getNavItems(enabledIds = [], section = 'public') {
    const enabledModules = this.getEnabled(enabledIds)
    const items = []

    for (const mod of enabledModules) {
      const navList = mod.navItems?.[section]
      if (Array.isArray(navList)) {
        for (const item of navList) {
          items.push({
            moduleId: mod.id,
            moduleName: mod.name,
            ...item,
          })
        }
      }
    }

    // Sort by order ascending if order is defined
    return items.sort((a, b) => (a.order ?? 999) - (b.order ?? 999))
  }

  /**
   * Validates whether all dependencies of a module are satisfied in the enabledIds list.
   *
   * @param {string} id
   * @param {string[]|Set<string>} enabledIds
   * @returns {{ valid: boolean, missingDependencies: string[] }}
   */
  validateDependencies(id, enabledIds = []) {
    const mod = this.get(id)
    if (!mod) {
      return {
        valid: false,
        missingDependencies: [],
        error: `Module [${id}] is not registered.`,
      }
    }

    const idSet = enabledIds instanceof Set ? enabledIds : new Set(enabledIds || [])
    const missing = (mod.dependencies || []).filter((depId) => !idSet.has(depId))

    return {
      valid: missing.length === 0,
      missingDependencies: missing,
    }
  }

  /**
   * Finds all modules that depend on the given module ID.
   *
   * @param {string} id
   * @returns {Array<import('./types').ModuleDefinition>}
   */
  getDependents(id) {
    const targetId = String(id).trim()
    return this.getAll().filter((mod) => (mod.dependencies || []).includes(targetId))
  }
}

export const moduleRegistry = new ModuleRegistry()
export default moduleRegistry
