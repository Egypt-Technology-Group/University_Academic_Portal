<template>
  <div class="rich-text-editor-wrapper space-y-1.5" :dir="editorDir">
    <!-- Label -->
    <div v-if="label" class="flex items-center justify-between">
      <label :for="editorId" class="block text-xs font-bold text-slate-700">
        {{ label }}
        <span v-if="required" class="text-rose-500 font-black ms-0.5">*</span>
      </label>
      <span v-if="hint" class="text-[11px] text-slate-400 font-medium">
        {{ hint }}
      </span>
    </div>

    <!-- Main Editor Box -->
    <div
      class="border rounded-2xl overflow-hidden transition-all bg-white shadow-2xs"
      :class="[
        errorMessage
          ? 'border-rose-300 ring-1 ring-rose-200'
          : isFocused
            ? 'border-navy-900 ring-1 ring-navy-900 shadow-sm'
            : 'border-slate-300 hover:border-slate-400',
        disabled ? 'opacity-60 bg-slate-50 pointer-events-none' : ''
      ]"
    >
      <!-- Toolbar -->
      <div
        v-if="!disabled"
        class="bg-slate-50/90 border-b border-slate-200 p-1.5 flex flex-wrap items-center gap-1.5 text-slate-700 select-none text-xs"
        @mousedown="handleToolbarMouseDown"
      >
        <!-- Formatting: Bold, Italic, Underline -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': activeFormats.bold }"
          :title="localeStore.isRtl ? 'عريض (Bold)' : 'Bold'"
          @click="execCmd('bold')"
        >
          <Bold class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': activeFormats.italic }"
          :title="localeStore.isRtl ? 'مائل (Italic)' : 'Italic'"
          @click="execCmd('italic')"
        >
          <Italic class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': activeFormats.underline }"
          :title="localeStore.isRtl ? 'تسطير (Underline)' : 'Underline'"
          @click="execCmd('underline')"
        >
          <Underline class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Headings & Paragraph Custom Segmented Buttons -->
        <div class="flex items-center bg-white border border-slate-200 rounded-lg p-0.5 shadow-2xs">
          <button
            type="button"
            class="px-2 py-1 rounded text-xs font-bold transition-colors"
            :class="currentHeading === 'p' ? 'bg-navy-950 text-white' : 'text-slate-600 hover:bg-slate-100'"
            @click="applyBlock('p')"
          >
            P
          </button>
          <button
            type="button"
            class="px-2 py-1 rounded text-xs font-bold transition-colors"
            :class="currentHeading === 'h2' ? 'bg-navy-950 text-white' : 'text-slate-600 hover:bg-slate-100'"
            @click="applyBlock('h2')"
          >
            H2
          </button>
          <button
            type="button"
            class="px-2 py-1 rounded text-xs font-bold transition-colors"
            :class="currentHeading === 'h3' ? 'bg-navy-950 text-white' : 'text-slate-600 hover:bg-slate-100'"
            @click="applyBlock('h3')"
          >
            H3
          </button>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Preset Color Palette Palette Chips -->
        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg px-2 py-1 shadow-2xs">
          <button
            v-for="c in colorPalette"
            :key="c.hex"
            type="button"
            class="w-4 h-4 rounded-full border border-slate-300 hover:scale-125 transition-transform"
            :style="{ backgroundColor: c.hex }"
            :title="c.name"
            @click="applyColor(c.hex)"
          ></button>
          <div class="relative flex items-center ms-1">
            <input
              type="color"
              class="w-4 h-4 rounded cursor-pointer border-0 bg-transparent p-0"
              :value="currentColor"
              :title="localeStore.isRtl ? 'لون مخصص' : 'Custom Color'"
              @change="handleCustomColor"
            />
          </div>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Lists: Unordered, Ordered -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': activeFormats.unorderedList }"
          :title="localeStore.isRtl ? 'قائمة نقطية (Bullet List)' : 'Bullet List'"
          @click="applyList('unordered')"
        >
          <List class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': activeFormats.orderedList }"
          :title="localeStore.isRtl ? 'قائمة مرقمة (Numbered List)' : 'Numbered List'"
          @click="applyList('ordered')"
        >
          <ListOrdered class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Text Alignment: Right, Center, Left -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :title="localeStore.isRtl ? 'محاذاة لليمين' : 'Align Right'"
          @click="applyAlign('right')"
        >
          <AlignRight class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :title="localeStore.isRtl ? 'محاذاة للوسط' : 'Align Center'"
          @click="applyAlign('center')"
        >
          <AlignCenter class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :title="localeStore.isRtl ? 'محاذاة لليسار' : 'Align Left'"
          @click="applyAlign('left')"
        >
          <AlignLeft class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Link Insertion -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :title="localeStore.isRtl ? 'إدراج رابط (Link)' : 'Insert Link'"
          @click="insertLink"
        >
          <Link class="w-4 h-4" />
        </button>

        <!-- Quote Block -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors"
          :title="localeStore.isRtl ? 'اقتباس مميز (Quote Block)' : 'Blockquote'"
          @click="applyQuote"
        >
          <Quote class="w-4 h-4" />
        </button>

        <!-- Clear Formatting -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-rose-600 transition-colors ms-auto"
          :title="localeStore.isRtl ? 'إزالة التنسيق' : 'Remove Formatting'"
          @click="clearFormatting"
        >
          <RemoveFormatting class="w-4 h-4" />
        </button>
      </div>

      <!-- Editable Content Area -->
      <div
        :id="editorId"
        ref="editorRef"
        contenteditable="true"
        class="rich-text-content px-4 py-3 min-h-[140px] focus:outline-none text-xs sm:text-sm text-slate-800 leading-relaxed overflow-y-auto"
        :style="{ minHeight: minHeight, maxHeight: maxHeight }"
        :data-placeholder="placeholder"
        @input="handleInput"
        @focus="isFocused = true"
        @blur="handleBlur"
        @keyup="checkActiveFormats"
        @mouseup="checkActiveFormats"
        @paste="handlePaste"
      ></div>
    </div>

    <!-- Error Message -->
    <p v-if="errorMessage" class="text-xs text-rose-600 font-semibold flex items-center gap-1 mt-1">
      <span class="w-1 h-1 rounded-full bg-rose-600"></span>
      {{ errorMessage }}
    </p>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import {
  Bold,
  Italic,
  Underline,
  List,
  ListOrdered,
  AlignLeft,
  AlignCenter,
  AlignRight,
  Link,
  Quote,
  RemoveFormatting
} from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'اكتب المحتوى بالتنسيق المناسب...'
  },
  hint: {
    type: String,
    default: ''
  },
  errorMessage: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  minHeight: {
    type: String,
    default: '150px'
  },
  maxHeight: {
    type: String,
    default: '450px'
  },
  dir: {
    type: String,
    default: 'auto' // 'rtl' | 'ltr' | 'auto'
  }
})

const emit = defineEmits(['update:modelValue', 'change', 'blur', 'focus'])

const localeStore = useLocaleStore()
const editorRef = ref(null)
const isFocused = ref(false)
const savedRange = ref(null)
const currentHeading = ref('p')
const currentColor = ref('#0f172a')
const editorId = `rte_${Math.random().toString(36).substring(2, 9)}`

const colorPalette = [
  { name: 'Navy 950', hex: '#0b192c' },
  { name: 'Gold 600', hex: '#d97706' },
  { name: 'Emerald 600', hex: '#059669' },
  { name: 'Rose 600', hex: '#e11d48' },
  { name: 'Blue 600', hex: '#2563eb' }
]

const activeFormats = reactive({
  bold: false,
  italic: false,
  underline: false,
  unorderedList: false,
  orderedList: false
})

const editorDir = computed(() => {
  if (props.dir === 'auto') {
    return localeStore.dir
  }
  return props.dir
})

const handleToolbarMouseDown = (e) => {
  // Allow interactive color input while preventing losing selection for buttons
  if (e.target.tagName !== 'INPUT') {
    e.preventDefault()
  }
}

const saveSelection = () => {
  const sel = window.getSelection()
  if (sel.rangeCount > 0) {
    const range = sel.getRangeAt(0)
    if (editorRef.value && editorRef.value.contains(range.commonAncestorContainer)) {
      savedRange.value = range
    }
  }
}

const restoreSelection = () => {
  if (savedRange.value) {
    const sel = window.getSelection()
    sel.removeAllRanges()
    sel.addRange(savedRange.value)
  } else if (editorRef.value) {
    editorRef.value.focus()
  }
}

const checkActiveFormats = () => {
  saveSelection()
  activeFormats.bold = document.queryCommandState('bold')
  activeFormats.italic = document.queryCommandState('italic')
  activeFormats.underline = document.queryCommandState('underline')
  activeFormats.unorderedList = document.queryCommandState('insertUnorderedList')
  activeFormats.orderedList = document.queryCommandState('insertOrderedList')

  // Check parent node for current heading block
  const sel = window.getSelection()
  if (sel && sel.anchorNode) {
    let node = sel.anchorNode
    if (node.nodeType === 3) node = node.parentNode
    const tagName = node.tagName ? node.tagName.toLowerCase() : 'p'
    if (['h2', 'h3', 'h4'].includes(tagName)) {
      currentHeading.value = tagName
    } else {
      currentHeading.value = 'p'
    }
  }
}

onMounted(() => {
  if (editorRef.value) {
    if (props.modelValue) {
      editorRef.value.innerHTML = props.modelValue
    }
  }
})

watch(() => props.modelValue, (newVal) => {
  if (editorRef.value && editorRef.value.innerHTML !== newVal) {
    editorRef.value.innerHTML = newVal || ''
  }
})

const handleInput = () => {
  if (!editorRef.value) return
  const html = editorRef.value.innerHTML
  emit('update:modelValue', html)
  emit('change', html)
  checkActiveFormats()
}

const handleBlur = () => {
  saveSelection()
  isFocused.value = false
  emit('blur')
}

const execCmd = (cmd, val = null) => {
  restoreSelection()
  document.execCommand(cmd, false, val)
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const applyBlock = (tag) => {
  restoreSelection()
  currentHeading.value = tag
  if (tag === 'p') {
    document.execCommand('formatBlock', false, '<p>')
  } else {
    document.execCommand('formatBlock', false, `<${tag}>`)
  }
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const applyColor = (hex) => {
  currentColor.value = hex
  restoreSelection()
  document.execCommand('foreColor', false, hex)
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const handleCustomColor = (e) => {
  applyColor(e.target.value)
}

const applyList = (type) => {
  restoreSelection()
  if (type === 'ordered') {
    document.execCommand('insertOrderedList', false, null)
  } else {
    document.execCommand('insertUnorderedList', false, null)
  }
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const applyAlign = (align) => {
  restoreSelection()
  if (align === 'right') {
    document.execCommand('justifyRight', false, null)
  } else if (align === 'center') {
    document.execCommand('justifyCenter', false, null)
  } else {
    document.execCommand('justifyLeft', false, null)
  }
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const applyQuote = () => {
  restoreSelection()
  document.execCommand('formatBlock', false, '<blockquote>')
  handleInput()
  if (editorRef.value) {
    editorRef.value.focus()
  }
}

const insertLink = () => {
  saveSelection()
  const url = prompt(localeStore.isRtl ? 'أدخل رابط الموقع (URL):' : 'Enter URL:', 'https://')
  if (url) {
    restoreSelection()
    document.execCommand('createLink', false, url)
    handleInput()
  }
}

const clearFormatting = () => {
  restoreSelection()
  document.execCommand('removeFormat', false, null)
  document.execCommand('formatBlock', false, '<p>')
  currentHeading.value = 'p'
  handleInput()
}

const handlePaste = (e) => {
  e.preventDefault()
  const text = e.clipboardData?.getData('text/plain') || ''
  document.execCommand('insertText', false, text)
  handleInput()
}
</script>

<style scoped>
.rich-text-content:empty:before {
  content: attr(data-placeholder);
  color: #94a3b8;
  pointer-events: none;
  font-style: italic;
}
.rich-text-content blockquote {
  border-inline-start: 4px solid #eab308;
  padding-inline-start: 1rem;
  margin: 0.75rem 0;
  color: #475569;
  font-style: italic;
  background-color: #f8fafc;
  padding-block: 0.5rem;
  border-radius: 0.375rem;
}
/* Unordered List Strict Display */
.rich-text-content ul {
  list-style: disc inside !important;
  list-style-type: disc !important;
  padding-inline-start: 1.5rem !important;
  margin: 0.75rem 0 !important;
}
.rich-text-content ul li {
  list-style: disc inside !important;
  list-style-type: disc !important;
  display: list-item !important;
  margin-bottom: 0.25rem;
}
/* Ordered List Strict Numbered Display */
.rich-text-content ol {
  list-style: decimal inside !important;
  list-style-type: decimal !important;
  padding-inline-start: 1.5rem !important;
  margin: 0.75rem 0 !important;
}
.rich-text-content ol li {
  list-style: decimal inside !important;
  list-style-type: decimal !important;
  display: list-item !important;
  margin-bottom: 0.25rem;
}
.rich-text-content h2 {
  font-size: 1.35rem;
  font-weight: 800;
  margin: 0.85rem 0 0.35rem;
  color: #0b192c;
  line-height: 1.3;
}
.rich-text-content h3 {
  font-size: 1.15rem;
  font-weight: 700;
  margin: 0.65rem 0 0.25rem;
  color: #1e293b;
  line-height: 1.3;
}
.rich-text-content p {
  margin: 0.35rem 0;
  line-height: 1.6;
}
.rich-text-content a {
  color: #0284c7;
  text-decoration: underline;
}
</style>
