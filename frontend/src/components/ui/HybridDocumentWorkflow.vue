<template>
  <div class="space-y-4">
    <!-- Mode Switcher (If configurable mode) -->
    <div v-if="mode === 'both'" class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl border border-slate-200 w-fit">
      <button
        type="button"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5',
          activeTab === 'structured' ? 'bg-white text-navy-950 shadow-xs' : 'text-slate-600 hover:text-navy-950'
        ]"
        @click="activeTab = 'structured'"
      >
        <FileText class="w-3.5 h-3.5" />
        <span>{{ localeStore.isRtl ? 'البيانات المنظمة وتوليد الوثيقة' : 'Structured Data (Auto-Generate)' }}</span>
      </button>

      <button
        type="button"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5',
          activeTab === 'upload' ? 'bg-white text-navy-950 shadow-xs' : 'text-slate-600 hover:text-navy-950'
        ]"
        @click="activeTab = 'upload'"
      >
        <Upload class="w-3.5 h-3.5" />
        <span>{{ localeStore.isRtl ? 'رفع وثيقة رقمية جاهزة' : 'Upload Digital Document' }}</span>
      </button>
    </div>

    <!-- Structured Form Generation Mode -->
    <div v-if="activeTab === 'structured'" class="space-y-4">
      <slot name="structured-form">
        <EnterpriseFormEngine
          v-if="schema"
          v-model="modelValue.structuredData"
          :schema="schema"
          :errors="errors"
          :show-actions="false"
        />
      </slot>

      <!-- Live Generated Preview Ribbon -->
      <div v-if="showLivePreview" class="p-4 rounded-2xl bg-navy-50/70 border border-navy-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-navy-900 text-white flex items-center justify-center font-bold text-xs">
            📄
          </div>
          <div>
            <div class="text-xs font-black text-navy-950">
              {{ localeStore.isRtl ? 'توليد الوثيقة الرسمية آلياً' : 'Automated Credential Rendering Engine' }}
            </div>
            <div class="text-[11px] text-slate-500 font-medium">
              {{ localeStore.isRtl ? 'سيتم توليد وثيقة PDF معتمدة مع باركود التحقق الرقمي وختم الجامعة.' : 'System will generate a cryptographically signed PDF with verification QR.' }}
            </div>
          </div>
        </div>
        <button
          type="button"
          class="px-3 py-1.5 rounded-xl bg-navy-900 hover:bg-gold-500 hover:text-navy-950 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
          @click="$emit('preview-generated', modelValue.structuredData)"
        >
          {{ localeStore.isRtl ? 'معاينة النموذج' : 'Preview Document' }}
        </button>
      </div>
    </div>

    <!-- Direct File / Document Upload Mode -->
    <div v-if="activeTab === 'upload'" class="space-y-4">
      <div
        class="border-2 border-dashed rounded-2xl p-6 text-center transition-all cursor-pointer relative"
        :class="[
          selectedFile || existingFileUrl
            ? 'border-emerald-500 bg-emerald-50/30'
            : isDragging
              ? 'border-gold-500 bg-gold-50/40'
              : 'border-slate-300 bg-slate-50 hover:bg-slate-100/80 hover:border-navy-900'
        ]"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="$refs.fileInput.click()"
      >
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          :accept="accept"
          @change="handleFileChange"
        />

        <!-- Selected / Existing File Preview -->
        <div v-if="selectedFile || existingFileUrl" class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3.5 text-start">
            <div class="w-12 h-12 rounded-xl bg-navy-950 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
              {{ fileTypeLabel }}
            </div>
            <div>
              <div class="font-bold text-navy-950 text-xs sm:text-sm truncate max-w-sm">
                {{ selectedFile ? selectedFile.name : (existingFileName || 'Document Asset') }}
              </div>
              <div class="text-[11px] text-emerald-700 font-medium flex items-center gap-2 mt-0.5">
                <span class="font-mono">{{ fileSizeLabel }}</span>
                <span>•</span>
                <span>{{ localeStore.isRtl ? 'جاهز للاعتماد والتخزين' : 'Verified & Ready' }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2" @click.stop>
            <a
              v-if="existingFileUrl"
              :href="existingFileUrl"
              target="_blank"
              class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-navy-950 font-bold text-xs transition-colors"
            >
              {{ localeStore.isRtl ? 'عرض الملف' : 'View' }}
            </a>
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
              @click="$refs.fileInput.click()"
            >
              {{ localeStore.isRtl ? 'استبدال' : 'Replace' }}
            </button>
            <button
              type="button"
              class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
              @click="clearFile"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Empty Dropzone State -->
        <div v-else class="space-y-2 py-4">
          <div class="w-12 h-12 rounded-2xl bg-navy-50 text-navy-900 flex items-center justify-center mx-auto mb-2">
            <Upload class="w-6 h-6 text-navy-800" />
          </div>
          <div class="font-bold text-navy-950 text-sm">
            {{ localeStore.isRtl ? 'انقر لاختيار ملف أو اسحب الملف وأفلته هنا' : 'Click to browse device or drag & drop document here' }}
          </div>
          <p class="text-xs text-slate-400">
            {{ supportedFormatsText || 'PDF, Word, Excel, PowerPoint, JPEG, PNG (Max: 50MB)' }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import EnterpriseFormEngine from './EnterpriseFormEngine.vue'
import { FileText, Upload, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ structuredData: {}, file: null, mode: 'structured' })
  },
  mode: {
    type: String,
    default: 'both' // 'structured' | 'upload' | 'both'
  },
  schema: {
    type: Object,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  accept: {
    type: String,
    default: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png'
  },
  existingFileUrl: {
    type: String,
    default: ''
  },
  existingFileName: {
    type: String,
    default: ''
  },
  showLivePreview: {
    type: Boolean,
    default: true
  },
  supportedFormatsText: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'file-selected', 'file-removed', 'preview-generated'])

const localeStore = useLocaleStore()
const activeTab = ref(props.mode === 'upload' ? 'upload' : 'structured')
const isDragging = ref(false)
const selectedFile = ref(null)

watch(() => props.mode, (newMode) => {
  if (newMode === 'upload') activeTab.value = 'upload'
  else if (newMode === 'structured') activeTab.value = 'structured'
})

const fileTypeLabel = computed(() => {
  if (selectedFile.value) {
    const ext = selectedFile.value.name.split('.').pop()?.toUpperCase()
    return ext || 'FILE'
  }
  if (props.existingFileName) {
    const ext = props.existingFileName.split('.').pop()?.toUpperCase()
    return ext || 'PDF'
  }
  return 'PDF'
})

const fileSizeLabel = computed(() => {
  if (selectedFile.value) {
    return (selectedFile.value.size / (1024 * 1024)).toFixed(2) + ' MB'
  }
  return 'Uploaded Document'
})

const handleFileChange = (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  selectedFile.value = file
  emit('file-selected', file)
  emit('update:modelValue', {
    ...props.modelValue,
    file,
    mode: activeTab.value
  })
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files?.[0]
  if (!file) return
  selectedFile.value = file
  emit('file-selected', file)
  emit('update:modelValue', {
    ...props.modelValue,
    file,
    mode: activeTab.value
  })
}

const clearFile = () => {
  selectedFile.value = null
  emit('file-removed')
  emit('update:modelValue', {
    ...props.modelValue,
    file: null
  })
}
</script>
