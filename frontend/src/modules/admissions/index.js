/**
 * Admissions & Enrollment Micro-Module
 *
 * Online applications, submission tracking, applicant review queue, and credential verification.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import admissionsRoutes from './routes.js'
import admissionsApi from './services/admissionsApi.js'

export const AdmissionsModule = {
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
  routes: admissionsRoutes,
  api: admissionsApi,
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
        label: {
          ar: 'إدارة طلبات الالتحاق',
          en: 'Admissions Queue',
        },
        icon: 'UserCheck',
        badge: '14',
        badgeVariant: 'warning',
        order: 10,
      },
    ],
  },
}

export { admissionsRoutes, admissionsApi }
export default AdmissionsModule
