<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('documents.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="primary" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('documents.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('documents.subtitle') }}
      </p>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-6 rounded-2xl shadow-academic border border-slate-200/80 space-y-4">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Category Buttons -->
        <div class="flex flex-wrap items-center gap-2">
          <button
            v-for="cat in categories"
            :key="cat.slug"
            type="button"
            :class="[
              'px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all cursor-pointer',
              selectedCategory === cat.slug
                ? 'bg-navy-900 text-white shadow-sm'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
            ]"
            @click="selectedCategory = cat.slug"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-72">
          <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('documents.searchDocs')"
            class="w-full ps-10 pe-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-navy-800 outline-none transition-all"
          />
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <ErrorState
      v-else-if="error"
      :message="error"
      @retry="loadDocumentsData"
    />

    <EmptyState
      v-else-if="filteredDocs.length === 0"
      :title="$t('documents.noDocs')"
    />

    <!-- Documents List Table / Cards -->
    <div v-else class="space-y-4">
      <div
        v-for="doc in filteredDocs"
        :key="doc.id"
        class="bg-white rounded-2xl p-5 shadow-academic border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-gold-300 transition-all duration-200"
      >
        <!-- Icon & Doc Title -->
        <div class="flex items-start sm:items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-navy-50 text-navy-900 flex items-center justify-center font-black text-xs shrink-0 border border-navy-100 shadow-xs">
            {{ doc.file_type || 'PDF' }}
          </div>
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <h3 class="text-sm sm:text-base font-bold text-navy-950">
                {{ getTranslated(doc.title, localeStore.locale) }}
              </h3>
              <span class="font-mono text-[10px] font-bold text-navy-800 bg-slate-100 px-1.5 py-0.5 rounded">
                v{{ doc.version || '1.0' }}
              </span>
            </div>
            <p v-if="doc.description" class="text-xs text-slate-500 line-clamp-1 max-w-xl">
              {{ getTranslated(doc.description, localeStore.locale) }}
            </p>
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 pt-0.5">
              <span>📁 {{ getCategoryLabel(doc.category) }}</span>
              <span>•</span>
              <span>💾 {{ doc.file_size }}</span>
              <span>•</span>
              <span>⬇ {{ doc.download_count }} {{ $t('documents.downloadsCount') }}</span>
            </div>
          </div>
        </div>

        <!-- Download Action -->
        <button
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold bg-navy-950 hover:bg-gold-500 hover:text-navy-950 text-white rounded-xl shadow-sm transition-all shrink-0 cursor-pointer"
          @click="downloadDocument(doc)"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          {{ $t('documents.download') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import ErrorState from '../components/ui/ErrorState.vue'
import { useToast } from '../composables/useToast'

const { t } = useI18n()
const localeStore = useLocaleStore()
const toast = useToast()

const documents = ref([])
const loading = ref(true)
const error = ref('')
const selectedCategory = ref('all')
const searchQuery = ref('')

const categories = computed(() => [
  { slug: 'all', label: t('documents.categoryAll') },
  { slug: 'bylaws', label: t('documents.categoryBylaws') },
  { slug: 'regulations', label: t('documents.categoryRegulations') },
  { slug: 'forms', label: t('documents.categoryForms') },
  { slug: 'schedules', label: t('documents.categorySchedules') },
])

const getCategoryLabel = (slug) => {
  const cat = categories.value.find((c) => c.slug === slug)
  return cat ? cat.label : slug
}

const filteredDocs = computed(() => {
  return documents.value.filter((d) => {
    if (selectedCategory.value !== 'all' && d.category !== selectedCategory.value) {
      return false
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const titleAr = (d.title?.ar || '').toLowerCase()
      const titleEn = (d.title?.en || '').toLowerCase()
      if (!titleAr.includes(q) && !titleEn.includes(q)) return false
    }
    return true
  })
})

const downloadDocument = async (doc) => {
  try {
    await api.incrementDocumentDownload(doc.id)
    doc.download_count++
    
    toast.info(
      localeStore.isRtl ? `جاري تحميل ملف: ${getTranslated(doc.title, localeStore.locale)}` : `Downloading: ${getTranslated(doc.title, localeStore.locale)}`,
      localeStore.isRtl ? 'تحميل الوثيقة' : 'Downloading'
    )

    if (doc.file_path && (doc.file_path.startsWith('http') || doc.file_path.startsWith('/storage') || doc.file_path.startsWith('blob:'))) {
      const link = document.createElement('a')
      link.href = doc.file_path
      link.target = '_blank'
      link.download = doc.file_path.split('/').pop() || 'document.pdf'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      return
    }

    const officialDocContent = `=== EgyiTech University Official Document ===\nTitle: ${getTranslated(doc.title, localeStore.locale)}\nCategory: ${doc.category}\nVersion: v${doc.version || '1.0'}\nFile: ${doc.file_path || 'document.pdf'}\nVerified by EgyiTech Academic Council.`
    const blob = new Blob([officialDocContent], { type: 'application/pdf' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = (doc.file_path || 'document.pdf').split('/').pop()
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (e) {
    toast.error(
      localeStore.isRtl ? 'حدث خطأ أثناء محاولة تحميل الوثيقة.' : 'Failed to download document.',
      localeStore.isRtl ? 'خطأ في التحميل' : 'Download Error'
    )
  }
}

const loadDocumentsData = async () => {
  loading.value = true
  error.value = ''
  try {
    documents.value = await api.getDocuments()
  } catch (e) {
    error.value = e.message || (localeStore.isRtl ? 'تعذر جلب الوثائق واللوائح من الخادم.' : 'Failed to load documents.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDocumentsData()
})
</script>
