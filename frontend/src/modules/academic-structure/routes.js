/**
 * Academic Structure module route definitions
 */
export const academicStructureRoutes = {
  public: [
    {
      path: '/colleges',
      name: 'colleges',
      component: () => import('./views/CollegesView.vue'),
      meta: { title: 'Colleges', module: 'academic-structure' },
    },
    {
      path: '/colleges/:slug',
      name: 'college-detail',
      component: () => import('./views/CollegeDetailView.vue'),
      meta: { title: 'College Details', module: 'academic-structure' },
    },
    {
      path: '/programs',
      name: 'programs',
      component: () => import('./views/ProgramsView.vue'),
      meta: { title: 'Programs', module: 'academic-structure' },
    },
    {
      path: '/programs/:slug',
      name: 'program-detail',
      component: () => import('./views/ProgramDetailView.vue'),
      meta: { title: 'Program Details', module: 'academic-structure' },
    },
    {
      path: '/faculty',
      name: 'faculty',
      component: () => import('./views/FacultyDirectoryView.vue'),
      meta: { title: 'Faculty Directory', module: 'academic-structure' },
    },
  ],
  admin: [
    {
      path: 'academic-structure',
      name: 'admin-academic-structure',
      component: () => import('./views/AdminAcademicStructureView.vue'),
      meta: { title: 'Academic Structure & Programs Management', requiresAuth: true, module: 'academic-structure' },
    },
  ],
}

export default academicStructureRoutes
