/**
 * Admissions module route definitions
 */
export const admissionsRoutes = {
  public: [
    {
      path: '/admissions',
      name: 'admissions',
      component: () => import('./views/AdmissionsView.vue'),
      meta: { title: 'Admissions', module: 'admissions' },
    },
    {
      path: '/admissions/track',
      name: 'admissions-track',
      component: () => import('./views/ApplicationTrackView.vue'),
      meta: { title: 'Track Application', module: 'admissions' },
    },
  ],
  admin: [
    {
      path: 'admissions',
      name: 'admin-admissions',
      component: () => import('./views/AdminAdmissionsView.vue'),
      meta: { title: 'Admissions Management Queue', requiresAuth: true, module: 'admissions' },
    },
  ],
}

export default admissionsRoutes
