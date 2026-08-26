/**
 * Module System Type Definitions & Validation Helpers
 *
 * Defines the contract and schema for frontend micro-modules in the University Academic Portal.
 */

/**
 * @typedef {Object} LocalizedString
 * @property {string} ar - Arabic string
 * @property {string} en - English string
 */

/**
 * @typedef {Object} ModuleNavItem
 * @property {string} [id] - Unique nav item identifier
 * @property {string|LocalizedString} label - Display label or i18n key
 * @property {string} [path] - Destination route path
 * @property {string} [to] - Alias for destination route path
 * @property {string|Object} [icon] - Lucide icon name or component
 * @property {string} [group] - Group name (for admin sidebar)
 * @property {number} [order] - Display sort order
 * @property {string} [badge] - Optional badge text
 * @property {string} [badgeVariant] - Badge style (e.g., 'warning', 'info')
 */

/**
 * @typedef {Object} ModuleDefinition
 * @property {string} id - Unique module identifier (e.g. 'admissions', 'academic-structure')
 * @property {LocalizedString} name - Localized module name
 * @property {LocalizedString} [description] - Localized module description
 * @property {string} [version] - Module semantic version
 * @property {string[]} [dependencies] - Array of required module IDs
 * @property {string[]} [ownedTables] - Backend database tables managed by this module
 * @property {Array<Object>} [publicRoutes] - Vue Router route definitions for public portal
 * @property {Array<Object>} [adminRoutes] - Vue Router route definitions for admin portal
 * @property {Object} [navItems] - Navigation definitions
 * @property {Array<ModuleNavItem>} [navItems.public] - Public navigation items
 * @property {Array<ModuleNavItem>} [navItems.admin] - Admin navigation items
 */

export const MODULE_STATUS = Object.freeze({
  ENABLED: 'enabled',
  DISABLED: 'disabled',
})

export const KNOWN_MODULE_IDS = Object.freeze({
  ACADEMIC_STRUCTURE: 'academic-structure',
  ACADEMIC_SERVICES: 'academic-services',
  ADMISSIONS: 'admissions',
  CMS: 'cms',
  EVENTS: 'events',
  DOCUMENTS: 'documents',
  RESULTS: 'results',
})

/**
 * Validates a module definition object against the required schema.
 *
 * @param {Object} definition - The module definition candidate.
 * @returns {{ valid: boolean, errors: string[] }}
 */
export function validateModuleDefinition(definition) {
  const errors = []

  if (!definition || typeof definition !== 'object') {
    return { valid: false, errors: ['Module definition must be a non-null object.'] }
  }

  if (!definition.id || typeof definition.id !== 'string' || definition.id.trim() === '') {
    errors.push('Module id is required and must be a non-empty string.')
  }

  if (!definition.name || typeof definition.name !== 'object') {
    errors.push('Module name is required and must be an object with localized strings ({ ar, en }).')
  } else {
    if (!definition.name.ar && !definition.name.en) {
      errors.push('Module name must contain at least an Arabic ("ar") or English ("en") label.')
    }
  }

  if (definition.dependencies && !Array.isArray(definition.dependencies)) {
    errors.push('Module dependencies must be an array of module IDs.')
  }

  if (definition.ownedTables && !Array.isArray(definition.ownedTables)) {
    errors.push('Module ownedTables must be an array of table name strings.')
  }

  if (definition.navItems && typeof definition.navItems !== 'object') {
    errors.push('Module navItems must be an object containing public and/or admin arrays.')
  }

  return {
    valid: errors.length === 0,
    errors,
  }
}

/**
 * Normalizes a module definition with sensible defaults.
 *
 * @param {Object} def
 * @returns {ModuleDefinition}
 */
export function normalizeModuleDefinition(def) {
  const name = typeof def.name === 'string'
    ? { ar: def.name, en: def.name }
    : { ar: def.name?.ar || '', en: def.name?.en || '' }

  const description = typeof def.description === 'string'
    ? { ar: def.description, en: def.description }
    : { ar: def.description?.ar || '', en: def.description?.en || '' }

  return {
    id: String(def.id).trim(),
    name,
    description,
    version: def.version || '1.0.0',
    dependencies: Array.isArray(def.dependencies) ? [...def.dependencies] : [],
    ownedTables: Array.isArray(def.ownedTables) ? [...def.ownedTables] : [],
    publicRoutes: Array.isArray(def.publicRoutes) ? [...def.publicRoutes] : [],
    adminRoutes: Array.isArray(def.adminRoutes) ? [...def.adminRoutes] : [],
    navItems: {
      public: Array.isArray(def.navItems?.public) ? [...def.navItems.public] : [],
      admin: Array.isArray(def.navItems?.admin) ? [...def.navItems.admin] : [],
    },
    ...def,
  }
}
