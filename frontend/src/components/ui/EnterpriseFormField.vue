<template>
  <div :class="[colSpanClass, 'space-y-1.5']">
    <div class="flex items-center justify-between">
      <label v-if="label" :for="fieldId" class="block text-xs font-bold text-slate-700">
        {{ label }}
        <span v-if="required" class="text-red-500 ms-0.5">*</span>
      </label>
      <span v-if="hint" class="text-[10px] text-slate-400 font-normal">{{ hint }}</span>
    </div>

    <!-- TEXT / NUMBER / EMAIL / PASSWORD / DATE / TIME -->
    <div v-if="['text', 'number', 'email', 'password', 'tel', 'url', 'date', 'time'].includes(type)" class="relative">
      <input
        :id="fieldId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        class="w-full rounded-xl border bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 transition-all focus:ring-1 outline-none disabled:bg-slate-100 disabled:text-slate-400"
        :class="[
          errorMessage
            ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
            : 'border-slate-300 focus:border-navy-900 focus:ring-navy-900'
        ]"
        @input="$emit('update:modelValue', type === 'number' ? ($event.target.value === '' ? null : Number($event.target.value)) : $event.target.value)"
      />
    </div>

    <!-- TEXTAREA -->
    <div v-else-if="type === 'textarea'" class="relative">
      <textarea
        :id="fieldId"
        :value="modelValue"
        :placeholder="placeholder"
        :rows="rows || 3"
        :disabled="disabled"
        :required="required"
        class="w-full rounded-xl border bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 transition-all focus:ring-1 outline-none disabled:bg-slate-100 disabled:text-slate-400"
        :class="[
          errorMessage
            ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
            : 'border-slate-300 focus:border-navy-900 focus:ring-navy-900'
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
      ></textarea>
    </div>

    <!-- RICH TEXT EDITOR -->
    <div v-else-if="['richtext', 'rich-text', 'editor'].includes(type)" class="relative">
      <RichTextEditor
        :model-value="modelValue || ''"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :error-message="errorMessage"
        :min-height="minHeight || '160px'"
        @update:model-value="$emit('update:modelValue', $event)"
        @change="$emit('change', $event)"
      />
    </div>

    <!-- SELECT / DROPDOWN -->
    <div v-else-if="type === 'select'" class="relative">
      <select
        :id="fieldId"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        class="w-full rounded-xl border bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 transition-all focus:ring-1 outline-none disabled:bg-slate-100 disabled:text-slate-400"
        :class="[
          errorMessage
            ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
            : 'border-slate-300 focus:border-navy-900 focus:ring-navy-900'
        ]"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option
          v-for="opt in normalizedOptions"
          :key="opt.value"
          :value="opt.value"
        >
          {{ opt.label }}
        </option>
      </select>
    </div>

    <!-- CHECKBOX / TOGGLE -->
    <div v-else-if="type === 'checkbox'" class="flex items-center gap-2.5 pt-1">
      <input
        :id="fieldId"
        type="checkbox"
        :checked="Boolean(modelValue)"
        :disabled="disabled"
        class="w-4 h-4 rounded text-navy-900 focus:ring-navy-900 border-slate-300 transition-colors cursor-pointer"
        @change="$emit('update:modelValue', $event.target.checked)"
      />
      <label :for="fieldId" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
        {{ label }}
      </label>
    </div>

    <!-- FILE UPLOAD WITH PREVIEW -->
    <div v-else-if="type === 'file' || type === 'image'" class="space-y-2">
      <div class="flex items-center gap-3">
        <div
          v-if="type === 'image' && (previewUrl || modelValue)"
          class="w-14 h-14 rounded-xl overflow-hidden border border-slate-200 shadow-xs shrink-0 bg-slate-100 flex items-center justify-center"
        >
          <img
            :src="previewUrl || modelValue"
            alt="Preview"
            class="w-full h-full object-cover"
          />
        </div>
        <div class="flex-1 min-w-0">
          <input
            :id="fieldId"
            ref="fileInputRef"
            type="file"
            :accept="accept || (type === 'image' ? 'image/*' : '*/*')"
            :disabled="disabled"
            class="hidden"
            @change="handleFileChange"
          />
          <button
            type="button"
            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 border border-slate-300 transition-colors"
            :disabled="disabled"
            @click="fileInputRef?.click()"
          >
            <Upload class="w-3.5 h-3.5 text-navy-900" />
            <span>{{ buttonText || (type === 'image' ? (previewUrl || modelValue ? 'Change Image' : 'Select Image') : 'Select File') }}</span>
          </button>
          <div v-if="selectedFileName" class="text-[11px] text-slate-500 font-mono mt-1 truncate">
            {{ selectedFileName }}
          </div>
        </div>
      </div>
    </div>

    <!-- ERROR MESSAGE -->
    <p v-if="errorMessage" class="text-[11px] font-bold text-red-500 flex items-center gap-1">
      <span>⚠️</span>
      <span>{{ errorMessage }}</span>
    </p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Upload } from 'lucide-vue-next'
import RichTextEditor from './RichTextEditor.vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, Array, Object, null],
    default: ''
  },
  minHeight: {
    type: String,
    default: '160px'
  },
  type: {
    type: String,
    default: 'text' // text, number, email, password, textarea, select, checkbox, image, file, date, time
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
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
  rows: {
    type: Number,
    default: 3
  },
  min: {
    type: [Number, String],
    default: undefined
  },
  max: {
    type: [Number, String],
    default: undefined
  },
  step: {
    type: [Number, String],
    default: undefined
  },
  options: {
    type: Array,
    default: () => [] // [{ label: '...', value: '...' }] or ['A', 'B']
  },
  colSpan: {
    type: [Number, String],
    default: 12 // 1..12 or 'full', 'half'
  },
  accept: {
    type: String,
    default: ''
  },
  buttonText: {
    type: String,
    default: ''
  },
  previewUrl: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'change', 'file-selected'])

const fileInputRef = ref(null)
const selectedFileName = ref('')

const fieldId = computed(() => 'field_' + Math.random().toString(36).substring(2, 9))

const colSpanClass = computed(() => {
  if (props.colSpan === 'half' || props.colSpan === 6) return 'col-span-1 sm:col-span-6'
  if (props.colSpan === 12 || props.colSpan === 'full') return 'col-span-1 sm:col-span-12'
  if (props.colSpan === 4) return 'col-span-1 sm:col-span-4'
  if (props.colSpan === 3) return 'col-span-1 sm:col-span-3'
  return 'col-span-1 sm:col-span-12'
})

const normalizedOptions = computed(() => {
  return props.options.map(opt => {
    if (typeof opt === 'object' && opt !== null) {
      return {
        label: opt.label !== undefined ? opt.label : opt.name || opt.title || opt.value,
        value: opt.value !== undefined ? opt.value : opt.id || opt.key
      }
    }
    return { label: String(opt), value: opt }
  })
})

const handleFileChange = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    selectedFileName.value = file.name
    emit('file-selected', file)
  }
}
</script>
