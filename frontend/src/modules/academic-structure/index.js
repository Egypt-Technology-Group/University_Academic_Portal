/**
 * Academic Structure & Faculty Micro-Module
 *
 * Manages colleges, academic departments, degree programs, curricula, and faculty directory.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import { academicStructureRoutes } from './routes.js'
import { academicStructureApi } from './services/academicStructureApi.js'

export const AcademicStructureModule = {
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
  routes: academicStructureRoutes,
  api: academicStructureApi,
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
        label: {
          ar: 'الهيكل الأكاديمي والكليات والبرامج',
          en: 'Academic Colleges & Programs',
        },
        icon: 'School',
        order: 20,
      },
    ],
  },
}

export default AcademicStructureModule
export { academicStructureApi, academicStructureRoutes }
