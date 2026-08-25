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
  mockApplications
} from './mockData'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1',
  timeout: 4000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Request Interceptor to dynamically set Accept-Language and X-Locale
apiClient.interceptors.request.use((config) => {
  const currentLocale = localStorage.getItem('egyitech_locale') || 'ar'
  config.headers['Accept-Language'] = currentLocale
  config.headers['X-Locale'] = currentLocale
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
          ]
        }
      }
      throw new Error('Student record not found')
    }
  }
}

export default apiClient
