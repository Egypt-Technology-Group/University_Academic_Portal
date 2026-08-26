<template>
  <div class="min-h-[75vh] flex items-center justify-center px-4 py-16 bg-slate-50">
    <div class="max-w-xl w-full bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200/80 text-center space-y-6 animate-fade-in relative overflow-hidden">
      <!-- Decorative background accent -->
      <div class="absolute -top-12 -right-12 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
      <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-navy-950/5 rounded-full blur-2xl pointer-events-none"></div>

      <!-- Icon Badge -->
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/60 shadow-inner">
        <ShieldAlert class="w-10 h-10 animate-pulse" />
      </div>

      <!-- Main Headline -->
      <div class="space-y-2">
        <span class="inline-block px-3 py-1 text-xs font-bold font-mono tracking-wider text-amber-700 bg-amber-100/80 rounded-full uppercase border border-amber-200">
          {{ moduleId ? `MODULE: ${moduleId}` : 'OFFLINE MODULE' }}
        </span>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ localeStore.isRtl ? 'هذه الخدمة الأكاديمية معطلة حالياً' : 'Module Temporarily Offline' }}
        </h1>
      </div>

      <!-- Module Name & Description -->
      <div v-if="moduleInfo" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-start space-y-2">
        <div class="flex items-center justify-between gap-2">
          <span class="font-bold text-navy-950 text-sm sm:text-base">
            {{ moduleName }}
          </span>
          <span v-if="moduleInfo.version" class="text-[11px] font-mono font-semibold bg-slate-200 text-slate-700 px-2 py-0.5 rounded">
            v{{ moduleInfo.version }}
          </span>
        </div>
        <p v-if="moduleDescription" class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          {{ moduleDescription }}
        </p>
      </div>

      <!-- Explanatory message -->
      <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
        {{
          localeStore.isRtl
            ? 'تم إيقاف هذه الوحدة أو الخدمة الأكاديمية مؤقتاً لأعمال الصيانة والتطوير أو بناءً على إعدادات إدارة النظام.'
            : 'This academic service or module is currently disabled for maintenance, scheduled upgrades, or administrative policy.'
        }}
      </p>

      <!-- Action Buttons -->
      <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
        <Button
          variant="primary"
          size="md"
          rounded="xl"
          class="w-full sm:w-auto"
          @click="checkStatusAndRetry"
          :disabled="isChecking"
        >
          <RefreshCw class="w-4 h-4 me-2" :class="{ 'animate-spin': isChecking }" />
          <span>{{ localeStore.isRtl ? 'إعادة التحقق من الحالة' : 'Check Status & Retry' }}</span>
        </Button>

        <Button
          variant="outline"
          size="md"
          rounded="xl"
          to="/"
          class="w-full sm:w-auto"
        >
          <Home class="w-4 h-4 me-2" />
          <span>{{ $t('nav.home') }}</span>
        </Button>
      </div>

      <!-- Admin Direct Link if user has admin privileges -->
      <div v-if="authStore.isAuthenticated" class="pt-2 border-t border-slate-100 text-xs text-slate-500">
        <span>{{ localeStore.isRtl ? 'هل أنت مسؤول النظام؟' : 'Are you an administrator?' }}</span>
        <router-link to="/admin/dashboard" class="font-bold text-navy-950 hover:text-gold-600 underline ms-1">
          {{ localeStore.isRtl ? 'الانتقال إلى لوحة الإدارة' : 'Go to Admin Dashboard' }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ShieldAlert, RefreshCw, Home } from 'lucide-vue-next'
import { useLocaleStore } from '../stores/locale'
import { useModulesStore } from '../stores/modules'
import { useAuthStore } from '../stores/auth'
import { moduleRegistry } from '../core/modules/moduleRegistry'
import Button from '../components/ui/Button.vue'
import { useToast } from '../composables/useToast'

const route = useRoute()
const router = useRouter()
const localeStore = useLocaleStore()
const modulesStore = useModulesStore()
const authStore = useAuthStore()
const toast = useToast()

const isChecking = ref(false)

const moduleId = computed(() => {
  return route.query.module ? String(route.query.module).trim() : ''
})

const redirectPath = computed(() => {
  return route.query.redirect ? decodeURIComponent(String(route.query.redirect)) : '/'
})

const moduleInfo = computed(() => {
  if (!moduleId.value) return null
  return modulesStore.getModule(moduleId.value) || moduleRegistry.get(moduleId.value)
})

const moduleName = computed(() => {
  if (!moduleInfo.value?.name) return moduleId.value
  const locale = localeStore.locale
  if (typeof moduleInfo.value.name === 'string') return moduleInfo.value.name
  return moduleInfo.value.name[locale] || moduleInfo.value.name.ar || moduleInfo.value.name.en || moduleId.value
})

const moduleDescription = computed(() => {
  if (!moduleInfo.value?.description) return ''
  const locale = localeStore.locale
  if (typeof moduleInfo.value.description === 'string') return moduleInfo.value.description
  return moduleInfo.value.description[locale] || moduleInfo.value.description.ar || moduleInfo.value.description.en || ''
})

const checkStatusAndRetry = async () => {
  isChecking.value = true
  try {
    await modulesStore.fetchModules(true)
    if (moduleId.value && modulesStore.isModuleEnabled(moduleId.value)) {
      toast.success(
        localeStore.isRtl ? 'تم تفعيل الوحدة بنجاح! جاري تحويلك...' : 'Module is now active! Redirecting...'
      )
      setTimeout(() => {
        router.replace(redirectPath.value)
      }, 500)
    } else {
      toast.info(
        localeStore.isRtl ? 'الوحدة لا تزال معطلة حالياً.' : 'Module remains offline.'
      )
    }
  } catch (err) {
    toast.error(
      localeStore.isRtl ? 'تعذر التحقق من حالة الوحدة.' : 'Failed to verify module status.'
    )
  } finally {
    isChecking.value = false
  }
}
</script>
