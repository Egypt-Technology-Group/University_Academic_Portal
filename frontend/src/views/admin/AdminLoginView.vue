<template>
  <div class="min-h-screen bg-gradient-to-br from-navy-950 via-navy-900 to-slate-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden selection:bg-gold-500 selection:text-white" :dir="localeStore.dir">
    <!-- Ambient Background Glow & Decorative Elements -->
    <div class="absolute top-0 -left-20 w-96 h-96 bg-navy-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 -right-20 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <!-- Top Corner Actions: Language & Public Return -->
    <div class="absolute top-6 start-6 end-6 flex items-center justify-between z-10">
      <router-link
        to="/"
        class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-300 hover:text-gold-400 bg-navy-900/80 hover:bg-navy-800/90 border border-navy-700/80 px-3.5 py-2 rounded-xl backdrop-blur-md transition-all shadow-sm"
      >
        <ArrowLeft v-if="!localeStore.isRtl" class="w-4 h-4" />
        <ArrowRight v-else class="w-4 h-4" />
        <span>{{ $t('admin.login.backToPortal') }}</span>
      </router-link>

      <button
        type="button"
        class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-gold-400 hover:text-gold-300 bg-navy-900/80 border border-navy-700/80 px-3 py-2 rounded-xl backdrop-blur-md transition-colors"
        @click="localeStore.toggleLocale"
      >
        <Globe class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'English' : 'العربية' }}</span>
      </button>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <!-- University Brand Seal Header -->
      <div class="flex flex-col items-center text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gold-400 via-gold-500 to-gold-600 flex items-center justify-center text-navy-950 shadow-gold-glow mb-4 ring-4 ring-white/10">
          <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v7m-3-4l3-3 3 3" />
          </svg>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
          {{ $t('admin.login.title') }}
        </h2>
        <p class="text-sm text-slate-400 mt-1 max-w-xs font-medium">
          {{ $t('admin.login.subtitle') }}
        </p>
      </div>

      <!-- Main Login Glass Card -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <!-- Error Alert -->
        <div
          v-if="errorMessage"
          class="mb-6 bg-red-500/15 border border-red-500/40 rounded-2xl p-4 flex items-start gap-3 text-red-200 text-xs sm:text-sm animate-shake"
        >
          <AlertCircle class="w-5 h-5 text-red-400 shrink-0 mt-0.5" />
          <div class="flex-1">
            <div class="font-bold text-red-300">{{ $t('admin.login.errorTitle') }}</div>
            <div class="mt-0.5">{{ errorMessage }}</div>
          </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Email Field -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
              {{ $t('admin.login.emailLabel') }}
            </label>
            <div class="relative rounded-2xl shadow-xs">
              <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-slate-400">
                <Mail class="w-5 h-5" />
              </div>
              <input
                v-model="form.email"
                type="email"
                required
                autocomplete="username"
                :placeholder="$t('admin.login.emailPlaceholder')"
                class="block w-full rounded-2xl border-0 bg-navy-950/60 text-white placeholder:text-slate-500 ps-10 pe-4 py-3 text-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-gold-500 transition-all"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
              {{ $t('admin.login.passwordLabel') }}
            </label>
            <div class="relative rounded-2xl shadow-xs">
              <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-slate-400">
                <Lock class="w-5 h-5" />
              </div>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="current-password"
                :placeholder="$t('admin.login.passwordPlaceholder')"
                class="block w-full rounded-2xl border-0 bg-navy-950/60 text-white placeholder:text-slate-500 ps-10 pe-11 py-3 text-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-gold-500 transition-all"
              />
              <button
                type="button"
                class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-slate-400 hover:text-white transition-colors"
                @click="showPassword = !showPassword"
              >
                <EyeOff v-if="showPassword" class="w-5 h-5" />
                <Eye v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded text-gold-500 focus:ring-gold-400 border-slate-700 bg-navy-900"
              />
              <span class="text-xs font-medium text-slate-300">{{ $t('admin.login.rememberMe') }}</span>
            </label>
            <span class="text-xs text-slate-400">SSL 256-bit Encrypted</span>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full relative flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-navy-950 font-black px-4 py-3.5 text-sm shadow-gold-glow hover:shadow-lg transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
            >
              <div v-if="isLoading" class="w-5 h-5 border-2 border-navy-950 border-t-transparent rounded-full animate-spin"></div>
              <LogIn v-else class="w-5 h-5" />
              <span>{{ isLoading ? $t('admin.login.signingIn') : $t('admin.login.submitButton') }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Security Notice -->
      <div class="text-center mt-6 text-xs text-slate-400/80">
        {{ $t('admin.login.securityNotice') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useLocaleStore } from '../../stores/locale'
import {
  Mail,
  Lock,
  Eye,
  EyeOff,
  LogIn,
  AlertCircle,
  Sparkles,
  ArrowLeft,
  ArrowRight,
  Globe,
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const localeStore = useLocaleStore()

const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const form = reactive({
  email: 'admin@university.edu.eg',
  password: 'admin123',
  remember: true,
})

const quickFill = (email, password) => {
  form.email = email
  form.password = password
  errorMessage.value = ''
}

const handleSubmit = async () => {
  errorMessage.value = ''
  isLoading.value = true
  try {
    await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember,
    })

    const redirectPath = route.query.redirect || '/admin/dashboard'
    router.push(redirectPath)
  } catch (err) {
    errorMessage.value = authStore.error || err.message || 'فشل تسجيل الدخول. يرجى التحقق من البيانات.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-4px); }
  40%, 80% { transform: translateX(4px); }
}
.animate-shake {
  animation: shake 0.4s ease-in-out;
}
</style>
