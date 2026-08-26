/**
 * Academic & Student Services Micro-Module
 *
 * Student e-requests, official statement generation, exam schedules, and study plans.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import academicServicesRoutes from './routes.js'
import academicServicesApi from './services/academicServicesApi.js'

export const AcademicServicesModule = {
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
  ownedTables: ['student_records', 'student_service_requests', 'official_statements', 'exam_schedules'],
  routes: academicServicesRoutes,
  api: academicServicesApi,
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
        label: {
          ar: 'الخدمات الأكاديمية والطلابية',
          en: 'Academic & Student Services',
        },
        icon: 'GraduationCap',
        order: 30,
      },
    ],
  },
}

export { academicServicesRoutes, academicServicesApi }
export default AcademicServicesModule
