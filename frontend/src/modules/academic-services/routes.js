/**
 * Academic Services module route definitions
 */
export const academicServicesRoutes = {
  public: [],
  admin: [
    {
      path: 'academic-services',
      name: 'admin-academic-services',
      component: () => import('./views/AdminAcademicServicesView.vue'),
      meta: { title: 'Academic & Student Services Management', requiresAuth: true, module: 'academic-services' },
    },
  ],
}

export default academicServicesRoutes
