<template>
  <div
    class="flex flex-col items-center justify-center text-center p-8 sm:p-12 rounded-3xl bg-white border border-slate-200/80 shadow-xs max-w-lg mx-auto space-y-4 animate-fade-in"
    role="alert"
    :dir="localeStore.dir"
  >
    <div class="w-14 h-14 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-red-600 shrink-0">
      <AlertTriangle class="w-7 h-7" />
    </div>

    <div class="space-y-1.5">
      <h3 class="text-base sm:text-lg font-black text-navy-950">
        {{ title || defaultTitle }}
      </h3>
      <p class="text-xs sm:text-sm text-slate-600 max-w-sm leading-relaxed">
        {{ message || defaultMessage }}
      </p>
    </div>

    <div v-if="retryable" class="pt-2 flex items-center gap-3">
      <Button
        type="button"
        variant="primary"
        size="md"
        rounded="xl"
        :loading="isRetrying"
        @click="handleRetry"
      >
        <RotateCcw class="w-4 h-4 me-1.5" />
        <span>{{ retryLabel || defaultRetryLabel }}</span>
      </Button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import Button from './Button.vue'
import { AlertTriangle, RotateCcw } from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
  message: {
    type: String,
    default: '',
  },
  retryable: {
    type: Boolean,
    default: true,
  },
  retryLabel: {
    type: String,
    default: '',
  },
  onRetry: {
    type: Function,
    default: null,
  },
})

const emit = defineEmits(['retry'])
const localeStore = useLocaleStore()
const isRetrying = ref(false)

const defaultTitle = computed(() =>
  localeStore.isRtl ? 'تعذر تحميل البيانات' : 'Failed to Load Data'
)

const defaultMessage = computed(() =>
  localeStore.isRtl
    ? 'حدث خطأ أثناء جلب البيانات من الخادم، يرجى المحاولة مرة أخرى.'
    : 'An error occurred while fetching data from the server. Please try again.'
)

const defaultRetryLabel = computed(() =>
  localeStore.isRtl ? 'إعادة المحاولة' : 'Try Again'
)

const handleRetry = async () => {
  isRetrying.value = true
  try {
    if (typeof props.onRetry === 'function') {
      await props.onRetry()
    }
    emit('retry')
  } finally {
    isRetrying.value = false
  }
}
</script>
