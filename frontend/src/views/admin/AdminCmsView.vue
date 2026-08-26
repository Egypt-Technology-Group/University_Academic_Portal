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
        <EmptyState
          v-if="filteredNews.length === 0"
          :title="localeStore.isRtl ? 'لا توجد أخبار صحفية منشورة' : 'No news articles found'"
          :description="localeStore.isRtl ? 'استخدم زر إضافة خبر جديد بالأعلى لنشر مقال إخباري جديد على البوابة.' : 'Use the action button above to publish your first news article.'"
        />
        <div v-else class="overflow-x-auto">
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
                <!-- Article Title & Image -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <img
                      :src="article.featured_image"
                      :alt="getTranslated(article.title, localeStore.locale)"
                      class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0"
                    />
                    <div class="max-w-md">
                      <div class="font-bold text-navy-950 text-sm line-clamp-1">
                        {{ getTranslated(article.title, localeStore.locale) }}
                      </div>
                      <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                        {{ getTranslated(article.excerpt, localeStore.locale) }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Category -->
                <td class="py-3.5 px-4">
                  <span class="inline-block text-[11px] font-bold text-navy-800 bg-navy-50 px-2 py-0.5 rounded border border-navy-100">
                    {{ getTranslated(article.category?.name, localeStore.locale) || $t('admin.cms.catAcademic') }}
                  </span>
                </td>

                <!-- Views Count -->
                <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                  {{ Number(article.views_count || 0).toLocaleString() }}
                </td>

                <!-- Date -->
                <td class="py-3.5 px-4 font-mono text-slate-500 whitespace-nowrap">
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
      <EmptyState
        v-if="announcementsList.length === 0"
        :title="localeStore.isRtl ? 'لا توجد تنويهات أو إعلانات إدارية' : 'No announcements posted'"
        :description="localeStore.isRtl ? 'استخدم زر إصدار تعميم جديد لنشر تنبيه عاجل للطلاب وأعضاء هيئة التدريس.' : 'Post urgent alerts to students or faculty members.'"
      />
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <EnterpriseFormField
            v-model="newsForm.title_ar"
            type="text"
            :label="$t('admin.cms.labelTitleAr')"
            required
            col-span="6"
            placeholder="مثال: مؤتمر الذكاء الاصطناعي السنوي..."
          />
          <EnterpriseFormField
            v-model="newsForm.title_en"
            type="text"
            :label="$t('admin.cms.labelTitleEn')"
            required
            col-span="6"
            placeholder="e.g. Annual AI & Robotics Summit..."
          />
          <EnterpriseFormField
            v-model="newsForm.category"
            type="select"
            :label="$t('admin.cms.labelCategory')"
            col-span="6"
            :options="[
              { label: 'الشؤون الأكاديمية (Academic)', value: 'academic' },
              { label: 'البحث العلمي والابتكار (Research)', value: 'scientific' },
              { label: 'الفعاليات والمؤتمرات (Events)', value: 'events' }
            ]"
          />
          <EnterpriseFormField
            type="image"
            :label="$t('admin.cms.labelImage')"
            col-span="6"
            :preview-url="newsImagePreview || newsForm.featured_image || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=400&q=80'"
            :button-text="localeStore.isRtl ? 'اختيار صورة من جهازك' : 'Choose Image from Device'"
            @file-selected="handleNewsImageSelect"
          />
          <EnterpriseFormField
            v-model="newsForm.summary_ar"
            type="textarea"
            :label="$t('admin.cms.labelSummaryAr')"
            :rows="2"
            col-span="6"
            placeholder="ملخص موجز باللغة العربية..."
          />
          <EnterpriseFormField
            v-model="newsForm.summary_en"
            type="textarea"
            :label="$t('admin.cms.labelSummaryEn')"
            :rows="2"
            col-span="6"
            placeholder="Brief summary in English..."
          />
          <EnterpriseFormField
            v-model="newsForm.content_ar"
            type="richtext"
            :label="$t('admin.cms.labelBodyAr')"
            required
            col-span="12"
            min-height="180px"
            placeholder="التفاصيل الكاملة للخبر باللغة العربية مع إمكانية التنسيق، العناوين، والقوائم..."
          />
          <EnterpriseFormField
            v-model="newsForm.content_en"
            type="richtext"
            :label="localeStore.isRtl ? 'نص الخبر والتفاصيل (إنجليزي)' : 'Full Article Body (English)'"
            col-span="12"
            min-height="180px"
            placeholder="Full article content in English with rich text formatting, headings, and lists..."
          />
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
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <EnterpriseFormField
            v-model="announcementForm.title_ar"
            type="text"
            :label="$t('admin.cms.labelTitleAr')"
            required
            col-span="6"
            placeholder="مثال: بدء تسجيل المقررات..."
          />
          <EnterpriseFormField
            v-model="announcementForm.title_en"
            type="text"
            :label="$t('admin.cms.labelTitleEn')"
            required
            col-span="6"
            placeholder="e.g. Course Registration Open..."
          />
          <EnterpriseFormField
            v-model="announcementForm.content_ar"
            type="textarea"
            :label="$t('admin.cms.labelBodyAr')"
            required
            :rows="3"
            col-span="12"
            placeholder="نص الإعلان الرسمي باللغة العربية..."
          />
          <EnterpriseFormField
            v-model="announcementForm.target_audience"
            type="select"
            :label="$t('admin.cms.labelAudience')"
            col-span="6"
            :options="[
              { label: $t('admin.cms.audAll'), value: 'all' },
              { label: $t('admin.cms.audStudents'), value: 'students' },
              { label: $t('admin.cms.audFaculty'), value: 'faculty' },
              { label: $t('admin.cms.audApplicants'), value: 'applicants' }
            ]"
          />
          <EnterpriseFormField
            v-model="announcementForm.is_urgent"
            type="checkbox"
            :label="$t('admin.cms.labelPriorityUrgent')"
            col-span="6"
          />
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
import EmptyState from '../../components/ui/EmptyState.vue'
import EnterpriseFormField from '../../components/ui/EnterpriseFormField.vue'
import { useDialog } from '../../composables/useDialog'
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
const dialog = useDialog()

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
    await dialog.alert({
      title: localeStore.isRtl ? 'حقول إلزامية' : 'Required Fields',
      message: localeStore.isRtl ? 'يرجى إدخال عنوان ومحتوى الخبر باللغة العربية على الأقل.' : 'Please enter news title and content.',
      variant: 'warning',
    })
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
    await dialog.alert({
      title: localeStore.isRtl ? 'خطأ في الحفظ' : 'Error Saving',
      message: localeStore.isRtl ? 'تعذر حفظ المقال الخبري، يرجى المحاولة لاحقاً.' : 'Failed to save news article.',
      variant: 'danger',
    })
  }
}

const submitAnnouncementForm = async () => {
  if (!announcementForm.title_ar || !announcementForm.content_ar) {
    await dialog.alert({
      title: localeStore.isRtl ? 'حقول إلزامية' : 'Required Fields',
      message: localeStore.isRtl ? 'يرجى إدخال عنوان ونص الإعلان للمتابعة.' : 'Please enter announcement title and content.',
      variant: 'warning',
    })
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
    await dialog.alert({
      title: localeStore.isRtl ? 'خطأ في الحفظ' : 'Error Saving',
      message: localeStore.isRtl ? 'تعذر حفظ الإعلان، يرجى المحاولة لاحقاً.' : 'Failed to save announcement.',
      variant: 'danger',
    })
  }
}

const handleDeleteNews = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف الخبر' : 'Delete News Article',
    message: t('admin.cms.confirmDeleteNews') || (localeStore.isRtl ? 'هل أنت متأكد من حذف هذا الخبر؟' : 'Are you sure you want to delete this news article?'),
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    await api.deleteNews(id)
    newsList.value = newsList.value.filter((n) => n.id !== id)
  }
}

const handleDeleteAnnouncement = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف الإعلان' : 'Delete Announcement',
    message: t('admin.cms.confirmDeleteAnnouncement') || (localeStore.isRtl ? 'هل أنت متأكد من حذف هذا الإعلان؟' : 'Are you sure you want to delete this announcement?'),
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    await api.deleteAnnouncement(id)
    announcementsList.value = announcementsList.value.filter((a) => a.id !== id)
  }
}

onMounted(() => {
  loadData()
})
</script>
