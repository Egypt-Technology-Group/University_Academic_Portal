/**
 * Student Results & Simulation Portal module route definitions
 */
export const resultsRoutes = {
  public: [
    {
      path: '/student-portal',
      name: 'student-portal',
      component: () => import('./views/StudentResultsView.vue'),
      meta: { title: 'Student Results & Simulation Portal', module: 'results' },
    },
  ],
  admin: [],
}

export default resultsRoutes

