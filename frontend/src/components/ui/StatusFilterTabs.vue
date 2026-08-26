<template>
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
    <button
      v-for="statusTab in statusTabs"
      :key="statusTab.key"
      type="button"
      :class="[
        'p-3.5 rounded-2xl border text-start transition-all cursor-pointer',
        modelValue === statusTab.key
          ? 'bg-navy-950 text-white border-navy-950 shadow-md ring-2 ring-navy-950/20'
          : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50'
      ]"
      @click="$emit('update:modelValue', statusTab.key)"
    >
      <div
        class="text-xs font-semibold text-slate-400"
        :class="{ 'text-slate-300': modelValue === statusTab.key }"
      >
        {{ statusTab.label }}
      </div>
      <div
        class="text-xl font-black font-mono mt-1"
        :class="{
          'text-gold-400': modelValue === statusTab.key,
          'text-navy-950': modelValue !== statusTab.key
        }"
      >
        {{ statusCounts[statusTab.key] || 0 }}
      </div>
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  },
  statusTabs: {
    type: Array,
    required: true
  },
  statusCounts: {
    type: Object,
    default: () => ({})
  }
})

defineEmits(['update:modelValue'])
</script>
