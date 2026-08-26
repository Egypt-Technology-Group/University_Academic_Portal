/**
 * News & Announcements CMS Micro-Module
 *
 * Publishing and managing campus news articles, press releases, and urgent announcements.
 */

import { KNOWN_MODULE_IDS } from '../../core/modules/types.js'

export const CmsModule = {
  id: KNOWN_MODULE_IDS.CMS,
  name: {
    ar: 'إدارة المحتوى والأخبار',
    en: 'News & Announcements CMS',
  },
  description: {
    ar: 'نشر وإدارة الأخبار والبيانات الصحفية والإعلانات واللوحات الإرشادية.',
    en: 'Publish and manage campus news articles, press releases, and urgent announcements.',
  },
  version: '1.0.0',
  dependencies: [],
  ownedTables: ['news_articles', 'announcements'],
  publicRoutes: [
    { path: '/news', name: 'news' },
    { path: '/news/:slug', name: 'news-detail' },
  ],
  adminRoutes: [
    { path: '/admin/cms', name: 'admin-cms' },
  ],
  navItems: {
    public: [
      { id: 'nav-news', label: 'nav.news', to: '/news', order: 60 },
    ],
    admin: [
      {
        id: 'admin-cms',
        group: 'groupContent',
        path: '/admin/cms',
        label: {
          ar: 'الأخبار والإعلانات',
          en: 'News & Announcements',
        },
        icon: 'Newspaper',
        order: 10,
      },
    ],
  },
}

export default CmsModule
