<template>
  <div
    ref="elementRef"
    class="bg-white rounded-xl p-4 border border-slate-200/80 flex items-center gap-3.5 shadow-2xs hover:border-slate-300 hover-lift transition-all duration-300"
  >
    <div
      class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
      :class="iconBgClass"
    >
      <slot name="icon">
        <component :is="icon" v-if="icon" class="w-4 h-4" />
      </slot>
    </div>
    <div class="min-w-0 flex-1">
      <div class="text-xs text-slate-500 font-medium truncate">{{ label }}</div>
      <div class="text-lg font-black text-navy-950 font-mono leading-tight mt-0.5">{{ displayValue }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed, toRef } from 'vue'
import { useAnimatedCounter } from '../../composables/useAnimatedCounter'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: [Object, Function],
    default: null
  },
  color: {
    type: String,
    default: 'slate' // slate, navy, gold, emerald, blue, amber
  }
})

const { displayValue, elementRef } = useAnimatedCounter(toRef(props, 'value'))

const iconBgClass = computed(() => {
  switch (props.color) {
    case 'navy':
      return 'bg-navy-50 text-navy-900'
    case 'gold':
      return 'bg-amber-50 text-amber-700'
    case 'emerald':
      return 'bg-emerald-50 text-emerald-700'
    case 'blue':
      return 'bg-blue-50 text-blue-700'
    case 'slate':
    default:
      return 'bg-slate-100 text-slate-700'
  }
})
</script>
