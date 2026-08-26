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

    <!-- Main Editor Container -->
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
        v-if="editor && !disabled"
        class="bg-slate-50/95 border-b border-slate-200 p-1.5 flex flex-wrap items-center gap-1.5 text-slate-700 select-none text-xs"
        @mousedown.prevent
      >
        <!-- Formatting: Bold, Italic, Underline -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('bold') }"
          :title="localeStore.isRtl ? 'عريض (Bold)' : 'Bold'"
          @click="editor.chain().focus().toggleBold().run()"
        >
          <Bold class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('italic') }"
          :title="localeStore.isRtl ? 'مائل (Italic)' : 'Italic'"
          @click="editor.chain().focus().toggleItalic().run()"
        >
          <Italic class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('underline') }"
          :title="localeStore.isRtl ? 'تسطير (Underline)' : 'Underline'"
          @click="editor.chain().focus().toggleUnderline().run()"
        >
          <Underline class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Paragraph & Headings Segmented Controls -->
        <div class="flex items-center bg-white border border-slate-200 rounded-lg p-0.5 shadow-2xs">
          <button
            type="button"
            class="px-2.5 py-1 rounded text-xs font-bold transition-colors cursor-pointer"
            :class="editor.isActive('paragraph') && !editor.isActive('heading') ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            :title="localeStore.isRtl ? 'فقرة نصية عادية' : 'Normal Paragraph'"
            @click="editor.chain().focus().setParagraph().run()"
          >
            P
          </button>
          <button
            type="button"
            class="px-2.5 py-1 rounded text-xs font-bold transition-colors cursor-pointer"
            :class="editor.isActive('heading', { level: 2 }) ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            :title="localeStore.isRtl ? 'عنوان رئيسي' : 'Heading 2'"
            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
          >
            H2
          </button>
          <button
            type="button"
            class="px-2.5 py-1 rounded text-xs font-bold transition-colors cursor-pointer"
            :class="editor.isActive('heading', { level: 3 }) ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            :title="localeStore.isRtl ? 'عنوان فرعي' : 'Heading 3'"
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
          >
            H3
          </button>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Color Preset Swatches & Custom Color Picker -->
        <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-lg px-2 py-1 shadow-2xs">
          <button
            v-for="c in colorPalette"
            :key="c.hex"
            type="button"
            class="w-4 h-4 rounded-full border border-slate-300 hover:scale-125 transition-transform cursor-pointer"
            :style="{ backgroundColor: c.hex }"
            :title="c.name"
            @click="editor.chain().focus().setColor(c.hex).run()"
          ></button>
          <div class="relative flex items-center ms-1">
            <input
              type="color"
              class="w-4 h-4 rounded cursor-pointer border-0 bg-transparent p-0"
              :value="currentColor"
              :title="localeStore.isRtl ? 'لون مخصص' : 'Custom Color'"
              @input="onCustomColorChange"
            />
          </div>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Lists: Unordered, Ordered -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('bulletList') }"
          :title="localeStore.isRtl ? 'قائمة نقطية (Bullet List)' : 'Bullet List'"
          @click="editor.chain().focus().toggleBulletList().run()"
        >
          <List class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('orderedList') }"
          :title="localeStore.isRtl ? 'قائمة مرقمة (Numbered List)' : 'Numbered List'"
          @click="editor.chain().focus().toggleOrderedList().run()"
        >
          <ListOrdered class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Text Alignment: Right, Center, Left -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive({ textAlign: 'right' }) }"
          :title="localeStore.isRtl ? 'محاذاة لليمين' : 'Align Right'"
          @click="editor.chain().focus().setTextAlign('right').run()"
        >
          <AlignRight class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive({ textAlign: 'center' }) }"
          :title="localeStore.isRtl ? 'محاذاة للوسط' : 'Align Center'"
          @click="editor.chain().focus().setTextAlign('center').run()"
        >
          <AlignCenter class="w-4 h-4" />
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive({ textAlign: 'left' }) }"
          :title="localeStore.isRtl ? 'محاذاة لليسار' : 'Align Left'"
          @click="editor.chain().focus().setTextAlign('left').run()"
        >
          <AlignLeft class="w-4 h-4" />
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        <!-- Link Insertion -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('link') }"
          :title="localeStore.isRtl ? 'إدراج رابط (Link)' : 'Insert Link'"
          @click="setLink"
        >
          <Link class="w-4 h-4" />
        </button>

        <!-- Quote Block -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-navy-950 transition-colors cursor-pointer"
          :class="{ 'bg-slate-200 text-navy-950 font-bold ring-1 ring-slate-300': editor.isActive('blockquote') }"
          :title="localeStore.isRtl ? 'اقتباس مميز (Quote Block)' : 'Blockquote'"
          @click="editor.chain().focus().toggleBlockquote().run()"
        >
          <Quote class="w-4 h-4" />
        </button>

        <!-- Clear Formatting -->
        <button
          type="button"
          class="p-1.5 rounded-lg hover:bg-slate-200 hover:text-rose-600 transition-colors ms-auto cursor-pointer"
          :title="localeStore.isRtl ? 'إزالة التنسيق' : 'Remove Formatting'"
          @click="editor.chain().focus().unsetAllMarks().clearNodes().run()"
        >
          <RemoveFormatting class="w-4 h-4" />
        </button>
      </div>

      <!-- Tiptap Editor Component -->
      <editor-content
        :editor="editor"
        class="tiptap-content-container px-4 py-3 min-h-[140px] focus:outline-none text-xs sm:text-sm text-slate-800 leading-relaxed overflow-y-auto"
        :style="{ minHeight: minHeight, maxHeight: maxHeight }"
      />
    </div>

    <!-- Error Message -->
    <p v-if="errorMessage" class="text-xs text-rose-600 font-semibold flex items-center gap-1 mt-1">
      <span class="w-1 h-1 rounded-full bg-rose-600"></span>
      {{ errorMessage }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount, watch } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { Underline as UnderlineExtension } from '@tiptap/extension-underline'
import { TextStyle } from '@tiptap/extension-text-style'
import { Color } from '@tiptap/extension-color'
import { TextAlign } from '@tiptap/extension-text-align'
import { Link as LinkExtension } from '@tiptap/extension-link'
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
const isFocused = ref(false)
const currentColor = ref('#0b192c')
const editorId = `rte_${Math.random().toString(36).substring(2, 9)}`

const colorPalette = [
  { name: 'Navy 950', hex: '#0b192c' },
  { name: 'Gold 600', hex: '#d97706' },
  { name: 'Emerald 600', hex: '#059669' },
  { name: 'Rose 600', hex: '#e11d48' },
  { name: 'Blue 600', hex: '#2563eb' }
]

const editorDir = computed(() => {
  if (props.dir === 'auto') {
    return localeStore.dir
  }
  return props.dir
})

const editor = useEditor({
  content: props.modelValue || '',
  editable: !props.disabled,
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [2, 3, 4]
      },
      bulletList: {
        HTMLAttributes: {
          class: 'tiptap-ul'
        }
      },
      orderedList: {
        HTMLAttributes: {
          class: 'tiptap-ol'
        }
      }
    }),
    UnderlineExtension,
    TextStyle,
    Color,
    TextAlign.configure({
      types: ['heading', 'paragraph']
    }),
    LinkExtension.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-sky-600 underline font-medium'
      }
    })
  ],
  onUpdate: ({ editor }) => {
    const html = editor.getHTML()
    emit('update:modelValue', html)
    emit('change', html)
  },
  onFocus: () => {
    isFocused.value = true
    emit('focus')
  },
  onBlur: () => {
    isFocused.value = false
    emit('blur')
  }
})

watch(() => props.modelValue, (newVal) => {
  if (editor.value && editor.value.getHTML() !== newVal) {
    editor.value.commands.setContent(newVal || '', false)
  }
})

watch(() => props.disabled, (newVal) => {
  if (editor.value) {
    editor.value.setEditable(!newVal)
  }
})

const onCustomColorChange = (e) => {
  const hex = e.target.value
  currentColor.value = hex
  if (editor.value) {
    editor.value.chain().focus().setColor(hex).run()
  }
}

const setLink = () => {
  if (!editor.value) return
  const previousUrl = editor.value.getAttributes('link').href || ''
  const url = window.prompt(localeStore.isRtl ? 'أدخل رابط الموقع (URL):' : 'Enter URL:', previousUrl || 'https://')
  
  if (url === null) return
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }
  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style>
/* TipTap ProseMirror Scoped Styles */
.tiptap-content-container .ProseMirror {
  outline: none;
  min-height: 120px;
}

.tiptap-content-container .ProseMirror p {
  margin: 0.35rem 0;
  line-height: 1.6;
}

.tiptap-content-container .ProseMirror h2 {
  font-size: 1.4rem;
  font-weight: 800;
  margin: 0.85rem 0 0.35rem;
  color: #0b192c;
  line-height: 1.3;
}

.tiptap-content-container .ProseMirror h3 {
  font-size: 1.2rem;
  font-weight: 700;
  margin: 0.65rem 0 0.25rem;
  color: #1e293b;
  line-height: 1.3;
}

.tiptap-content-container .ProseMirror ul.tiptap-ul,
.tiptap-content-container .ProseMirror ul {
  list-style-type: disc !important;
  padding-inline-start: 1.75rem !important;
  margin: 0.75rem 0 !important;
}

.tiptap-content-container .ProseMirror ul li {
  list-style-type: disc !important;
  display: list-item !important;
  margin-bottom: 0.35rem;
}

.tiptap-content-container .ProseMirror ol.tiptap-ol,
.tiptap-content-container .ProseMirror ol {
  list-style-type: decimal !important;
  padding-inline-start: 1.75rem !important;
  margin: 0.75rem 0 !important;
}

.tiptap-content-container .ProseMirror ol li {
  list-style-type: decimal !important;
  display: list-item !important;
  margin-bottom: 0.35rem;
}

.tiptap-content-container .ProseMirror blockquote {
  border-inline-start: 4px solid #eab308;
  padding-inline-start: 1rem;
  margin: 0.75rem 0;
  color: #475569;
  font-style: italic;
  background-color: #f8fafc;
  padding-block: 0.5rem;
  border-radius: 0.375rem;
}

.tiptap-content-container .ProseMirror a {
  color: #0284c7;
  text-decoration: underline;
}
</style>
