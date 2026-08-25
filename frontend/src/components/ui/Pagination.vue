<template>
  <div v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 pt-6">
    <button
      type="button"
      :disabled="currentPage === 1"
      class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      @click="$emit('change', currentPage - 1)"
    >
      <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <button
      v-for="page in totalPages"
      :key="page"
      type="button"
      :class="[
        'w-9 h-9 text-sm font-semibold rounded-lg transition-colors',
        currentPage === page
          ? 'bg-navy-900 text-white shadow-sm'
          : 'text-slate-700 hover:bg-slate-100 border border-transparent hover:border-slate-200',
      ]"
      @click="$emit('change', page)"
    >
      {{ page }}
    </button>

    <button
      type="button"
      :disabled="currentPage === totalPages"
      class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      @click="$emit('change', currentPage + 1)"
    >
      <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
</template>

<script setup>
defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
})

defineEmits(['change'])
</script>
