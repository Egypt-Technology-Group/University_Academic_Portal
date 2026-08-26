<template>
  <Modal :model-value="modelValue" max-width="2xl" @update:model-value="$emit('update:modelValue', $event)">
    <div class="space-y-4">
      <!-- Search Input -->
      <div class="relative">
        <svg
          class="absolute start-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          ></path>
        </svg>
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          :placeholder="$t('search.placeholder')"
          class="w-full ps-12 pe-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-base focus:ring-2 focus:ring-navy-800 focus:border-navy-800 outline-none transition-all placeholder:text-slate-400"
          autofocus
        />
        <button
          v-if="searchQuery"
          type="button"
          class="absolute end-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600 bg-slate-200 px-2 py-0.5 rounded"
          @click="searchQuery = ''"
        >
          ESC
        </button>
      </div>

      <!-- Live Search Results -->
      <div v-if="searchQuery.trim().length > 1" class="space-y-5 max-h-[60vh] overflow-y-auto pt-2">
        <!-- Colleges Results -->
        <div v-if="filteredColleges.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-navy-800"></span>
            {{ $t('search.sectionColleges') }}
          </h4>
          <div class="space-y-1.5">
            <router-link
              v-for="college in filteredColleges"
              :key="college.id"
              :to="`/colleges/${college.slug}`"
              class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-100 transition-colors group"
              @click="close"
            >
              <span class="font-semibold text-navy-950 group-hover:text-navy-800">
                {{ getTranslated(college.name, localeStore.locale) }}
              </span>
              <span class="text-xs text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-md">
                {{ college.departments_count }} {{ $t('colleges.departments') }}
              </span>
            </router-link>
          </div>
        </div>

        <!-- Programs Results -->
        <div v-if="filteredPrograms.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-gold-500"></span>
            {{ $t('search.sectionPrograms') }}
          </h4>
          <div class="space-y-1.5">
            <router-link
              v-for="program in filteredPrograms"
              :key="program.id"
              :to="`/programs/${program.slug}`"
              class="flex items-center justify-between p-3 rounded-xl hover:bg-gold-50/50 transition-colors group border border-transparent hover:border-gold-200"
              @click="close"
            >
              <div>
                <span class="font-semibold text-navy-950 group-hover:text-navy-900 block">
                  {{ getTranslated(program.name, localeStore.locale) }}
                </span>
                <span class="text-xs text-slate-500">
                  {{ getTranslated(program.college_name, localeStore.locale) }}
                </span>
              </div>
              <span class="text-xs font-medium text-gold-700 bg-gold-100 px-2 py-0.5 rounded">
                {{ program.credit_hours }} {{ $t('programs.creditHours') }}
              </span>
            </router-link>
          </div>
        </div>

        <!-- Faculty Results -->
        <div v-if="filteredFaculty.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ $t('search.sectionFaculty') }}
          </h4>
          <div class="space-y-1.5">
            <router-link
              v-for="fac in filteredFaculty"
              :key="fac.id"
              to="/faculty"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-100 transition-colors"
              @click="close"
            >
              <img :src="fac.avatar" :alt="fac.name" class="w-9 h-9 rounded-full object-cover border border-slate-200" />
              <div>
                <div class="font-semibold text-navy-950 text-sm">{{ fac.name }}</div>
                <div class="text-xs text-slate-500">{{ getTranslated(fac.academic_title, localeStore.locale) }}</div>
              </div>
            </router-link>
          </div>
        </div>

        <!-- News Results -->
        <div v-if="filteredNews.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            {{ $t('search.sectionNews') }}
          </h4>
          <div class="space-y-1.5">
            <router-link
              v-for="article in filteredNews"
              :key="article.id"
              :to="`/news/${article.slug}`"
              class="block p-3 rounded-xl hover:bg-slate-100 transition-colors"
              @click="close"
            >
              <span class="font-medium text-navy-950 text-sm line-clamp-1">
                {{ getTranslated(article.title, localeStore.locale) }}
              </span>
            </router-link>
          </div>
        </div>

        <!-- Documents Results -->
        <div v-if="filteredDocs.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
            {{ $t('search.sectionDocs') }}
          </h4>
          <div class="space-y-1.5">
            <router-link
              v-for="doc in filteredDocs"
              :key="doc.id"
              to="/documents"
              class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-100 transition-colors"
              @click="close"
            >
              <span class="font-medium text-navy-950 text-sm truncate">
                {{ getTranslated(doc.title, localeStore.locale) }}
              </span>
              <span class="text-xs text-slate-500 bg-slate-200 px-2 py-0.5 rounded">
                {{ doc.file_type }}
              </span>
            </router-link>
          </div>
        </div>

        <!-- No Results -->
        <div
          v-if="
            filteredColleges.length === 0 &&
            filteredPrograms.length === 0 &&
            filteredFaculty.length === 0 &&
            filteredNews.length === 0 &&
            filteredDocs.length === 0
          "
          class="text-center py-8 text-slate-500"
        >
          <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p>{{ $t('search.noResults') }} "{{ searchQuery }}"</p>
        </div>
      </div>

      <!-- Quick Search Tips / Suggestions when search is empty -->
      <div v-else class="py-4 text-xs text-slate-500 bg-slate-50 rounded-xl p-4 border border-slate-100">
        <p class="font-semibold text-slate-700 mb-2">{{ $t('search.quickTips') }}</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="kw in ['الذكاء الاصطناعي', 'هندسة', 'القبول', 'الرسوم', 'AI', 'Engineering']"
            :key="kw"
            type="button"
            class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-slate-600 hover:border-navy-700 hover:text-navy-900 transition-colors"
            @click="searchQuery = kw"
          >
            {{ kw }}
          </button>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Modal from './Modal.vue'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'

defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])
const localeStore = useLocaleStore()
const searchQuery = ref('')
const searchInput = ref(null)

const collegesList = ref([])
const programsList = ref([])
const facultyList = ref([])
const newsList = ref([])
const docsList = ref([])

onMounted(async () => {
  try {
    const [c, p, f, n, d] = await Promise.all([
      api.getColleges(),
      api.getPrograms(),
      api.getFaculty(),
      api.getNews(),
      api.getDocuments(),
    ])
    collegesList.value = c || []
    programsList.value = p || []
    facultyList.value = f || []
    newsList.value = n || []
    docsList.value = d || []
  } catch (err) {
    console.error('SearchModal data load error:', err)
  }
})

const close = () => {
  emit('update:modelValue', false)
  searchQuery.value = ''
}

const matchText = (field, q) => {
  if (!field) return false
  if (typeof field === 'string') return field.toLowerCase().includes(q)
  if (typeof field === 'object') {
    const ar = (field.ar || '').toLowerCase()
    const en = (field.en || '').toLowerCase()
    return ar.includes(q) || en.includes(q)
  }
  return false
}

const filteredColleges = computed(() => {
  if (!searchQuery.value) return []
  const q = searchQuery.value.toLowerCase().trim()
  return collegesList.value.filter((c) => matchText(c.name, q) || matchText(c.about, q)).slice(0, 3)
})

const filteredPrograms = computed(() => {
  if (!searchQuery.value) return []
  const q = searchQuery.value.toLowerCase().trim()
  return programsList.value.filter((p) => matchText(p.name, q) || matchText(p.overview, q)).slice(0, 4)
})

const filteredFaculty = computed(() => {
  if (!searchQuery.value) return []
  const q = searchQuery.value.toLowerCase().trim()
  return facultyList.value.filter((f) => (f.name && f.name.toLowerCase().includes(q)) || matchText(f.academic_title, q)).slice(0, 3)
})

const filteredNews = computed(() => {
  if (!searchQuery.value) return []
  const q = searchQuery.value.toLowerCase().trim()
  return newsList.value.filter((n) => matchText(n.title, q) || matchText(n.excerpt, q)).slice(0, 3)
})

const filteredDocs = computed(() => {
  if (!searchQuery.value) return []
  const q = searchQuery.value.toLowerCase().trim()
  return docsList.value.filter((d) => matchText(d.title, q)).slice(0, 3)
})
</script>
