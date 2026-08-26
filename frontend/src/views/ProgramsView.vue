<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('programs.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="emerald" size="md" rounded="full">
        {{ $t('programs.allDegrees') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('programs.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('programs.subtitle') }}
      </p>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-academic border border-slate-200/80 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
        <!-- Search Input -->
        <div class="md:col-span-6 relative">
          <svg class="w-5 h-5 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('programs.searchProgram')"
            class="w-full ps-11 pe-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 focus:border-navy-800 outline-none transition-all"
          />
        </div>

        <!-- Degree Level Filter -->
        <div class="md:col-span-3">
          <select
            v-model="selectedDegree"
            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 focus:border-navy-800 outline-none transition-all"
          >
            <option value="">{{ $t('programs.allDegrees') }}</option>
            <option value="bachelor">{{ $t('programs.bachelor') }}</option>
            <option value="master">{{ $t('programs.master') }}</option>
            <option value="doctorate">{{ $t('programs.doctorate') }}</option>
            <option value="diploma">{{ $t('programs.diploma') }}</option>
          </select>
        </div>

        <!-- College Filter -->
        <div class="md:col-span-3">
          <select
            v-model="selectedCollege"
            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 focus:border-navy-800 outline-none transition-all"
          >
            <option value="">{{ $t('programs.allColleges') }}</option>
            <option v-for="c in colleges" :key="c.id" :value="c.id">
              {{ getTranslated(c.name, localeStore.locale) }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Programs Grid -->
    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <ErrorState
      v-else-if="error"
      :message="error"
      @retry="loadProgramsData"
    />

    <div v-else-if="filteredPrograms.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200 space-y-3">
      <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-slate-600 font-semibold">{{ $t('programs.noProgramsFound') }}</p>
      <Button variant="ghost" size="sm" @click="resetFilters">
        {{ $t('common.all') }}
      </Button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card
        v-for="program in filteredPrograms"
        :key="program.id"
        padding="lg"
        class="flex flex-col justify-between hover:border-gold-300"
      >
        <div class="space-y-3">
          <div class="flex items-center justify-between gap-2">
            <Badge variant="subtle" size="sm">
              {{ $t(`programs.${program.degree_level}`) || program.degree_level }}
            </Badge>
            <span class="text-xs font-semibold text-slate-500">
              {{ program.credit_hours }} {{ $t('programs.creditHours') }}
            </span>
          </div>

          <h3 class="text-lg font-bold text-navy-950 leading-snug">
            {{ getTranslated(program.name, localeStore.locale) }}
          </h3>

          <p class="text-xs font-medium text-slate-500">
            {{ getTranslated(program.college_name, localeStore.locale) }}
          </p>

          <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
            {{ getTranslated(program.overview, localeStore.locale) }}
          </p>
        </div>

        <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
          <router-link
            :to="`/programs/${program.slug}`"
            class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors"
          >
            {{ $t('programs.overview') }} →
          </router-link>

          <Button
            :to="`/admissions?program_id=${program.id}`"
            variant="gold"
            size="sm"
            rounded="lg"
          >
            {{ $t('programs.applyForProgram') }}
          </Button>
        </div>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import Card from '../components/ui/Card.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'
import ErrorState from '../components/ui/ErrorState.vue'

const localeStore = useLocaleStore()
const programs = ref([])
const colleges = ref([])
const loading = ref(true)
const error = ref('')

const searchQuery = ref('')
const selectedDegree = ref('')
const selectedCollege = ref('')

const resetFilters = () => {
  searchQuery.value = ''
  selectedDegree.value = ''
  selectedCollege.value = ''
}

const filteredPrograms = computed(() => {
  return programs.value.filter((p) => {
    // Degree filter
    if (selectedDegree.value && p.degree_level !== selectedDegree.value) {
      return false
    }
    // Search query
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const nameAr = (p.name?.ar || '').toLowerCase()
      const nameEn = (p.name?.en || '').toLowerCase()
      const descAr = (p.overview?.ar || '').toLowerCase()
      const descEn = (p.overview?.en || '').toLowerCase()
      const slug = (p.slug || '').toLowerCase()
      if (!nameAr.includes(q) && !nameEn.includes(q) && !descAr.includes(q) && !descEn.includes(q) && !slug.includes(q)) {
        return false
      }
    }
    return true
  })
})

const loadProgramsData = async () => {
  loading.value = true
  error.value = ''
  try {
    const [pData, cData] = await Promise.all([
      api.getPrograms(),
      api.getColleges(),
    ])
    programs.value = pData || []
    colleges.value = cData || []
  } catch (e) {
    error.value = e.message || (localeStore.isRtl ? 'تعذر جلب البرامج الأكاديمية من الخادم.' : 'Failed to load degree programs.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProgramsData()
})
</script>
