<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="state.isOpen"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        @keydown.esc="handleCancel"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm transition-opacity"
          @click="handleCancel"
        ></div>

        <!-- Dialog Card -->
        <div
          ref="dialogRef"
          tabindex="-1"
          class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 flex flex-col focus:outline-none z-10 space-y-6 text-start"
          @click.stop
        >
          <!-- Icon & Header -->
          <div class="flex items-start gap-4">
            <div
              :class="[
                'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0',
                iconContainerClass[state.variant] || iconContainerClass.info
              ]"
            >
              <AlertTriangle v-if="state.variant === 'danger' || state.variant === 'warning'" :class="['w-6 h-6', iconClass[state.variant]]" />
              <CheckCircle2 v-else-if="state.variant === 'success'" :class="['w-6 h-6', iconClass[state.variant]]" />
              <Info v-else :class="['w-6 h-6', iconClass[state.variant]]" />
            </div>

            <div class="flex-1 min-w-0 pt-0.5">
              <h3 class="text-base sm:text-lg font-black text-navy-950 leading-snug">
                {{ state.title || defaultTitle }}
              </h3>
              <p v-if="state.message" class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line">
                {{ state.message }}
              </p>
            </div>
          </div>

          <!-- Prompt Input Field -->
          <div v-if="state.type === 'prompt'" class="space-y-1.5 pt-1">
            <label v-if="state.inputLabel" class="block text-xs font-bold text-slate-700">
              {{ state.inputLabel }}
            </label>
            <input
              ref="promptInputRef"
              v-model="state.inputValue"
              type="text"
              :placeholder="state.placeholder"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 font-medium focus:border-navy-900 focus:ring-1 focus:ring-navy-900 outline-none transition-all"
              @keydown.enter.prevent="handleConfirm"
            />
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-2">
            <Button
              v-if="state.type === 'confirm' || state.type === 'prompt'"
              type="button"
              variant="ghost"
              size="md"
              rounded="xl"
              @click="handleCancel"
            >
              {{ state.cancelText || defaultCancelText }}
            </Button>

            <Button
              ref="confirmBtnRef"
              type="button"
              :variant="confirmBtnVariant"
              size="md"
              rounded="xl"
              :loading="state.isLoading"
              @click="handleConfirm"
            >
              {{ state.confirmText || defaultConfirmText }}
            </Button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import { useDialog } from '../../composables/useDialog'
import Button from './Button.vue'
import { AlertTriangle, CheckCircle2, Info } from 'lucide-vue-next'

const { t, locale } = useI18n()
const { state, handleConfirm, handleCancel } = useDialog()

const promptInputRef = ref(null)
const confirmBtnRef = ref(null)
const dialogRef = ref(null)

const isRtl = computed(() => locale.value === 'ar')

const defaultTitle = computed(() => {
  if (state.type === 'alert') return isRtl.value ? 'تنبيه' : 'Notification'
  if (state.type === 'prompt') return isRtl.value ? 'إدخال البيانات' : 'Input Prompt'
  return isRtl.value ? 'تأكيد الإجراء' : 'Confirm Action'
})

const defaultConfirmText = computed(() => {
  if (state.type === 'alert') return isRtl.value ? 'حسناً' : 'OK'
  return isRtl.value ? 'تأكيد' : 'Confirm'
})

const defaultCancelText = computed(() => isRtl.value ? 'إلغاء' : 'Cancel')

const confirmBtnVariant = computed(() => {
  if (state.variant === 'danger') return 'danger'
  if (state.variant === 'warning') return 'gold'
  if (state.variant === 'success') return 'emerald'
  return 'primary'
})

const iconContainerClass = {
  danger: 'bg-red-50 text-red-600 border border-red-100',
  warning: 'bg-gold-50 text-gold-600 border border-gold-100',
  success: 'bg-emerald-50 text-emerald-600 border border-emerald-100',
  info: 'bg-navy-50 text-navy-800 border border-navy-100',
}

const iconClass = {
  danger: 'text-red-600',
  warning: 'text-gold-600',
  success: 'text-emerald-600',
  info: 'text-navy-800',
}

watch(
  () => state.isOpen,
  (open) => {
    if (open) {
      document.body.style.overflow = 'hidden'
      nextTick(() => {
        if (state.type === 'prompt' && promptInputRef.value) {
          promptInputRef.value.focus()
          promptInputRef.value.select?.()
        } else if (dialogRef.value) {
          dialogRef.value.focus()
        }
      })
    } else {
      document.body.style.overflow = ''
    }
  }
)
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.96);
}
</style>
