/**
 * Academic & Student Services Micro-Module
 *
 * Student e-requests, official statement generation, exam schedules, and study plans.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'

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

export default AcademicServicesModule
