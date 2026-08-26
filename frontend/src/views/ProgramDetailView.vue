<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs
      :items="[
        { label: $t('programs.title'), to: '/programs' },
        { label: getTranslated(program?.name, localeStore.locale) || $t('common.loading') },
      ]"
    />

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <div v-else-if="program" class="space-y-12">
      <!-- Program Header Hero Banner -->
      <div class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-navy-800 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl space-y-6">
          <div class="flex flex-wrap items-center gap-2">
            <Badge variant="gold" size="sm" rounded="full">
              {{ $t(`programs.${program.degree_level}`) || program.degree_level }}
            </Badge>
            <Badge variant="emerald" size="sm" rounded="full">
              {{ program.credit_hours }} {{ $t('programs.creditHours') }}
            </Badge>
            <Badge variant="slate" size="sm" rounded="full">
              {{ program.duration_years }} {{ $t('programs.durationYears') }}
            </Badge>
          </div>

          <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
            {{ getTranslated(program.name, localeStore.locale) }}
          </h1>

          <p class="text-sm sm:text-base text-slate-300">
            {{ getTranslated(program.college_name, localeStore.locale) }} • {{ getTranslated(program.department_name, localeStore.locale) }}
          </p>

          <div class="pt-2 flex flex-wrap items-center gap-4">
            <Button
              :to="`/admissions?program_id=${program.id}`"
              variant="gold"
              size="lg"
              rounded="xl"
            >
              {{ $t('programs.applyForProgram') }}
              <template #trailingIcon>
                <svg class="w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </template>
            </Button>

            <Button
              to="/documents"
              variant="white"
              size="lg"
              rounded="xl"
            >
              {{ $t('documents.title') }}
            </Button>
          </div>
        </div>
      </div>

      <!-- Quick Metrics Summary Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-academic text-center space-y-1">
          <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $t('programs.filterDegree') }}</div>
          <div class="text-lg font-black text-navy-950">{{ $t(`programs.${program.degree_level}`) || program.degree_level }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-academic text-center space-y-1">
          <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $t('programs.creditHours') }}</div>
          <div class="text-lg font-black text-navy-950">{{ program.credit_hours }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-academic text-center space-y-1">
          <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $t('programs.durationYears') }}</div>
          <div class="text-lg font-black text-navy-950">{{ program.duration_years }} {{ $t('programs.durationYears') }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-academic text-center space-y-1">
          <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $t('admissions.cycle') }}</div>
          <div class="text-lg font-black text-emerald-600">2025 / 2026</div>
        </div>
      </div>

      <!-- Main Detailed Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left 8 cols: Overview, Curriculum, Career Opportunities -->
        <div class="lg:col-span-8 space-y-8">
          <!-- Overview -->
          <Card padding="lg">
            <h2 class="text-xl font-bold text-navy-950 mb-4 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-navy-900"></span>
              {{ $t('programs.overview') }}
            </h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
              {{ getTranslated(program.overview, localeStore.locale) }}
            </p>
          </Card>

          <!-- Curriculum Breakdown -->
          <Card padding="lg">
            <h2 class="text-xl font-bold text-navy-950 mb-4 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
              {{ $t('programs.curriculum') }}
            </h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line mb-6">
              {{ getTranslated(program.curriculum, localeStore.locale) }}
            </p>

            <!-- Sample Course Table Structure -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
              <table class="w-full text-xs sm:text-sm text-start">
                <thead class="bg-slate-100/80 text-navy-950 font-bold border-b border-slate-200">
                  <tr>
                    <th class="py-3 px-4 text-start">{{ $t('programs.courseCode') }}</th>
                    <th class="py-3 px-4 text-start">{{ $t('programs.courseTitle') }}</th>
                    <th class="py-3 px-4 text-center">{{ $t('programs.credits') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-4 font-mono font-bold text-navy-900">CS101</td>
                    <td class="py-2.5 px-4 font-medium">{{ localeStore.isRtl ? 'مقدمة في البرمجة والتفكير الخوارزمي' : 'Introduction to Programming & Algorithms' }}</td>
                    <td class="py-2.5 px-4 text-center font-bold">3</td>
                  </tr>
                  <tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-4 font-mono font-bold text-navy-900">MATH102</td>
                    <td class="py-2.5 px-4 font-medium">{{ localeStore.isRtl ? 'التفاضل والتكامل والجبر الخطي التطبيقي' : 'Applied Calculus & Linear Algebra' }}</td>
                    <td class="py-2.5 px-4 text-center font-bold">3</td>
                  </tr>
                  <tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-4 font-mono font-bold text-navy-900">ENG103</td>
                    <td class="py-2.5 px-4 font-medium">{{ localeStore.isRtl ? 'اللغة الإنجليزية الأكاديمية والتواصل الفني' : 'Technical English & Academic Communication' }}</td>
                    <td class="py-2.5 px-4 text-center font-bold">2</td>
                  </tr>
                  <tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-4 font-mono font-bold text-navy-900">AI201</td>
                    <td class="py-2.5 px-4 font-medium">{{ localeStore.isRtl ? 'هياكل البيانات والذكاء الاصطناعي الأساسي' : 'Data Structures & Core AI Methods' }}</td>
                    <td class="py-2.5 px-4 text-center font-bold">3</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </Card>

          <!-- Official Study Plan Blueprint / Curriculum Document Download (Hybrid Workflow Asset) -->
          <div v-if="program.study_plan_document_path" class="p-5 rounded-2xl bg-gold-50/70 border border-gold-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-start">
              <div class="w-12 h-12 rounded-xl bg-navy-950 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                PDF
              </div>
              <div>
                <h3 class="font-bold text-navy-950 text-sm">
                  {{ localeStore.isRtl ? 'لائحة الخطة الدراسية وتوزيع المقررات المعتمدة' : 'Official Accredited Curriculum Blueprint & Matrix' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                  {{ program.study_plan_file_name || (localeStore.isRtl ? 'مصفوفة الساعات وتوصيف المقررات الرسمية' : 'Full Degree Specification Document') }}
                </p>
              </div>
            </div>
            <a
              :href="program.study_plan_document_path"
              target="_blank"
              class="px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-gold-500 hover:text-navy-950 text-white font-bold text-xs shadow-md transition-all shrink-0 inline-flex items-center gap-2"
            >
              <span>📥</span>
              <span>{{ localeStore.isRtl ? 'تحميل الخطة المعتمدة (PDF)' : 'Download Curriculum (PDF)' }}</span>
            </a>
          </div>
          <Card padding="lg">
            <h2 class="text-xl font-bold text-navy-950 mb-4 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
              {{ $t('programs.careerOpportunities') }}
            </h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
              {{ getTranslated(program.career_opportunities, localeStore.locale) }}
            </p>
          </Card>
        </div>

        <!-- Right 4 cols: Requirements, Tuition Fees, Sidebar Apply CTA -->
        <div class="lg:col-span-4 space-y-6">
          <!-- Admission Requirements -->
          <Card padding="lg" class="border-t-4 border-t-gold-500">
            <h3 class="font-bold text-navy-950 text-base mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ $t('programs.admissionReqs') }}
            </h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
              {{ getTranslated(program.admission_requirements, localeStore.locale) }}
            </p>
          </Card>

          <!-- Tuition Fees -->
          <Card padding="lg" class="border-t-4 border-t-emerald-600">
            <h3 class="font-bold text-navy-950 text-base mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ $t('programs.tuitionFees') }}
            </h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
              {{ getTranslated(program.tuition_fees, localeStore.locale) }}
            </p>
          </Card>

          <!-- Apply Now Action Box -->
          <div class="bg-navy-950 text-white p-6 rounded-2xl space-y-4 text-center">
            <h4 class="font-bold text-base">{{ $t('admissions.title') }}</h4>
            <p class="text-xs text-slate-300">
              {{ localeStore.isRtl ? 'التقديم مفتوح الآن للعام الجامعي 2025/2026. المقاعد محدودة.' : 'Admissions open for Academic Year 2025/2026. Limited seats available.' }}
            </p>
            <Button
              :to="`/admissions?program_id=${program.id}`"
              variant="gold"
              size="md"
              rounded="xl"
              block
            >
              {{ $t('programs.applyForProgram') }}
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import Card from '../components/ui/Card.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'

const route = useRoute()
const localeStore = useLocaleStore()

const program = ref(null)
const loading = ref(true)

const loadProgram = async () => {
  loading.value = true
  try {
    const slug = route.params.slug
    const res = await api.getProgram(slug)
    program.value = res
  } catch (e) {
    console.error('Failed to load program detail:', e)
  } finally {
    loading.value = false
  }
}

watch(() => route.params.slug, loadProgram)
onMounted(loadProgram)
</script>
