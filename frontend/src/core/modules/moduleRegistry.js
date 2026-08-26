/**
 * Module Registry - Central Micro-Module Engine
 *
 * Manages client-side module definitions, route registrations,
 * dependency validations, and dynamic navigation filtering.
 */

import { validateModuleDefinition, normalizeModuleDefinition, KNOWN_MODULE_IDS } from './types.js'

class ModuleRegistry {
  constructor() {
    /** @type {Map<string, import('./types').ModuleDefinition>} */
    this._modules = new Map()
    this._initializeCoreModules()
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

  /**
   * Pre-load core university academic portal modules into the registry.
   * @private
   */
  _initializeCoreModules() {
    this.register({
      id: KNOWN_MODULE_IDS.ACADEMIC_STRUCTURE,
      name: {
        ar: 'الهيكل الأكاديمي والكليات والبرامج',
        en: 'Academic Structure & Programs',
      },
      description: {
        ar: 'إدارة الكليات، الأقسام العلمية، البرامج الدراسية، والخطط التعليمية وأعضاء هيئة التدريس.',
        en: 'Manage colleges, academic departments, degree programs, study plans, and faculty directory.',
      },
      version: '1.0.0',
      dependencies: [],
      ownedTables: ['colleges', 'departments', 'programs', 'courses', 'study_plans', 'faculty'],
      publicRoutes: [
        { path: '/colleges', name: 'colleges' },
        { path: '/colleges/:slug', name: 'college-detail' },
        { path: '/programs', name: 'programs' },
        { path: '/programs/:slug', name: 'program-detail' },
        { path: '/faculty', name: 'faculty' },
      ],
      adminRoutes: [
        { path: '/admin/academic-structure', name: 'admin-academic-structure' },
      ],
      navItems: {
        public: [
          { id: 'nav-colleges', label: 'nav.colleges', to: '/colleges', order: 20 },
          { id: 'nav-programs', label: 'nav.programs', to: '/programs', order: 30 },
          { id: 'nav-faculty', label: 'nav.faculty', to: '/faculty', order: 50 },
        ],
        admin: [
          {
            id: 'admin-academic-structure',
            group: 'groupAdmissions',
            path: '/admin/academic-structure',
            label: 'Academic Colleges & Programs',
            icon: 'School',
            order: 20,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.ADMISSIONS,
      name: {
        ar: 'القبول والتسجيل',
        en: 'Admissions & Enrollment',
      },
      description: {
        ar: 'بوابة التقديم الإلكتروني، متابعة طلبات الالتحاق، ومراجعة وتدقيق الشهادات.',
        en: 'Online admissions application portal, application tracking, and credential verification.',
      },
      version: '1.0.0',
      dependencies: [KNOWN_MODULE_IDS.ACADEMIC_STRUCTURE],
      ownedTables: ['admission_cycles', 'applications', 'application_documents'],
      publicRoutes: [
        { path: '/admissions', name: 'admissions' },
        { path: '/admissions/track', name: 'admissions-track' },
      ],
      adminRoutes: [
        { path: '/admin/admissions', name: 'admin-admissions' },
      ],
      navItems: {
        public: [
          { id: 'nav-admissions', label: 'nav.admissions', to: '/admissions', order: 40 },
          { id: 'nav-track', label: 'nav.trackApp', to: '/admissions/track', order: 41 },
        ],
        admin: [
          {
            id: 'admin-admissions',
            group: 'groupAdmissions',
            path: '/admin/admissions',
            label: 'Admissions Queue',
            icon: 'UserCheck',
            badge: '14',
            badgeVariant: 'warning',
            order: 10,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.ACADEMIC_SERVICES,
      name: {
        ar: 'الخدمات الأكاديمية والطلابية',
        en: 'Academic & Student Services',
      },
      description: {
        ar: 'إدارة الطلبات الطلابية، إصدار الإفادات والشهادات الرسمية، وجداول الامتحانات.',
        en: 'Manage student requests, official verifiable statements, and exam timetables.',
      },
      version: '1.0.0',
      dependencies: [KNOWN_MODULE_IDS.ACADEMIC_STRUCTURE],
      ownedTables: ['student_requests', 'official_statements', 'exam_schedules'],
      publicRoutes: [],
      adminRoutes: [
        { path: '/admin/academic-services', name: 'admin-academic-services' },
      ],
      navItems: {
        public: [],
        admin: [
          {
            id: 'admin-academic-services',
            group: 'groupAdmissions',
            path: '/admin/academic-services',
            label: 'Academic & Student Services',
            icon: 'GraduationCap',
            order: 30,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.CMS,
      name: {
        ar: 'إدارة المحتوى والأخبار',
        en: 'News & Announcements CMS',
      },
      description: {
        ar: 'نشر وإدارة الأخبار والبيانات الصحفية والإعلانات واللوحات الإرشادية.',
        en: 'Publish and manage campus news articles, press releases, and urgent announcements.',
      },
      version: '1.0.0',
      dependencies: [],
      ownedTables: ['news_articles', 'announcements'],
      publicRoutes: [
        { path: '/news', name: 'news' },
        { path: '/news/:slug', name: 'news-detail' },
      ],
      adminRoutes: [
        { path: '/admin/cms', name: 'admin-cms' },
      ],
      navItems: {
        public: [
          { id: 'nav-news', label: 'nav.news', to: '/news', order: 60 },
        ],
        admin: [
          {
            id: 'admin-cms',
            group: 'groupContent',
            path: '/admin/cms',
            label: 'News & Announcements',
            icon: 'Newspaper',
            order: 10,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.EVENTS,
      name: {
        ar: 'الفعاليات والمؤتمرات',
        en: 'Events & Calendar Manager',
      },
      description: {
        ar: 'جدولة المؤتمرات وورش العمل والندوات العلمية وإدارة سعة الحضور.',
        en: 'Schedule conferences, workshops, scientific symposia, and registration capacities.',
      },
      version: '1.0.0',
      dependencies: [],
      ownedTables: ['events', 'event_registrations'],
      publicRoutes: [
        { path: '/events', name: 'events' },
      ],
      adminRoutes: [
        { path: '/admin/events', name: 'admin-events' },
      ],
      navItems: {
        public: [
          { id: 'nav-events', label: 'nav.events', to: '/events', order: 70 },
        ],
        admin: [
          {
            id: 'admin-events',
            group: 'groupContent',
            path: '/admin/events',
            label: 'Events & Calendar',
            icon: 'Calendar',
            order: 20,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.DOCUMENTS,
      name: {
        ar: 'مركز الوثائق واللوائح',
        en: 'Documents & Repository',
      },
      description: {
        ar: 'مستودع الوثائق واللوائح الأكاديمية والجداول والقرارات الإدارية.',
        en: 'Academic bylaws repository, study regulations, timetables, and administrative forms.',
      },
      version: '1.0.0',
      dependencies: [],
      ownedTables: ['documents', 'document_categories'],
      publicRoutes: [
        { path: '/documents', name: 'documents' },
      ],
      adminRoutes: [
        { path: '/admin/documents', name: 'admin-documents' },
      ],
      navItems: {
        public: [
          { id: 'nav-documents', label: 'nav.documents', to: '/documents', order: 80 },
        ],
        admin: [
          {
            id: 'admin-documents',
            group: 'groupContent',
            path: '/admin/documents',
            label: 'Documents & Bylaws',
            icon: 'FolderArchive',
            order: 30,
          },
        ],
      },
    })

    this.register({
      id: KNOWN_MODULE_IDS.RESULTS,
      name: {
        ar: 'بوابة نتائج الطلاب والتسجيل',
        en: 'Student Results & Simulation Portal',
      },
      description: {
        ar: 'استعلام نتائج الطلاب الفصلية والتراكمية والمعدل التراكمي ومحاكاة التسجيل الأكاديمي.',
        en: 'Student inquiry for semester/cumulative grades, GPA calculation, and registration simulation.',
      },
      version: '1.0.0',
      dependencies: [KNOWN_MODULE_IDS.ACADEMIC_STRUCTURE],
      ownedTables: ['student_results', 'student_terms', 'course_enrollments'],
      publicRoutes: [
        { path: '/student-portal', name: 'student-portal' },
      ],
      adminRoutes: [],
      navItems: {
        public: [
          { id: 'nav-student-portal', label: 'nav.studentPortal', to: '/student-portal', order: 90 },
        ],
        admin: [],
      },
    })
  }
}

export const moduleRegistry = new ModuleRegistry()
export default moduleRegistry
