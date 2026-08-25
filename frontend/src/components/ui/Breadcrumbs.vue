<template>
  <nav aria-label="Breadcrumb" class="py-3 px-4 bg-slate-100/70 rounded-xl mb-6 flex items-center text-sm text-slate-600 overflow-x-auto no-scrollbar">
    <ol class="inline-flex items-center space-x-2 rtl:space-x-reverse">
      <li class="inline-flex items-center">
        <router-link to="/" class="inline-flex items-center text-slate-600 hover:text-navy-900 font-medium transition-colors">
          <svg class="w-4 h-4 me-1.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
          </svg>
          {{ $t('nav.home') }}
        </router-link>
      </li>
      <li v-for="(item, index) in items" :key="index" class="inline-flex items-center">
        <svg class="w-3 h-3 text-slate-400 mx-1.5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <router-link
          v-if="item.to && index < items.length - 1"
          :to="item.to"
          class="text-slate-600 hover:text-navy-900 font-medium transition-colors truncate max-w-[200px]"
        >
          {{ item.label }}
        </router-link>
        <span v-else class="text-navy-950 font-bold truncate max-w-[240px]">
          {{ item.label }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    required: true,
    // [ { label: 'Colleges', to: '/colleges' }, { label: 'Faculty of Computers' } ]
  },
})
</script>
