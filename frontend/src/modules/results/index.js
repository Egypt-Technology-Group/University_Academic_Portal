/**
 * Student Results & Simulation Portal Micro-Module
 *
 * Student semester inquiry, transcript simulation, GPA calculations, and academic standings.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import resultsRoutes from './routes.js'
import resultsApi from './services/resultsApi.js'

export const ResultsModule = {
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
  dependencies: [KNOWN_MODULE_IDS.ACADEMIC_STRUCTURE, KNOWN_MODULE_IDS.ACADEMIC_SERVICES],
  ownedTables: ['student_results', 'student_terms', 'course_enrollments'],
  routes: resultsRoutes,
  api: resultsApi,
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
}

export { resultsRoutes, resultsApi }
export default ResultsModule

