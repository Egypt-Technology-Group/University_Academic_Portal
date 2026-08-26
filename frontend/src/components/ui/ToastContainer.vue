<template>
  <Teleport to="body">
    <div
      :class="[
        'fixed z-50 pointer-events-none p-4 sm:p-6 flex flex-col gap-3 max-w-sm sm:max-w-md w-full transition-all duration-300',
        positionClasses[toastStore.position] || positionClasses['top-end']
      ]"
      :dir="localeStore.dir"
      aria-live="polite"
      aria-atomic="true"
    >
      <TransitionGroup name="toast-list">
        <div
          v-for="toast in toastStore.toasts"
          :key="toast.id"
          :class="[
            'pointer-events-auto w-full rounded-2xl shadow-xl border p-4 flex items-start gap-3.5 backdrop-blur-md transition-all duration-300 transform',
            variantClasses[toast.variant] || variantClasses.info
          ]"
          role="alert"
        >
          <!-- Icon / Spinner -->
          <div :class="['w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5', iconBgClasses[toast.variant]]">
            <Loader2 v-if="toast.variant === 'loading'" class="w-5 h-5 animate-spin text-navy-800" />
            <CheckCircle2 v-else-if="toast.variant === 'success'" class="w-5 h-5 text-emerald-600" />
            <AlertCircle v-else-if="toast.variant === 'error'" class="w-5 h-5 text-red-600" />
            <AlertTriangle v-else-if="toast.variant === 'warning'" class="w-5 h-5 text-gold-600" />
            <Info v-else class="w-5 h-5 text-navy-800" />
          </div>

          <!-- Message Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <h4 v-if="toast.title" class="text-xs sm:text-sm font-bold text-slate-900 truncate">
                {{ toast.title }}
              </h4>
              <span
                v-if="toast.count > 1"
                class="px-1.5 py-0.5 text-[10px] font-black rounded-full bg-slate-200 text-slate-700"
              >
                ×{{ toast.count }}
              </span>
            </div>

            <p class="text-xs sm:text-sm text-slate-700 mt-0.5 leading-snug break-words">
              {{ toast.message }}
            </p>

            <!-- Custom Action Button -->
            <button
              v-if="toast.actionLabel && toast.onAction"
              type="button"
              class="mt-2 text-xs font-bold text-navy-950 underline hover:text-gold-600 transition-colors"
              @click="handleAction(toast)"
            >
              {{ toast.actionLabel }}
            </button>
          </div>

          <!-- Dismiss Button -->
          <button
            v-if="toast.dismissible !== false"
            type="button"
            class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-black/5 transition-colors shrink-0"
            @click="toastStore.dismiss(toast.id)"
          >
            <span class="sr-only">Dismiss</span>
            <X class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToastStore } from '../../stores/toast'
import { useLocaleStore } from '../../stores/locale'
import {
  CheckCircle2,
  AlertCircle,
  AlertTriangle,
  Info,
  Loader2,
  X,
} from 'lucide-vue-next'

const toastStore = useToastStore()
const localeStore = useLocaleStore()

const positionClasses = {
  'top-end': 'top-0 end-0',
  'top-start': 'top-0 start-0',
  'bottom-end': 'bottom-0 end-0',
  'bottom-start': 'bottom-0 start-0',
  'top-center': 'top-0 start-1/2 -translate-x-1/2 rtl:translate-x-1/2',
  'bottom-center': 'bottom-0 start-1/2 -translate-x-1/2 rtl:translate-x-1/2',
}

const variantClasses = {
  success: 'bg-white/95 border-emerald-200 text-slate-900 shadow-emerald-500/10',
  error: 'bg-white/95 border-red-200 text-slate-900 shadow-red-500/10',
  warning: 'bg-white/95 border-gold-200 text-slate-900 shadow-gold-500/10',
  info: 'bg-white/95 border-slate-200 text-slate-900 shadow-navy-950/10',
  loading: 'bg-white/95 border-navy-200 text-slate-900 shadow-navy-950/10',
}

const iconBgClasses = {
  success: 'bg-emerald-50 text-emerald-600',
  error: 'bg-red-50 text-red-600',
  warning: 'bg-gold-50 text-gold-600',
  info: 'bg-navy-50 text-navy-800',
  loading: 'bg-navy-50 text-navy-800',
}

const handleAction = (toast) => {
  if (typeof toast.onAction === 'function') {
    toast.onAction()
  }
  toastStore.dismiss(toast.id)
}
</script>

<style scoped>
.toast-list-enter-active,
.toast-list-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-list-enter-from {
  opacity: 0;
  transform: translateY(-12px) scale(0.95);
}

.toast-list-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.toast-list-move {
  transition: transform 0.3s ease;
}
</style>
