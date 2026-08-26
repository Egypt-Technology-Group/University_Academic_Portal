/**
 * Documents & Regulations Repository module route definitions
 */
export const documentsRoutes = {
  public: [
    {
      path: '/documents',
      name: 'documents',
      component: () => import('./views/DocumentsView.vue'),
      meta: { title: 'Documents & Regulations Repository', module: 'documents' },
    },
  ],
  admin: [
    {
      path: 'documents',
      name: 'admin-documents',
      component: () => import('./views/AdminDocumentsView.vue'),
      meta: { title: 'Documents Repository Manager', requiresAuth: true, module: 'documents' },
    },
  ],
}

export default documentsRoutes

