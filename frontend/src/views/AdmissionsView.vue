<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('admissions.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
      <Badge variant="gold" size="md" rounded="full">
        {{ $t('admissions.cycle') }}: 2025 / 2026
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('admissions.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('admissions.subtitle') }}
      </p>
    </div>

    <!-- SUCCESS RECEIPT SCREEN -->
    <div v-if="submittedApp" class="bg-white rounded-3xl p-8 sm:p-12 shadow-academic-lg border border-slate-200/80 space-y-8 text-center printable-area">
      <!-- Success Icon -->
      <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-inner animate-bounce">
        ✓
      </div>

      <div class="space-y-2">
        <h2 class="text-2xl sm:text-3xl font-black text-navy-950">
          {{ $t('admissions.successTitle') }}
        </h2>
        <p class="text-sm text-slate-600 max-w-lg mx-auto">
          {{ $t('admissions.successSubtitle') }}
        </p>
      </div>

      <!-- Application Tracking Code Box -->
      <div class="p-6 bg-slate-50 border-2 border-dashed border-gold-400 rounded-2xl max-w-md mx-auto space-y-2">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">
          {{ $t('admissions.appNumber') }}
        </div>
        <div class="text-2xl sm:text-3xl font-mono font-black text-navy-950 tracking-wider">
          {{ submittedApp.application_number }}
        </div>
        <div class="text-xs text-slate-400">
          {{ localeStore.isRtl ? 'احتفظ بهذا الرمز لمتابعة موقف طلبك وطباعة إشعار القبول' : 'Save this code to track your review status and print official slip' }}
        </div>
      </div>

      <!-- Application Summary Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-start text-xs sm:text-sm bg-slate-50 p-6 rounded-2xl border border-slate-200 max-w-2xl mx-auto">
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.applicant') }}:</span>
          <strong class="text-navy-950">{{ submittedApp.first_name }} {{ submittedApp.last_name }}</strong>
        </div>
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('admissions.nationalId') }}:</span>
          <strong class="text-navy-950 font-mono">{{ submittedApp.national_id }}</strong>
        </div>
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('tracking.program') }}:</span>
          <strong class="text-navy-950">{{ getTranslated(submittedApp.program?.name, localeStore.locale) }}</strong>
        </div>
        <div>
          <span class="text-slate-500 font-medium block">{{ $t('admissions.highSchoolScore') }}:</span>
          <strong class="text-emerald-600 font-bold">{{ submittedApp.high_school_score }}%</strong>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center justify-center gap-4 pt-4 no-print">
        <Button
          variant="primary"
          size="md"
          rounded="xl"
          @click="printReceipt"
        >
          <template #icon>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
          </template>
          {{ $t('admissions.saveReceipt') }}
        </Button>

        <Button
          :to="`/admissions/track?code=${submittedApp.application_number}`"
          variant="gold"
          size="md"
          rounded="xl"
        >
          {{ $t('admissions.trackNow') }}
        </Button>

        <Button
          variant="ghost"
          size="md"
          @click="resetForm"
        >
          {{ $t('admissions.applyAnother') }}
        </Button>
      </div>
    </div>

    <!-- MULTI-STEP WIZARD -->
    <div v-else class="bg-white rounded-3xl shadow-academic border border-slate-200/80 overflow-hidden">
      <!-- Stepper Progress Header -->
      <div class="bg-slate-50/80 border-b border-slate-200 p-6">
        <div class="grid grid-cols-4 gap-2 text-center">
          <div
            v-for="(step, idx) in steps"
            :key="idx"
            :class="[
              'flex flex-col items-center gap-2 cursor-pointer transition-all',
              currentStep === idx + 1
                ? 'text-navy-950 font-bold'
                : currentStep > idx + 1
                ? 'text-emerald-700 font-semibold'
                : 'text-slate-400',
            ]"
            @click="goToStep(idx + 1)"
          >
            <!-- Step Circle Badge -->
            <div
              :class="[
                'w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs',
                currentStep === idx + 1
                  ? 'bg-navy-900 text-white ring-4 ring-navy-100'
                  : currentStep > idx + 1
                  ? 'bg-emerald-600 text-white'
                  : 'bg-slate-200 text-slate-500',
              ]"
            >
              <span v-if="currentStep > idx + 1">✓</span>
              <span v-else>{{ idx + 1 }}</span>
            </div>
            <span class="text-[11px] sm:text-xs hidden sm:inline">{{ step.label }}</span>
          </div>
        </div>
      </div>

      <!-- Step Forms -->
      <form @submit.prevent="handleNext" class="p-6 sm:p-10 space-y-8">
        <!-- Error Alert -->
        <div v-if="formError" class="p-4 bg-red-50 text-red-700 text-xs rounded-xl font-bold border border-red-200">
          ⚠️ {{ formError }}
        </div>

        <!-- STEP 1: Personal Information -->
        <div v-if="currentStep === 1" class="space-y-6">
          <h3 class="text-lg font-bold text-navy-950 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-navy-900"></span>
            {{ $t('admissions.step1') }}
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.firstName') }} *</label>
              <input
                v-model="form.first_name"
                type="text"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.lastName') }} *</label>
              <input
                v-model="form.last_name"
                type="text"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.nationalId') }} *</label>
              <input
                v-model="form.national_id"
                type="text"
                required
                maxlength="14"
                placeholder="14-digit National ID / Passport"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.email') }} *</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.phone') }} *</label>
              <input
                v-model="form.phone"
                type="tel"
                required
                placeholder="+2010..."
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.gender') }} *</label>
              <select
                v-model="form.gender"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              >
                <option value="male">{{ $t('admissions.male') }}</option>
                <option value="female">{{ $t('admissions.female') }}</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.address') }} *</label>
              <input
                v-model="form.address"
                type="text"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>
          </div>
        </div>

        <!-- STEP 2: Academic Background & High School Score -->
        <div v-if="currentStep === 2" class="space-y-6">
          <h3 class="text-lg font-bold text-navy-950 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-gold-500"></span>
            {{ $t('admissions.step2') }}
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.highSchoolType') }} *</label>
              <select
                v-model="form.high_school_type"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              >
                <option value="thanawya">{{ $t('admissions.thanawya') }}</option>
                <option value="stem">{{ $t('admissions.stem') }}</option>
                <option value="igcse">{{ $t('admissions.igcse') }}</option>
                <option value="american">{{ $t('admissions.american') }}</option>
                <option value="azhar">{{ $t('admissions.azhar') }}</option>
                <option value="equivalent">{{ $t('admissions.equivalent') }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.gradYear') }} *</label>
              <select
                v-model="form.grad_year"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              >
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                {{ $t('admissions.highSchoolScore') }} *
              </label>
              <input
                v-model.number="form.high_school_score"
                type="number"
                step="0.1"
                min="50"
                max="100"
                required
                :placeholder="$t('admissions.scorePlaceholder')"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.uploadCert') }}</label>
              <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 text-center hover:bg-slate-100 transition-colors cursor-pointer">
                <input type="file" class="hidden" id="certFile" @change="onFileSelected($event, 'cert')" />
                <label for="certFile" class="cursor-pointer block text-xs text-slate-600">
                  <span v-if="files.cert" class="text-emerald-600 font-bold">✓ {{ files.cert.name }}</span>
                  <span v-else>{{ $t('admissions.fileDrop') }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- STEP 3: Program Selection & Document Uploads -->
        <div v-if="currentStep === 3" class="space-y-6">
          <h3 class="text-lg font-bold text-navy-950 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            {{ $t('admissions.step3') }}
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.selectProgram') }} *</label>
              <select
                v-model="form.program_id"
                required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              >
                <option value="">-- {{ $t('admissions.selectProgram') }} --</option>
                <option v-for="p in programs" :key="p.id" :value="p.id">
                  {{ getTranslated(p.name, localeStore.locale) }} ({{ getTranslated(p.college_name, localeStore.locale) }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.uploadId') }}</label>
              <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 text-center cursor-pointer hover:bg-slate-100">
                <input type="file" class="hidden" id="idFile" @change="onFileSelected($event, 'id')" />
                <label for="idFile" class="cursor-pointer text-xs text-slate-600 block truncate">
                  <span v-if="files.id" class="text-emerald-600 font-bold">✓ {{ files.id.name }}</span>
                  <span v-else>📁 {{ $t('admissions.uploadId') }}</span>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.uploadPhoto') }}</label>
              <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 text-center cursor-pointer hover:bg-slate-100">
                <input type="file" class="hidden" id="photoFile" @change="onFileSelected($event, 'photo')" />
                <label for="photoFile" class="cursor-pointer text-xs text-slate-600 block truncate">
                  <span v-if="files.photo" class="text-emerald-600 font-bold">✓ {{ files.photo.name }}</span>
                  <span v-else>📷 {{ $t('admissions.uploadPhoto') }}</span>
                </label>
              </div>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ $t('admissions.additionalNotes') }}</label>
              <textarea
                v-model="form.notes"
                rows="3"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- STEP 4: Review & Final Submit -->
        <div v-if="currentStep === 4" class="space-y-6">
          <h3 class="text-lg font-bold text-navy-950 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-navy-900"></span>
            {{ $t('admissions.step4') }}
          </h3>

          <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 leading-relaxed">
            {{ $t('admissions.reviewNotice') }}
          </div>

          <!-- Review Summary Table -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm bg-slate-50 p-6 rounded-2xl border border-slate-200">
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.firstName') }} & {{ $t('admissions.lastName') }}:</span>
              <strong class="text-navy-950">{{ form.first_name }} {{ form.last_name }}</strong>
            </div>
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.nationalId') }}:</span>
              <strong class="text-navy-950 font-mono">{{ form.national_id }}</strong>
            </div>
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.email') }}:</span>
              <strong class="text-navy-950">{{ form.email }}</strong>
            </div>
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.phone') }}:</span>
              <strong class="text-navy-950">{{ form.phone }}</strong>
            </div>
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.highSchoolScore') }}:</span>
              <strong class="text-emerald-600 font-bold">{{ form.high_school_score }}%</strong>
            </div>
            <div>
              <span class="text-slate-500 font-medium block">{{ $t('admissions.selectProgram') }}:</span>
              <strong class="text-navy-950">{{ selectedProgramName }}</strong>
            </div>
          </div>

          <!-- Terms Checkbox -->
          <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <input
              id="agreeTerms"
              v-model="form.agree_terms"
              type="checkbox"
              required
              class="mt-1 w-4 h-4 text-navy-900 rounded focus:ring-navy-800"
            />
            <label for="agreeTerms" class="text-xs text-slate-700 font-medium leading-relaxed cursor-pointer">
              {{ $t('admissions.agreeTerms') }}
            </label>
          </div>
        </div>

        <!-- Stepper Navigation Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-100">
          <Button
            v-if="currentStep > 1"
            type="button"
            variant="outline"
            size="md"
            rounded="xl"
            @click="currentStep--"
          >
            ← {{ $t('admissions.prev') }}
          </Button>
          <div v-else></div>

          <Button
            v-if="currentStep < 4"
            type="button"
            variant="primary"
            size="md"
            rounded="xl"
            @click="handleNext"
          >
            {{ $t('admissions.next') }} →
          </Button>

          <Button
            v-else
            type="submit"
            variant="gold"
            size="lg"
            rounded="xl"
            :loading="submitting"
          >
            {{ $t('admissions.submit') }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import { useToast } from '../composables/useToast'

const { t } = useI18n()
const route = useRoute()
const localeStore = useLocaleStore()
const toast = useToast()

const currentStep = ref(1)
const submitting = ref(false)
const formError = ref('')
const programs = ref([])
const submittedApp = ref(null)

const files = reactive({
  cert: null,
  id: null,
  photo: null,
})

const form = reactive({
  first_name: '',
  last_name: '',
  national_id: '',
  email: '',
  phone: '',
  gender: 'male',
  address: '',
  high_school_type: 'thanawya',
  grad_year: '2025',
  high_school_score: null,
  program_id: '',
  notes: '',
  agree_terms: false,
})

const steps = computed(() => [
  { label: t('admissions.step1') },
  { label: t('admissions.step2') },
  { label: t('admissions.step3') },
  { label: t('admissions.step4') },
])

const selectedProgramName = computed(() => {
  const p = programs.value.find((pr) => pr.id === Number(form.program_id))
  return p ? getTranslated(p.name, localeStore.locale) : '-'
})

const onFileSelected = (e, key) => {
  const file = e.target.files[0]
  if (file) {
    files[key] = file
  }
}

const goToStep = (step) => {
  if (step < currentStep.value) {
    currentStep.value = step
  }
}

const handleNext = async () => {
  formError.value = ''

  if (currentStep.value === 1) {
    if (!form.first_name || !form.last_name || !form.national_id || !form.email || !form.phone) {
      formError.value = t('common.required')
      return
    }
    if (form.national_id.length < 8) {
      formError.value = 'National ID must be at least 8 digits'
      return
    }
    currentStep.value = 2
  } else if (currentStep.value === 2) {
    if (!form.high_school_score || form.high_school_score < 50 || form.high_school_score > 100) {
      formError.value = 'Please enter a valid high school percentage (50% - 100%)'
      return
    }
    currentStep.value = 3
  } else if (currentStep.value === 3) {
    if (!form.program_id) {
      formError.value = 'Please select a degree program'
      return
    }
    currentStep.value = 4
  } else if (currentStep.value === 4) {
    if (!form.agree_terms) {
      formError.value = 'Please accept the terms and bylaws'
      return
    }

    // Submit Application
    submitting.value = true
    try {
      const payload = {
        first_name: form.first_name,
        last_name: form.last_name,
        national_id: form.national_id,
        email: form.email,
        phone: form.phone,
        high_school_score: form.high_school_score,
        program_id: form.program_id,
        notes: form.notes,
      }
      const result = await api.submitApplication(payload)
      submittedApp.value = result
      toast.success(
        localeStore.isRtl ? `تم تقديم طلب الالتحاق بنجاح برقم قيد: ${result.application_number || ''}` : `Application submitted successfully. Ref: ${result.application_number || ''}`,
        localeStore.isRtl ? 'تم التقديم بنجاح' : 'Application Submitted'
      )
    } catch (e) {
      formError.value = e.message || 'Failed to submit application'
      toast.error(
        e.message || (localeStore.isRtl ? 'تعذر تقديم الطلب، يرجى مراجعة البيانات.' : 'Failed to submit application.'),
        localeStore.isRtl ? 'خطأ في التقديم' : 'Submission Error'
      )
    } finally {
      submitting.value = false
    }
  }
}

const printReceipt = () => {
  window.print()
}

const resetForm = () => {
  submittedApp.value = null
  currentStep.value = 1
  form.first_name = ''
  form.last_name = ''
  form.national_id = ''
  form.email = ''
  form.phone = ''
  form.high_school_score = null
  form.agree_terms = false
}

onMounted(async () => {
  try {
    const cycleData = await api.getActiveCycle()
    programs.value = cycleData.programs || []
    if (route.query.program_id) {
      form.program_id = Number(route.query.program_id)
    }
  } catch (e) {
    console.error('Failed to load admission cycle:', e)
  }
})
</script>
