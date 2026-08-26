<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('faculty.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="primary" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('faculty.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('faculty.subtitle') }}
      </p>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-6 rounded-2xl shadow-academic border border-slate-200/80 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
        <!-- Search Input -->
        <div class="md:col-span-6 relative">
          <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('faculty.searchPlaceholder')"
            class="w-full ps-10 pe-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none transition-all"
          />
        </div>

        <!-- Academic Rank Filter -->
        <div class="md:col-span-6">
          <select
            v-model="selectedRank"
            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-navy-800 outline-none transition-all"
          >
            <option value="">{{ $t('faculty.allRanks') }}</option>
            <option value="prof">{{ $t('faculty.prof') }}</option>
            <option value="assocProf">{{ $t('faculty.assocProf') }}</option>
            <option value="assistProf">{{ $t('faculty.assistProf') }}</option>
            <option value="lecturer">{{ $t('faculty.lecturer') }}</option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <EmptyState
      v-else-if="filteredFaculty.length === 0"
      :title="$t('faculty.noFacultyFound')"
    />

    <!-- Faculty Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card
        v-for="fac in filteredFaculty"
        :key="fac.id"
        padding="lg"
        class="flex flex-col justify-between hover:border-gold-300 group"
      >
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <img
              :src="fac.avatar"
              :alt="fac.name"
              class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 shrink-0 group-hover:scale-105 transition-transform"
            />
            <div class="space-y-1 flex-1 overflow-hidden">
              <h3 class="font-bold text-base text-navy-950 truncate">{{ fac.name }}</h3>
              <p class="text-xs text-gold-600 font-semibold truncate">
                {{ getTranslated(fac.academic_title, localeStore.locale) }}
              </p>
              <p class="text-xs text-slate-400 truncate">
                {{ getTranslated(fac.department?.name, localeStore.locale) }}
              </p>
            </div>
          </div>

          <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
            {{ getTranslated(fac.bio, localeStore.locale) }}
          </p>

          <div v-if="fac.research_interests" class="p-2.5 bg-slate-50 rounded-xl text-[11px] text-slate-500 line-clamp-2">
            <strong class="text-slate-700 font-semibold">{{ $t('faculty.researchInterests') }}:</strong>
            {{ getTranslated(fac.research_interests, localeStore.locale) }}
          </div>
        </div>

        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
          <span class="text-xs text-slate-400 truncate max-w-[160px]">✉ {{ fac.email }}</span>
          
          <Button
            type="button"
            variant="primary"
            size="sm"
            rounded="lg"
            @click="openModal(fac)"
          >
            {{ $t('faculty.viewProfile') }}
          </Button>
        </div>
      </Card>
    </div>

    <!-- Faculty Profile Modal -->
    <Modal v-model="showModal" :title="selectedFac?.name" max-width="2xl">
      <div v-if="selectedFac" class="space-y-6 text-start">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 border-b border-slate-100 pb-6 text-center sm:text-start">
          <img
            :src="selectedFac.avatar"
            :alt="selectedFac.name"
            class="w-24 h-24 rounded-3xl object-cover border-4 border-gold-400 shrink-0 shadow-md"
          />
          <div class="space-y-1">
            <h3 class="text-xl font-bold text-navy-950">{{ selectedFac.name }}</h3>
            <p class="text-sm font-semibold text-gold-600">
              {{ getTranslated(selectedFac.academic_title, localeStore.locale) }}
            </p>
            <p class="text-xs text-slate-500">
              {{ getTranslated(selectedFac.department?.name, localeStore.locale) }}
            </p>
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 pt-2 justify-center sm:justify-start">
              <span>✉ {{ selectedFac.email }}</span>
              <span v-if="selectedFac.phone">•</span>
              <span v-if="selectedFac.phone">📞 {{ selectedFac.phone }}</span>
              <span v-if="selectedFac.cv_path">•</span>
              <a
                v-if="selectedFac.cv_path"
                :href="selectedFac.cv_path"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gold-500 hover:bg-gold-400 text-navy-950 font-black text-xs shadow-xs transition-all"
              >
                <span>📥</span>
                <span>{{ localeStore.isRtl ? 'تحميل السيرة الذاتية الرسمية (PDF)' : 'Download Full CV (PDF)' }}</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Biography -->
        <div class="space-y-2">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $t('faculty.bio') }}</h4>
          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
            {{ getTranslated(selectedFac.bio, localeStore.locale) }}
          </p>
        </div>

        <!-- Research Interests -->
        <div v-if="selectedFac.research_interests" class="space-y-2">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $t('faculty.researchInterests') }}</h4>
          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
            {{ getTranslated(selectedFac.research_interests, localeStore.locale) }}
          </p>
        </div>

        <!-- Academic & Scientific Publications Hub -->
        <div v-if="selectedFac.publications && selectedFac.publications.length > 0" class="space-y-3 pt-2">
          <h4 class="text-xs font-black uppercase tracking-wider text-navy-950 flex items-center gap-2">
            <span>📚</span>
            <span>{{ localeStore.isRtl ? 'الأبحاث والمنشورات العلمية المحكمة' : 'Peer-Reviewed Research Publications' }}</span>
          </h4>
          <div class="space-y-2.5">
            <div
              v-for="(pub, pIdx) in selectedFac.publications"
              :key="pIdx"
              class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5"
            >
              <div class="text-xs sm:text-sm font-bold text-navy-950 leading-snug">
                {{ pub.title }}
              </div>
              <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                <span class="font-semibold text-gold-700">{{ pub.journal }}</span>
                <span>•</span>
                <span>{{ pub.year }}</span>
                <span v-if="pub.citations">•</span>
                <span v-if="pub.citations" class="text-emerald-700 font-mono font-bold">{{ pub.citations }} Citations</span>
                <span v-if="pub.doi">•</span>
                <a
                  v-if="pub.doi"
                  :href="`https://doi.org/${pub.doi}`"
                  target="_blank"
                  rel="noopener"
                  class="text-navy-900 font-mono hover:underline text-[10px] bg-slate-200 px-1.5 py-0.5 rounded"
                >
                  DOI: {{ pub.doi }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Office Hours & Consultation Schedule -->
        <div v-if="selectedFac.office_hours" class="p-4 bg-navy-50/60 rounded-2xl border border-navy-100 space-y-2">
          <h4 class="text-xs font-black text-navy-950 uppercase tracking-wider flex items-center gap-1.5">
            <span>⏰</span>
            <span>{{ localeStore.isRtl ? 'الساعات المكتبية والإرشاد الأكاديمي' : 'Faculty Office Hours & Advising' }}</span>
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
            <div
              v-for="(oh, ohIdx) in selectedFac.office_hours"
              :key="ohIdx"
              class="bg-white p-2.5 rounded-xl border border-navy-200/60 flex items-center justify-between"
            >
              <span class="font-bold text-navy-950">{{ oh.day }}</span>
              <span class="text-slate-600 font-mono text-[11px]">{{ oh.time }}</span>
            </div>
          </div>
        </div>

        <!-- Location & External Profile Badges -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-700 flex flex-wrap items-center justify-between gap-3">
          <span>📍 <strong>{{ $t('faculty.office') }}:</strong> {{ getTranslated(selectedFac.office_location, localeStore.locale) }}</span>
          
          <div class="flex items-center gap-2">
            <a
              v-if="selectedFac.google_scholar_url"
              :href="selectedFac.google_scholar_url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[11px] hover:bg-blue-100 transition-colors"
            >
              🎓 Google Scholar
            </a>
            <a
              v-if="selectedFac.orcid_id"
              :href="`https://orcid.org/${selectedFac.orcid_id}`"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition-colors"
            >
              🆔 ORCID
            </a>
          </div>
        </div>
      </div>
    </Modal>
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
import Modal from '../components/ui/Modal.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'
import EmptyState from '../components/ui/EmptyState.vue'

const localeStore = useLocaleStore()

const facultyList = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedRank = ref('')

const showModal = ref(false)
const selectedFac = ref(null)

const openModal = (fac) => {
  selectedFac.value = fac
  showModal.value = true
}

const filteredFaculty = computed(() => {
  return facultyList.value.filter((f) => {
    if (selectedRank.value && f.rank !== selectedRank.value) {
      return false
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const name = f.name.toLowerCase()
      const email = f.email.toLowerCase()
      const titleAr = (f.academic_title?.ar || '').toLowerCase()
      const titleEn = (f.academic_title?.en || '').toLowerCase()
      const bioAr = (f.bio?.ar || '').toLowerCase()
      const bioEn = (f.bio?.en || '').toLowerCase()
      if (!name.includes(q) && !email.includes(q) && !titleAr.includes(q) && !titleEn.includes(q) && !bioAr.includes(q) && !bioEn.includes(q)) {
        return false
      }
    }
    return true
  })
})

onMounted(async () => {
  try {
    facultyList.value = await api.getFaculty()
  } catch (e) {
    console.error('Failed to load faculty directory:', e)
  } finally {
    loading.value = false
  }
})
</script>
