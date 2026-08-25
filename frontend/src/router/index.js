import { createRouter, createWebHistory } from 'vue-router'

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

const routes = [
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

export default router
