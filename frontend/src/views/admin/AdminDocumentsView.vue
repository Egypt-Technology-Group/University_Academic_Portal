<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.documents.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ localeStore.isRtl ? 'إدارة اللوائح والقرارات الأكاديمية والجداول والوثائق المعتمدة والتحكم في الإصدارات والصلاحيات' : 'Manage academic regulations, bylaws, official schedules, versions, and publication access controls' }}
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition-colors cursor-pointer"
          @click="loadDocs"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" />
          <span>{{ $t('admin.admissions.refresh') }}</span>
        </button>

        <button
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewDocModal"
        >
          <Upload class="w-4 h-4 text-gold-400" />
          <span>{{ $t('admin.documents.uploadDoc') }}</span>
        </button>
      </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
      <!-- Search Input -->
      <div class="relative flex-1 w-full">
        <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('admin.documents.searchPlaceholder')"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
        />
        <button
          v-if="searchQuery"
          type="button"
          class="absolute inset-y-0 end-3 flex items-center text-slate-400 hover:text-slate-600"
          @click="searchQuery = ''"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Category Filter -->
      <div class="w-full md:w-52 shrink-0">
        <select
          v-model="categoryFilter"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
        >
          <option value="all">{{ $t('admin.documents.allCategories') }}</option>
          <option value="bylaws">{{ localeStore.isRtl ? 'اللوائح الأكاديمية والقرارات' : 'Academic Bylaws & Regulations' }}</option>
          <option value="schedules">{{ $t('admin.documents.catSchedules') }}</option>
          <option value="forms">{{ $t('admin.documents.catForms') }}</option>
          <option value="guides">{{ $t('admin.documents.catGuides') }}</option>
          <option value="regulations">{{ localeStore.isRtl ? 'القوانين والسياسات العامة' : 'Institutional Policies' }}</option>
        </select>
      </div>

      <!-- Audience Filter -->
      <div class="w-full md:w-44 shrink-0">
        <select
          v-model="audienceFilter"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
        >
          <option value="all">{{ localeStore.isRtl ? 'كافة الفئات المستهدفة' : 'All Audiences' }}</option>
          <option value="students">{{ localeStore.isRtl ? 'الطلاب فقط' : 'Students Only' }}</option>
          <option value="faculty">{{ localeStore.isRtl ? 'أعضاء هيئة التدريس' : 'Faculty & Staff' }}</option>
        </select>
      </div>

      <!-- Status Filter -->
      <div class="w-full md:w-40 shrink-0">
        <select
          v-model="statusFilter"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
        >
          <option value="all">{{ localeStore.isRtl ? 'كل الحالات' : 'All Statuses' }}</option>
          <option value="published">{{ localeStore.isRtl ? 'منشور ونشط' : 'Published' }}</option>
          <option value="draft">{{ localeStore.isRtl ? 'مسودة' : 'Draft' }}</option>
          <option value="archived">{{ localeStore.isRtl ? 'مؤرشف' : 'Archived' }}</option>
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
        <p class="text-xs text-slate-400 mt-1">
          {{ localeStore.isRtl ? 'لا توجد لوائح أو مستندات تطابق الفلاتر المحددة حالياً.' : 'No regulations or files match the current query.' }}
        </p>
      </div>

      <!-- Documents Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">{{ $t('admin.documents.colDocTitle') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.documents.colCategory') }}</th>
              <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الإصدار والحالة' : 'Version & Status' }}</th>
              <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الجمهور المستهدف' : 'Audience' }}</th>
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
                  <div class="w-10 h-10 rounded-xl bg-navy-50 text-navy-900 flex items-center justify-center font-black text-xs shrink-0 shadow-xs border border-navy-100">
                    {{ doc.file_type || 'PDF' }}
                  </div>
                  <div>
                    <div class="font-bold text-navy-950 text-sm flex items-center gap-2">
                      <span>{{ getTranslated(doc.title, localeStore.locale) }}</span>
                      <span v-if="doc.is_featured" class="text-[10px] bg-gold-100 text-gold-900 px-1.5 py-0.5 rounded font-black">
                        ★ {{ localeStore.isRtl ? 'مميز' : 'Featured' }}
                      </span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-md">
                      {{ getTranslated(doc.description, localeStore.locale) || (localeStore.isRtl ? 'وثيقة تنظيمية معتمدة' : 'Official institutional regulatory file') }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1 font-mono">
                      <span>Size: {{ doc.file_size || '2.4 MB' }}</span>
                      <span class="mx-1.5">•</span>
                      <span>Path: {{ doc.file_path }}</span>
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

              <!-- Version & Status -->
              <td class="py-3.5 px-4 text-center">
                <div class="inline-flex flex-col items-center gap-1">
                  <span class="font-mono font-bold text-[11px] text-navy-900 bg-slate-100 px-2 py-0.5 rounded">
                    v{{ doc.version || '1.0' }}
                  </span>
                  <span
                    :class="[
                      'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase',
                      doc.is_archived || doc.status === 'archived' ? 'bg-slate-200 text-slate-700' :
                      doc.status === 'draft' ? 'bg-amber-100 text-amber-800' :
                      'bg-emerald-100 text-emerald-800'
                    ]"
                  >
                    {{ doc.is_archived ? 'Archived' : (doc.status || 'Published') }}
                  </span>
                </div>
              </td>

              <!-- Target Audience -->
              <td class="py-3.5 px-4 text-center">
                <span
                  :class="[
                    'text-[10px] font-bold px-2.5 py-1 rounded-lg',
                    doc.target_audience === 'students' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                    doc.target_audience === 'faculty' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                    'bg-slate-100 text-slate-700'
                  ]"
                >
                  {{ doc.target_audience === 'students' ? (localeStore.isRtl ? 'الطلاب' : 'Students') :
                     doc.target_audience === 'faculty' ? (localeStore.isRtl ? 'أعضاء التدريس' : 'Faculty') :
                     (localeStore.isRtl ? 'الجميع' : 'Public') }}
                </span>
              </td>

              <!-- Downloads -->
              <td class="py-3.5 px-4 text-center font-mono font-bold text-navy-950">
                <div class="flex items-center justify-center gap-1">
                  <Download class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ (doc.download_count || 0).toLocaleString() }}</span>
                </div>
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Download / Preview -->
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-navy-900 hover:bg-slate-100 transition-colors cursor-pointer"
                    title="Download"
                    @click="handleDownload(doc)"
                  >
                    <Download class="w-4 h-4" />
                  </button>

                  <!-- Edit -->
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                    title="Edit Metadata & Version"
                    @click="openEditDocModal(doc)"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <!-- Toggle Archive -->
                  <button
                    type="button"
                    :class="[
                      'p-1.5 rounded-lg transition-colors cursor-pointer',
                      doc.is_archived ? 'text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-slate-700 hover:bg-slate-100'
                    ]"
                    :title="doc.is_archived ? 'Unarchive' : 'Archive Document'"
                    @click="handleToggleArchive(doc)"
                  >
                    <Archive class="w-4 h-4" />
                  </button>

                  <!-- Delete -->
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
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

    <!-- MODAL: UPLOAD / EDIT REGULATION DOCUMENT -->
    <Modal
      v-model="isModalOpen"
      :title="editingDocId ? (localeStore.isRtl ? 'تعديل وثيقة وإصدار لائحة' : 'Edit Document & Version') : $t('admin.documents.modalTitle')"
      size="xl"
      @close="isModalOpen = false"
    >
      <form @submit.prevent="submitForm" class="space-y-4 text-start text-xs">
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
              placeholder="مثال: اللائحة الداخلية المعتمدة لكلية الحاسبات والذكاء الاصطناعي..."
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
              placeholder="e.g. Approved Academic Bylaws for Faculty of CS & AI..."
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelCategory') }}
            </label>
            <select
              v-model="form.category"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="bylaws">{{ localeStore.isRtl ? 'لوائح وقرارات وزارية' : 'Bylaws & Ministerial Decrees' }}</option>
              <option value="schedules">{{ $t('admin.documents.catSchedules') }}</option>
              <option value="forms">{{ $t('admin.documents.catForms') }}</option>
              <option value="guides">{{ $t('admin.documents.catGuides') }}</option>
              <option value="regulations">{{ localeStore.isRtl ? 'سياسات عامة وتنظيمية' : 'Institutional Policies' }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ localeStore.isRtl ? 'رقم الإصدار' : 'Version Number' }}
            </label>
            <input
              v-model="form.version"
              type="text"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm font-mono focus:border-navy-900"
              placeholder="1.0"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ localeStore.isRtl ? 'حالة الوثيقة' : 'Document Status' }}
            </label>
            <select
              v-model="form.status"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="published">{{ localeStore.isRtl ? 'منشور ومعتمد' : 'Published' }}</option>
              <option value="draft">{{ localeStore.isRtl ? 'مسودة قيد المراجعة' : 'Draft' }}</option>
              <option value="archived">{{ localeStore.isRtl ? 'مؤرشف' : 'Archived' }}</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ localeStore.isRtl ? 'الجمهور المستهدف' : 'Target Audience' }}
            </label>
            <select
              v-model="form.target_audience"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            >
              <option value="all">{{ localeStore.isRtl ? 'كافة الطلاب والمجتمع' : 'Public (All)' }}</option>
              <option value="students">{{ localeStore.isRtl ? 'الطلاب فقط' : 'Students Only' }}</option>
              <option value="faculty">{{ localeStore.isRtl ? 'أعضاء هيئة التدريس والإداريين' : 'Faculty & Staff' }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelSize') }}
            </label>
            <input
              v-model="form.file_size"
              type="text"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="2.4 MB"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ localeStore.isRtl ? 'تاريخ السريان والاعتماد' : 'Effective Date' }}
            </label>
            <input
              v-model="form.effective_date"
              type="date"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.documents.labelDescriptionAr') }}
            </label>
            <textarea
              v-model="form.description_ar"
              rows="2"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="نبذة عن اللائحة والقرارات المنظمة..."
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Description (English)
            </label>
            <textarea
              v-model="form.description_en"
              rows="2"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="Summary of regulation rules and clauses..."
            ></textarea>
          </div>
        </div>

        <!-- Interactive Device File Picker Dropzone -->
        <div class="space-y-2">
          <label class="block text-xs font-bold text-slate-700">
            {{ localeStore.isRtl ? 'الملف الرقمي (اختر أو اسحب ملف من جهازك)' : 'Digital File (Upload directly from device)' }} *
          </label>
          <div
            class="border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer relative"
            :class="[
              selectedLocalFile ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-300 bg-slate-50 hover:bg-slate-100/80 hover:border-navy-900'
            ]"
            @dragover.prevent
            @drop.prevent="handleFileDrop"
            @click="$refs.deviceFileInput.click()"
          >
            <input
              ref="deviceFileInput"
              type="file"
              class="hidden"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png"
              @change="handleDeviceFileSelect"
            />
            <div v-if="selectedLocalFile" class="flex items-center justify-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                📄
              </div>
              <div class="text-start">
                <div class="font-bold text-navy-950 text-xs truncate max-w-xs">{{ selectedLocalFile.name }}</div>
                <div class="text-[10px] text-emerald-700 font-mono">
                  {{ (selectedLocalFile.size / (1024 * 1024)).toFixed(2) }} MB • {{ selectedLocalFile.type || 'Document' }}
                </div>
              </div>
              <button
                type="button"
                class="p-1 rounded-lg text-slate-400 hover:text-red-600 hover:bg-white"
                @click.stop="clearSelectedLocalFile"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
            <div v-else class="space-y-1 text-slate-500 py-2">
              <Upload class="w-7 h-7 mx-auto text-slate-400 mb-1" />
              <div class="font-bold text-navy-950 text-xs">
                {{ localeStore.isRtl ? 'انقر لاختيار ملف من جهازك أو اسحبه هنا' : 'Click to browse device or drop file here' }}
              </div>
              <p class="text-[10px] text-slate-400">PDF, Word, Excel, PowerPoint, ZIP (Max 50MB)</p>
            </div>
          </div>
        </div>

        <!-- Upload Progress Indicator (If uploading) -->
        <div v-if="isUploadingFile" class="space-y-1.5 p-3 rounded-xl bg-navy-50 border border-navy-100">
          <div class="flex items-center justify-between text-[11px] font-bold text-navy-950">
            <span>{{ localeStore.isRtl ? 'جاري رفع الملف إلى الخادم الأكاديمي المشفر...' : 'Uploading secure asset to server...' }}</span>
            <span class="font-mono">{{ uploadProgress }}%</span>
          </div>
          <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
            <div
              class="h-full bg-gold-500 transition-all duration-200"
              :style="{ width: uploadProgress + '%' }"
            ></div>
          </div>
        </div>

        <!-- File Path & Featured Flag -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
          <div class="flex-1 w-full">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              {{ localeStore.isRtl ? 'مسار أو رابط المستند الرقمي (تلقائي)' : 'File Asset URL / Path (Auto-filled)' }}
            </label>
            <input
              v-model="form.file_path"
              type="text"
              class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-mono"
              placeholder="/downloads/bylaws_2025.pdf"
            />
          </div>

          <label class="flex items-center gap-2 cursor-pointer mt-2 sm:mt-4 shrink-0">
            <input
              type="checkbox"
              v-model="form.is_featured"
              class="rounded text-navy-900 focus:ring-navy-900 cursor-pointer"
            />
            <span class="font-bold text-slate-800 text-xs">{{ localeStore.isRtl ? 'تثبيت كوثيقة مميزة بالصفحة الرئيسية' : 'Feature on Public Regulations Center' }}</span>
          </label>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors"
          @click="isModalOpen = false"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="submitForm"
        >
          {{ editingDocId ? (localeStore.isRtl ? 'حفظ التعديلات' : 'Save Changes') : $t('admin.documents.uploadDoc') }}
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
  RefreshCw,
  Edit3,
  Archive,
  X,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()

const documentsList = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const categoryFilter = ref('all')
const audienceFilter = ref('all')
const statusFilter = ref('all')
const isModalOpen = ref(false)
const editingDocId = ref(null)

const form = reactive({
  title_ar: '',
  title_en: '',
  description_ar: '',
  description_en: '',
  category: 'bylaws',
  version: '1.0',
  status: 'published',
  target_audience: 'all',
  file_size: '2.4 MB',
  file_path: '/downloads/academic_bylaws_2025.pdf',
  is_featured: false,
  effective_date: new Date().toISOString().substring(0, 10),
})

const filteredDocuments = computed(() => {
  let list = [...documentsList.value]

  if (categoryFilter.value !== 'all') {
    list = list.filter((d) => d.category === categoryFilter.value)
  }

  if (audienceFilter.value !== 'all') {
    list = list.filter((d) => d.target_audience === audienceFilter.value || !d.target_audience || d.target_audience === 'all')
  }

  if (statusFilter.value !== 'all') {
    list = list.filter((d) => {
      if (statusFilter.value === 'archived') return d.is_archived || d.status === 'archived'
      return d.status === statusFilter.value
    })
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase()
    list = list.filter((d) => {
      const titleAr = (typeof d.title === 'object' ? d.title?.ar : d.title) || ''
      const titleEn = (typeof d.title === 'object' ? d.title?.en : d.title) || ''
      return titleAr.toLowerCase().includes(q) || titleEn.toLowerCase().includes(q)
    })
  }

  return list
})

const getCategoryLabel = (cat) => {
  if (cat === 'bylaws' || cat === 'regulations') return localeStore.isRtl ? 'لوائح وقرارات' : 'Bylaws & Regulations'
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

const selectedLocalFile = ref(null)
const isUploadingFile = ref(false)
const uploadProgress = ref(0)
const deviceFileInput = ref(null)

const handleDeviceFileSelect = (e) => {
  const file = e.target.files[0]
  if (file) {
    processLocalFile(file)
  }
}

const handleFileDrop = (e) => {
  const file = e.dataTransfer.files[0]
  if (file) {
    processLocalFile(file)
  }
}

const processLocalFile = (file) => {
  if (file.size > 50 * 1024 * 1024) {
    alert('حجم الملف يتجاوز الحد المسموح (50 ميجابايت).')
    return
  }
  selectedLocalFile.value = file
  form.file_path = `/storage/documents_repo/${file.name}`
  const bytes = file.size
  form.file_size = bytes >= 1048576 
    ? (bytes / 1048576).toFixed(1) + ' MB' 
    : (bytes / 1024).toFixed(0) + ' KB'
  
  // Auto-fill titles if empty
  const rawName = file.name.replace(/\.[^/.]+$/, "")
  if (!form.title_ar) form.title_ar = rawName
  if (!form.title_en) form.title_en = rawName
}

const clearSelectedLocalFile = () => {
  selectedLocalFile.value = null
  if (deviceFileInput.value) deviceFileInput.value.value = ''
}

const openNewDocModal = () => {
  editingDocId.value = null
  clearSelectedLocalFile()
  form.title_ar = ''
  form.title_en = ''
  form.description_ar = ''
  form.description_en = ''
  form.category = 'bylaws'
  form.version = '1.0'
  form.status = 'published'
  form.target_audience = 'all'
  form.file_size = '2.4 MB'
  form.file_path = '/downloads/academic_bylaws.pdf'
  form.is_featured = false
  form.effective_date = new Date().toISOString().substring(0, 10)
  uploadProgress.value = 0
  isUploadingFile.value = false
  isModalOpen.value = true
}

const openEditDocModal = (doc) => {
  editingDocId.value = doc.id
  clearSelectedLocalFile()
  form.title_ar = typeof doc.title === 'object' ? doc.title.ar : doc.title
  form.title_en = typeof doc.title === 'object' ? doc.title.en : doc.title
  form.description_ar = typeof doc.description === 'object' ? doc.description?.ar : (doc.description || '')
  form.description_en = typeof doc.description === 'object' ? doc.description?.en : (doc.description || '')
  form.category = doc.category || 'bylaws'
  form.version = doc.version || '1.0'
  form.status = doc.status || 'published'
  form.target_audience = doc.target_audience || 'all'
  form.file_size = doc.file_size || '2.4 MB'
  form.file_path = doc.file_path || '/downloads/doc.pdf'
  form.is_featured = Boolean(doc.is_featured)
  form.effective_date = doc.effective_date ? doc.effective_date.substring(0, 10) : new Date().toISOString().substring(0, 10)
  uploadProgress.value = 0
  isUploadingFile.value = false
  isModalOpen.value = true
}

const submitForm = async () => {
  if (!form.title_ar || !form.title_en) {
    alert('يرجى إدخال عنوان الوثيقة باللغتين العربية والإنجليزية')
    return
  }

  isUploadingFile.value = true
  uploadProgress.value = 10

  try {
    const formData = new FormData()
    formData.append('title_ar', form.title_ar)
    formData.append('title_en', form.title_en)
    if (form.description_ar) formData.append('description_ar', form.description_ar)
    if (form.description_en) formData.append('description_en', form.description_en)
    formData.append('category', form.category)
    formData.append('version', form.version)
    formData.append('status', form.status)
    formData.append('target_audience', form.target_audience)
    formData.append('is_featured', form.is_featured ? 1 : 0)
    formData.append('effective_date', form.effective_date)
    if (form.file_path) formData.append('file_path', form.file_path)
    if (form.file_size) formData.append('file_size', form.file_size)

    if (selectedLocalFile.value) {
      formData.append('file', selectedLocalFile.value)
    }

    if (editingDocId.value) {
      const updated = await api.updateDocument(editingDocId.value, formData, (percent) => {
        uploadProgress.value = percent
      })
      const idx = documentsList.value.findIndex((d) => d.id === editingDocId.value)
      if (idx !== -1) {
        documentsList.value[idx] = { ...documentsList.value[idx], ...updated }
      }
    } else {
      const created = await api.createDocument(formData, (percent) => {
        uploadProgress.value = percent
      })
      documentsList.value.unshift(created)
    }
    isModalOpen.value = false
    clearSelectedLocalFile()
  } catch (err) {
    alert('Failed to save document')
  } finally {
    isUploadingFile.value = false
    uploadProgress.value = 0
  }
}

const handleToggleArchive = async (doc) => {
  try {
    const updated = await api.toggleArchiveDocument(doc.id)
    doc.is_archived = !doc.is_archived
    doc.status = doc.is_archived ? 'archived' : 'published'
  } catch (err) {
    console.error('Failed to toggle archive', err)
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

