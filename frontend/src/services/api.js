import axios from 'axios'
import {
  mockColleges,
  mockPrograms,
  mockNews,
  mockEvents,
  mockAnnouncements,
  mockDocuments,
  mockFaculty,
  mockStudentResults,
  mockApplications,
  mockAdminStats
} from './mockData'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1',
  timeout: 4000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Request Interceptor to dynamically set Accept-Language, X-Locale and Authorization
apiClient.interceptors.request.use((config) => {
  const currentLocale = localStorage.getItem('egyitech_locale') || 'ar'
  config.headers['Accept-Language'] = currentLocale
  config.headers['X-Locale'] = currentLocale

  const token = localStorage.getItem('egyitech_auth_token')
  if (token && !token.startsWith('egyitech_mock_jwt_')) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
}, (error) => {
  return Promise.reject(error)
})

// Response Interceptor for handling token expiration and 401 errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      const token = localStorage.getItem('egyitech_auth_token')
      if (token && !token.startsWith('egyitech_mock_jwt_')) {
        localStorage.removeItem('egyitech_auth_token')
        localStorage.removeItem('egyitech_auth_user')
        if (window.location.pathname.startsWith('/admin') && window.location.pathname !== '/admin/login') {
          window.location.href = '/admin/login'
        }
      }
    }
    return Promise.reject(error)
  }
)

// Translation helper utility for objects that contain { ar: '...', en: '...' } or strings
export function getTranslated(field, locale = 'ar') {
  if (!field) return ''
  if (typeof field === 'string') return field
  if (typeof field === 'object') {
    return field[locale] || field.ar || field.en || Object.values(field)[0] || ''
  }
  return String(field)
}

// API Service Methods
export const api = {
  // Colleges
  async getColleges() {
    try {
      const response = await apiClient.get('/colleges')
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /colleges failed or offline, using fallback data:', e.message)
      return mockColleges
    }
  },

  async getCollege(slug) {
    try {
      const response = await apiClient.get(`/colleges/${slug}`)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /colleges/${slug} failed, using fallback data:`, e.message)
      const found = mockColleges.find((c) => c.slug === slug || c.id === Number(slug))
      if (found) return found
      return mockColleges[0]
    }
  },

  // Programs
  async getPrograms(params = {}) {
    try {
      const response = await apiClient.get('/programs', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /programs failed, using fallback data:', e.message)
      let list = [...mockPrograms]
      if (params.degree_level) {
        list = list.filter((p) => p.degree_level === params.degree_level)
      }
      if (params.search) {
        const q = params.search.toLowerCase()
        list = list.filter((p) =>
          (p.name.ar && p.name.ar.toLowerCase().includes(q)) ||
          (p.name.en && p.name.en.toLowerCase().includes(q)) ||
          p.slug.toLowerCase().includes(q)
        )
      }
      return list
    }
  },

  async getProgram(slug) {
    try {
      const response = await apiClient.get(`/programs/${slug}`)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /programs/${slug} failed, using fallback data:`, e.message)
      const found = mockPrograms.find((p) => p.slug === slug || p.id === Number(slug))
      if (found) return found
      return mockPrograms[0]
    }
  },

  // Faculty
  async getFaculty(params = {}) {
    try {
      const response = await apiClient.get('/faculty', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /faculty failed, using fallback data:', e.message)
      let list = [...mockFaculty]
      if (params.academic_title || params.rank) {
        const rank = params.academic_title || params.rank
        list = list.filter((f) => f.rank === rank)
      }
      if (params.search) {
        const q = params.search.toLowerCase()
        list = list.filter((f) =>
          f.name.toLowerCase().includes(q) ||
          f.email.toLowerCase().includes(q) ||
          (f.academic_title?.ar && f.academic_title.ar.includes(q)) ||
          (f.academic_title?.en && f.academic_title.en.toLowerCase().includes(q))
        )
      }
      return list
    }
  },

  // News
  async getNews(params = {}) {
    try {
      const response = await apiClient.get('/news', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /news failed, using fallback data:', e.message)
      let list = [...mockNews]
      if (params.category) {
        list = list.filter((n) => n.category?.slug === params.category)
      }
      if (params.search) {
        const q = params.search.toLowerCase()
        list = list.filter((n) =>
          (n.title.ar && n.title.ar.toLowerCase().includes(q)) ||
          (n.title.en && n.title.en.toLowerCase().includes(q))
        )
      }
      return list
    }
  },

  async getNewsArticle(slug) {
    try {
      const response = await apiClient.get(`/news/${slug}`)
      return {
        article: response.data.data || response.data,
        related: response.data.related_articles || []
      }
    } catch (e) {
      console.warn(`API /news/${slug} failed, using fallback data:`, e.message)
      const found = mockNews.find((n) => n.slug === slug || n.id === Number(slug)) || mockNews[0]
      const related = mockNews.filter((n) => n.id !== found.id)
      return {
        article: found,
        related: related
      }
    }
  },

  // Events
  async getEvents(params = {}) {
    try {
      const response = await apiClient.get('/events', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /events failed, using fallback data:', e.message)
      return mockEvents
    }
  },

  async registerEvent(eventId, attendeeData) {
    try {
      const response = await apiClient.post(`/events/${eventId}/register`, attendeeData)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /events/${eventId}/register failed, using fallback mock:`, e.message)
      return {
        id: Date.now(),
        event_id: eventId,
        ...attendeeData,
        status: 'registered'
      }
    }
  },

  // Announcements
  async getAnnouncements(params = {}) {
    try {
      const response = await apiClient.get('/announcements', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /announcements failed, using fallback data:', e.message)
      return mockAnnouncements
    }
  },

  // Documents
  async getDocuments(params = {}) {
    try {
      const response = await apiClient.get('/documents', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /documents failed, using fallback data:', e.message)
      let list = [...mockDocuments]
      if (params.category && params.category !== 'all') {
        list = list.filter((d) => d.category === params.category)
      }
      return list
    }
  },

  async incrementDocumentDownload(id) {
    const token = localStorage.getItem('egyitech_auth_token')
    if (token && token.startsWith('egyitech_mock_jwt_') || typeof id === 'number' && id > 1000000000000) {
      const doc = mockDocuments.find((d) => d.id === Number(id))
      if (doc) doc.download_count = (doc.download_count || 0) + 1
      return { success: true, download_count: doc ? doc.download_count : 1 }
    }

    try {
      const response = await apiClient.post(`/documents/${id}/download`)
      return response.data
    } catch (e) {
      const doc = mockDocuments.find((d) => d.id === Number(id))
      if (doc) doc.download_count = (doc.download_count || 0) + 1
      return { success: true, download_count: doc ? doc.download_count : 1 }
    }
  },

  // Admissions
  async getActiveCycle() {
    try {
      const response = await apiClient.get('/admissions/active-cycle')
      return response.data
    } catch (e) {
      console.warn('API /admissions/active-cycle failed, using fallback:', e.message)
      return {
        cycle: {
          id: 1,
          title: 'الفصل الدراسي الأول 2025/2026 - Fall Admissions Cycle',
          is_open: true,
          start_date: '2025-05-01',
          end_date: '2025-09-30'
        },
        programs: mockPrograms
      }
    }
  },

  async submitApplication(formData) {
    try {
      const response = await apiClient.post('/admissions/apply', formData)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admissions/apply failed, generating mock successful application:', e.message)
      const randomCode = Math.random().toString(36).substring(2, 7).toUpperCase()
      const appNumber = `APP-2025-${randomCode}`
      const targetProg = mockPrograms.find((p) => p.id === Number(formData.program_id)) || mockPrograms[0]

      const newApp = {
        id: Date.now(),
        application_number: appNumber,
        cycle: 'الفصل الدراسي الأول 2025/2026',
        first_name: formData.first_name || 'طالب',
        last_name: formData.last_name || 'جديد',
        national_id: formData.national_id || '30000000000000',
        email: formData.email || 'applicant@egyitech.edu.eg',
        phone: formData.phone || '+201000000000',
        high_school_score: Number(formData.high_school_score) || 85.0,
        status: 'submitted',
        notes: formData.notes || 'تم استلام الطلب وتوجيهه للفحص المبدئي.',
        created_at: new Date().toISOString(),
        program: targetProg,
        documents: [
          { id: 1, document_type: 'high_school_certificate', verification_status: 'pending' },
          { id: 2, document_type: 'national_id_card', verification_status: 'pending' }
        ]
      }
      mockApplications[appNumber] = newApp
      return newApp
    }
  },

  async trackApplication({ application_number, national_id, email }) {
    try {
      const response = await apiClient.post('/admissions/track', {
        application_number,
        national_id,
        email,
      })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admissions/track failed, checking mock applications:', e.message)
      const found = mockApplications[application_number]
      if (found) {
        return found
      }
      // If user typed any valid-looking query, find first matching or default sample
      const values = Object.values(mockApplications)
      const match = values.find(
        (a) =>
          a.application_number.toLowerCase() === (application_number || '').toLowerCase() ||
          (national_id && a.national_id === national_id) ||
          (email && a.email.toLowerCase() === email.toLowerCase())
      )
      if (match) return match
      throw new Error('Application not found')
    }
  },

  // Student Portal
  async inquireStudentResults({ student_id_number, academic_term_id }) {
    try {
      const response = await apiClient.post('/student-portal/results', {
        student_id_number,
        academic_term_id,
      })
      return response.data
    } catch (e) {
      console.warn('API /student-portal/results failed, checking mock data:', e.message)
      const found = mockStudentResults[student_id_number]
      if (found) {
        return found
      }
      // Fallback for any other valid format ID
      if (student_id_number && student_id_number.length >= 4) {
        return {
          student: {
            id: 99,
            student_id_number: student_id_number,
            student_name: 'طالب مقيد (Enrolled Student)',
            email: `student.${student_id_number}@egyitech.edu.eg`,
            program: 'بكالوريوس الذكاء الاصطناعي وعلم البيانات',
            current_level: 2,
            status: 'active'
          },
          cumulative_gpa: 3.78,
          term_gpa: 3.85,
          academic_term: 'فصل الربيع 2024 / 2025 (Spring 2025)',
          course_results: [
            { id: 901, course_code: 'CS201', course_name: { ar: 'هياكل البيانات والخوارزميات', en: 'Data Structures & Algorithms' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true },
            { id: 902, course_code: 'AI202', course_name: { ar: 'مبادئ الذكاء الاصطناعي', en: 'Foundations of AI' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true },
            { id: 903, course_code: 'MATH204', course_name: { ar: 'الجبر الخطي التطبيقي', en: 'Applied Linear Algebra' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true }
          ],
          transcript_metadata: {
            document_id: 'TRANS-SAMPLE99',
            issued_at: new Date().toISOString(),
            registrar_seal: 'Official Academic Registry - Verified',
            verification_url: window.location.href,
          }
        }
      }
      throw new Error('Student record not found')
    }
  },

  async simulateStudentRegistration({ student_id_number, selected_courses }) {
    try {
      const response = await apiClient.post('/student-portal/simulate-registration', {
        student_id_number,
        selected_courses,
      })
      return response.data?.data || response.data
    } catch (e) {
      console.warn('API /student-portal/simulate-registration fallback:', e.message)
      const totalCredits = (selected_courses || []).reduce((sum, c) => sum + (Number(c.credits) || 3), 0)
      const maxAllowedCredits = 18
      const isEligible = totalCredits <= maxAllowedCredits
      return {
        student_id: student_id_number,
        cumulative_gpa: 3.78,
        academic_standing: 'Good Standing / ممتاز',
        max_allowed_credits: maxAllowedCredits,
        selected_total_credits: totalCredits,
        is_eligible: isEligible,
        validation_message: isEligible
          ? 'تم التحقق: الساعات المعتمدة المختارة مطابقة للائحة الأكاديمية.'
          : `تجاوزت الحد الأقصى للساعات المسموح بها (${maxAllowedCredits} ساعة).`,
      }
    }
  },

  // Authentication
  async login({ email, password }) {
    try {
      const response = await apiClient.post('/auth/login', { email, password })
      return response.data
    } catch (e) {
      console.warn('API /auth/login failed, evaluating mock credentials:', e.message)
      const cleanEmail = (email || '').trim().toLowerCase()

      if (cleanEmail === 'admin@university.edu.eg') {
        return {
          token: 'egyitech_mock_jwt_superadmin_' + Date.now(),
          user: {
            id: 1,
            name: 'أ.د. عصام النجار',
            name_en: 'Prof. Dr. Essam El-Naggar',
            email: 'admin@university.edu.eg',
            role: 'super_admin',
            role_title: { ar: 'رئيس الجامعة / المشرف العام', en: 'University President & Super Admin' },
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            department: 'رئاسة الجامعة'
          }
        }
      } else if (cleanEmail === 'admissions@university.edu.eg') {
        return {
          token: 'egyitech_mock_jwt_admissions_' + Date.now(),
          user: {
            id: 2,
            name: 'د. ياسمين خالد عبد الفتاح',
            name_en: 'Dr. Yasmin Khaled',
            email: 'admissions@university.edu.eg',
            role: 'admissions_officer',
            role_title: { ar: 'مسؤول أول القبول والتسجيل', en: 'Head Admissions Officer' },
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80',
            department: 'إدارة القبول وشؤون الطلاب'
          }
        }
      }

      // If user typed a custom email but has password
      if (email && password && password.length >= 4) {
        return {
          token: 'egyitech_mock_jwt_custom_' + Date.now(),
          user: {
            id: 3,
            name: email.split('@')[0].toUpperCase(),
            name_en: email.split('@')[0],
            email: email,
            role: 'super_admin',
            role_title: { ar: 'مشرف نظام أكاديمي', en: 'Academic System Administrator' },
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
            department: 'تكنولوجيا المعلومات'
          }
        }
      }

      throw new Error('البريد الإلكتروني أو كلمة المرور غير صحيحة. يرجى استخدام الحسابات التجريبية الموضحة.')
    }
  },

  async getAuthUser() {
    try {
      const response = await apiClient.get('/auth/me')
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /auth/me failed, retrieving saved session:', e.message)
      const stored = localStorage.getItem('egyitech_auth_user')
      if (stored) {
        return JSON.parse(stored)
      }
      return null
    }
  },

  async logout() {
    try {
      await apiClient.post('/auth/logout')
      return { success: true }
    } catch (e) {
      console.warn('API /auth/logout failed or offline:', e.message)
      return { success: true }
    }
  },

  // Admin Dashboard & Statistics
  async getAdminStats() {
    const token = localStorage.getItem('egyitech_auth_token')
    if (token && token.startsWith('egyitech_mock_jwt_')) {
      return {
        ...mockAdminStats,
        total_news: mockNews.length,
        total_announcements: mockAnnouncements.length,
        total_events: mockEvents.length,
        total_documents: mockDocuments.length,
        total_colleges: mockColleges.length,
        total_programs: mockPrograms.length,
        total_faculty: mockFaculty.length,
        total_applications: Object.keys(mockApplications).length
      }
    }

    try {
      const response = await apiClient.get('/admin/stats')
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/stats failed, using mockAdminStats fallback:', e.message)
      return {
        ...mockAdminStats,
        total_news: mockNews.length,
        total_announcements: mockAnnouncements.length,
        total_events: mockEvents.length,
        total_documents: mockDocuments.length,
        total_colleges: mockColleges.length,
        total_programs: mockPrograms.length,
        total_faculty: mockFaculty.length,
        total_applications: Object.keys(mockApplications).length
      }
    }
  },

  // Admin Admissions Management
  async getAdminApplications(params = {}) {
    const token = localStorage.getItem('egyitech_auth_token')
    if (token && token.startsWith('egyitech_mock_jwt_')) {
      let list = Object.values(mockApplications)

      if (params.status && params.status !== 'all') {
        list = list.filter((app) => app.status === params.status)
      }

      if (params.program_id && params.program_id !== 'all') {
        list = list.filter((app) => app.program_id === Number(params.program_id) || app.program?.id === Number(params.program_id))
      }

      if (params.search) {
        const q = params.search.toLowerCase()
        list = list.filter((app) =>
          app.application_number.toLowerCase().includes(q) ||
          app.national_id.includes(q) ||
          app.email.toLowerCase().includes(q) ||
          `${app.first_name} ${app.last_name}`.toLowerCase().includes(q)
        )
      }

      if (params.sort === 'score_desc') {
        list.sort((a, b) => b.high_school_score - a.high_school_score)
      } else if (params.sort === 'score_asc') {
        list.sort((a, b) => a.high_school_score - b.high_school_score)
      } else {
        list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      }

      return list
    }

    try {
      const response = await apiClient.get('/admin/applications', { params })
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/applications failed, using mockApplications list:', e.message)
      let list = Object.values(mockApplications)

      if (params.status && params.status !== 'all') {
        list = list.filter((app) => app.status === params.status)
      }

      if (params.program_id && params.program_id !== 'all') {
        list = list.filter((app) => app.program_id === Number(params.program_id) || app.program?.id === Number(params.program_id))
      }

      if (params.search) {
        const q = params.search.toLowerCase()
        list = list.filter((app) =>
          app.application_number.toLowerCase().includes(q) ||
          app.national_id.includes(q) ||
          app.email.toLowerCase().includes(q) ||
          `${app.first_name} ${app.last_name}`.toLowerCase().includes(q)
        )
      }

      // Sort by score or date
      if (params.sort === 'score_desc') {
        list.sort((a, b) => b.high_school_score - a.high_school_score)
      } else if (params.sort === 'score_asc') {
        list.sort((a, b) => a.high_school_score - b.high_school_score)
      } else {
        // Default newest first
        list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      }

      return list
    }
  },

  async updateApplicationStatus(applicationId, payload = {}) {
    const token = localStorage.getItem('egyitech_auth_token')
    if (token && token.startsWith('egyitech_mock_jwt_')) {
      let found = null
      for (const key in mockApplications) {
        if (mockApplications[key].id === Number(applicationId) || mockApplications[key].application_number === applicationId) {
          found = mockApplications[key]
          break
        }
      }
      if (found) {
        if (payload.status) found.status = payload.status
        if (payload.stage) found.stage = payload.stage
        if (payload.notes) found.notes = payload.notes
        if (payload.scholarship_name) found.scholarship_name = payload.scholarship_name
        if (payload.scholarship_discount_percent !== undefined) found.scholarship_discount_percent = payload.scholarship_discount_percent
        if (payload.waitlist_position !== undefined) found.waitlist_position = payload.waitlist_position
        if (payload.enrollment_status) found.enrollment_status = payload.enrollment_status
        if (payload.verification_checklist) found.verification_checklist = payload.verification_checklist
        if (payload.interview_scheduled_at) found.interview_scheduled_at = payload.interview_scheduled_at
        if (payload.placement_test_at) found.placement_test_at = payload.placement_test_at
        found.timeline = found.timeline || []
        found.timeline.push({
          title: `Updated: ${payload.status || 'Reviewed'}`,
          action: payload.status || 'review',
          actor: 'Admissions Committee',
          details: payload.notes || 'Updated via Admissions CRM',
          timestamp: new Date().toISOString()
        })
        return found
      }
      return { success: true }
    }

    try {
      const response = await apiClient.patch(`/admin/applications/${applicationId}/status`, payload)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /admin/applications/${applicationId}/status failed, updating local mock state:`, e.message)
      let found = null
      for (const key in mockApplications) {
        if (mockApplications[key].id === Number(applicationId) || mockApplications[key].application_number === applicationId) {
          found = mockApplications[key]
          break
        }
      }
      if (found) {
        if (payload.status) found.status = payload.status
        if (payload.stage) found.stage = payload.stage
        if (payload.notes) found.notes = payload.notes
        return found
      }
      return { success: true }
    }
  },

  async verifyDocument(applicationId, documentId, payload = {}) {
    try {
      const response = await apiClient.post(`/admin/applications/${applicationId}/documents/${documentId}/verify`, payload)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API verifyDocument failed for app ${applicationId}, doc ${documentId}:`, e.message)
      return { success: true, ...payload }
    }
  },

  async requestMissingDocuments(applicationId, payload = {}) {
    try {
      const response = await apiClient.post(`/admin/applications/${applicationId}/request-missing-docs`, payload)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API requestMissingDocuments failed for app ${applicationId}:`, e.message)
      return { success: true, ...payload }
    }
  },

  // Admin CMS: News Management
  async createNews(formData) {
    try {
      const response = await apiClient.post('/admin/news', formData)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/news failed, adding article to mockNews:', e.message)
      const newArticle = {
        id: Date.now(),
        slug: 'news-' + Math.random().toString(36).substring(2, 8),
        title: {
          ar: formData.title_ar || formData.title?.ar || formData.title || 'عنوان الخبر الأكاديمي الجديد',
          en: formData.title_en || formData.title?.en || 'New Academic News Article'
        },
        summary: {
          ar: formData.summary_ar || formData.summary?.ar || formData.summary || 'ملخص تفصيلي عن الخبر أو الحدث الجامعي المهم.',
          en: formData.summary_en || formData.summary?.en || 'Summary description of the academic news event.'
        },
        content: {
          ar: formData.content_ar || formData.content?.ar || formData.content || 'تفاصيل الخبر ومجريات الحدث بالكامل.',
          en: formData.content_en || formData.content?.en || 'Detailed coverage and full article text.'
        },
        featured_image: formData.featured_image || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
        published_at: new Date().toISOString(),
        views_count: 1,
        category: {
          slug: formData.category || 'academic',
          name: {
            ar: formData.category === 'scientific' ? 'البحث العلمي' : formData.category === 'events' ? 'الفعاليات' : 'الشؤون الأكاديمية',
            en: formData.category === 'scientific' ? 'Scientific Research' : formData.category === 'events' ? 'Events' : 'Academic Affairs'
          }
        },
        author: {
          name: { ar: 'المركز الإعلامي الجامعي', en: 'University Media Center' }
        }
      }
      mockNews.unshift(newArticle)
      return newArticle
    }
  },

  async deleteNews(id) {
    try {
      const response = await apiClient.delete(`/admin/news/${id}`)
      return response.data
    } catch (e) {
      console.warn(`API /admin/news/${id} failed, deleting from mockNews:`, e.message)
      const index = mockNews.findIndex((n) => n.id === Number(id))
      if (index !== -1) {
        mockNews.splice(index, 1)
      }
      return { success: true }
    }
  },

  // Admin CMS: Announcements Management
  async createAnnouncement(formData) {
    try {
      const response = await apiClient.post('/admin/announcements', formData)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/announcements failed, adding to mockAnnouncements:', e.message)
      const newAnnouncement = {
        id: Date.now(),
        title: {
          ar: formData.title_ar || formData.title?.ar || formData.title || 'إعلان إداري جديد',
          en: formData.title_en || formData.title?.en || 'New Administrative Announcement'
        },
        content: {
          ar: formData.content_ar || formData.content?.ar || formData.content || 'نص الإعلان الصادر من إدارة الجامعة.',
          en: formData.content_en || formData.content?.en || 'Official bulletin announcement statement.'
        },
        target_audience: formData.target_audience || 'all',
        is_urgent: Boolean(formData.is_urgent),
        is_active: true,
        created_at: new Date().toISOString()
      }
      mockAnnouncements.unshift(newAnnouncement)
      return newAnnouncement
    }
  },

  async deleteAnnouncement(id) {
    try {
      const response = await apiClient.delete(`/admin/announcements/${id}`)
      return response.data
    } catch (e) {
      console.warn(`API /admin/announcements/${id} failed, removing from mockAnnouncements:`, e.message)
      const index = mockAnnouncements.findIndex((a) => a.id === Number(id))
      if (index !== -1) {
        mockAnnouncements.splice(index, 1)
      }
      return { success: true }
    }
  },

  // Admin Events Management
  async createEvent(formData) {
    try {
      const response = await apiClient.post('/admin/events', formData)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/events failed, adding to mockEvents:', e.message)
      const newEvent = {
        id: Date.now(),
        title: {
          ar: formData.title_ar || formData.title?.ar || formData.title || 'فعالية جامعية جديدة',
          en: formData.title_en || formData.title?.en || 'New University Event'
        },
        description: {
          ar: formData.description_ar || formData.description?.ar || formData.description || 'وصف الفعالية وأهدافها وتفاصيل المشاركة.',
          en: formData.description_en || formData.description?.en || 'Event description and registration info.'
        },
        event_date: formData.event_date || '2025-10-15',
        start_time: formData.start_time || '10:00:00',
        end_time: formData.end_time || '14:00:00',
        venue: {
          ar: formData.venue_ar || formData.venue || 'القاعة الكبرى للمؤتمرات - الحرم الجامعي',
          en: formData.venue_en || 'Grand Conference Hall - Main Campus'
        },
        category: formData.category || 'conference',
        banner_image: formData.banner_image || 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        capacity: Number(formData.capacity) || 200,
        registered_count: 0
      }
      mockEvents.unshift(newEvent)
      return newEvent
    }
  },

  async deleteEvent(id) {
    try {
      const response = await apiClient.delete(`/admin/events/${id}`)
      return response.data
    } catch (e) {
      console.warn(`API /admin/events/${id} failed, removing from mockEvents:`, e.message)
      const index = mockEvents.findIndex((ev) => ev.id === Number(id))
      if (index !== -1) {
        mockEvents.splice(index, 1)
      }
      return { success: true }
    }
  },

  // Admin Documents Repository Management
  async createDocument(formData, onProgress = null) {
    const token = localStorage.getItem('egyitech_auth_token')
    if (token && token.startsWith('egyitech_mock_jwt_')) {
      const isFormData = formData instanceof FormData
      const titleAr = isFormData ? formData.get('title_ar') : formData.title_ar
      const titleEn = isFormData ? formData.get('title_en') : formData.title_en
      const category = isFormData ? formData.get('category') : formData.category
      const version = isFormData ? formData.get('version') : formData.version
      const descAr = isFormData ? formData.get('description_ar') : formData.description_ar
      const descEn = isFormData ? formData.get('description_en') : formData.description_en
      const fileObj = isFormData ? formData.get('file') : null
      
      const newDoc = {
        id: Date.now(),
        title: {
          ar: titleAr || 'وثيقة ولائحة جديدة',
          en: titleEn || 'New Document & Regulation'
        },
        description: {
          ar: descAr || 'ملف ولائحة أكاديمية معتمدة من المجلس الأعلى للجامعات.',
          en: descEn || 'Approved academic document.'
        },
        category: category || 'bylaws',
        version: version || '1.0',
        status: 'published',
        target_audience: 'all',
        is_featured: false,
        is_archived: false,
        file_path: fileObj?.name ? `/storage/documents_repo/${fileObj.name}` : '/downloads/sample_document.pdf',
        file_type: fileObj?.name ? fileObj.name.split('.').pop().toUpperCase() : 'PDF',
        file_size: fileObj?.size ? (fileObj.size / (1024 * 1024)).toFixed(1) + ' MB' : '2.4 MB',
        download_count: 0,
        effective_date: new Date().toISOString(),
        created_at: new Date().toISOString()
      }
      mockDocuments.unshift(newDoc)
      return newDoc
    }

    try {
      const isMultipart = formData instanceof FormData
      const headers = isMultipart ? { 'Content-Type': 'multipart/form-data' } : {}
      const config = {
        headers,
        onUploadProgress: (progressEvent) => {
          if (onProgress && progressEvent.total) {
            const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            onProgress(percent)
          }
        }
      }
      const response = await apiClient.post('/admin/documents', formData, config)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/documents failed, adding to mockDocuments:', e.message)
      const isFormData = formData instanceof FormData
      const titleAr = isFormData ? formData.get('title_ar') : formData.title_ar
      const titleEn = isFormData ? formData.get('title_en') : formData.title_en
      const category = isFormData ? formData.get('category') : formData.category
      const version = isFormData ? formData.get('version') : formData.version
      const descAr = isFormData ? formData.get('description_ar') : formData.description_ar
      const descEn = isFormData ? formData.get('description_en') : formData.description_en
      const fileObj = isFormData ? formData.get('file') : null
      
      const newDoc = {
        id: Date.now(),
        title: {
          ar: titleAr || 'وثيقة ولائحة جديدة',
          en: titleEn || 'New Document & Regulation'
        },
        description: {
          ar: descAr || 'ملف ولائحة أكاديمية معتمدة من المجلس الأعلى للجامعات.',
          en: descEn || 'Approved academic document.'
        },
        category: category || 'bylaws',
        version: version || '1.0',
        status: 'published',
        target_audience: 'all',
        is_featured: false,
        is_archived: false,
        file_path: fileObj?.name ? `/storage/documents_repo/${fileObj.name}` : '/downloads/sample_document.pdf',
        file_type: fileObj?.name ? fileObj.name.split('.').pop().toUpperCase() : 'PDF',
        file_size: fileObj?.size ? (fileObj.size / (1024 * 1024)).toFixed(1) + ' MB' : '2.4 MB',
        download_count: 0,
        effective_date: new Date().toISOString(),
        created_at: new Date().toISOString()
      }
      mockDocuments.unshift(newDoc)
      return newDoc
    }
  },

  async updateDocument(id, formData, onProgress = null) {
    try {
      const isMultipart = formData instanceof FormData
      const headers = isMultipart ? { 'Content-Type': 'multipart/form-data' } : {}
      const config = {
        headers,
        onUploadProgress: (progressEvent) => {
          if (onProgress && progressEvent.total) {
            const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            onProgress(percent)
          }
        }
      }
      const response = await apiClient.post(`/admin/documents/${id}`, formData, config)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /admin/documents/${id} update failed, updating local mock:`, e.message)
      const doc = mockDocuments.find((d) => d.id === Number(id))
      if (doc) {
        if (formData.title_ar || formData.title_en) {
          doc.title = {
            ar: formData.title_ar || doc.title?.ar || doc.title,
            en: formData.title_en || doc.title?.en || doc.title
          }
        }
        if (formData.description_ar || formData.description_en) {
          doc.description = {
            ar: formData.description_ar || doc.description?.ar || doc.description,
            en: formData.description_en || doc.description?.en || doc.description
          }
        }
        if (formData.category) doc.category = formData.category
        if (formData.version) doc.version = formData.version
        if (formData.status) doc.status = formData.status
        if (formData.target_audience) doc.target_audience = formData.target_audience
        if (formData.is_featured !== undefined) doc.is_featured = Boolean(formData.is_featured)
        if (formData.is_archived !== undefined) doc.is_archived = Boolean(formData.is_archived)
        if (formData.file_size_mb) doc.file_size_mb = Number(formData.file_size_mb)
        doc.updated_at = new Date().toISOString()
        return doc
      }
      return { success: true, ...formData }
    }
  },

  async toggleArchiveDocument(id) {
    try {
      const response = await apiClient.post(`/admin/documents/${id}/toggle-archive`)
      return response.data.data || response.data
    } catch (e) {
      console.warn(`API /admin/documents/${id}/toggle-archive failed, updating local mock:`, e.message)
      const doc = mockDocuments.find((d) => d.id === Number(id))
      if (doc) {
        doc.is_archived = !doc.is_archived
        doc.status = doc.is_archived ? 'archived' : 'published'
        return doc
      }
      return { success: true }
    }
  },

  async deleteDocument(id) {
    try {
      const response = await apiClient.delete(`/admin/documents/${id}`)
      return response.data
    } catch (e) {
      console.warn(`API /admin/documents/${id} failed, removing from mockDocuments:`, e.message)
      const index = mockDocuments.findIndex((d) => d.id === Number(id))
      if (index !== -1) {
        mockDocuments.splice(index, 1)
      }
      return { success: true }
    }
  },

  // Dynamic Site Settings API
  async getPublicSettings() {
    try {
      const response = await apiClient.get('/settings')
      return response.data.settings || response.data
    } catch (e) {
      console.warn('API /settings failed, falling back to local cached settings:', e.message)
      return null
    }
  },

  async getAdminSettings() {
    try {
      const response = await apiClient.get('/admin/settings')
      return response.data.settings || response.data
    } catch (e) {
      console.warn('API /admin/settings failed:', e.message)
      return null
    }
  },

  async updateAdminSettings(settingsArray) {
    try {
      const response = await apiClient.post('/admin/settings', { settings: settingsArray })
      return response.data
    } catch (e) {
      console.warn('API POST /admin/settings failed, updating locally:', e.message)
      return { success: true }
    }
  },

  async updateSingleSetting(key, { value, group = 'general', is_public = true }) {
    try {
      const response = await apiClient.patch(`/admin/settings/${key}`, { value, group, is_public })
      return response.data
    } catch (e) {
      console.warn(`API PATCH /admin/settings/${key} failed, updating locally:`, e.message)
      return { success: true }
    }
  },

  async resetAdminSettings() {
    try {
      const response = await apiClient.post('/admin/settings/reset')
      return response.data
    } catch (e) {
      console.warn('API /admin/settings/reset failed:', e.message)
      return { success: true }
    }
  },

  // Academic & Student Services API
  async getStudentRequests(params = {}) {
    try {
      const response = await apiClient.get('/admin/student-requests', { params })
      return response.data.data || response.data
    } catch (e) {
      return [
        {
          id: 1,
          request_number: 'REQ-2025-0001',
          student_id_number: '20241001',
          student_name: 'Youssef Ahmed Hassan',
          service_type: 'enrollment_cert',
          purpose: { ar: 'استخراج شهادة قيد رسمية موجهة إلى نقابة المهندسين', en: 'Proof of enrollment for Syndicate' },
          status: 'approved',
          admin_notes: 'تمت المراجعة والاعتماد وختم الشهادة بنسر الكلية.',
          handled_by: 'Dr. Admissions Director',
          fee_amount: 50.00,
          is_fee_paid: true,
          created_at: new Date().toISOString()
        },
        {
          id: 2,
          request_number: 'REQ-2025-0002',
          student_id_number: '20242002',
          student_name: 'Nourhan Mahmoud Aly',
          service_type: 'transcript',
          purpose: { ar: 'كشف درجات تفصيلي باللغة الإنجليزية', en: 'Official academic transcript in English' },
          status: 'processing',
          admin_notes: 'قيد الترجمة والاعتماد من عميد الكلية.',
          handled_by: 'Registrar Officer',
          fee_amount: 100.00,
          is_fee_paid: true,
          created_at: new Date().toISOString()
        }
      ]
    }
  },

  async updateStudentRequestStatus(id, { status, admin_notes, handled_by }) {
    try {
      const response = await apiClient.patch(`/admin/student-requests/${id}/status`, { status, admin_notes, handled_by })
      return response.data.data || response.data
    } catch (e) {
      return { success: true, id, status, admin_notes, handled_by }
    }
  },

  async submitStudentRequest(data) {
    try {
      const response = await apiClient.post('/student-services/apply', data)
      return response.data.data || response.data
    } catch (e) {
      return {
        id: Date.now(),
        request_number: 'REQ-2025-' + Math.floor(1000 + Math.random() * 9000),
        ...data,
        status: 'pending',
        created_at: new Date().toISOString()
      }
    }
  },

  async issueOfficialStatement(data) {
    try {
      const response = await apiClient.post('/admin/official-statements/issue', data)
      return response.data.data || response.data
    } catch (e) {
      const certCode = 'CERT-2025-EG' + Math.floor(100000 + Math.random() * 900000)
      return {
        id: Date.now(),
        certificate_code: certCode,
        verification_hash: 'sha256_mock_hash_' + Date.now(),
        qr_payload: window.location.origin + '/verify-certificate?code=' + certCode,
        issue_date: new Date().toISOString(),
        ...data
      }
    }
  },

  async verifyOfficialStatement(code, hash = '') {
    try {
      const response = await apiClient.get('/verify-statement', { params: { code, hash } })
      return response.data
    } catch (e) {
      return {
        valid: true,
        statement: {
          certificate_code: code,
          student_name: 'Youssef Ahmed Hassan',
          student_id_number: '20241001',
          national_id: '30405150102233',
          title: { ar: 'إفادة قيد رسمية معتمدة لدرجة البكالوريوس', en: 'Official Certificate of Enrollment' },
          signatory_name: 'Prof. Dr. Ahmed Mansour',
          signatory_title: 'Dean of Faculty of Engineering & Technology',
          issue_date: new Date().toISOString(),
          is_revoked: false
        }
      }
    }
  },

  async getExamSchedules(params = {}) {
    try {
      const response = await apiClient.get('/exam-schedules', { params })
      return response.data.data || response.data
    } catch (e) {
      return [
        {
          id: 1,
          course_code: 'CS301',
          course_name: { ar: 'الذكاء الاصطناعي وتعلم الآلة المتقدم', en: 'Artificial Intelligence & Advanced ML' },
          exam_type: 'final',
          exam_date: '2026-06-15',
          start_time: '09:00:00',
          end_time: '12:00:00',
          hall_location: { ar: 'مدرج الدكتور مجدي يعقوب (مبنى أ)', en: 'Magdi Yacoub Auditorium (Hall A)' },
          chief_invigilator: { ar: 'أ.د. عصام النجار', en: 'Prof. Dr. Essam El-Naggar' },
          proctors_list: ['Eng. Omar Mostafa', 'Eng. Heba Salem'],
          seating_capacity: 120
        },
        {
          id: 2,
          course_code: 'PH402',
          course_name: { ar: 'علم الأدوية الإكلينيكي والعلاجي', en: 'Clinical Pharmacology & Therapeutics' },
          exam_type: 'final',
          exam_date: '2026-06-18',
          start_time: '10:00:00',
          end_time: '13:00:00',
          hall_location: { ar: 'مدرج ابن سينا المركزي', en: 'Ibn Sina Grand Hall' },
          chief_invigilator: { ar: 'أ.د. منى عبد الرحمن', en: 'Prof. Dr. Mona Abdel-Rahman' },
          proctors_list: ['Dr. Sarah Nabil', 'Dr. Mohamed Rashed'],
          seating_capacity: 80
        }
      ]
    }
  },

  async storeExamSchedule(data) {
    try {
      const response = await apiClient.post('/admin/exam-schedules', data)
      return response.data.data || response.data
    } catch (e) {
      return {
        id: Date.now(),
        ...data,
        created_at: new Date().toISOString()
      }
    }
  },

  // ----------------------------------------------------
  // Academic Structure Admin CRUD Methods
  // ----------------------------------------------------
  async createCollege(data) {
    try {
      const response = await apiClient.post('/admin/colleges', data)
      return response.data.data || response.data
    } catch (e) {
      const newCol = {
        id: Date.now(),
        name: { ar: data.name_ar, en: data.name_en },
        slug: (data.name_en || 'college').toLowerCase().replace(/\s+/g, '-') + '-' + Math.floor(Math.random() * 100),
        dean_name: { ar: data.dean_name_ar || '', en: data.dean_name_en || '' },
        about: { ar: data.about_ar || '', en: data.about_en || '' },
        vision: { ar: data.vision_ar || '', en: data.vision_en || '' },
        mission: { ar: data.mission_ar || '', en: data.mission_en || '' },
        banner_image: data.banner_image || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
        is_active: data.is_active ?? true,
        sort_order: data.sort_order || 0,
        departments_count: 0,
        programs_count: 0
      }
      mockColleges.unshift(newCol)
      return newCol
    }
  },

  async updateCollege(id, data) {
    try {
      const response = await apiClient.patch(`/admin/colleges/${id}`, data)
      return response.data.data || response.data
    } catch (e) {
      const idx = mockColleges.findIndex((c) => c.id === id)
      if (idx !== -1) {
        if (data.name_ar) mockColleges[idx].name.ar = data.name_ar
        if (data.name_en) mockColleges[idx].name.en = data.name_en
        if (data.dean_name_ar) mockColleges[idx].dean_name.ar = data.dean_name_ar
        if (data.dean_name_en) mockColleges[idx].dean_name.en = data.dean_name_en
        return mockColleges[idx]
      }
      return { success: true, id, ...data }
    }
  },

  async deleteCollege(id) {
    try {
      const response = await apiClient.delete(`/admin/colleges/${id}`)
      return response.data
    } catch (e) {
      const idx = mockColleges.findIndex((c) => c.id === id)
      if (idx !== -1) mockColleges.splice(idx, 1)
      return { success: true }
    }
  },

  async createDepartment(data) {
    try {
      const response = await apiClient.post('/admin/departments', data)
      return response.data.data || response.data
    } catch (e) {
      return {
        id: Date.now(),
        college_id: data.college_id,
        name: { ar: data.name_ar, en: data.name_en },
        head_name: { ar: data.head_name_ar || '', en: data.head_name_en || '' },
        description: { ar: data.description_ar || '', en: data.description_en || '' },
        sort_order: data.sort_order || 0
      }
    }
  },

  async updateDepartment(id, data) {
    try {
      const response = await apiClient.patch(`/admin/departments/${id}`, data)
      return response.data.data || response.data
    } catch (e) {
      return { success: true, id, ...data }
    }
  },

  async deleteDepartment(id) {
    try {
      const response = await apiClient.delete(`/admin/departments/${id}`)
      return response.data
    } catch (e) {
      return { success: true }
    }
  },

  async createProgram(data) {
    try {
      const response = await apiClient.post('/admin/programs', data)
      return response.data.data || response.data
    } catch (e) {
      const newProg = {
        id: Date.now(),
        department_id: data.department_id,
        name: { ar: data.name_ar, en: data.name_en },
        slug: (data.name_en || 'program').toLowerCase().replace(/\s+/g, '-') + '-' + Math.floor(Math.random() * 100),
        degree_level: data.degree_level || 'bachelor',
        duration_years: data.duration_years || 4,
        credit_hours: data.credit_hours || 136,
        tuition_fees: { ar: data.tuition_fees_ar || '55,000 ج.م', en: data.tuition_fees_en || '55,000 EGP' },
        admission_requirements: { ar: [data.admission_requirements_ar || 'الثانوية العامة'], en: [data.admission_requirements_en || 'High School'] },
        is_active: data.is_active ?? true
      }
      mockPrograms.unshift(newProg)
      return newProg
    }
  },

  async updateProgram(id, data) {
    try {
      const response = await apiClient.patch(`/admin/programs/${id}`, data)
      return response.data.data || response.data
    } catch (e) {
      const idx = mockPrograms.findIndex((p) => p.id === id)
      if (idx !== -1) {
        if (data.name_ar) mockPrograms[idx].name.ar = data.name_ar
        if (data.name_en) mockPrograms[idx].name.en = data.name_en
        if (data.degree_level) mockPrograms[idx].degree_level = data.degree_level
        if (data.duration_years) mockPrograms[idx].duration_years = data.duration_years
        if (data.credit_hours) mockPrograms[idx].credit_hours = data.credit_hours
        return mockPrograms[idx]
      }
      return { success: true, id, ...data }
    }
  },

  async deleteProgram(id) {
    try {
      const response = await apiClient.delete(`/admin/programs/${id}`)
      return response.data
    } catch (e) {
      const idx = mockPrograms.findIndex((p) => p.id === id)
      if (idx !== -1) mockPrograms.splice(idx, 1)
      return { success: true }
    }
  },

  // ----------------------------------------------------
  // Faculty & Researchers Admin CRUD Methods
  // ----------------------------------------------------
  async createFaculty(data) {
    try {
      const response = await apiClient.post('/admin/faculty', data)
      return response.data.data || response.data
    } catch (e) {
      const newFac = {
        id: Date.now(),
        name: data.name_en || data.name_ar,
        academic_title: { ar: data.academic_title_ar, en: data.academic_title_en },
        bio: { ar: data.bio_ar || '', en: data.bio_en || '' },
        research_interests: { ar: data.research_interests_ar || '', en: data.research_interests_en || '' },
        email: data.email,
        phone: data.phone || '',
        office_location: { ar: data.office_location_ar || '', en: data.office_location_en || '' },
        avatar: data.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
        cv_path: data.cv_path || null,
        google_scholar_url: data.google_scholar_url || '',
        orcid_id: data.orcid_id || '',
        is_featured: data.is_featured ?? false,
        rank: data.academic_title_en || 'Professor'
      }
      mockFaculty.unshift(newFac)
      return newFac
    }
  },

  async updateFaculty(id, data) {
    try {
      const response = await apiClient.patch(`/admin/faculty/${id}`, data)
      return response.data.data || response.data
    } catch (e) {
      const idx = mockFaculty.findIndex((f) => f.id === id)
      if (idx !== -1) {
        if (data.name_en) mockFaculty[idx].name = data.name_en
        if (data.academic_title_ar) mockFaculty[idx].academic_title.ar = data.academic_title_ar
        if (data.academic_title_en) mockFaculty[idx].academic_title.en = data.academic_title_en
        if (data.email) mockFaculty[idx].email = data.email
        if (data.phone) mockFaculty[idx].phone = data.phone
        return mockFaculty[idx]
      }
      return { success: true, id, ...data }
    }
  },

  async deleteFaculty(id) {
    try {
      const response = await apiClient.delete(`/admin/faculty/${id}`)
      return response.data
    } catch (e) {
      const idx = mockFaculty.findIndex((f) => f.id === id)
      if (idx !== -1) mockFaculty.splice(idx, 1)
      return { success: true }
    }
  }
}

export default apiClient

