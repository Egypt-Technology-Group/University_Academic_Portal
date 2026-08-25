import { createRouter, createWebHistory } from 'vue-router'

// Public Views
import HomeView from '../views/HomeView.vue'
import CollegesView from '../views/CollegesView.vue'
import CollegeDetailView from '../views/CollegeDetailView.vue'
import ProgramsView from '../views/ProgramsView.vue'
import ProgramDetailView from '../views/ProgramDetailView.vue'
import AdmissionsView from '../views/AdmissionsView.vue'
import ApplicationTrackView from '../views/ApplicationTrackView.vue'
import FacultyDirectoryView from '../views/FacultyDirectoryView.vue'
import NewsView from '../views/NewsView.vue'
import NewsDetailView from '../views/NewsDetailView.vue'
import EventsView from '../views/EventsView.vue'
import DocumentsView from '../views/DocumentsView.vue'
import StudentResultsView from '../views/StudentResultsView.vue'
import NotFoundView from '../views/NotFoundView.vue'

// Admin Views & Layout
import AdminLayout from '../components/layout/AdminLayout.vue'
import AdminLoginView from '../views/admin/AdminLoginView.vue'
import AdminDashboardView from '../views/admin/AdminDashboardView.vue'
import AdminAdmissionsView from '../views/admin/AdminAdmissionsView.vue'
import AdminCmsView from '../views/admin/AdminCmsView.vue'
import AdminEventsView from '../views/admin/AdminEventsView.vue'
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
    meta: { title: 'Colleges' },
  },
  {
    path: '/colleges/:slug',
    name: 'college-detail',
    component: CollegeDetailView,
    meta: { title: 'College Details' },
  },
  {
    path: '/programs',
    name: 'programs',
    component: ProgramsView,
    meta: { title: 'Programs' },
  },
  {
    path: '/programs/:slug',
    name: 'program-detail',
    component: ProgramDetailView,
    meta: { title: 'Program Details' },
  },
  {
    path: '/admissions',
    name: 'admissions',
    component: AdmissionsView,
    meta: { title: 'Admissions' },
  },
  {
    path: '/admissions/track',
    name: 'admissions-track',
    component: ApplicationTrackView,
    meta: { title: 'Track Application' },
  },
  {
    path: '/faculty',
    name: 'faculty',
    component: FacultyDirectoryView,
    meta: { title: 'Faculty Directory' },
  },
  {
    path: '/news',
    name: 'news',
    component: NewsView,
    meta: { title: 'News' },
  },
  {
    path: '/news/:slug',
    name: 'news-detail',
    component: NewsDetailView,
    meta: { title: 'News Details' },
  },
  {
    path: '/events',
    name: 'events',
    component: EventsView,
    meta: { title: 'Events' },
  },
  {
    path: '/documents',
    name: 'documents',
    component: DocumentsView,
    meta: { title: 'Documents' },
  },
  {
    path: '/student-portal',
    name: 'student-portal',
    component: StudentResultsView,
    meta: { title: 'Student Results Portal' },
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
        meta: { title: 'Admissions Management Queue', requiresAuth: true },
      },
      {
        path: 'academic-services',
        name: 'admin-academic-services',
        component: () => import('../views/admin/AdminAcademicServicesView.vue'),
        meta: { title: 'Academic & Student Services', requiresAuth: true },
      },
      {
        path: 'cms',
        name: 'admin-cms',
        component: AdminCmsView,
        meta: { title: 'News & Announcements CMS', requiresAuth: true },
      },
      {
        path: 'events',
        name: 'admin-events',
        component: AdminEventsView,
        meta: { title: 'Events & Calendar Manager', requiresAuth: true },
      },
      {
        path: 'documents',
        name: 'admin-documents',
        component: AdminDocumentsView,
        meta: { title: 'Documents Repository Manager', requiresAuth: true },
      },
      {
        path: 'settings',
        name: 'admin-settings',
        component: AdminSettingsView,
        meta: { title: 'Site Customization & Settings', requiresAuth: true },
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
router.beforeEach((to) => {
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

  return true
})

export default router
