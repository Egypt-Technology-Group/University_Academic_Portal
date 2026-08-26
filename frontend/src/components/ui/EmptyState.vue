<template>
  <div class="text-center py-16 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-3 p-6">
    <slot name="icon">
      <component :is="icon" v-if="icon" class="w-12 h-12 text-slate-300 mx-auto" />
      <Inbox v-else class="w-12 h-12 text-slate-300 mx-auto" />
    </slot>
    <div class="space-y-1">
      <h3 class="text-sm sm:text-base font-black text-navy-950">
        {{ title }}
      </h3>
      <p v-if="description" class="text-xs text-slate-500 max-w-sm mx-auto">
        {{ description }}
      </p>
    </div>
    <div v-if="$slots.action" class="pt-2">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
import { Inbox } from 'lucide-vue-next'

defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: [Object, Function],
    default: null
  }
})
</script>
