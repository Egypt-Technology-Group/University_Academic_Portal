<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.cms.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ $t('admin.cms.subtitle') }}
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          v-if="activeTab === 'news'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewNewsModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ $t('admin.cms.publishNews') }}</span>
        </button>

        <button
          v-else
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewAnnouncementModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ $t('admin.cms.postAnnouncement') }}</span>
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-3 border-b border-slate-200">
      <button
        type="button"
        :class="[
          'py-3 px-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2 cursor-pointer',
          activeTab === 'news'
            ? 'border-navy-950 text-navy-950'
            : 'border-transparent text-slate-500 hover:text-slate-700'
        ]"
        @click="activeTab = 'news'"
      >
        <Newspaper class="w-4 h-4" />
        <span>{{ $t('admin.cms.tabNews') }}</span>
        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-mono font-bold">{{ newsList.length }}</span>
      </button>

      <button
        type="button"
        :class="[
          'py-3 px-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2 cursor-pointer',
          activeTab === 'announcements'
            ? 'border-navy-950 text-navy-950'
            : 'border-transparent text-slate-500 hover:text-slate-700'
        ]"
        @click="activeTab = 'announcements'"
      >
        <Megaphone class="w-4 h-4" />
        <span>{{ $t('admin.cms.tabAnnouncements') }}</span>
        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-mono font-bold">{{ announcementsList.length }}</span>
      </button>
    </div>

    <!-- TAB 1: NEWS ARTICLES -->
    <div v-if="activeTab === 'news'" class="space-y-4">
      <!-- Search & Filters -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
          <input
            v-model="newsSearch"
            type="text"
            :placeholder="$t('admin.cms.searchNewsPlaceholder')"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
          />
        </div>

        <div class="w-full sm:w-56 shrink-0">
          <select
            v-model="newsCategoryFilter"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
          >
            <option value="all">{{ $t('admin.cms.allCategories') }}</option>
            <option value="academic">{{ $t('admin.cms.catAcademic') }}</option>
            <option value="scientific">{{ $t('admin.cms.catScientific') }}</option>
            <option value="events">{{ $t('admin.cms.catEvents') }}</option>
          </select>
        </div>
      </div>

      <!-- News Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-start text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4 text-start">{{ $t('admin.cms.colArticle') }}</th>
                <th class="py-3.5 px-4 text-start">{{ $t('admin.cms.colCategory') }}</th>
                <th class="py-3.5 px-4 text-center">{{ $t('admin.cms.colViews') }}</th>
                <th class="py-3.5 px-4 text-start">{{ $t('admin.cms.colDate') }}</th>
                <th class="py-3.5 px-4 text-end">{{ $t('admin.cms.colActions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="article in filteredNews"
                :key="article.id"
                class="hover:bg-slate-50/80 transition-colors"
              >
                <!-- Thumbnail & Title -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3.5">
                    <img
                      :src="article.featured_image"
                      :alt="getTranslated(article.title, localeStore.locale)"
                      class="w-14 h-10 rounded-lg object-cover border border-slate-200 shrink-0"
                    />
                    <div class="max-w-md">
                      <div class="font-bold text-navy-950 text-sm line-clamp-1">
                        {{ getTranslated(article.title, localeStore.locale) }}
                      </div>
                      <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                        {{ getTranslated(article.summary, localeStore.locale) }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Category -->
                <td class="py-3.5 px-4">
                  <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold bg-navy-50 text-navy-900 border border-navy-100">
                    {{ getTranslated(article.category?.name, localeStore.locale) }}
                  </span>
                </td>

                <!-- Views -->
                <td class="py-3.5 px-4 text-center font-mono text-slate-600 font-bold">
                  {{ article.views_count || 142 }}
                </td>

                <!-- Date -->
                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                  {{ formatDate(article.published_at) }}
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <router-link
                      :to="`/news/${article.slug}`"
                      target="_blank"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-navy-900 hover:bg-slate-100"
                      title="View Live"
                    >
                      <ExternalLink class="w-4 h-4" />
                    </router-link>

                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-navy-900 hover:bg-slate-100 transition-colors"
                      title="Edit Article"
                      @click="openEditNewsModal(article)"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>

                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                      title="Delete Article"
                      @click="handleDeleteNews(article.id)"
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
    </div>

    <!-- TAB 2: ANNOUNCEMENTS -->
    <div v-else class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="item in announcementsList"
          :key="item.id"
          class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between gap-2 mb-3">
              <span
                :class="[
                  'text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-md flex items-center gap-1',
                  item.is_urgent ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-700'
                ]"
              >
                <span v-if="item.is_urgent" class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
                {{ item.is_urgent ? $t('admin.cms.urgent') : $t('admin.cms.normal') }}
              </span>

              <span class="text-[11px] font-mono text-slate-400">
                {{ formatDate(item.created_at) }}
              </span>
            </div>

            <h3 class="font-bold text-navy-950 text-base mb-1.5">
              {{ getTranslated(item.title, localeStore.locale) }}
            </h3>

            <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
              {{ getTranslated(item.content, localeStore.locale) }}
            </p>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500 font-medium flex items-center gap-1">
              <Users class="w-3.5 h-3.5 text-slate-400" />
              <span>{{ getAudienceLabel(item.target_audience) }}</span>
            </span>

            <div class="flex items-center gap-2">
              <button
                type="button"
                class="text-navy-950 hover:text-navy-700 font-bold flex items-center gap-1 p-1 hover:bg-slate-100 rounded"
                @click="openEditAnnouncementModal(item)"
              >
                <Edit3 class="w-3.5 h-3.5" />
                <span>{{ localeStore.isRtl ? 'تعديل' : 'Edit' }}</span>
              </button>

              <button
                type="button"
                class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1 p-1 hover:bg-red-50 rounded"
                @click="handleDeleteAnnouncement(item.id)"
              >
                <Trash2 class="w-3.5 h-3.5" />
                <span>{{ $t('common.delete') || 'حذف' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 1: PUBLISH / EDIT NEWS -->
    <Modal
      v-model="isNewsModalOpen"
      :title="isEditingNews ? (localeStore.isRtl ? 'تعديل الخبر الصحفي' : 'Edit News Article') : $t('admin.cms.modalNewsTitle')"
      max-width="2xl"
      @close="isNewsModalOpen = false"
    >
      <form @submit.prevent="submitNewsForm" class="space-y-4 text-start">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Arabic Title -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelTitleAr') }} *
            </label>
            <input
              v-model="newsForm.title_ar"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
              placeholder="مثال: مؤتمر الذكاء الاصطناعي السنوي..."
            />
          </div>

          <!-- English Title -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelTitleEn') }} *
            </label>
            <input
              v-model="newsForm.title_en"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
              placeholder="e.g. Annual AI & Robotics Summit..."
            />
          </div>
        </div>

        <!-- Category & Featured Image URL -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelCategory') }}
            </label>
            <select
              v-model="newsForm.category"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="academic">الشؤون الأكاديمية (Academic)</option>
              <option value="scientific">البحث العلمي والابتكار (Research)</option>
              <option value="events">الفعاليات والمؤتمرات (Events)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelImage') }}
            </label>
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <img
                  :src="newsImagePreview || newsForm.featured_image || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=400&q=80'"
                  class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-xs shrink-0"
                />
                <div class="flex-1 min-w-0">
                  <input
                    ref="newsFileInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleNewsImageSelect"
                  />
                  <button
                    type="button"
                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 border border-slate-300"
                    @click="$refs.newsFileInput.click()"
                  >
                    <Upload class="w-3.5 h-3.5 text-gold-600" />
                    <span>{{ localeStore.isRtl ? 'اختيار صورة من جهازك' : 'Choose Image from Device' }}</span>
                  </button>
                  <div v-if="newsSelectedFile" class="text-[10px] text-emerald-700 font-mono mt-1 truncate">
                    ✓ {{ newsSelectedFile.name }} ({{ (newsSelectedFile.size / 1024).toFixed(0) }} KB)
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summaries -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelSummaryAr') }}
            </label>
            <textarea
              v-model="newsForm.summary_ar"
              rows="2"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="ملخص موجز باللغة العربية..."
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelSummaryEn') }}
            </label>
            <textarea
              v-model="newsForm.summary_en"
              rows="2"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="Brief summary in English..."
            ></textarea>
          </div>
        </div>

        <!-- Full Content Body -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            {{ $t('admin.cms.labelBodyAr') }} *
          </label>
          <textarea
            v-model="newsForm.content_ar"
            rows="4"
            required
            class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            placeholder="التفاصيل الكاملة للخبر باللغة العربية..."
          ></textarea>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200"
          @click="isNewsModalOpen = false"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md"
          @click="submitNewsForm"
        >
          {{ $t('admin.cms.publishNews') }}
        </button>
      </template>
    </Modal>

    <!-- MODAL 2: POST / EDIT ANNOUNCEMENT -->
    <Modal
      v-model="isAnnouncementModalOpen"
      :title="isEditingAnnouncement ? (localeStore.isRtl ? 'تعديل الإعلان الإداري' : 'Edit Announcement') : $t('admin.cms.modalAnnouncementTitle')"
      max-width="xl"
      @close="isAnnouncementModalOpen = false"
    >
      <form @submit.prevent="submitAnnouncementForm" class="space-y-4 text-start">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelTitleAr') }} *
            </label>
            <input
              v-model="announcementForm.title_ar"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="مثال: بدء تسجيل المقررات..."
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelTitleEn') }} *
            </label>
            <input
              v-model="announcementForm.title_en"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="e.g. Course Registration Open..."
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            {{ $t('admin.cms.labelBodyAr') }} *
          </label>
          <textarea
            v-model="announcementForm.content_ar"
            rows="3"
            required
            class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            placeholder="نص الإعلان الرسمي باللغة العربية..."
          ></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.cms.labelAudience') }}
            </label>
            <select
              v-model="announcementForm.target_audience"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="all">{{ $t('admin.cms.audAll') }}</option>
              <option value="students">{{ $t('admin.cms.audStudents') }}</option>
              <option value="faculty">{{ $t('admin.cms.audFaculty') }}</option>
              <option value="applicants">{{ $t('admin.cms.audApplicants') }}</option>
            </select>
          </div>

          <div class="pt-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="announcementForm.is_urgent"
                type="checkbox"
                class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300"
              />
              <span class="text-xs font-bold text-red-600 flex items-center gap-1">
                <AlertTriangle class="w-3.5 h-3.5" />
                {{ $t('admin.cms.labelPriorityUrgent') }}
              </span>
            </label>
          </div>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200"
          @click="isAnnouncementModalOpen = false"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md"
          @click="submitAnnouncementForm"
        >
          {{ $t('admin.cms.postAnnouncement') }}
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
import { formatStandardDate } from '../../utils/dateFormat'
import Modal from '../../components/ui/Modal.vue'
import {
  Newspaper,
  Megaphone,
  Plus,
  Search,
  ExternalLink,
  Edit3,
  Trash2,
  Users,
  AlertTriangle,
  Upload,
  X,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()

const activeTab = ref('news')
const newsList = ref([])
const announcementsList = ref([])
const isLoading = ref(true)

const newsSearch = ref('')
const newsCategoryFilter = ref('all')

const isNewsModalOpen = ref(false)
const isEditingNews = ref(false)
const editingNewsId = ref(null)

const isAnnouncementModalOpen = ref(false)
const isEditingAnnouncement = ref(false)
const editingAnnouncementId = ref(null)

const newsSelectedFile = ref(null)
const newsImagePreview = ref('')

const newsForm = reactive({
  title_ar: '',
  title_en: '',
  category: 'academic',
  featured_image: '',
  summary_ar: '',
  summary_en: '',
  content_ar: '',
  content_en: '',
  is_featured: false,
})

const compressImage = (file, maxWidth = 800, quality = 0.75) => {
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (event) => {
      const img = new Image()
      img.src = event.target.result
      img.onload = () => {
        const elem = document.createElement('canvas')
        let width = img.width
        let height = img.height
        if (width > maxWidth) {
          height = Math.round((height * maxWidth) / width)
          width = maxWidth
        }
        elem.width = width
        elem.height = height
        const ctx = elem.getContext('2d')
        ctx.drawImage(img, 0, 0, width, height)
        resolve(elem.toDataURL('image/jpeg', quality))
      }
    }
  })
}

const handleNewsImageSelect = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  newsSelectedFile.value = file
  const compressed = await compressImage(file, 800, 0.7)
  newsImagePreview.value = compressed
  newsForm.featured_image = compressed
}

const announcementForm = reactive({
  title_ar: '',
  title_en: '',
  content_ar: '',
  content_en: '',
  target_audience: 'all',
  is_urgent: false,
})

const filteredNews = computed(() => {
  let list = [...newsList.value]

  if (newsCategoryFilter.value !== 'all') {
    list = list.filter((n) => n.category?.slug === newsCategoryFilter.value)
  }

  if (newsSearch.value.trim()) {
    const q = newsSearch.value.trim().toLowerCase()
    list = list.filter((n) =>
      (n.title?.ar && n.title.ar.toLowerCase().includes(q)) ||
      (n.title?.en && n.title.en.toLowerCase().includes(q))
    )
  }

  return list
})

const formatDate = (isoStr) => formatStandardDate(isoStr, localeStore.locale)

const getAudienceLabel = (aud) => {
  if (aud === 'students') return t('admin.cms.audStudents')
  if (aud === 'faculty') return t('admin.cms.audFaculty')
  if (aud === 'applicants') return t('admin.cms.audApplicants')
  return t('admin.cms.audAll')
}

const loadData = async () => {
  isLoading.value = true
  try {
    const [news, announces] = await Promise.all([
      api.getNews(),
      api.getAnnouncements(),
    ])
    newsList.value = news || []
    announcementsList.value = announces || []
  } catch (e) {
    console.error('Failed to load CMS data', e)
  } finally {
    isLoading.value = false
  }
}

const openNewNewsModal = () => {
  isEditingNews.value = false
  editingNewsId.value = null
  newsSelectedFile.value = null
  newsImagePreview.value = ''
  newsForm.title_ar = ''
  newsForm.title_en = ''
  newsForm.summary_ar = ''
  newsForm.summary_en = ''
  newsForm.content_ar = ''
  newsForm.content_en = ''
  newsForm.featured_image = ''
  newsForm.category = 'academic'
  newsForm.is_featured = false
  isNewsModalOpen.value = true
}

const openEditNewsModal = (article) => {
  isEditingNews.value = true
  editingNewsId.value = article.id
  newsSelectedFile.value = null
  newsImagePreview.value = article.featured_image || ''
  newsForm.title_ar = article.title?.ar || article.title || ''
  newsForm.title_en = article.title?.en || article.title || ''
  newsForm.summary_ar = article.excerpt?.ar || article.excerpt || ''
  newsForm.summary_en = article.excerpt?.en || article.excerpt || ''
  newsForm.content_ar = article.body?.ar || article.body || ''
  newsForm.content_en = article.body?.en || article.body || ''
  newsForm.featured_image = article.featured_image || ''
  newsForm.category = article.category?.slug || article.category || 'academic'
  newsForm.is_featured = Boolean(article.is_featured)
  isNewsModalOpen.value = true
}

const openNewAnnouncementModal = () => {
  isEditingAnnouncement.value = false
  editingAnnouncementId.value = null
  announcementForm.title_ar = ''
  announcementForm.title_en = ''
  announcementForm.content_ar = ''
  announcementForm.content_en = ''
  announcementForm.target_audience = 'all'
  announcementForm.is_urgent = false
  isAnnouncementModalOpen.value = true
}

const openEditAnnouncementModal = (item) => {
  isEditingAnnouncement.value = true
  editingAnnouncementId.value = item.id
  announcementForm.title_ar = item.title?.ar || item.title || ''
  announcementForm.title_en = item.title?.en || item.title || ''
  announcementForm.content_ar = item.content?.ar || item.content || ''
  announcementForm.content_en = item.content?.en || item.content || ''
  announcementForm.target_audience = item.target_audience || 'all'
  announcementForm.is_urgent = Boolean(item.is_urgent)
  isAnnouncementModalOpen.value = true
}

const submitNewsForm = async () => {
  if (!newsForm.title_ar || !newsForm.content_ar) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }

  try {
    if (isEditingNews.value) {
      const updated = await api.updateNews(editingNewsId.value, { ...newsForm })
      const idx = newsList.value.findIndex((n) => n.id === editingNewsId.value)
      if (idx !== -1) {
        newsList.value[idx] = {
          ...newsList.value[idx],
          title: { ar: newsForm.title_ar, en: newsForm.title_en },
          excerpt: { ar: newsForm.summary_ar, en: newsForm.summary_en },
          body: { ar: newsForm.content_ar, en: newsForm.content_en },
          featured_image: newsForm.featured_image || newsList.value[idx].featured_image,
          category: { name: { ar: newsForm.category, en: newsForm.category }, slug: newsForm.category },
          is_featured: newsForm.is_featured,
          ...updated,
        }
      }
    } else {
      const created = await api.createNews({ ...newsForm })
      newsList.value.unshift(created)
    }
    isNewsModalOpen.value = false
  } catch (err) {
    alert('Failed to save news article')
  }
}

const submitAnnouncementForm = async () => {
  if (!announcementForm.title_ar || !announcementForm.content_ar) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }

  try {
    if (isEditingAnnouncement.value) {
      const updated = await api.updateAnnouncement(editingAnnouncementId.value, { ...announcementForm })
      const idx = announcementsList.value.findIndex((a) => a.id === editingAnnouncementId.value)
      if (idx !== -1) {
        announcementsList.value[idx] = {
          ...announcementsList.value[idx],
          title: { ar: announcementForm.title_ar, en: announcementForm.title_en },
          content: { ar: announcementForm.content_ar, en: announcementForm.content_en },
          target_audience: announcementForm.target_audience,
          is_urgent: announcementForm.is_urgent,
          ...updated,
        }
      }
    } else {
      const created = await api.createAnnouncement({ ...announcementForm })
      announcementsList.value.unshift(created)
    }
    isAnnouncementModalOpen.value = false
  } catch (err) {
    alert('Failed to save announcement')
  }
}

const handleDeleteNews = async (id) => {
  if (window.confirm(t('admin.cms.confirmDeleteNews'))) {
    await api.deleteNews(id)
    newsList.value = newsList.value.filter((n) => n.id !== id)
  }
}

const handleDeleteAnnouncement = async (id) => {
  if (window.confirm(t('admin.cms.confirmDeleteAnnouncement'))) {
    await api.deleteAnnouncement(id)
    announcementsList.value = announcementsList.value.filter((a) => a.id !== id)
  }
}

onMounted(() => {
  loadData()
})
</script>
