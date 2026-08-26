/**
 * News & Announcements CMS module route definitions
 */
export const cmsRoutes = {
  public: [
    {
      path: '/news',
      name: 'news',
      component: () => import('./views/NewsView.vue'),
      meta: { title: 'News', module: 'cms' },
    },
    {
      path: '/news/:slug',
      name: 'news-detail',
      component: () => import('./views/NewsDetailView.vue'),
      meta: { title: 'News Details', module: 'cms' },
    },
  ],
  admin: [
    {
      path: 'cms',
      name: 'admin-cms',
      component: () => import('./views/AdminCmsView.vue'),
      meta: { title: 'News & Announcements CMS', requiresAuth: true, module: 'cms' },
    },
  ],
}

export default cmsRoutes
