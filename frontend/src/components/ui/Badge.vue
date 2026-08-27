<template>
  <span
    :class="[
      'inline-flex items-center font-medium transition-all duration-200',
      sizeClasses[size] || sizeClasses.md,
      variantClasses[variant] || variantClasses.primary,
      roundedClasses[rounded] || roundedClasses.md,
    ]"
  >
    <span
      v-if="dot || pulse"
      :class="['relative flex shrink-0', dotSizeClasses[size] || dotSizeClasses.md]"
    >
      <span
        v-if="pulse"
        :class="[
          'animate-ping absolute inline-flex h-full w-full rounded-full opacity-75',
          dotColorClasses[variant] || 'bg-current',
        ]"
      ></span>
      <span
        :class="[
          'relative inline-flex rounded-full h-full w-full',
          dotColorClasses[variant] || 'bg-current',
        ]"
      ></span>
    </span>
    <slot name="icon"></slot>
    <slot></slot>
  </span>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) =>
      ['primary', 'secondary', 'gold', 'emerald', 'danger', 'warning', 'slate', 'outline', 'subtle'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (s) => ['xs', 'sm', 'md', 'lg'].includes(s),
  },
  rounded: {
    type: String,
    default: 'md',
    validator: (r) => ['none', 'sm', 'md', 'lg', 'full'].includes(r),
  },
  dot: {
    type: Boolean,
    default: false,
  },
  pulse: {
    type: Boolean,
    default: false,
  },
})

const sizeClasses = {
  xs: 'px-2 py-0.5 text-xs gap-1',
  sm: 'px-2.5 py-0.5 text-xs gap-1.5',
  md: 'px-3 py-1 text-sm gap-1.5',
  lg: 'px-4 py-1.5 text-base gap-2',
}

const roundedClasses = {
  none: 'rounded-none',
  sm: 'rounded',
  md: 'rounded-md',
  lg: 'rounded-lg',
  full: 'rounded-full',
}

const variantClasses = {
  primary: 'bg-navy-900 text-white shadow-sm',
  secondary: 'bg-navy-100 text-navy-900',
  gold: 'bg-gold-500 text-white shadow-sm',
  emerald: 'bg-emerald-600 text-white shadow-sm',
  danger: 'bg-red-600 text-white',
  warning: 'bg-amber-100 text-amber-900 border border-amber-300',
  slate: 'bg-slate-100 text-slate-700 border border-slate-200',
  outline: 'border border-slate-300 text-slate-700 bg-transparent',
  subtle: 'bg-primary-50 text-primary-800 border border-primary-100',
}

const dotSizeClasses = {
  xs: 'h-1.5 w-1.5',
  sm: 'h-1.5 w-1.5',
  md: 'h-2 w-2',
  lg: 'h-2.5 w-2.5',
}

const dotColorClasses = {
  primary: 'bg-navy-300',
  secondary: 'bg-navy-600',
  gold: 'bg-amber-200',
  emerald: 'bg-emerald-300',
  danger: 'bg-red-300',
  warning: 'bg-amber-500',
  slate: 'bg-slate-500',
  outline: 'bg-navy-600',
  subtle: 'bg-primary-600',
}
</script>
