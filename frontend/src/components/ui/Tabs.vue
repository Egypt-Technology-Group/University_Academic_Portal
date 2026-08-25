<template>
  <div>
    <!-- Tabs Nav -->
    <div :class="['flex border-b border-slate-200 overflow-x-auto no-scrollbar', customNavClass]">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        :class="[
          'whitespace-nowrap px-5 py-3 text-sm font-semibold transition-all duration-200 border-b-2 -mb-px flex items-center gap-2 cursor-pointer',
          modelValue === tab.id
            ? 'border-gold-500 text-navy-950 font-bold bg-white/50'
            : 'border-transparent text-slate-500 hover:text-navy-800 hover:border-slate-300',
        ]"
        @click="$emit('update:modelValue', tab.id)"
      >
        <component :is="tab.icon" v-if="tab.icon" class="w-4 h-4" />
        {{ tab.label }}
        <span
          v-if="tab.count !== undefined"
          :class="[
            'px-2 py-0.5 text-xs rounded-full font-bold',
            modelValue === tab.id ? 'bg-gold-100 text-gold-900' : 'bg-slate-100 text-slate-600',
          ]"
        >
          {{ tab.count }}
        </span>
      </button>
    </div>

    <!-- Tabs Content -->
    <div class="pt-6">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tabs: {
    type: Array,
    required: true,
    // each item: { id: string|number, label: string, icon?: Component, count?: number }
  },
  modelValue: {
    type: [String, Number],
    required: true,
  },
  customNavClass: {
    type: String,
    default: '',
  },
})

defineEmits(['update:modelValue'])
</script>
