<template>
  <component
    :is="to ? 'router-link' : href ? 'a' : 'button'"
    :to="to"
    :href="href"
    :type="!to && !href ? type : undefined"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 select-none active:scale-[0.97] hover-lift btn-press',
      sizeClasses[size] || sizeClasses.md,
      variantClasses[variant] || variantClasses.primary,
      roundedClasses[rounded] || roundedClasses.md,
      shimmer ? 'btn-shimmer' : '',
      (disabled || loading) ? 'opacity-60 cursor-not-allowed pointer-events-none active:scale-100 hover:transform-none' : '',
      block ? 'w-full' : '',
    ]"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin -ms-1 me-2 h-4 w-4 text-current"
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

    <span v-if="$slots.icon && !loading" class="inline-flex items-center me-2">
      <slot name="icon"></slot>
    </span>

    <slot></slot>

    <span v-if="$slots.trailingIcon" class="inline-flex items-center ms-2">
      <slot name="trailingIcon"></slot>
    </span>
  </component>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) =>
      ['primary', 'secondary', 'gold', 'emerald', 'danger', 'outline', 'ghost', 'white'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (s) => ['sm', 'md', 'lg', 'xl'].includes(s),
  },
  rounded: {
    type: String,
    default: 'md',
    validator: (r) => ['none', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(r),
  },
  shimmer: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'button',
  },
  to: {
    type: [String, Object],
    default: null,
  },
  href: {
    type: String,
    default: null,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['click'])

const sizeClasses = {
  sm: 'px-3 py-1.5 text-xs font-semibold gap-1.5',
  md: 'px-4 py-2 text-sm font-medium gap-2',
  lg: 'px-6 py-2.5 text-base font-semibold gap-2.5 shadow-sm',
  xl: 'px-8 py-3.5 text-lg font-bold gap-3 shadow-md',
}

const roundedClasses = {
  none: 'rounded-none',
  sm: 'rounded',
  md: 'rounded-lg',
  lg: 'rounded-xl',
  xl: 'rounded-2xl',
  '2xl': 'rounded-3xl',
  full: 'rounded-full',
}

const variantClasses = {
  primary: 'bg-navy-900 text-white hover:bg-navy-800 focus:ring-navy-800 shadow-sm border border-navy-900 hover:shadow-md hover:shadow-navy-900/20',
  secondary: 'bg-navy-50 text-navy-900 hover:bg-navy-100 focus:ring-navy-400 border border-navy-200',
  gold: 'bg-gold-500 text-navy-950 hover:bg-gold-400 focus:ring-gold-400 font-semibold shadow-gold-glow border border-gold-400 hover:shadow-md hover:shadow-gold-500/25',
  emerald: 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm border border-emerald-600 hover:shadow-md hover:shadow-emerald-600/20',
  danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm hover:shadow-md hover:shadow-red-600/20',
  outline: 'bg-transparent text-navy-900 border-2 border-navy-900 hover:bg-navy-900 hover:text-white focus:ring-navy-900',
  ghost: 'bg-transparent text-slate-700 hover:bg-slate-100 hover:text-navy-900 focus:ring-slate-300',
  white: 'bg-white text-navy-950 hover:bg-slate-100 focus:ring-white shadow-md border border-slate-100 hover:shadow-lg',
}
</script>
