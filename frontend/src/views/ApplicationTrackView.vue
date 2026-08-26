<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('tracking.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
      <Badge variant="primary" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('tracking.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('tracking.subtitle') }}
      </p>
    </div>

    <!-- Inquiry Card Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-academic border border-slate-200/80 space-y-6 no-print">
      <form @submit.prevent="handleTrack" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
        <div class="sm:col-span-7">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('tracking.searchCode') }} *</label>
          <input
            v-model="searchCode"
            type="text"
            required
            placeholder="APP-2025-XXXXX"
            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-navy-800 outline-none uppercase"
          />
        </div>

        <div class="sm:col-span-5">
          <Button
            type="submit"
            variant="primary"
            size="md"
            rounded="xl"
            block
            :loading="loading"
          >
            {{ $t('tracking.checkBtn') }}
          </Button>
        </div>
      </form>

      <!-- Quick sample codes helper -->
      <div class="flex items-center gap-2 text-xs text-slate-500 pt-1">
        <span>{{ localeStore.isRtl ? 'رموز تجريبية للاختبار:' : 'Sample test codes:' }}</span>
        <button
          type="button"
          class="font-mono text-navy-900 font-bold underline cursor-pointer"
          @click="fillCode('APP-2025-A1B2C')"
        >
          APP-2025-A1B2C
        </button>
        <span>•</span>
        <button
          type="button"
          class="font-mono text-navy-900 font-bold underline cursor-pointer"
          @click="fillCode('APP-2025-X7K9P')"
        >
          APP-2025-X7K9P
        </button>
      </div>

      <div v-if="errorMessage" class="p-4 bg-red-50 text-red-700 text-xs rounded-xl font-bold border border-red-200">
        ⚠️ {{ errorMessage }}
      </div>
    </div>

    <!-- Application Result Display -->
    <div v-if="appData" class="bg-white rounded-3xl p-6 sm:p-10 shadow-academic border border-slate-200/80 space-y-8 printable-area">
      <!-- Status & Progress Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
        <div>
          <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">
            {{ $t('admissions.appNumber') }}
          </span>
          <div class="text-2xl font-black font-mono text-navy-950">
            {{ appData.application_number }}
          </div>
        </div>

        <!-- Status Badge -->
        <div>
          <Badge :variant="getStatusVariant(appData.status)" size="lg" rounded="full">
            {{ $t(`tracking.status_${appData.status}`) || appData.status }}
          </Badge>
        </div>
      </div>

      <!-- Applicant & Program Details Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 text-xs sm:text-sm">
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.applicant') }}:</span>
          <strong class="text-navy-950 text-base">{{ appData.first_name }} {{ appData.last_name }}</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('admissions.nationalId') }}:</span>
          <strong class="text-navy-950 font-mono">{{ appData.national_id }}</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.program') }}:</span>
          <strong class="text-navy-950 font-bold">
            {{ getTranslated(appData.program?.name, localeStore.locale) }}
          </strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.score') }}:</span>
          <strong class="text-emerald-600 font-bold text-base">{{ appData.high_school_score }}%</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('admissions.email') }}:</span>
          <span class="text-slate-700">{{ appData.email }}</span>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.appliedOn') }}:</span>
          <span class="text-slate-700">{{ formatDate(appData.created_at) }}</span>
        </div>
      </div>

      <!-- Attached Documents Verification Checklist -->
      <div class="space-y-3">
        <h3 class="font-bold text-sm text-navy-950 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-navy-900"></span>
          {{ $t('tracking.docsStatus') }}
        </h3>

        <div class="space-y-2">
          <div
            v-for="(doc, idx) in appData.documents || defaultDocuments"
            :key="idx"
            class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs"
          >
            <span class="font-semibold text-slate-700">
              📄 {{ formatDocType(doc.document_type) }}
            </span>
            <Badge :variant="doc.verification_status === 'verified' ? 'emerald' : 'warning'" size="xs" rounded="md">
              {{ doc.verification_status === 'verified' ? $t('tracking.docVerified') : $t('tracking.docPending') }}
            </Badge>
          </div>
        </div>
      </div>

      <!-- ADMISSIONS PROGRESSION STEPPER -->
      <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200/80 space-y-4">
        <h3 class="font-bold text-xs sm:text-sm text-navy-950 uppercase tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-gold-500"></span>
          {{ localeStore.isRtl ? 'مراحل خط سير مراجعة طلب الالتحاق' : 'Admission Review Progress Pipeline' }}
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
          <!-- Step 1: Initial Screening -->
          <div class="p-3 rounded-xl border bg-white flex flex-col items-center gap-1.5 shadow-xs" :class="appData.stage ? 'border-emerald-300 text-emerald-900' : 'border-slate-200 text-slate-400'">
            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">✓</div>
            <span class="text-xs font-bold">{{ localeStore.isRtl ? 'الفحص الأولي' : 'Screening' }}</span>
          </div>

          <!-- Step 2: Placement / Aptitude Test -->
          <div
            class="p-3 rounded-xl border bg-white flex flex-col items-center gap-1.5 shadow-xs"
            :class="['placement_test', 'interview', 'final_decision', 'completed'].includes(appData.stage) ? 'border-emerald-300 text-emerald-900' : 'border-slate-200 text-slate-400'"
          >
            <div
              class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center"
              :class="['placement_test', 'interview', 'final_decision', 'completed'].includes(appData.stage) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500'"
            >
              <span v-if="['interview', 'final_decision', 'completed'].includes(appData.stage)">✓</span>
              <span v-else>2</span>
            </div>
            <span class="text-xs font-bold">{{ localeStore.isRtl ? 'اختبار القدرات' : 'Placement Test' }}</span>
          </div>

          <!-- Step 3: Interview -->
          <div
            class="p-3 rounded-xl border bg-white flex flex-col items-center gap-1.5 shadow-xs"
            :class="['interview', 'final_decision', 'completed'].includes(appData.stage) ? 'border-emerald-300 text-emerald-900' : 'border-slate-200 text-slate-400'"
          >
            <div
              class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center"
              :class="['interview', 'final_decision', 'completed'].includes(appData.stage) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500'"
            >
              <span v-if="['final_decision', 'completed'].includes(appData.stage)">✓</span>
              <span v-else>3</span>
            </div>
            <span class="text-xs font-bold">{{ localeStore.isRtl ? 'المقابلة الشخصية' : 'Interview' }}</span>
          </div>

          <!-- Step 4: Final Offer -->
          <div
            class="p-3 rounded-xl border bg-white flex flex-col items-center gap-1.5 shadow-xs"
            :class="['final_decision', 'completed'].includes(appData.stage) ? 'border-emerald-300 text-emerald-900' : 'border-slate-200 text-slate-400'"
          >
            <div
              class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center"
              :class="['final_decision', 'completed'].includes(appData.stage) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500'"
            >
              <span v-if="appData.status === 'accepted'">🎓</span>
              <span v-else>4</span>
            </div>
            <span class="text-xs font-bold">{{ localeStore.isRtl ? 'القرار النهائي' : 'Final Offer' }}</span>
          </div>
        </div>
      </div>

      <!-- CRM: Scholarship Award Banner (If awarded) -->
      <div v-if="appData.scholarship_name" class="p-5 bg-gradient-to-r from-gold-600 to-gold-500 text-navy-950 rounded-2xl shadow-md border border-gold-400 flex items-center justify-between gap-4">
        <div>
          <div class="text-xs font-black uppercase tracking-wider text-navy-950/80">
            🎉 {{ localeStore.isRtl ? 'تم منحك منحة دراسية رسمية' : 'Official Scholarship Granted' }}
          </div>
          <div class="text-lg font-black text-navy-950">
            {{ appData.scholarship_name }} ({{ appData.scholarship_discount_percent }}% {{ localeStore.isRtl ? 'خصم من المصروفات' : 'Tuition Waiver' }})
          </div>
          <p class="text-xs font-medium text-navy-900 mt-0.5">
            {{ localeStore.isRtl ? 'تهانينا! يرجى مراجعة إدارة القبول لاستكمال مستندات القيد النهائي وتثبيت المنحة.' : 'Congratulations! Please finalize enrollment with Admissions to confirm your merit grant.' }}
          </p>
        </div>
        <div class="text-3xl">🏅</div>
      </div>

      <!-- CRM: Waitlist Position Card (If on waitlist) -->
      <div v-if="appData.waitlist_position" class="p-4 bg-amber-50 border border-amber-300 rounded-2xl flex items-center justify-between text-amber-950 text-xs sm:text-sm">
        <div>
          <strong class="font-black block">⏳ {{ localeStore.isRtl ? 'حالة قائمة الانتظار' : 'Waitlist Status' }}</strong>
          <span>{{ localeStore.isRtl ? `أنت في الترتيب رقم (${appData.waitlist_position}) على قائمة انتظار البرنامج.` : `You are currently position #${appData.waitlist_position} on the program waitlist.` }}</span>
        </div>
      </div>

      <!-- Scheduled Interview Notice Card (If scheduled) -->
      <div v-if="appData.interview_scheduled_at" class="p-5 bg-gradient-to-r from-navy-950 to-navy-900 text-white rounded-2xl shadow-md border border-navy-800 flex items-center justify-between gap-4">
        <div>
          <div class="text-xs text-gold-400 font-bold uppercase tracking-wider mb-1">
            {{ localeStore.isRtl ? 'موعد المقابلة الشخصية للقبول' : 'Scheduled Admissions Interview' }}
          </div>
          <div class="text-lg font-black text-white">
            {{ new Date(appData.interview_scheduled_at).toLocaleString(localeStore.locale === 'ar' ? 'ar-EG' : 'en-US', { dateStyle: 'full', timeStyle: 'short' }) }}
          </div>
          <p class="text-xs text-slate-300 mt-1">
            {{ localeStore.isRtl ? 'يرجى الحضور إلى مقر الكلية مصطحباً بطاقة الرقم القومي وأصل المستندات.' : 'Please report to the faculty deanery with original identification and credentials.' }}
          </p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-gold-500 text-navy-950 flex items-center justify-center font-bold text-xl shrink-0 shadow-gold-glow">
          🗓️
        </div>
      </div>

      <!-- CRM: Original Document Verification Checklist -->
      <div v-if="appData.verification_checklist && appData.verification_checklist.length > 0" class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
        <h4 class="text-xs font-black text-navy-950 uppercase tracking-wider">
          📑 {{ localeStore.isRtl ? 'حالة فحص أصول المستندات المطلوبة للقيد:' : 'Required Document Verification Checklist:' }}
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
          <div
            v-for="(item, idx) in appData.verification_checklist"
            :key="idx"
            class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between"
          >
            <span class="text-slate-800 font-medium">{{ item.label }}</span>
            <Badge :variant="item.verified ? 'emerald' : 'amber'" size="xs" rounded="md">
              {{ item.verified ? (localeStore.isRtl ? 'معتمد ومستوفى' : 'Verified') : (localeStore.isRtl ? 'قيد الاستيفاء' : 'Pending') }}
            </Badge>
          </div>
        </div>
      </div>

      <!-- Committee Reviewer Notes -->
      <div v-if="appData.notes" class="p-4 bg-primary-50 border border-primary-200 rounded-xl space-y-1 text-xs">
        <strong class="text-primary-900 block font-bold">📝 {{ $t('tracking.adminNotes') }}:</strong>
        <p class="text-primary-800 leading-relaxed">{{ appData.notes }}</p>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 no-print">
        <Button variant="outline" size="md" rounded="xl" @click="printSlip">
          <template #icon>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
          </template>
          {{ $t('tracking.printSlip') }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import { formatStandardDate } from '../utils/dateFormat'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'

const { t } = useI18n()
const route = useRoute()
const localeStore = useLocaleStore()

const searchCode = ref('')
const loading = ref(false)
const errorMessage = ref('')
const appData = ref(null)

const defaultDocuments = [
  { document_type: 'high_school_certificate', verification_status: 'verified' },
  { document_type: 'national_id_card', verification_status: 'pending' },
  { document_type: 'passport_photo', verification_status: 'verified' },
]

const fillCode = (code) => {
  searchCode.value = code
  handleTrack()
}

const getStatusVariant = (status) => {
  if (status === 'approved') return 'emerald'
  if (status === 'under_review' || status === 'conditionally_accepted') return 'warning'
  if (status === 'rejected') return 'danger'
  return 'primary'
}

const formatDocType = (type) => {
  const map = {
    high_school_certificate: t('admissions.uploadCert'),
    national_id_card: t('admissions.uploadId'),
    passport_photo: t('admissions.uploadPhoto'),
    birth_certificate: t('admissions.uploadBirth'),
  }
  return map[type] || type
}

const formatDate = (dateStr) => formatStandardDate(dateStr, localeStore.locale)

const handleTrack = async () => {
  if (!searchCode.value) return
  loading.value = true
  errorMessage.value = ''
  appData.value = null

  try {
    const result = await api.trackApplication({
      application_number: searchCode.value.trim().toUpperCase(),
    })
    appData.value = result
  } catch (e) {
    errorMessage.value = t('tracking.notFound')
  } finally {
    loading.value = false
  }
}

const printSlip = () => {
  window.print()
}

onMounted(() => {
  if (route.query.code) {
    searchCode.value = String(route.query.code)
    handleTrack()
  }
})
</script>
