<template>
  <div class="max-w-5xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('studentPortal.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-2xl mx-auto space-y-3 no-print">
      <Badge variant="gold" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('studentPortal.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('studentPortal.subtitle') }}
      </p>
    </div>

    <!-- Inquiry Card Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-academic border border-slate-200/80 space-y-6 no-print">
      <form @submit.prevent="handleInquire" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
        <div class="sm:col-span-7">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            {{ $t('studentPortal.studentId') }} *
          </label>
          <input
            v-model="studentId"
            type="text"
            required
            :placeholder="$t('studentPortal.idPlaceholder')"
            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-navy-800 outline-none"
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
            {{ $t('studentPortal.inquireBtn') }}
          </Button>
        </div>
      </form>

      <!-- Quick sample IDs helper -->
      <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 pt-1">
        <span>{{ localeStore.isRtl ? 'أرقام أكاديمية تجريبية:' : 'Sample student IDs:' }}</span>
        <button
          type="button"
          class="font-mono text-navy-900 font-bold underline cursor-pointer"
          @click="fillId('20241001')"
        >
          20241001 (AI)
        </button>
        <span>•</span>
        <button
          type="button"
          class="font-mono text-navy-900 font-bold underline cursor-pointer"
          @click="fillId('20241002')"
        >
          20241002 (Cyber)
        </button>
        <span>•</span>
        <button
          type="button"
          class="font-mono text-navy-900 font-bold underline cursor-pointer"
          @click="fillId('20242001')"
        >
          20242001 (Mechatronics)
        </button>
      </div>

      <div v-if="errorMessage" class="p-4 bg-red-50 text-red-700 text-xs rounded-xl font-bold border border-red-200">
        ⚠️ {{ errorMessage }}
      </div>
    </div>

    <!-- Official Transcript View & Print Template -->
    <div v-if="resultData" class="bg-white rounded-3xl p-8 sm:p-12 shadow-academic-lg border border-slate-200/80 space-y-8 printable-area">
      <!-- Official University Header (Visible on Screen & Print) -->
      <div class="border-b-2 border-navy-900 pb-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-start">
        <div class="flex items-center gap-3">
          <div class="w-14 h-14 rounded-2xl bg-navy-950 text-gold-400 flex items-center justify-center border border-gold-500/40">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v7m-3-4l3-3 3 3" />
            </svg>
          </div>
          <div>
            <h2 class="text-lg sm:text-xl font-black text-navy-950 leading-tight">
              {{ $t('app.name') }}
            </h2>
            <p class="text-xs text-slate-500 font-semibold">
              {{ $t('studentPortal.officialTranscript') }} • General Administration of Educational Affairs
            </p>
          </div>
        </div>

        <div class="text-center sm:text-end text-xs text-slate-500">
          <div><strong class="text-slate-700">{{ $t('studentPortal.issuedDate') }}:</strong> {{ currentDate }}</div>
          <div class="font-mono text-[11px] text-slate-400">DOC-REF: TR-{{ resultData.student?.student_id_number }}-2025</div>
        </div>
      </div>

      <!-- Student Academic Profile Information Card -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 text-xs sm:text-sm">
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('studentPortal.studentName') }}:</span>
          <strong class="text-navy-950 text-base">{{ resultData.student?.student_name }}</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('studentPortal.studentId') }}:</span>
          <strong class="text-navy-950 font-mono text-base">{{ resultData.student?.student_id_number }}</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.program') }}:</span>
          <strong class="text-navy-950">{{ resultData.student?.program }}</strong>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('studentPortal.level') }}:</span>
          <span class="text-slate-800 font-bold">{{ $t(`studentPortal.level${resultData.student?.current_level || 1}`) }}</span>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('studentPortal.term') }}:</span>
          <span class="text-slate-800 font-bold">{{ resultData.academic_term }}</span>
        </div>

        <div>
          <span class="text-slate-500 font-medium block">{{ $t('studentPortal.academicStanding') }}:</span>
          <Badge variant="emerald" size="xs" rounded="md">
            {{ $t('studentPortal.standingExcellent') }}
          </Badge>
        </div>
      </div>

      <!-- Key Performance GPA Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-navy-950 text-white p-5 rounded-2xl text-center space-y-1 shadow-sm">
          <div class="text-xs text-gold-400 font-bold uppercase tracking-wider">{{ $t('studentPortal.cumGpa') }}</div>
          <div class="text-3xl font-black">{{ resultData.cumulative_gpa?.toFixed(2) }} <span class="text-xs text-slate-400 font-normal">/ 4.00</span></div>
        </div>

        <div class="bg-navy-900 text-white p-5 rounded-2xl text-center space-y-1 shadow-sm">
          <div class="text-xs text-emerald-400 font-bold uppercase tracking-wider">{{ $t('studentPortal.termGpa') }}</div>
          <div class="text-3xl font-black">{{ resultData.term_gpa ? resultData.term_gpa.toFixed(2) : resultData.cumulative_gpa?.toFixed(2) }} <span class="text-xs text-slate-400 font-normal">/ 4.00</span></div>
        </div>

        <div class="bg-slate-100 text-navy-950 p-5 rounded-2xl text-center space-y-1 border border-slate-200 shadow-sm">
          <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ $t('studentPortal.totalCredits') }}</div>
          <div class="text-3xl font-black text-navy-950">{{ totalCredits }} <span class="text-xs text-slate-500 font-normal">{{ $t('programs.creditHours') }}</span></div>
        </div>
      </div>

      <!-- Course Results Table -->
      <div class="space-y-3">
        <h3 class="text-base font-bold text-navy-950 flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
          {{ $t('studentPortal.resultsTable') }}
        </h3>

        <div class="border border-slate-200 rounded-2xl overflow-x-auto shadow-xs">
          <table class="w-full text-xs sm:text-sm text-start min-w-[540px]">
            <thead class="bg-navy-950 text-white font-bold">
              <tr>
                <th class="py-3.5 px-4 text-start">{{ $t('studentPortal.courseCode') }}</th>
                <th class="py-3.5 px-4 text-start">{{ $t('studentPortal.courseName') }}</th>
                <th class="py-3.5 px-4 text-center">{{ $t('studentPortal.credits') }}</th>
                <th class="py-3.5 px-4 text-center">{{ $t('studentPortal.grade') }}</th>
                <th class="py-3.5 px-4 text-center">{{ $t('studentPortal.gradePoints') }}</th>
                <th class="py-3.5 px-4 text-center">{{ $t('studentPortal.resultStatus') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200/80 bg-white">
              <tr
                v-for="res in resultData.course_results"
                :key="res.id"
                class="hover:bg-slate-50/80 transition-colors"
              >
                <td class="py-3 px-4 font-mono font-bold text-navy-900">{{ res.course_code }}</td>
                <td class="py-3 px-4 font-medium text-slate-800">{{ getTranslated(res.course_name, localeStore.locale) }}</td>
                <td class="py-3 px-4 text-center font-semibold">{{ res.credit_hours }}</td>
                <td class="py-3 px-4 text-center font-bold text-navy-950">
                  <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono">{{ res.grade }}</span>
                </td>
                <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">{{ Number(res.grade_points).toFixed(2) }}</td>
                <td class="py-3 px-4 text-center">
                  <Badge variant="emerald" size="xs" rounded="full">
                    {{ $t('studentPortal.passed') }}
                  </Badge>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grading Scale Legend & Official Stamp Footer -->
      <div class="pt-6 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-500">
        <div>
          <strong class="text-slate-700 block mb-1 font-bold">{{ $t('studentPortal.gradingSystem') }}:</strong>
          <p class="leading-relaxed">A+ (4.00) | A (3.70) | B+ (3.30) | B (3.00) | C+ (2.70) | C (2.40) | D (2.00) | F (&lt;2.00)</p>
        </div>

        <div class="text-center sm:text-end space-y-1">
          <div class="font-bold text-navy-950">Dean of Admissions & Registration</div>
          <div class="text-[11px] text-slate-400 italic">Official Electronic Verification Stamp</div>
        </div>
      </div>

      <!-- Print Button -->
      <div class="flex justify-end gap-3 pt-4 no-print border-t border-slate-100">
        <Button variant="primary" size="md" rounded="xl" @click="printTranscript">
          <template #icon>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
          </template>
          {{ $t('studentPortal.printTranscript') }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'

const { t } = useI18n()
const localeStore = useLocaleStore()

const studentId = ref('')
const loading = ref(false)
const errorMessage = ref('')
const resultData = ref(null)

const currentDate = computed(() => {
  return new Date().toLocaleDateString(localeStore.isRtl ? 'ar-EG' : 'en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
})

const totalCredits = computed(() => {
  if (!resultData.value?.course_results) return 0
  return resultData.value.course_results.reduce((acc, c) => acc + (c.credit_hours || 0), 0)
})

const fillId = (id) => {
  studentId.value = id
  handleInquire()
}

const handleInquire = async () => {
  if (!studentId.value) return
  loading.value = true
  errorMessage.value = ''
  resultData.value = null

  try {
    const res = await api.inquireStudentResults({
      student_id_number: studentId.value.trim(),
    })
    resultData.value = res
  } catch (e) {
    errorMessage.value = t('studentPortal.notFound')
  } finally {
    loading.value = false
  }
}

const printTranscript = () => {
  window.print()
}
</script>
