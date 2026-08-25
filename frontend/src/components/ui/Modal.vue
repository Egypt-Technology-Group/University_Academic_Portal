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
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
          <div
            :class="[
              'relative transform overflow-hidden rounded-2xl bg-white text-start shadow-2xl transition-all w-full my-8 border border-slate-100 flex flex-col',
              maxWidthClasses[maxWidth] || maxWidthClasses.lg,
            ]"
            @click.stop
          >
            <!-- Header -->
            <div
              v-if="title || $slots.title"
              class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50"
            >
              <h3 class="text-lg font-bold text-navy-950">
                <slot name="title">{{ title }}</slot>
              </h3>
              <button
                type="button"
                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
                @click="close"
              >
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 overflow-y-auto max-h-[calc(85vh-120px)]">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div
              v-if="$slots.footer"
              class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-end gap-3"
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
