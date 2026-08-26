import { defineStore } from 'pinia'
import { api } from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => {
    let savedUser = null
    try {
      const stored = localStorage.getItem('egyitech_auth_user')
      if (stored) {
        savedUser = JSON.parse(stored)
      }
    } catch (e) {
      console.error('Failed to parse saved user from localStorage', e)
    }

    const savedToken = localStorage.getItem('egyitech_auth_token') || null

    return {
      token: savedToken,
      user: savedUser,
      isAuthenticated: Boolean(savedToken),
      isLoading: false,
      error: null,
    }
  },

  getters: {
    currentUser: (state) => state.user,
    userRole: (state) => {
      if (!state.user) return 'guest'
      if (state.user.role) return state.user.role
      if (Array.isArray(state.user.roles) && state.user.roles.length > 0) {
        return state.user.roles[0]
      }
      return 'guest'
    },
    isSuperAdmin: (state) => {
      if (!state.user) return false
      const role = state.user.role
      const roles = Array.isArray(state.user.roles) ? state.user.roles : []
      return (
        role === 'super_admin' ||
        role === 'super-admin' ||
        roles.includes('super-admin') ||
        roles.includes('super_admin')
      )
    },
    isAdmissionsOfficer: (state) => {
      if (!state.user) return false
      const role = state.user.role
      const roles = Array.isArray(state.user.roles) ? state.user.roles : []
      return (
        role === 'admissions_officer' ||
        role === 'admissions-officer' ||
        roles.includes('admissions-officer') ||
        roles.includes('admissions_officer') ||
        roles.includes('super-admin') ||
        role === 'super-admin'
      )
    },
    userName: (state) => state.user?.name || 'Administrator',
    userEmail: (state) => state.user?.email || '',
    userAvatar: (state) =>
      state.user?.avatar ||
      'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
  },

  actions: {
    async login({ email, password, remember = true }) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.login({ email, password })
        const { token, user } = response

        this.token = token
        this.user = user
        this.isAuthenticated = true

        localStorage.setItem('egyitech_auth_token', token)
        localStorage.setItem('egyitech_auth_user', JSON.stringify(user))

        return user
      } catch (err) {
        const message =
          err.response?.data?.message ||
          err.message ||
          'فشل تسجيل الدخول. يرجى التحقق من صحة البيانات.'
        this.error = message
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchUser() {
      if (!this.token) return null
      try {
        const userData = await api.getAuthUser()
        if (userData) {
          const userObj = userData.user || userData.data || userData
          this.user = userObj
          localStorage.setItem('egyitech_auth_user', JSON.stringify(userObj))
        }
        return this.user
      } catch (err) {
        console.warn('Failed to fetch authenticated user session:', err)
        if (err.status === 401 || err.response?.status === 401) {
          this.logout()
        }
        return null
      }
    },

    async logout() {
      try {
        if (this.token) {
          await api.logout()
        }
      } catch (err) {
        console.warn('Logout API call failed or offline:', err)
      } finally {
        this.token = null
        this.user = null
        this.isAuthenticated = false
        this.error = null
        localStorage.removeItem('egyitech_auth_token')
        localStorage.removeItem('egyitech_auth_user')
      }
    },

    clearError() {
      this.error = null
    },
  },
})
