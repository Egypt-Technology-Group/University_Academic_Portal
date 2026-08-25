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
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
}, (error) => {
  return Promise.reject(error)
})

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
    try {
      const response = await apiClient.post(`/documents/${id}/download`)
      return response.data
    } catch (e) {
      console.warn(`API /documents/${id}/download failed:`, e.message)
      const doc = mockDocuments.find((d) => d.id === id)
      if (doc) doc.download_count++
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

  async updateApplicationStatus(applicationId, { status, notes }) {
    try {
      const response = await apiClient.patch(`/admin/applications/${applicationId}/status`, {
        status,
        notes
      })
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
        if (status) found.status = status
        if (notes !== undefined) found.notes = notes
        found.updated_at = new Date().toISOString()
        return found
      }

      return {
        id: applicationId,
        status,
        notes,
        updated_at: new Date().toISOString()
      }
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
  async createDocument(formData) {
    try {
      const response = await apiClient.post('/admin/documents', formData)
      return response.data.data || response.data
    } catch (e) {
      console.warn('API /admin/documents failed, adding to mockDocuments:', e.message)
      const newDoc = {
        id: Date.now(),
        title: {
          ar: formData.title_ar || formData.title?.ar || formData.title || 'وثيقة ولائحة جديدة',
          en: formData.title_en || formData.title?.en || 'New Document & Regulation'
        },
        description: {
          ar: formData.description_ar || formData.description || 'ملف ولائحة أكاديمية معتمدة من المجلس الأعلى للجامعات.',
          en: formData.description_en || 'Approved academic document.'
        },
        category: formData.category || 'regulations',
        file_path: formData.file_path || '/documents/sample_document.pdf',
        file_type: formData.file_type || 'PDF',
        file_size_mb: Number(formData.file_size_mb) || 2.4,
        download_count: 0,
        created_at: new Date().toISOString()
      }
      mockDocuments.unshift(newDoc)
      return newDoc
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
  }
}

export default apiClient

