<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        @keydown.esc="closeOnEsc && close()"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm transition-opacity"
          @click="closeOnBackdrop && close()"
        ></div>

        <!-- Dialog Container -->
        <div class="flex min-h-full items-end sm:items-center justify-center p-2 sm:p-6 text-center">
          <div
            :class="[
              'relative transform overflow-hidden rounded-2xl sm:rounded-3xl bg-white text-start shadow-2xl transition-all w-full my-2 sm:my-8 border border-slate-100 flex flex-col',
              maxWidthClasses[size || maxWidth] || maxWidthClasses.lg,
            ]"
            @click.stop
          >
            <!-- Header -->
            <div
              v-if="title || $slots.title"
              class="flex items-center justify-between border-b border-slate-100 px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/50"
            >
              <h3 class="text-base sm:text-lg font-bold text-navy-950 truncate pe-2">
                <slot name="title">{{ title }}</slot>
              </h3>
              <button
                type="button"
                class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors shrink-0"
                @click="close"
              >
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-4 sm:px-6 py-4 sm:py-5 overflow-y-auto max-h-[calc(85vh-100px)]">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div
              v-if="$slots.footer"
              class="border-t border-slate-100 px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50 flex flex-wrap items-center justify-end gap-2 sm:gap-3"
            >
              <slot name="footer"></slot>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  maxWidth: {
    type: String,
    default: 'lg',
    validator: (m) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl'].includes(m),
  },
  size: {
    type: String,
    default: '',
    validator: (s) => !s || ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl'].includes(s),
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true,
  },
  closeOnEsc: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['update:modelValue', 'close'])

const close = () => {
  emit('update:modelValue', false)
  emit('close')
}

const maxWidthClasses = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  '2xl': 'max-w-2xl',
  '3xl': 'max-w-3xl',
  '4xl': 'max-w-4xl',
  '5xl': 'max-w-5xl',
}

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.modelValue && props.closeOnEsc) {
    close()
  }
}

watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
)

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
