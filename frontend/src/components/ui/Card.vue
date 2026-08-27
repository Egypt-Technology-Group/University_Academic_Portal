<template>
  <div
    :class="[
      'bg-white rounded-2xl border border-slate-200/80 transition-all duration-300 ease-out overflow-hidden flex flex-col',
      hover ? 'hover:shadow-academic-lg hover:-translate-y-1 hover:border-slate-300/80' : 'shadow-academic',
      paddingClasses[padding] || paddingClasses.md,
      customClass,
    ]"
  >
    <div v-if="$slots.header" class="border-b border-slate-100 pb-4 mb-4">
      <slot name="header"></slot>
    </div>

    <div :class="['flex-1', contentClass]">
      <slot></slot>
    </div>

    <div v-if="$slots.footer" class="border-t border-slate-100 pt-4 mt-4">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  hover: {
    type: Boolean,
    default: true,
  },
  padding: {
    type: String,
    default: 'md',
    validator: (p) => ['none', 'sm', 'md', 'lg', 'xl'].includes(p),
  },
  customClass: {
    type: String,
    default: '',
  },
  contentClass: {
    type: String,
    default: '',
  },
})

const paddingClasses = {
  none: 'p-0',
  sm: 'p-4',
  md: 'p-6',
  lg: 'p-8',
  xl: 'p-10',
}
</script>
