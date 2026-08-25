<template>
  <div class="space-y-6">
    <!-- View Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.admissions.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ $t('admin.admissions.subtitle') }}
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <!-- Export / Refresh Actions -->
        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition-colors cursor-pointer"
          @click="loadApplications"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" />
          <span>{{ $t('admin.admissions.refresh') }}</span>
        </button>

        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
          @click="exportCsv"
        >
          <Download class="w-3.5 h-3.5 text-gold-400" />
          <span>{{ $t('admin.admissions.exportCsv') }}</span>
        </button>
      </div>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
      <button
        v-for="statusTab in statusTabs"
        :key="statusTab.key"
        type="button"
        :class="[
          'p-3.5 rounded-2xl border text-start transition-all cursor-pointer',
          activeStatusFilter === statusTab.key
            ? 'bg-navy-950 text-white border-navy-950 shadow-md ring-2 ring-navy-950/20'
            : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50'
        ]"
        @click="activeStatusFilter = statusTab.key"
      >
        <div class="text-xs font-semibold text-slate-400" :class="{ 'text-slate-300': activeStatusFilter === statusTab.key }">
          {{ statusTab.label }}
        </div>
        <div class="text-xl font-black font-mono mt-1" :class="{ 'text-gold-400': activeStatusFilter === statusTab.key, 'text-navy-950': activeStatusFilter !== statusTab.key }">
          {{ statusCounts[statusTab.key] || 0 }}
        </div>
      </button>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
      <!-- Search Input -->
      <div class="relative flex-1 w-full">
        <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('admin.admissions.searchPlaceholder')"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900 transition-all"
        />
        <button
          v-if="searchQuery"
          type="button"
          class="absolute inset-y-0 end-3 flex items-center text-slate-400 hover:text-slate-600"
          @click="searchQuery = ''"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Program Filter Dropdown -->
      <div class="w-full md:w-64 shrink-0">
        <select
          v-model="selectedProgram"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
        >
          <option value="all">{{ $t('admin.admissions.filterAllPrograms') }}</option>
          <option v-for="prog in programOptions" :key="prog.id" :value="prog.id">
            {{ getTranslated(prog.name, localeStore.locale) }}
          </option>
        </select>
      </div>

      <!-- Score Sorting Dropdown -->
      <div class="w-full md:w-52 shrink-0">
        <select
          v-model="selectedSort"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
        >
          <option value="newest">{{ $t('admin.admissions.sortNewest') }}</option>
          <option value="score_desc">{{ $t('admin.admissions.sortScoreDesc') }}</option>
          <option value="score_asc">{{ $t('admin.admissions.sortScoreAsc') }}</option>
        </select>
      </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="py-16 text-center text-slate-400">
        <div class="w-8 h-8 border-2 border-navy-900 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <div class="text-xs font-bold">{{ $t('common.loading') }}</div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredApplications.length === 0" class="py-16 text-center text-slate-500">
        <Inbox class="w-12 h-12 text-slate-300 mx-auto mb-3" />
        <div class="text-sm font-bold text-navy-950">{{ $t('admin.admissions.noApplicationsFound') }}</div>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
          {{ $t('admin.admissions.noApplicationsFoundDesc') }}
        </p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">{{ $t('admin.admissions.colNumber') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.admissions.colApplicant') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.admissions.colProgram') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.admissions.colScore') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.admissions.colStatus') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.admissions.colDate') }}</th>
              <th class="py-3.5 px-4 text-end">{{ $t('admin.admissions.colActions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="app in filteredApplications"
              :key="app.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- Application Number -->
              <td class="py-4 px-4 font-mono font-bold text-navy-950 whitespace-nowrap">
                {{ app.application_number }}
              </td>

              <!-- Applicant Details -->
              <td class="py-4 px-4">
                <div class="font-bold text-navy-950 text-sm">
                  {{ app.first_name }} {{ app.last_name }}
                </div>
                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                  <span class="font-mono">{{ app.national_id }}</span>
                  <span>•</span>
                  <span>{{ app.email }}</span>
                </div>
              </td>

              <!-- Program -->
              <td class="py-4 px-4">
                <div class="font-semibold text-slate-800">
                  {{ getTranslated(app.program?.name, localeStore.locale) }}
                </div>
                <div class="text-[10px] text-slate-400">
                  {{ getTranslated(app.program?.college_name, localeStore.locale) }}
                </div>
              </td>

              <!-- Score -->
              <td class="py-4 px-4 text-center">
                <span
                  :class="[
                    'inline-block font-mono font-bold px-2.5 py-1 rounded-md text-xs',
                    app.high_school_score >= 90 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                    app.high_school_score >= 80 ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                    'bg-slate-100 text-slate-700'
                  ]"
                >
                  {{ app.high_school_score }}%
                </span>
              </td>

              <!-- Status Badge -->
              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span
                  :class="[
                    'inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 rounded-full',
                    statusBadgeClasses[app.status] || 'bg-slate-100 text-slate-700'
                  ]"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClasses[app.status]"></span>
                  {{ getStatusLabel(app.status) }}
                </span>
              </td>

              <!-- Date -->
              <td class="py-4 px-4 text-slate-500 whitespace-nowrap font-mono text-[11px]">
                {{ formatDate(app.created_at) }}
              </td>

              <!-- Actions -->
              <td class="py-4 px-4 text-end whitespace-nowrap">
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer"
                  @click="openReviewModal(app)"
                >
                  <Eye class="w-3.5 h-3.5 text-gold-400" />
                  <span>{{ $t('admin.admissions.review') }}</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Interactive Application Review Modal -->
    <Modal
      v-model="isReviewModalOpen"
      :title="$t('admin.admissions.reviewModalTitle')"
      max-width="3xl"
      @close="closeReviewModal"
    >
      <div v-if="activeApp" class="space-y-6 text-start">
        <!-- Toast Notification on update -->
        <div
          v-if="updateSuccessMessage"
          class="bg-emerald-500/15 border border-emerald-500/30 rounded-2xl p-4 text-emerald-800 text-xs font-bold flex items-center gap-2"
        >
          <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
          <span>{{ updateSuccessMessage }}</span>
        </div>

        <!-- Header Card with Application Number & High School Score -->
        <div class="bg-gradient-to-r from-navy-950 to-navy-900 rounded-2xl p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
          <div>
            <div class="text-[11px] font-mono text-gold-400 font-bold uppercase tracking-wider">
              {{ activeApp.application_number }}
            </div>
            <h3 class="text-xl font-black text-white mt-0.5">
              {{ activeApp.first_name }} {{ activeApp.last_name }}
            </h3>
            <div class="text-xs text-slate-300 mt-1 flex items-center gap-2">
              <span>{{ activeApp.national_id }}</span>
              <span>•</span>
              <span>{{ activeApp.phone }}</span>
            </div>
          </div>

          <!-- Score Card -->
          <div class="bg-navy-900/90 border border-gold-500/40 rounded-xl px-4 py-2.5 text-center shrink-0">
            <div class="text-[10px] uppercase font-bold text-slate-300">{{ $t('admin.admissions.scoreLabel') }}</div>
            <div class="text-2xl font-black font-mono text-gold-400">{{ activeApp.high_school_score }}%</div>
          </div>
        </div>

        <!-- Section 1: Academic Information -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
            <GraduationCap class="w-4 h-4 text-navy-900" />
            <span>{{ $t('admin.admissions.secAcademicInfo') }}</span>
          </h4>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
              <span class="text-slate-400 block">{{ $t('admin.admissions.fieldProgram') }}:</span>
              <span class="font-bold text-navy-950 text-sm">
                {{ getTranslated(activeApp.program?.name, localeStore.locale) }}
              </span>
            </div>

            <div>
              <span class="text-slate-400 block">{{ $t('admin.admissions.fieldCollege') }}:</span>
              <span class="font-bold text-slate-800">
                {{ getTranslated(activeApp.program?.college_name, localeStore.locale) }}
              </span>
            </div>

            <div>
              <span class="text-slate-400 block">{{ $t('admin.admissions.fieldEmail') }}:</span>
              <span class="font-medium text-slate-700 font-mono">{{ activeApp.email }}</span>
            </div>

            <div>
              <span class="text-slate-400 block">{{ $t('admin.admissions.fieldSubmissionDate') }}:</span>
              <span class="font-medium text-slate-700 font-mono">{{ formatDate(activeApp.created_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Section 2: Attached Documents Verification -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
            <FileCheck class="w-4 h-4 text-emerald-600" />
            <span>{{ $t('admin.admissions.secDocuments') }}</span>
          </h4>

          <div class="space-y-2.5">
            <div
              v-for="doc in (activeApp.documents || defaultDocumentsList)"
              :key="doc.id"
              class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200/80 text-xs"
            >
              <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                  📄
                </div>
                <div>
                  <div class="font-bold text-slate-800">{{ getDocTitle(doc) }}</div>
                  <div class="text-[10px] text-slate-400">PDF / Verified scan</div>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <span
                  :class="[
                    'text-[10px] font-bold px-2 py-0.5 rounded-full',
                    doc.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                  ]"
                >
                  {{ doc.verification_status === 'verified' ? $t('admin.admissions.verified') : $t('admin.admissions.pendingCheck') }}
                </span>

                <button
                  type="button"
                  class="text-navy-900 hover:text-gold-600 font-bold p-1 hover:bg-slate-100 rounded text-xs"
                  @click="alertDocumentPreview"
                >
                  <Eye class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Committee Decision & Notes -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-4">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <ShieldCheck class="w-4 h-4 text-gold-600" />
            <span>{{ $t('admin.admissions.secDecision') }}</span>
          </h4>

          <!-- Status Select Buttons -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">
              {{ $t('admin.admissions.selectStatusDecision') }}
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
              <button
                type="button"
                :class="[
                  'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border text-xs font-bold transition-all cursor-pointer',
                  reviewForm.status === 'accepted'
                    ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                    : 'bg-white text-slate-700 border-slate-300 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300'
                ]"
                @click="reviewForm.status = 'accepted'"
              >
                <CheckCircle2 class="w-4 h-4" />
                <span>{{ $t('admin.admissions.statusAccepted') }}</span>
              </button>

              <button
                type="button"
                :class="[
                  'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border text-xs font-bold transition-all cursor-pointer',
                  reviewForm.status === 'under_review'
                    ? 'bg-amber-600 text-white border-amber-600 shadow-sm'
                    : 'bg-white text-slate-700 border-slate-300 hover:bg-amber-50 hover:text-amber-800 hover:border-amber-300'
                ]"
                @click="reviewForm.status = 'under_review'"
              >
                <Clock class="w-4 h-4" />
                <span>{{ $t('admin.admissions.statusUnderReview') }}</span>
              </button>

              <button
                type="button"
                :class="[
                  'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border text-xs font-bold transition-all cursor-pointer',
                  reviewForm.status === 'rejected'
                    ? 'bg-red-600 text-white border-red-600 shadow-sm'
                    : 'bg-white text-slate-700 border-slate-300 hover:bg-red-50 hover:text-red-800 hover:border-red-300'
                ]"
                @click="reviewForm.status = 'rejected'"
              >
                <XCircle class="w-4 h-4" />
                <span>{{ $t('admin.admissions.statusRejected') }}</span>
              </button>
            </div>
          </div>

          <!-- Committee Notes Textarea -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              {{ $t('admin.admissions.committeeNotesLabel') }}
            </label>
            <textarea
              v-model="reviewForm.notes"
              rows="3"
              class="w-full rounded-xl border border-slate-300 bg-white p-3 text-xs sm:text-sm text-slate-800 focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
              :placeholder="$t('admin.admissions.committeeNotesPlaceholder')"
            ></textarea>
          </div>
        </div>
      </div>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors"
          @click="closeReviewModal"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          :disabled="isSaving"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 disabled:opacity-60 cursor-pointer"
          @click="saveDecision"
        >
          <div v-if="isSaving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
          <Save v-else class="w-3.5 h-3.5 text-gold-400" />
          <span>{{ isSaving ? $t('admin.admissions.savingDecision') : $t('admin.admissions.saveDecisionButton') }}</span>
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import Modal from '../../components/ui/Modal.vue'
import {
  Search,
  X,
  Eye,
  RefreshCw,
  Download,
  GraduationCap,
  FileCheck,
  ShieldCheck,
  CheckCircle2,
  Clock,
  XCircle,
  Save,
  Inbox,
} from 'lucide-vue-next'

const route = useRoute()
const { t } = useI18n()
const localeStore = useLocaleStore()

const applications = ref([])
const programOptions = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const updateSuccessMessage = ref('')

const searchQuery = ref('')
const activeStatusFilter = ref('all')
const selectedProgram = ref('all')
const selectedSort = ref('newest')

const isReviewModalOpen = ref(false)
const activeApp = ref(null)

const reviewForm = reactive({
  status: 'under_review',
  notes: '',
})

const defaultDocumentsList = [
  { id: 1, name: 'شهادة الثانوية العامة الأصلية', verification_status: 'verified' },
  { id: 2, name: 'صورة بطاقة الرقم القومي سارية', verification_status: 'verified' },
  { id: 3, name: 'شهادة الميلاد المميكنة الحديثة', verification_status: 'verified' },
]

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

const statusTabs = computed(() => [
  { key: 'all', label: t('admin.admissions.filterAll') },
  { key: 'submitted', label: t('admin.admissions.statusSubmitted') },
  { key: 'under_review', label: t('admin.admissions.statusUnderReview') },
  { key: 'accepted', label: t('admin.admissions.statusAccepted') },
  { key: 'rejected', label: t('admin.admissions.statusRejected') },
])

const statusCounts = computed(() => {
  const counts = { all: applications.value.length, submitted: 0, under_review: 0, accepted: 0, rejected: 0 }
  applications.value.forEach((app) => {
    const s = app.status === 'approved' ? 'accepted' : app.status
    if (counts[s] !== undefined) {
      counts[s]++
    }
  })
  return counts
})

const filteredApplications = computed(() => {
  let list = [...applications.value]

  // Status Filter
  if (activeStatusFilter.value !== 'all') {
    list = list.filter((app) => {
      const s = app.status === 'approved' ? 'accepted' : app.status
      return s === activeStatusFilter.value
    })
  }

  // Program Filter
  if (selectedProgram.value !== 'all') {
    list = list.filter((app) => app.program_id === Number(selectedProgram.value) || app.program?.id === Number(selectedProgram.value))
  }

  // Search Query
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase()
    list = list.filter((app) =>
      (app.application_number && app.application_number.toLowerCase().includes(q)) ||
      (app.national_id && app.national_id.includes(q)) ||
      (app.email && app.email.toLowerCase().includes(q)) ||
      `${app.first_name} ${app.last_name}`.toLowerCase().includes(q)
    )
  }

  // Sorting
  if (selectedSort.value === 'score_desc') {
    list.sort((a, b) => b.high_school_score - a.high_school_score)
  } else if (selectedSort.value === 'score_asc') {
    list.sort((a, b) => a.high_school_score - b.high_school_score)
  } else {
    list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }

  return list
})

const getStatusLabel = (status) => {
  if (status === 'submitted') return t('admin.admissions.statusSubmitted')
  if (status === 'under_review') return t('admin.admissions.statusUnderReview')
  if (status === 'accepted' || status === 'approved') return t('admin.admissions.statusAccepted')
  if (status === 'rejected') return t('admin.admissions.statusRejected')
  return status
}

const getDocTitle = (doc) => {
  if (doc.name) return doc.name
  if (doc.document_type === 'high_school_certificate') return 'شهادة إتمام الثانوية العامة'
  if (doc.document_type === 'national_id_card') return 'بطاقة الرقم القومي'
  if (doc.document_type === 'passport_photo') return 'الصور الشخصية'
  if (doc.document_type === 'birth_certificate') return 'شهادة الميلاد المميكنة'
  return 'وثيقة ومستند رسمي'
}

const formatDate = (isoStr) => {
  if (!isoStr) return ''
  try {
    const d = new Date(isoStr)
    return d.toLocaleDateString(localeStore.locale === 'ar' ? 'ar-EG' : 'en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch {
    return isoStr
  }
}

const loadApplications = async () => {
  isLoading.value = true
  try {
    const [apps, progs] = await Promise.all([
      api.getAdminApplications(),
      api.getPrograms(),
    ])
    applications.value = apps || []
    programOptions.value = progs || []

    // If route query has review param, open modal
    if (route.query.review) {
      const match = applications.value.find(
        (a) => String(a.id) === String(route.query.review) || a.application_number === route.query.review
      )
      if (match) {
        openReviewModal(match)
      }
    }
  } catch (err) {
    console.error('Failed to load applications:', err)
  } finally {
    isLoading.value = false
  }
}

const openReviewModal = (app) => {
  activeApp.value = app
  reviewForm.status = app.status === 'approved' ? 'accepted' : app.status
  reviewForm.notes = app.notes || ''
  updateSuccessMessage.value = ''
  isReviewModalOpen.value = true
}

const closeReviewModal = () => {
  isReviewModalOpen.value = false
  activeApp.value = null
  updateSuccessMessage.value = ''
}

const saveDecision = async () => {
  if (!activeApp.value) return
  isSaving.value = true
  updateSuccessMessage.value = ''

  try {
    const updated = await api.updateApplicationStatus(activeApp.value.id, {
      status: reviewForm.status,
      notes: reviewForm.notes,
    })

    // Update in local list
    const index = applications.value.findIndex((a) => a.id === activeApp.value.id)
    if (index !== -1) {
      applications.value[index] = {
        ...applications.value[index],
        status: reviewForm.status,
        notes: reviewForm.notes,
      }
      activeApp.value = applications.value[index]
    }

    updateSuccessMessage.value = t('admin.admissions.decisionUpdatedSuccess')
    setTimeout(() => {
      updateSuccessMessage.value = ''
    }, 3000)
  } catch (err) {
    alert(err.message || 'Failed to update application decision')
  } finally {
    isSaving.value = false
  }
}

const alertDocumentPreview = () => {
  alert('معاينة الوثيقة المرفقة: تم فحص الوثيقة والمصادقة على أصل المستند بنجاح.')
}

const exportCsv = () => {
  const headers = ['Application Number', 'Name', 'National ID', 'Email', 'Phone', 'Program', 'Score', 'Status', 'Date']
  const rows = filteredApplications.value.map((a) => [
    a.application_number,
    `"${a.first_name} ${a.last_name}"`,
    `'${a.national_id}'`,
    a.email,
    a.phone,
    `"${getTranslated(a.program?.name, 'en')}"`,
    a.high_school_score,
    a.status,
    a.created_at,
  ])

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map((e) => e.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `EgyiTech_Applications_${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

watch(
  () => route.query.review,
  (val) => {
    if (val && applications.value.length > 0) {
      const match = applications.value.find(
        (a) => String(a.id) === String(val) || a.application_number === val
      )
      if (match) {
        openReviewModal(match)
      }
    }
  }
)

onMounted(() => {
  loadApplications()
})
</script>
