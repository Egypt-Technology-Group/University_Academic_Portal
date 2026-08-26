/**
 * Events & Calendar module route definitions
 */
export const eventsRoutes = {
  public: [
    {
      path: '/events',
      name: 'events',
      component: () => import('./views/EventsView.vue'),
      meta: { title: 'Events', module: 'events' },
    },
  ],
  admin: [
    {
      path: 'events',
      name: 'admin-events',
      component: () => import('./views/AdminEventsView.vue'),
      meta: { title: 'Events & Calendar Manager', requiresAuth: true, module: 'events' },
    },
  ],
}

export default eventsRoutes
