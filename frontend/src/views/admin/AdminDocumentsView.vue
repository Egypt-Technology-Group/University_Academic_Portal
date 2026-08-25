<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.documents.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ $t('admin.documents.subtitle') }}
        </p>
      </div>

      <button
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
        @click="openNewDocModal"
      >
        <Upload class="w-4 h-4 text-gold-400" />
        <span>{{ $t('admin.documents.uploadDoc') }}</span>
      </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-3">
      <div class="relative flex-1 w-full">
        <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('admin.documents.searchPlaceholder')"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
        />
      </div>

      <div class="w-full sm:w-60 shrink-0">
        <select
          v-model="categoryFilter"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
        >
          <option value="all">{{ $t('admin.documents.allCategories') }}</option>
          <option value="regulations">{{ $t('admin.documents.catRegulations') }}</option>
          <option value="schedules">{{ $t('admin.documents.catSchedules') }}</option>
          <option value="forms">{{ $t('admin.documents.catForms') }}</option>
          <option value="guides">{{ $t('admin.documents.catGuides') }}</option>
        </select>
      </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="py-16 text-center text-slate-400">
        <div class="w-8 h-8 border-2 border-navy-900 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <div class="text-xs font-bold">{{ $t('common.loading') }}</div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredDocuments.length === 0" class="py-16 text-center text-slate-500">
        <FileX class="w-12 h-12 text-slate-300 mx-auto mb-3" />
        <div class="text-sm font-bold text-navy-950">{{ $t('admin.documents.noDocsFound') }}</div>
      </div>

      <!-- Documents Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">{{ $t('admin.documents.colDocTitle') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.documents.colCategory') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.documents.colFormat') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.documents.colSize') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.documents.colDownloads') }}</th>
              <th class="py-3.5 px-4 text-end">{{ $t('admin.documents.colActions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="doc in filteredDocuments"
              :key="doc.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- Title & Description -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-lg bg-navy-50 text-navy-900 flex items-center justify-center font-bold text-xs shrink-0">
                    PDF
                  </div>
                  <div>
                    <div class="font-bold text-navy-950 text-sm">
                      {{ getTranslated(doc.title, localeStore.locale) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-md">
                      {{ getTranslated(doc.description, localeStore.locale) }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Category -->
              <td class="py-3.5 px-4">
                <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                  {{ getCategoryLabel(doc.category) }}
                </span>
              </td>

              <!-- Format Badge -->
              <td class="py-3.5 px-4 text-center">
                <span class="inline-block font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">
                  {{ doc.file_type || 'PDF' }}
                </span>
              </td>

              <!-- Size -->
              <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                {{ doc.file_size_mb || 2.4 }} MB
              </td>

              <!-- Downloads -->
              <td class="py-3.5 px-4 text-center font-mono font-bold text-navy-950">
                {{ (doc.download_count || 320).toLocaleString() }}
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-navy-900 hover:bg-slate-100 transition-colors"
                    title="Download File"
                    @click="handleDownload(doc)"
                  >
                    <Download class="w-4 h-4" />
                  </button>

                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                    title="Delete Document"
                    @click="handleDeleteDoc(doc.id)"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: UPLOAD DOCUMENT -->
    <Modal
      v-model="isModalOpen"
      :title="$t('admin.documents.modalTitle')"
      max-width="xl"
      @close="isModalOpen = false"
    >
      <form @submit.prevent="submitForm" class="space-y-4 text-start">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelTitleAr') }} *
            </label>
            <input
              v-model="form.title_ar"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="مثال: اللائحة الداخلية لكلية الحاسبات..."
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelTitleEn') }} *
            </label>
            <input
              v-model="form.title_en"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="e.g. Faculty of CS Internal Regulations..."
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelCategory') }}
            </label>
            <select
              v-model="form.category"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="regulations">{{ $t('admin.documents.catRegulations') }}</option>
              <option value="schedules">{{ $t('admin.documents.catSchedules') }}</option>
              <option value="forms">{{ $t('admin.documents.catForms') }}</option>
              <option value="guides">{{ $t('admin.documents.catGuides') }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelSize') }}
            </label>
            <input
              v-model="form.file_size_mb"
              type="number"
              step="0.1"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="2.4"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            {{ $t('admin.documents.labelDescriptionAr') }}
          </label>
          <textarea
            v-model="form.description_ar"
            rows="2"
            class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            placeholder="وصف محتوى اللائحة أو المستند..."
          ></textarea>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200"
          @click="isModalOpen = false"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md"
          @click="submitForm"
        >
          {{ $t('admin.documents.uploadDoc') }}
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import Modal from '../../components/ui/Modal.vue'
import {
  Upload,
  Search,
  Download,
  Trash2,
  FileX,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()

const documentsList = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const categoryFilter = ref('all')
const isModalOpen = ref(false)

const form = reactive({
  title_ar: '',
  title_en: '',
  category: 'regulations',
  file_size_mb: 2.4,
  description_ar: '',
})

const filteredDocuments = computed(() => {
  let list = [...documentsList.value]

  if (categoryFilter.value !== 'all') {
    list = list.filter((d) => d.category === categoryFilter.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase()
    list = list.filter((d) =>
      (d.title?.ar && d.title.ar.toLowerCase().includes(q)) ||
      (d.title?.en && d.title.en.toLowerCase().includes(q))
    )
  }

  return list
})

const getCategoryLabel = (cat) => {
  if (cat === 'regulations') return t('admin.documents.catRegulations')
  if (cat === 'schedules') return t('admin.documents.catSchedules')
  if (cat === 'forms') return t('admin.documents.catForms')
  if (cat === 'guides') return t('admin.documents.catGuides')
  return cat
}

const loadDocs = async () => {
  isLoading.value = true
  try {
    const data = await api.getDocuments()
    documentsList.value = data || []
  } catch (e) {
    console.error('Failed to load documents', e)
  } finally {
    isLoading.value = false
  }
}

const openNewDocModal = () => {
  form.title_ar = ''
  form.title_en = ''
  form.description_ar = ''
  form.category = 'regulations'
  form.file_size_mb = 2.4
  isModalOpen.value = true
}

const submitForm = async () => {
  if (!form.title_ar) {
    alert('يرجى إدخال عنوان الوثيقة')
    return
  }

  try {
    const created = await api.createDocument({ ...form })
    documentsList.value.unshift(created)
    isModalOpen.value = false
  } catch (err) {
    alert('Failed to save document')
  }
}

const handleDownload = (doc) => {
  api.incrementDocumentDownload(doc.id)
  doc.download_count = (doc.download_count || 0) + 1
  alert(`جاري تحميل ملف: ${getTranslated(doc.title, localeStore.locale)}`)
}

const handleDeleteDoc = async (id) => {
  if (window.confirm(t('admin.documents.confirmDeleteDoc'))) {
    await api.deleteDocument(id)
    documentsList.value = documentsList.value.filter((d) => d.id !== id)
  }
}

onMounted(() => {
  loadDocs()
})
</script>
