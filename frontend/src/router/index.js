import { createRouter, createWebHistory } from 'vue-router'
import { useModulesStore } from '../stores/modules'

// Public Views
import HomeView from '../views/HomeView.vue'
import CollegesView from '../modules/academic-structure/views/CollegesView.vue'
import CollegeDetailView from '../modules/academic-structure/views/CollegeDetailView.vue'
import ProgramsView from '../modules/academic-structure/views/ProgramsView.vue'
import ProgramDetailView from '../modules/academic-structure/views/ProgramDetailView.vue'
import AdmissionsView from '../modules/admissions/views/AdmissionsView.vue'
import ApplicationTrackView from '../modules/admissions/views/ApplicationTrackView.vue'
import FacultyDirectoryView from '../modules/academic-structure/views/FacultyDirectoryView.vue'
import NewsView from '../modules/cms/views/NewsView.vue'
import NewsDetailView from '../modules/cms/views/NewsDetailView.vue'
import EventsView from '../modules/events/views/EventsView.vue'
import DocumentsView from '../views/DocumentsView.vue'
import StudentResultsView from '../views/StudentResultsView.vue'
import ModuleDisabledView from '../views/ModuleDisabledView.vue'
import NotFoundView from '../views/NotFoundView.vue'

// Admin Views & Layout
import AdminLayout from '../components/layout/AdminLayout.vue'
import AdminLoginView from '../views/admin/AdminLoginView.vue'
import AdminDashboardView from '../views/admin/AdminDashboardView.vue'
import AdminAdmissionsView from '../modules/admissions/views/AdminAdmissionsView.vue'
import AdminCmsView from '../modules/cms/views/AdminCmsView.vue'
import AdminEventsView from '../modules/events/views/AdminEventsView.vue'
import AdminDocumentsView from '../views/admin/AdminDocumentsView.vue'
import AdminSettingsView from '../views/admin/AdminSettingsView.vue'

const routes = [
  // Public Routes
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Home' },
  },
  {
    path: '/colleges',
    name: 'colleges',
    component: CollegesView,
    meta: { title: 'Colleges', module: 'academic-structure' },
  },
  {
    path: '/colleges/:slug',
    name: 'college-detail',
    component: CollegeDetailView,
    meta: { title: 'College Details', module: 'academic-structure' },
  },
  {
    path: '/programs',
    name: 'programs',
    component: ProgramsView,
    meta: { title: 'Programs', module: 'academic-structure' },
  },
  {
    path: '/programs/:slug',
    name: 'program-detail',
    component: ProgramDetailView,
    meta: { title: 'Program Details', module: 'academic-structure' },
  },
  {
    path: '/admissions',
    name: 'admissions',
    component: AdmissionsView,
    meta: { title: 'Admissions', module: 'admissions' },
  },
  {
    path: '/admissions/track',
    name: 'admissions-track',
    component: ApplicationTrackView,
    meta: { title: 'Track Application', module: 'admissions' },
  },
  {
    path: '/faculty',
    name: 'faculty',
    component: FacultyDirectoryView,
    meta: { title: 'Faculty Directory', module: 'academic-structure' },
  },
  {
    path: '/news',
    name: 'news',
    component: NewsView,
    meta: { title: 'News', module: 'cms' },
  },
  {
    path: '/news/:slug',
    name: 'news-detail',
    component: NewsDetailView,
    meta: { title: 'News Details', module: 'cms' },
  },
  {
    path: '/events',
    name: 'events',
    component: EventsView,
    meta: { title: 'Events', module: 'events' },
  },
  {
    path: '/documents',
    name: 'documents',
    component: DocumentsView,
    meta: { title: 'Documents', module: 'documents' },
  },
  {
    path: '/student-portal',
    name: 'student-portal',
    component: StudentResultsView,
    meta: { title: 'Student Results Portal', module: 'results' },
  },

  // Fallback for disabled modules
  {
    path: '/module-disabled',
    name: 'module-disabled',
    component: ModuleDisabledView,
    meta: { title: 'Module Offline' },
  },

  // Admin Authentication Route
  {
    path: '/admin/login',
    name: 'admin-login',
    component: AdminLoginView,
    meta: { title: 'Admin Login', guestOnly: true },
  },

  // Admin Dashboard & Management Routes (Protected)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard',
      },
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: AdminDashboardView,
        meta: { title: 'Admin Dashboard', requiresAuth: true },
      },
      {
        path: 'admissions',
        name: 'admin-admissions',
        component: AdminAdmissionsView,
        meta: { title: 'Admissions Management Queue', requiresAuth: true, module: 'admissions' },
      },
      {
        path: 'academic-structure',
        name: 'admin-academic-structure',
        component: () => import('../modules/academic-structure/views/AdminAcademicStructureView.vue'),
        meta: { title: 'Academic Structure & Programs Management', requiresAuth: true, module: 'academic-structure' },
      },
      {
        path: 'academic-services',
        name: 'admin-academic-services',
        component: () => import('../modules/academic-services/views/AdminAcademicServicesView.vue'),
        meta: { title: 'Academic & Student Services', requiresAuth: true, module: 'academic-services' },
      },
      {
        path: 'cms',
        name: 'admin-cms',
        component: AdminCmsView,
        meta: { title: 'News & Announcements CMS', requiresAuth: true, module: 'cms' },
      },
      {
        path: 'events',
        name: 'admin-events',
        component: AdminEventsView,
        meta: { title: 'Events & Calendar Manager', requiresAuth: true, module: 'events' },
      },
      {
        path: 'documents',
        name: 'admin-documents',
        component: AdminDocumentsView,
        meta: { title: 'Documents Repository Manager', requiresAuth: true, module: 'documents' },
      },
      {
        path: 'settings',
        name: 'admin-settings',
        component: AdminSettingsView,
        meta: { title: 'Site Customization & Settings', requiresAuth: true },
      },
      {
        path: 'modules',
        name: 'admin-modules',
        component: () => import('../views/admin/AdminModulesView.vue'),
        meta: { title: 'Micro-Modules Management Center', requiresAuth: true },
      },
      {
        path: 'audit-trail',
        name: 'admin-audit-trail',
        component: () => import('../views/admin/AdminAuditTrailView.vue'),
        meta: { title: 'Enterprise Audit Trail & Compliance Log', requiresAuth: true },
      },
    ],
  },

  // Fallback 404 Route
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundView,
    meta: { title: 'Not Found' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Route Guards (Modern Vue Router returns value instead of calling next())
router.beforeEach(async (to) => {
  const token = localStorage.getItem('egyitech_auth_token')
  const isAuthenticated = Boolean(token)

  // Update document title
  if (to.meta?.title) {
    document.title = `${to.meta.title} | University Academic Portal`
  }

  // Check if route requires authentication
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      return {
        name: 'admin-login',
        query: { redirect: to.fullPath !== '/admin/dashboard' ? to.fullPath : undefined },
      }
    }
  }

  // Check if route is guest only (e.g. login page)
  if (to.matched.some((record) => record.meta.guestOnly)) {
    if (isAuthenticated) {
      return { name: 'admin-dashboard' }
    }
  }

  // Micro-Module Dynamic Routing Guard: Verify if targeted route belongs to a disabled module
  const targetModuleRecord = to.matched.find((record) => record.meta?.module)
  if (targetModuleRecord?.meta?.module) {
    const moduleId = targetModuleRecord.meta.module
    const modulesStore = useModulesStore()

    // Lazy load modules status if not already populated
    if (!modulesStore.initialized) {
      try {
        await modulesStore.fetchModules()
      } catch (err) {
        console.warn('[RouterModuleGuard] Could not load module statuses:', err)
      }
    }

    // If the module is not enabled, redirect to fallback disabled screen
    if (!modulesStore.isModuleEnabled(moduleId)) {
      return {
        name: 'module-disabled',
        query: {
          module: moduleId,
          redirect: to.fullPath,
        },
      }
    }
  }

  return true
})

export default router
