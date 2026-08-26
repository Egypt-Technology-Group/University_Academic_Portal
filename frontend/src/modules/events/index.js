/**
 * Events & Calendar Manager Micro-Module
 *
 * Scheduling conferences, workshops, scientific symposia, and registration capacities.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'
import eventsRoutes from './routes.js'
import eventsApi from './services/eventsApi.js'

export const EventsModule = {
  id: KNOWN_MODULE_IDS.EVENTS,
  name: {
    ar: 'الفعاليات والمؤتمرات',
    en: 'Events & Calendar Manager',
  },
  description: {
    ar: 'جدولة المؤتمرات وورش العمل والندوات العلمية وإدارة سعة الحضور.',
    en: 'Schedule conferences, workshops, scientific symposia, and registration capacities.',
  },
  version: '1.0.0',
  dependencies: [],
  ownedTables: ['events', 'event_attendees'],
  routes: eventsRoutes,
  api: eventsApi,
  publicRoutes: [
    { path: '/events', name: 'events' },
  ],
  adminRoutes: [
    { path: '/admin/events', name: 'admin-events' },
  ],
  navItems: {
    public: [
      { id: 'nav-events', label: 'nav.events', to: '/events', order: 70 },
    ],
    admin: [
      {
        id: 'admin-events',
        group: 'groupContent',
        path: '/admin/events',
        label: {
          ar: 'الفعاليات والتقويم',
          en: 'Events & Calendar',
        },
        icon: 'Calendar',
        order: 20,
      },
    ],
  },
}

export { eventsRoutes, eventsApi }
export default EventsModule
