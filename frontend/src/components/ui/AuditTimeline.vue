<template>
  <div class="space-y-2 max-h-48 overflow-y-auto pe-1">
    <div
      v-for="(event, eIdx) in timeline"
      :key="eIdx"
      class="text-xs p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start justify-between gap-3"
    >
      <div class="space-y-0.5">
        <div class="font-bold text-navy-950 flex items-center gap-1.5">
          <span>{{ event.title }}</span>
          <span v-if="event.actor" class="text-[10px] text-slate-400 font-normal">({{ event.actor }})</span>
        </div>
        <div v-if="event.details" class="text-[11px] text-slate-600">{{ event.details }}</div>
      </div>
      <div class="text-[10px] text-slate-400 whitespace-nowrap font-mono">
        {{ formatStandardDateTime(event.timestamp, localeStore.locale) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLocaleStore } from '../../stores/locale'
import { formatStandardDateTime } from '../../utils/dateFormat'

const localeStore = useLocaleStore()

defineProps({
  timeline: {
    type: Array,
    default: () => []
  }
})
</script>
