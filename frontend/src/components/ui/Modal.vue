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
          class="modal-backdrop fixed inset-0 bg-navy-950/70 backdrop-blur-sm"
          @click="closeOnBackdrop && close()"
        ></div>

        <!-- Dialog Container -->
        <div class="flex min-h-full items-end sm:items-center justify-center p-2 sm:p-6 text-center">
          <div
            ref="modalContainer"
            tabindex="-1"
            :class="[
              'modal-panel relative transform overflow-hidden rounded-2xl sm:rounded-3xl bg-white text-start shadow-2xl w-full my-2 sm:my-8 border border-slate-100 flex flex-col focus:outline-none',
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
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'

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

const modalContainer = ref(null)

const handleKeydown = (e) => {
  if (!props.modelValue) return

  if (e.key === 'Escape' && props.closeOnEsc) {
    close()
    return
  }

  if (e.key === 'Tab' && modalContainer.value) {
    const focusable = modalContainer.value.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    )
    if (focusable.length === 0) return
    const first = focusable[0]
    const last = focusable[focusable.length - 1]

    if (e.shiftKey) {
      if (document.activeElement === first) {
        last.focus()
        e.preventDefault()
      }
    } else {
      if (document.activeElement === last) {
        first.focus()
        e.preventDefault()
      }
    }
  }
}

watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden'
      nextTick(() => {
        if (modalContainer.value) {
          const first = modalContainer.value.querySelector('input, select, textarea, button:not([aria-label="Close"])')
          if (first) {
            first.focus()
          } else {
            modalContainer.value.focus()
          }
        }
      })
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
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-backdrop,
.modal-leave-active .modal-backdrop {
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-panel,
.modal-leave-active .modal-panel {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-backdrop,
.modal-leave-to .modal-backdrop {
  opacity: 0;
}

.modal-enter-from .modal-panel,
.modal-leave-to .modal-panel {
  opacity: 0;
  transform: translateY(1rem) scale(0.95);
}

@media (min-width: 640px) {
  .modal-enter-from .modal-panel,
  .modal-leave-to .modal-panel {
    transform: translateY(0) scale(0.95);
  }
}

.modal-enter-to .modal-panel,
.modal-leave-from .modal-panel {
  opacity: 1;
  transform: translateY(0) scale(1);
}
</style>
