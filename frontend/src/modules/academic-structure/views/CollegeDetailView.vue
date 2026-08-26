<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <!-- Breadcrumbs -->
    <Breadcrumbs
      :items="[
        { label: $t('colleges.title'), to: '/colleges' },
        { label: getTranslated(college?.name, localeStore.locale) || $t('common.loading') },
      ]"
    />

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <div v-else-if="college" class="space-y-12">
      <!-- College Hero Banner -->
      <div class="relative rounded-3xl overflow-hidden bg-navy-950 text-white min-h-[320px] flex items-end p-6 sm:p-10 shadow-xl">
        <img
          :src="college.banner_image"
          :alt="getTranslated(college.name, localeStore.locale)"
          class="absolute inset-0 w-full h-full object-cover opacity-35"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/60 to-transparent"></div>

        <div class="relative z-10 space-y-4 max-w-3xl">
          <div class="flex flex-wrap items-center gap-2">
            <Badge variant="gold" size="sm" rounded="full">
              {{ college.programs_count || 6 }} {{ $t('colleges.programsCount') }}
            </Badge>
            <Badge variant="emerald" size="sm" rounded="full">
              {{ college.departments_count || 3 }} {{ $t('colleges.departments') }}
            </Badge>
          </div>

          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
            {{ getTranslated(college.name, localeStore.locale) }}
          </h1>

          <p class="text-sm sm:text-base text-slate-200 leading-relaxed max-w-2xl">
            {{ getTranslated(college.about, localeStore.locale) }}
          </p>
        </div>
      </div>

      <!-- Vision, Mission & Dean Message Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Vision -->
        <Card padding="lg" class="border-t-4 border-t-gold-500">
          <div class="flex items-center gap-2 text-gold-600 mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <h3 class="font-bold text-navy-950 text-lg">{{ $t('colleges.vision') }}</h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ getTranslated(college.vision, localeStore.locale) }}
          </p>
        </Card>

        <!-- Mission -->
        <Card padding="lg" class="border-t-4 border-t-emerald-600">
          <div class="flex items-center gap-2 text-emerald-600 mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="font-bold text-navy-950 text-lg">{{ $t('colleges.mission') }}</h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ getTranslated(college.mission, localeStore.locale) }}
          </p>
        </Card>

        <!-- Dean Message -->
        <Card padding="lg" class="border-t-4 border-t-navy-900">
          <div class="flex items-center gap-2 text-navy-900 mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <h3 class="font-bold text-navy-950 text-lg">{{ $t('colleges.dean') }}</h3>
          </div>
          <div class="font-bold text-sm text-navy-900 mb-1">
            {{ getTranslated(college.dean_name, localeStore.locale) }}
          </div>
          <p class="text-xs text-slate-500 leading-relaxed">
            {{ localeStore.isRtl ? 'نرحب بجميع طلابنا وباحثينا في بيئة علمية ملهمة تمكّنهم من تحويل أفكارهم إلى ابتكارات ملموسة تخدم التنمية الشاملة.' : 'We welcome our students and researchers to an inspiring academic ecosystem empowering tangible innovations.' }}
          </p>
        </Card>
      </div>

      <!-- Departments & Degree Programs Tabs -->
      <section class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-black text-navy-950">
            {{ $t('colleges.departmentsList') }} & {{ $t('colleges.programsOffered') }}
          </h2>
          <Button to="/admissions" variant="gold" size="sm" rounded="lg">
            {{ $t('nav.applyNow') }}
          </Button>
        </div>

        <div v-if="departments.length > 0" class="space-y-6">
          <div
            v-for="dept in departments"
            :key="dept.id"
            class="bg-white rounded-2xl p-6 shadow-academic border border-slate-200/80 space-y-4"
          >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-navy-900"></span>
                <h3 class="text-lg font-bold text-navy-950">
                  {{ getTranslated(dept.name, localeStore.locale) }}
                </h3>
              </div>
              <span class="text-xs font-semibold text-slate-500">
                {{ dept.programs?.length || 0 }} {{ $t('colleges.programsCount') }}
              </span>
            </div>

            <!-- Department Programs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
              <div
                v-for="prog in dept.programs"
                :key="prog.id"
                class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-gold-50/40 hover:border-gold-300 transition-colors flex flex-col justify-between"
              >
                <div>
                  <Badge variant="subtle" size="xs" class="mb-2">
                    {{ $t(`programs.${prog.degree_level}`) || prog.degree_level }}
                  </Badge>
                  <h4 class="font-bold text-sm text-navy-950 mb-1 leading-snug">
                    {{ getTranslated(prog.name, localeStore.locale) }}
                  </h4>
                  <p class="text-xs text-slate-500">
                    {{ prog.credit_hours }} {{ $t('programs.creditHours') }} • {{ prog.duration_years }} {{ $t('programs.durationYears') }}
                  </p>
                </div>

                <div class="pt-4 mt-3 border-t border-slate-200/60 flex items-center justify-between">
                  <router-link
                    :to="`/programs/${prog.slug}`"
                    class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors"
                  >
                    {{ $t('common.viewDetails') }} →
                  </router-link>
                  <router-link
                    :to="`/admissions?program_id=${prog.id}`"
                    class="text-xs font-bold text-gold-700 hover:underline"
                  >
                    {{ $t('programs.applyForProgram') }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Faculty Profiles -->
      <section v-if="facultyList.length > 0" class="space-y-6">
        <h2 class="text-2xl font-black text-navy-950">
          {{ $t('colleges.facultyMembers') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="fac in facultyList"
            :key="fac.id"
            padding="md"
            class="flex items-center gap-4"
          >
            <img
              :src="fac.avatar"
              :alt="fac.name"
              class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 shrink-0"
            />
            <div class="space-y-1 flex-1 overflow-hidden">
              <h4 class="font-bold text-sm text-navy-950 truncate">{{ fac.name }}</h4>
              <p class="text-xs text-slate-500 truncate">
                {{ getTranslated(fac.academic_title, localeStore.locale) }}
              </p>
              <button
                type="button"
                class="text-xs font-bold text-gold-600 hover:text-gold-700 transition-colors block pt-1"
                @click="openFacultyModal(fac)"
              >
                {{ $t('faculty.viewProfile') }} →
              </button>
            </div>
          </Card>
        </div>
      </section>
    </div>

    <!-- Faculty Profile Detail Modal -->
    <Modal v-model="showFacultyModal" :title="selectedFaculty?.name" max-width="xl">
      <div v-if="selectedFaculty" class="space-y-6 text-start">
        <div class="flex items-center gap-4">
          <img
            :src="selectedFaculty.avatar"
            :alt="selectedFaculty.name"
            class="w-20 h-20 rounded-2xl object-cover border-2 border-gold-400 shrink-0"
          />
          <div>
            <h3 class="text-lg font-bold text-navy-950">{{ selectedFaculty.name }}</h3>
            <p class="text-xs text-gold-600 font-semibold">
              {{ getTranslated(selectedFaculty.academic_title, localeStore.locale) }}
            </p>
            <p class="text-xs text-slate-500 mt-1">
              ✉ {{ selectedFaculty.email }}
            </p>
          </div>
        </div>

        <div class="space-y-2">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $t('faculty.bio') }}</h4>
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ getTranslated(selectedFaculty.bio, localeStore.locale) }}
          </p>
        </div>

        <div v-if="selectedFaculty.research_interests" class="space-y-2">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $t('faculty.researchInterests') }}</h4>
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ getTranslated(selectedFaculty.research_interests, localeStore.locale) }}
          </p>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 border border-slate-100 flex items-center justify-between">
          <span>📍 {{ getTranslated(selectedFaculty.office_location, localeStore.locale) }}</span>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from '../../../stores/locale'
import { getTranslated } from '../../../services/api'
import { academicStructureApi } from '../services/academicStructureApi'
import Breadcrumbs from '../../../components/ui/Breadcrumbs.vue'
import Badge from '../../../components/ui/Badge.vue'
import Button from '../../../components/ui/Button.vue'
import Card from '../../../components/ui/Card.vue'
import Modal from '../../../components/ui/Modal.vue'
import LoadingSpinner from '../../../components/ui/LoadingSpinner.vue'

const route = useRoute()
const localeStore = useLocaleStore()

const college = ref(null)
const departments = ref([])
const facultyList = ref([])
const loading = ref(true)

const showFacultyModal = ref(false)
const selectedFaculty = ref(null)

const openFacultyModal = (fac) => {
  selectedFaculty.value = fac
  showFacultyModal.value = true
}

const loadCollege = async () => {
  loading.value = true
  try {
    const slug = route.params.slug
    const res = await academicStructureApi.getCollege(slug)
    college.value = res
    departments.value = res.departments || []
    facultyList.value = res.faculty_profiles || []
  } catch (e) {
    console.error('Failed to load college detail:', e)
  } finally {
    loading.value = false
  }
}

watch(() => route.params.slug, loadCollege)
onMounted(loadCollege)
</script>
