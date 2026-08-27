<template>
  <div
    ref="elementRef"
    class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover-lift transition-all duration-300 relative overflow-hidden group flex flex-col justify-between"
  >
    <div class="flex items-center justify-between">
      <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
        {{ label }}
      </span>
      <div
        class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
        :class="iconContainerClass"
      >
        <slot name="icon">
          <component :is="icon" v-if="icon" class="w-5 h-5" />
        </slot>
      </div>
    </div>

    <div class="mt-4 flex items-baseline justify-between gap-2">
      <span class="text-3xl font-black text-navy-950 font-mono">
        {{ displayValue }}
      </span>
      <span
        v-if="badge"
        class="text-xs font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shrink-0"
        :class="badgeClass"
      >
        <span v-if="pulse" class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
        {{ badge }}
      </span>
    </div>

    <div v-if="description" class="mt-3 text-xs text-slate-500 font-medium">
      {{ description }}
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
  variant: {
    type: String,
    default: 'navy' // amber, emerald, navy, blue, purple
  },
  badge: {
    type: String,
    default: ''
  },
  pulse: {
    type: Boolean,
    default: false
  },
  description: {
    type: String,
    default: ''
  }
})

const { displayValue, elementRef } = useAnimatedCounter(toRef(props, 'value'))

const iconContainerClass = computed(() => {
  switch (props.variant) {
    case 'amber':
      return 'bg-amber-50 text-amber-600'
    case 'emerald':
      return 'bg-emerald-50 text-emerald-600'
    case 'blue':
      return 'bg-blue-50 text-blue-600'
    case 'purple':
      return 'bg-purple-50 text-purple-600'
    case 'navy':
    default:
      return 'bg-navy-50 text-navy-800'
  }
})

const badgeClass = computed(() => {
  switch (props.variant) {
    case 'amber':
      return 'text-amber-700 bg-amber-100'
    case 'emerald':
      return 'text-emerald-700 bg-emerald-100'
    case 'blue':
      return 'text-blue-700 bg-blue-100'
    case 'purple':
      return 'text-purple-700 bg-purple-100'
    case 'navy':
    default:
      return 'text-navy-900 bg-navy-100'
  }
})
</script>
