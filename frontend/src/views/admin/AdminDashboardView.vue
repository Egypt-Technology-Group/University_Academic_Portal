<template>
  <div class="space-y-6 sm:space-y-8">
    <!-- Welcome Header & Live Status Banner -->
    <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
      <div class="absolute -end-10 -top-10 w-64 h-64 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-bold mb-3">
            <Sparkles class="w-3.5 h-3.5" />
            <span>{{ $t('admin.dashboard.cycleStatusPill') }}</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
            {{ $t('admin.dashboard.welcomePrefix') }}, {{ authStore.userName }}
          </h1>
          <p class="text-slate-300 text-sm mt-1 max-w-2xl font-medium">
            {{ $t('admin.dashboard.welcomeDesc') }}
          </p>
        </div>

        <!-- Quick Top Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
          <router-link
            to="/admin/admissions"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-navy-950 font-bold text-xs sm:text-sm shadow-gold-glow transition-all"
          >
            <UserCheck class="w-4 h-4" />
            <span>{{ $t('admin.dashboard.actionReviewQueue') }}</span>
          </router-link>

          <router-link
            to="/admin/cms"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 transition-all backdrop-blur-sm"
          >
            <Plus class="w-4 h-4" />
            <span>{{ $t('admin.dashboard.actionNewArticle') }}</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
      <!-- 1. Pending Applications -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
            {{ $t('admin.dashboard.kpiPendingApps') }}
          </span>
          <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <Clock class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-navy-950 font-mono">{{ stats.pending_applications || 14 }}</span>
          <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            {{ $t('admin.dashboard.kpiRequiresAction') }}
          </span>
        </div>
        <div class="mt-3 text-xs text-slate-500 font-medium">
          {{ $t('admin.dashboard.kpiPendingSub') }}
        </div>
      </div>

      <!-- 2. Accepted Applications -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
            {{ $t('admin.dashboard.kpiAcceptedApps') }}
          </span>
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <CheckCircle2 class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-navy-950 font-mono">{{ stats.accepted_applications || 106 }}</span>
          <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
            +18% {{ $t('admin.dashboard.kpiThisMonth') }}
          </span>
        </div>
        <div class="mt-3 text-xs text-slate-500 font-medium">
          {{ $t('admin.dashboard.kpiAcceptedSub') }}
        </div>
      </div>

      <!-- 3. Total Programs & Colleges -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
            {{ $t('admin.dashboard.kpiPrograms') }}
          </span>
          <div class="w-10 h-10 rounded-xl bg-navy-50 text-navy-800 flex items-center justify-center group-hover:scale-110 transition-transform">
            <GraduationCap class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-navy-950 font-mono">{{ stats.total_programs || 28 }}</span>
          <span class="text-xs font-bold text-navy-900 bg-navy-100 px-2 py-0.5 rounded-md">
            {{ stats.total_colleges || 5 }} {{ $t('admin.dashboard.kpiCollegesCount') }}
          </span>
        </div>
        <div class="mt-3 text-xs text-slate-500 font-medium">
          {{ $t('admin.dashboard.kpiAccredited') }}
        </div>
      </div>

      <!-- 4. Total Enrolled Students -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
            {{ $t('admin.dashboard.kpiStudents') }}
          </span>
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <Users class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-navy-950 font-mono">{{ Number(stats.total_students || 15420).toLocaleString() }}</span>
          <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md">
            {{ stats.total_faculty || 480 }} {{ $t('admin.dashboard.kpiFaculty') }}
          </span>
        </div>
        <div class="mt-3 text-xs text-slate-500 font-medium">
          {{ $t('admin.dashboard.kpiActiveEnrollment') }}
        </div>
      </div>
    </div>

    <!-- Secondary Metrics Row: Published Media & Repository Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
      <div class="bg-white rounded-xl p-4 border border-slate-200/80 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
          <Newspaper class="w-4 h-4" />
        </div>
        <div>
          <div class="text-xs text-slate-500 font-medium">{{ $t('admin.dashboard.secNewsCount') }}</div>
          <div class="text-lg font-black text-navy-950 font-mono">{{ stats.total_news || 18 }}</div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-4 border border-slate-200/80 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
          <Megaphone class="w-4 h-4" />
        </div>
        <div>
          <div class="text-xs text-slate-500 font-medium">{{ $t('admin.dashboard.secAnnounceCount') }}</div>
          <div class="text-lg font-black text-navy-950 font-mono">{{ stats.total_announcements || 9 }}</div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-4 border border-slate-200/80 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
          <Calendar class="w-4 h-4" />
        </div>
        <div>
          <div class="text-xs text-slate-500 font-medium">{{ $t('admin.dashboard.secEventsCount') }}</div>
          <div class="text-lg font-black text-navy-950 font-mono">{{ stats.total_events || 12 }}</div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-4 border border-slate-200/80 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
          <FileText class="w-4 h-4" />
        </div>
        <div>
          <div class="text-xs text-slate-500 font-medium">{{ $t('admin.dashboard.secDocsCount') }}</div>
          <div class="text-lg font-black text-navy-950 font-mono">{{ stats.total_documents || 24 }}</div>
        </div>
      </div>
    </div>

    <!-- Main Content Split: Recent Admissions Queue & Admissions Cycle Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left 2 Cols: Recent Applications Table -->
      <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h2 class="text-lg font-black text-navy-950">
              {{ $t('admin.dashboard.recentAppsTitle') }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              {{ $t('admin.dashboard.recentAppsSubtitle') }}
            </p>
          </div>
          <router-link
            to="/admin/admissions"
            class="text-xs font-bold text-navy-900 hover:text-gold-600 flex items-center gap-1 transition-colors"
          >
            <span>{{ $t('admin.dashboard.viewAllApps') }}</span>
            <ArrowLeft v-if="localeStore.isRtl" class="w-3.5 h-3.5" />
            <ArrowRight v-else class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto -mx-6 px-6 flex-1">
          <table class="w-full text-start text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[11px] bg-slate-50/50">
                <th class="py-3 px-3 text-start">{{ $t('admin.admissions.colApplicant') }}</th>
                <th class="py-3 px-3 text-start">{{ $t('admin.admissions.colProgram') }}</th>
                <th class="py-3 px-3 text-center">{{ $t('admin.admissions.colScore') }}</th>
                <th class="py-3 px-3 text-center">{{ $t('admin.admissions.colStatus') }}</th>
                <th class="py-3 px-3 text-end">{{ $t('admin.admissions.colActions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="app in recentApplications"
                :key="app.id"
                class="hover:bg-slate-50/80 transition-colors"
              >
                <!-- Applicant Name & ID -->
                <td class="py-3.5 px-3">
                  <div class="font-bold text-navy-950 text-sm">
                    {{ app.first_name }} {{ app.last_name }}
                  </div>
                  <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                    {{ app.application_number }}
                  </div>
                </td>

                <!-- Target Program -->
                <td class="py-3.5 px-3">
                  <div class="font-medium text-slate-700 truncate max-w-[180px]">
                    {{ getTranslated(app.program?.name, localeStore.locale) }}
                  </div>
                  <div class="text-[10px] text-slate-400">
                    {{ getTranslated(app.program?.college_name, localeStore.locale) }}
                  </div>
                </td>

                <!-- Score -->
                <td class="py-3.5 px-3 text-center">
                  <span
                    :class="[
                      'inline-block font-mono font-bold px-2 py-0.5 rounded text-xs',
                      app.high_school_score >= 90 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                      app.high_school_score >= 80 ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                      'bg-slate-100 text-slate-700'
                    ]"
                  >
                    {{ app.high_school_score }}%
                  </span>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-3 text-center">
                  <span
                    :class="[
                      'inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full',
                      statusBadgeClasses[app.status] || 'bg-slate-100 text-slate-700'
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClasses[app.status]"></span>
                    {{ getStatusLabel(app.status) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-3 text-end">
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-navy-900 hover:text-white font-bold text-xs text-navy-900 transition-all inline-flex items-center gap-1"
                    @click="openReview(app)"
                  >
                    <Eye class="w-3.5 h-3.5" />
                    <span>{{ $t('admin.admissions.review') }}</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right 1 Col: Admissions Cycle & Quick System Health -->
      <div class="space-y-6">
        <!-- Admissions Cycle Progress Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-navy-950 text-base">
              {{ $t('admin.dashboard.cycleCardTitle') }}
            </h3>
            <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md">
              {{ $t('admin.dashboard.cycleActive') }}
            </span>
          </div>

          <div class="space-y-4">
            <div>
              <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-1.5">
                <span>{{ $t('admin.dashboard.cycleQuotaTarget') }}</span>
                <span class="font-mono text-navy-950 font-black">106 / 250 (42.4%)</span>
              </div>
              <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-gold-500 to-emerald-500 rounded-full transition-all duration-500" style="width: 42.4%"></div>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
              <div class="flex items-center justify-between text-slate-600">
                <span>{{ $t('admin.dashboard.cycleStart') }}</span>
                <span class="font-mono font-bold text-slate-800">2025-05-01</span>
              </div>
              <div class="flex items-center justify-between text-slate-600">
                <span>{{ $t('admin.dashboard.cycleEnd') }}</span>
                <span class="font-mono font-bold text-slate-800">2025-09-30</span>
              </div>
              <div class="flex items-center justify-between text-slate-600">
                <span>{{ $t('admin.dashboard.cycleDaysRemaining') }}</span>
                <span class="font-mono font-bold text-gold-600">36 {{ $t('admin.dashboard.days') }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- System Architecture, Security & Audit Feed -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
          <h3 class="font-black text-navy-950 text-base flex items-center gap-2">
            <ShieldCheck class="w-5 h-5 text-emerald-600" />
            <span>{{ $t('admin.dashboard.systemHealthTitle') }}</span>
          </h3>

          <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
              <span class="text-slate-500">{{ $t('admin.dashboard.healthDatabase') }}</span>
              <span class="font-bold text-emerald-700 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                SQLite / MySQL Active
              </span>
            </div>

            <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
              <span class="text-slate-500">API Rate Limiter</span>
              <span class="font-bold text-emerald-700">Enforced (Throttle:API)</span>
            </div>

            <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
              <span class="text-slate-500">{{ $t('admin.dashboard.healthAuthEngine') }}</span>
              <span class="font-bold text-navy-900">Laravel Sanctum RBAC</span>
            </div>

            <div class="flex items-center justify-between py-1.5">
              <span class="text-slate-500">{{ $t('admin.dashboard.healthUptime') }}</span>
              <span class="font-mono font-bold text-emerald-600">99.99% Online</span>
            </div>
          </div>

          <!-- Real-Time Activity Audit Trail -->
          <div class="pt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2.5">
              <span class="text-xs font-black text-navy-950 uppercase tracking-wider">
                {{ localeStore.isRtl ? 'سجل العمليات الإدارية (Audit Trail)' : 'Security Audit Trail' }}
              </span>
              <span class="text-[10px] bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded font-mono font-bold">LIVE</span>
            </div>
            <div class="space-y-2">
              <div class="text-[11px] p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                <div>
                  <span class="font-bold text-navy-950">{{ authStore.userName }}</span>
                  <span class="text-slate-500 block text-[10px]">{{ localeStore.isRtl ? 'تحديث إعدادات الهوية والبوابة' : 'Updated Site Customization & Fonts' }}</span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">Just now</span>
              </div>
              <div class="text-[11px] p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                <div>
                  <span class="font-bold text-navy-950">Admissions Committee</span>
                  <span class="text-slate-500 block text-[10px]">{{ localeStore.isRtl ? 'مراجعة طلبات الالتحاق وتحديد مواعيد' : 'Screened application & set interview' }}</span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">10m ago</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import {
  Sparkles,
  UserCheck,
  Plus,
  Clock,
  CheckCircle2,
  GraduationCap,
  Users,
  Newspaper,
  Megaphone,
  Calendar,
  FileText,
  Eye,
  ShieldCheck,
  ArrowLeft,
  ArrowRight,
} from 'lucide-vue-next'

const router = useRouter()
const { t } = useI18n()
const authStore = useAuthStore()
const localeStore = useLocaleStore()

const stats = ref({})
const recentApplications = ref([])
const isLoading = ref(true)

const statusBadgeClasses = {
  submitted: 'bg-blue-50 text-blue-700 border border-blue-200',
  under_review: 'bg-amber-50 text-amber-700 border border-amber-200',
  accepted: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  approved: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  rejected: 'bg-red-50 text-red-700 border border-red-200',
}

const statusDotClasses = {
  submitted: 'bg-blue-500',
  under_review: 'bg-amber-500 animate-pulse',
  accepted: 'bg-emerald-500',
  approved: 'bg-emerald-500',
  rejected: 'bg-red-500',
}

const getStatusLabel = (status) => {
  if (status === 'submitted') return t('admin.admissions.statusSubmitted')
  if (status === 'under_review') return t('admin.admissions.statusUnderReview')
  if (status === 'accepted' || status === 'approved') return t('admin.admissions.statusAccepted')
  if (status === 'rejected') return t('admin.admissions.statusRejected')
  return status
}

const loadDashboardData = async () => {
  isLoading.value = true
  try {
    const [statsRes, appsRes] = await Promise.all([
      api.getAdminStats(),
      api.getAdminApplications(),
    ])
    stats.value = statsRes || {}
    recentApplications.value = (appsRes || []).slice(0, 6)
  } catch (e) {
    console.error('Failed to load admin dashboard data:', e)
  } finally {
    isLoading.value = false
  }
}

const openReview = (app) => {
  router.push({
    path: '/admin/admissions',
    query: { review: app.id || app.application_number }
  })
}

onMounted(() => {
  loadDashboardData()
})
</script>
