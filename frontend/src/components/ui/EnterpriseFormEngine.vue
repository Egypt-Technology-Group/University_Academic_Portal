<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Non-Field / Global Form Error -->
    <div
      v-if="globalError"
      class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-bold flex items-center gap-2"
    >
      <span>⚠️</span>
      <span>{{ globalError }}</span>
    </div>

    <!-- Sections Loop -->
    <div
      v-for="(section, sIdx) in schema.sections || [schema]"
      :key="sIdx"
      class="space-y-4"
    >
      <div v-if="section.title || section.description" class="border-b border-slate-100 pb-2">
        <h3 v-if="section.title" class="text-sm font-black text-navy-950">
          {{ section.title }}
        </h3>
        <p v-if="section.description" class="text-xs text-slate-500 mt-0.5">
          {{ section.description }}
        </p>
      </div>

      <!-- Fields Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
        <template v-for="field in section.fields" :key="field.key">
          <!-- Conditional Visibility Evaluation -->
          <template v-if="isFieldVisible(field)">
            <!-- Custom Slot Override -->
            <slot
              :name="`field-${field.key}`"
              :field="field"
              :model-value="modelValue[field.key]"
              :errors="errors[field.key]"
            >
              <EnterpriseFormField
                :model-value="modelValue[field.key]"
                :type="field.type || 'text'"
                :label="field.label"
                :placeholder="field.placeholder"
                :hint="field.hint"
                :required="field.required"
                :disabled="field.disabled || disabled"
                :options="field.options"
                :col-span="field.colSpan || 12"
                :rows="field.rows"
                :min="field.min"
                :max="field.max"
                :step="field.step"
                :accept="field.accept"
                :button-text="field.buttonText"
                :preview-url="field.previewUrl"
                :error-message="getFieldError(field.key)"
                @update:model-value="updateFieldValue(field.key, $event)"
                @file-selected="$emit('file-selected', { key: field.key, file: $event })"
              />
            </slot>
          </template>
        </template>
      </div>
    </div>

    <!-- Form Actions Slot / Default Buttons -->
    <div v-if="showActions" class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
      <slot name="actions" :is-submitting="isSubmitting" :reset="resetForm">
        <button
          v-if="showCancel"
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
          :disabled="isSubmitting"
          @click="$emit('cancel')"
        >
          {{ cancelText || 'Cancel' }}
        </button>
        <button
          type="submit"
          class="px-5 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-colors inline-flex items-center gap-2 cursor-pointer disabled:opacity-50"
          :disabled="isSubmitting"
        >
          <div
            v-if="isSubmitting"
            class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"
          ></div>
          <span>{{ submitText || 'Save Changes' }}</span>
        </button>
      </slot>
    </div>
  </form>
</template>

<script setup>
import { computed } from 'vue'
import EnterpriseFormField from './EnterpriseFormField.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  schema: {
    type: Object,
    required: true // { sections: [ { title, fields: [ ... ] } ] } OR { fields: [ ... ] }
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  globalError: {
    type: String,
    default: ''
  },
  isSubmitting: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showActions: {
    type: Boolean,
    default: false
  },
  showCancel: {
    type: Boolean,
    default: true
  },
  submitText: {
    type: String,
    default: ''
  },
  cancelText: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'submit', 'cancel', 'file-selected'])

const updateFieldValue = (key, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [key]: value
  })
}

const isFieldVisible = (field) => {
  if (typeof field.vIf === 'function') {
    return field.vIf(props.modelValue)
  }
  if (typeof field.showIf === 'function') {
    return field.showIf(props.modelValue)
  }
  if (field.dependsOn) {
    return Boolean(props.modelValue[field.dependsOn])
  }
  return true
}

const getFieldError = (key) => {
  if (!props.errors) return ''
  const err = props.errors[key]
  if (Array.isArray(err)) return err[0]
  if (typeof err === 'string') return err
  return ''
}

const handleSubmit = () => {
  emit('submit', props.modelValue)
}

const resetForm = () => {
  // Consumer handles reset or resets modelValue
}
</script>
