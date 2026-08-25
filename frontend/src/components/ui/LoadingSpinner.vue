<template>
  <div :class="['inline-flex items-center justify-center', containerClass]">
    <svg
      :class="['animate-spin', sizeClasses[size] || sizeClasses.md, colorClasses[color] || colorClasses.primary]"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <span v-if="label" class="ms-2 text-sm font-medium text-slate-600">{{ label }}</span>
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (s) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(s),
  },
  color: {
    type: String,
    default: 'primary',
    validator: (c) => ['primary', 'gold', 'emerald', 'white', 'slate'].includes(c),
  },
  label: {
    type: String,
    default: '',
  },
  containerClass: {
    type: String,
    default: '',
  },
})

const sizeClasses = {
  xs: 'w-3.5 h-3.5',
  sm: 'w-4 h-4',
  md: 'w-6 h-6',
  lg: 'w-8 h-8',
  xl: 'w-12 h-12',
}

const colorClasses = {
  primary: 'text-navy-900',
  gold: 'text-gold-500',
  emerald: 'text-emerald-600',
  white: 'text-white',
  slate: 'text-slate-400',
}
</script>
