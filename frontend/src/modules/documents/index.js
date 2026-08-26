/**
 * Documents & Regulations Repository Micro-Module
 *
 * Repository of academic bylaws, study regulations, administrative documents, and download tracking.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import documentsRoutes from './routes.js'
import documentsApi from './services/documentsApi.js'

export const DocumentsModule = {
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
  routes: documentsRoutes,
  api: documentsApi,
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
        label: {
          ar: 'مركز الوثائق واللوائح',
          en: 'Documents & Bylaws',
        },
        icon: 'FolderArchive',
        order: 30,
      },
    ],
  },
}

export { documentsRoutes, documentsApi }
export default DocumentsModule

