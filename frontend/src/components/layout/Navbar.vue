<template>
  <header class="sticky top-0 z-40 w-full bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all duration-300">
    <!-- Top Announcement & Utility Bar -->
    <div v-if="settingsStore.isTopAnnouncementActive" class="bg-navy-950 text-slate-200 text-[11px] sm:text-xs py-1 px-3 sm:px-8 border-b border-navy-900 announcement-bar">
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
        <!-- Urgent Alert Ticker / Headline -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-hidden flex-1 min-w-0">
          <span class="bg-gold-500 text-navy-950 font-bold px-1.5 sm:px-2 py-0.5 rounded text-[9px] sm:text-[11px] uppercase tracking-wider flex items-center gap-1 shrink-0 animate-pulse">
            <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-navy-950"></span>
            {{ $t('nav.urgent') }}
          </span>
          <router-link
            :to="settingsStore.topAnnouncementLink"
            class="text-slate-300 hover:text-gold-400 transition-colors truncate text-[11px] sm:text-xs font-medium"
          >
            {{ settingsStore.topAnnouncementText(localeStore.locale) }}
          </router-link>
        </div>

        <!-- Right Quick Links & Language Toggle -->
        <div class="flex items-center gap-2 sm:gap-4 ms-auto text-xs shrink-0">
          <router-link
            v-if="modulesStore.isModuleEnabled('results')"
            to="/student-portal"
            class="hidden md:inline-flex items-center gap-1 text-slate-300 hover:text-white transition-colors"
          >
            <svg class="w-3.5 h-3.5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
            {{ $t('nav.studentPortal') }}
          </router-link>

          <span v-if="modulesStore.isModuleEnabled('results')" class="hidden md:inline text-navy-700">|</span>

          <router-link
            v-if="modulesStore.isModuleEnabled('admissions')"
            to="/admissions/track"
            class="hidden sm:inline-flex items-center gap-1 text-slate-300 hover:text-white transition-colors"
          >
            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            {{ $t('nav.trackApp') }}
          </router-link>

          <span v-if="modulesStore.isModuleEnabled('admissions')" class="hidden sm:inline text-navy-700">|</span>

          <router-link
            to="/admin"
            class="hidden lg:inline-flex items-center gap-1 text-slate-400 hover:text-gold-400 transition-colors"
          >
            <svg class="w-3.5 h-3.5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            {{ $t('admin.sidebar.brandSubtitle') }}
          </router-link>

          <span class="hidden lg:inline text-navy-700">|</span>

          <!-- Language Switcher Button -->
          <button
            type="button"
            class="flex items-center gap-1 text-gold-400 hover:text-gold-300 font-bold px-1.5 sm:px-2 py-0.5 rounded bg-navy-900 border border-navy-800 transition-colors text-[10px] sm:text-xs"
            @click="localeStore.toggleLocale"
          >
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
            <span>{{ $t('nav.language') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-3 sm:px-8 py-2.5 sm:py-3 flex items-center justify-between gap-2 sm:gap-4">
      <!-- University Crest & Brand Logo -->
      <router-link to="/" class="flex items-center gap-2 sm:gap-3 group shrink min-w-0">
        <!-- Academic Crest Shield SVG -->
        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 flex items-center justify-center text-gold-400 shadow-md border border-gold-500/30 group-hover:scale-105 transition-transform duration-300 shrink-0">
          <svg class="w-5 h-5 sm:w-7 sm:h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v7m-3-4l3-3 3 3" />
          </svg>
        </div>
        <div class="text-start min-w-0">
          <span class="block font-black text-sm sm:text-lg lg:text-xl text-navy-950 tracking-tight leading-tight truncate group-hover:text-navy-800 transition-colors">
            {{ settingsStore.siteShortName(localeStore.locale) }}
          </span>
          <span class="block text-[10px] sm:text-xs text-slate-500 font-medium leading-none truncate max-w-[140px] xs:max-w-[200px] sm:max-w-none">
            {{ settingsStore.siteSlogan(localeStore.locale) || (localeStore.isRtl ? 'جامعة التكنولوجيا والعلوم التطبيقية' : 'University of Technology') }}
          </span>
        </div>
      </router-link>

      <!-- Desktop Dynamic Mega-Menu Nav Links -->
      <nav class="hidden lg:flex items-center gap-1 xl:gap-2">
        <!-- Always Present: Home Link -->
        <router-link
          to="/"
          class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
          :class="$route.path === '/' ? 'text-navy-950 bg-slate-100 font-bold' : 'text-slate-600 hover:text-navy-900 hover:bg-slate-50'"
        >
          {{ $t('nav.home') }}
        </router-link>

        <!-- Dynamically Rendered Enabled Module Public Nav Links -->
        <router-link
          v-for="item in publicNavItems"
          :key="item.id || item.to"
          :to="item.to || item.path"
          class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
          :class="isPublicRouteActive(item.to || item.path) ? 'text-navy-950 bg-slate-100 font-bold' : 'text-slate-600 hover:text-navy-900 hover:bg-slate-50'"
        >
          {{ resolveNavLabel(item.label) }}
        </router-link>
      </nav>

      <!-- Search Trigger & Actions -->
      <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
        <!-- Quick Search Button -->
        <button
          type="button"
          class="flex items-center gap-1.5 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-slate-500 bg-slate-100 hover:bg-slate-200/80 rounded-xl transition-colors border border-slate-200"
          :title="$t('nav.quickSearch')"
          @click="showSearchModal = true"
        >
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <span class="hidden md:inline">{{ $t('nav.quickSearch') }}</span>
          <kbd class="hidden xl:inline text-[10px] bg-white text-slate-500 px-1.5 py-0.5 rounded border border-slate-300 font-mono">⌘K</kbd>
        </button>

        <!-- Primary CTA: Apply Now (if Admissions module is enabled) -->
        <router-link
          v-if="modulesStore.isModuleEnabled('admissions')"
          to="/admissions"
          class="hidden sm:inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-navy-950 bg-gold-400 hover:bg-gold-300 rounded-xl shadow-sm hover:shadow-gold-glow transition-all duration-200"
        >
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <span>{{ $t('nav.applyNow') }}</span>
        </router-link>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          class="lg:hidden p-1.5 sm:p-2 text-slate-700 hover:bg-slate-100 rounded-xl transition-colors shrink-0"
          :aria-label="mobileMenuOpen ? 'Close navigation menu' : 'Open navigation menu'"
          @click="mobileMenuOpen = !mobileMenuOpen"
        >
          <svg v-if="!mobileMenuOpen" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div
      v-if="mobileMenuOpen"
      class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2 max-h-[80vh] overflow-y-auto shadow-xl animate-fade-in"
    >
      <!-- Always Present: Home Link -->
      <router-link
        to="/"
        class="block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy-950"
        @click="mobileMenuOpen = false"
      >
        {{ $t('nav.home') }}
      </router-link>

      <!-- Dynamically Rendered Enabled Module Public Nav Links -->
      <router-link
        v-for="item in publicNavItems"
        :key="item.id || item.to"
        :to="item.to || item.path"
        class="block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy-950"
        @click="mobileMenuOpen = false"
      >
        {{ resolveNavLabel(item.label) }}
      </router-link>

      <!-- Admin Portal Quick Link -->
      <router-link
        to="/admin"
        class="block px-4 py-2.5 rounded-xl text-base font-semibold text-gold-700 bg-gold-50 hover:bg-gold-100"
        @click="mobileMenuOpen = false"
      >
        ⚙️ {{ $t('admin.sidebar.brandSubtitle') }}
      </router-link>

      <!-- Apply Now CTA (if Admissions module is enabled) -->
      <div v-if="modulesStore.isModuleEnabled('admissions')" class="pt-3 border-t border-slate-100 flex flex-col gap-2">
        <router-link
          to="/admissions"
          class="w-full text-center py-3 font-bold text-navy-950 bg-gold-400 hover:bg-gold-300 rounded-xl shadow-sm"
          @click="mobileMenuOpen = false"
        >
          {{ $t('nav.applyNow') }}
        </router-link>
      </div>
    </div>

    <!-- Quick Search Modal Component -->
    <SearchModal v-model="showSearchModal" />
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { useSettingsStore } from '../../stores/settings'
import { useModulesStore } from '../../stores/modules'
import SearchModal from '../ui/SearchModal.vue'

const route = useRoute()
const { t } = useI18n()
const localeStore = useLocaleStore()
const settingsStore = useSettingsStore()
const modulesStore = useModulesStore()
const mobileMenuOpen = ref(false)
const showSearchModal = ref(false)

const publicNavItems = computed(() => {
  return modulesStore.getNavItems('public')
})

const resolveNavLabel = (label) => {
  if (!label) return ''
  if (typeof label === 'string') {
    return label.startsWith('nav.') || label.startsWith('admin.') || label.includes('.')
      ? t(label)
      : label
  }
  if (typeof label === 'object') {
    return label[localeStore.locale] || label.ar || label.en || ''
  }
  return String(label)
}

const isPublicRouteActive = (path) => {
  if (!path) return false
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

const handleKeyboardShortcut = (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    showSearchModal.value = true
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyboardShortcut)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyboardShortcut)
})
</script>
