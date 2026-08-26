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
      <router-link to="/" class="flex items-center gap-2 sm:gap-3 group shrink min-w-0" @click="closeAllDropdowns">
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

      <!-- Desktop Dynamic Responsive Navigation with Dropdown Groups -->
      <nav ref="navRef" class="hidden lg:flex items-center gap-1 xl:gap-1.5">
        <!-- Always Present: Home Link -->
        <router-link
          to="/"
          class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors shrink-0"
          :class="$route.path === '/' ? 'text-navy-950 bg-slate-100 font-bold' : 'text-slate-600 hover:text-navy-900 hover:bg-slate-50'"
          @click="closeAllDropdowns"
        >
          {{ $t('nav.home') }}
        </router-link>

        <!-- Navigation Groups with Clean Interactive Dropdowns -->
        <div
          v-for="group in navGroups"
          :key="group.id"
          class="relative"
          @mouseenter="openDropdown(group.id)"
          @mouseleave="scheduleCloseDropdown(group.id)"
        >
          <!-- Single item group rendered as a direct link -->
          <router-link
            v-if="group.items.length === 1"
            :to="group.items[0].to"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors shrink-0"
            :class="isPublicRouteActive(group.items[0].to) ? 'text-navy-950 bg-slate-100 font-bold' : 'text-slate-600 hover:text-navy-900 hover:bg-slate-50'"
            @click="closeAllDropdowns"
          >
            <component :is="group.items[0].icon" v-if="group.items[0].icon" class="w-4 h-4 text-slate-400" />
            <span>{{ resolveNavLabel(group.items[0].label) }}</span>
          </router-link>

          <!-- Multi-item dropdown trigger -->
          <template v-else-if="group.items.length > 1">
            <button
              type="button"
              class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 shrink-0"
              :class="isGroupActive(group) || activeDropdown === group.id ? 'text-navy-950 bg-slate-100 font-bold' : 'text-slate-600 hover:text-navy-900 hover:bg-slate-50'"
              :aria-expanded="activeDropdown === group.id"
              @click="toggleDropdown(group.id)"
            >
              <span>{{ resolveNavLabel(group.label) }}</span>
              <svg
                class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                :class="{ 'rotate-180 text-navy-950': activeDropdown === group.id }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu Popup with bridge padding to prevent premature closing -->
            <transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="transform scale-95 opacity-0 translate-y-1"
              enter-to-class="transform scale-100 opacity-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="transform scale-100 opacity-100 translate-y-0"
              leave-to-class="transform scale-95 opacity-0 translate-y-1"
            >
              <div
                v-if="activeDropdown === group.id"
                class="absolute top-full pt-1.5 z-50 min-w-[220px] max-w-[260px] start-0 ltr:left-0 rtl:right-0"
              >
                <div class="bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 px-1.5 space-y-0.5">
                  <router-link
                    v-for="item in group.items"
                    :key="item.id || item.to"
                    :to="item.to"
                    class="group/item flex items-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-all"
                    :class="isPublicRouteActive(item.to) ? 'bg-navy-50/80 text-navy-950 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-navy-950 font-medium'"
                    @click="closeAllDropdowns"
                  >
                    <!-- Item Icon -->
                    <div class="mt-0.5 w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 group-hover/item:bg-navy-950 group-hover/item:text-gold-400 transition-colors shrink-0">
                      <component :is="item.icon" class="w-3.5 h-3.5" />
                    </div>

                    <!-- Item Label & Optional Subtitle -->
                    <div class="min-w-0 flex-1">
                      <div class="truncate leading-tight">
                        {{ resolveNavLabel(item.label) }}
                      </div>
                      <div v-if="item.description" class="text-[11px] text-slate-400 font-normal truncate mt-0.5">
                        {{ resolveNavLabel(item.description) }}
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </transition>
          </template>
        </div>
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
          @click="closeAllDropdowns"
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

    <!-- Mobile Drawer Menu with Group Accordions -->
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

      <!-- Grouped Items in Mobile -->
      <div v-for="group in navGroups" :key="'mob-' + group.id" class="space-y-1">
        <!-- Single item in group -->
        <router-link
          v-if="group.items.length === 1"
          :to="group.items[0].to"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy-950"
          @click="mobileMenuOpen = false"
        >
          <component :is="group.items[0].icon" class="w-4 h-4 text-slate-400" />
          <span>{{ resolveNavLabel(group.items[0].label) }}</span>
        </router-link>

        <!-- Accordion for multiple items -->
        <div v-else-if="group.items.length > 1" class="border border-slate-100 rounded-xl overflow-hidden">
          <button
            type="button"
            class="w-full flex items-center justify-between px-4 py-2.5 text-base font-semibold text-slate-800 bg-slate-50/70 hover:bg-slate-100/80 transition-colors"
            @click="toggleMobileGroup(group.id)"
          >
            <span>{{ resolveNavLabel(group.label) }}</span>
            <svg
              class="w-4 h-4 text-slate-400 transition-transform duration-200"
              :class="{ 'rotate-180 text-navy-950': openMobileGroups.has(group.id) }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div v-if="openMobileGroups.has(group.id)" class="px-3 py-2 space-y-1 bg-white">
            <router-link
              v-for="item in group.items"
              :key="'mob-item-' + (item.id || item.to)"
              :to="item.to"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-navy-950"
              @click="mobileMenuOpen = false"
            >
              <component :is="item.icon" class="w-4 h-4 text-slate-400" />
              <span>{{ resolveNavLabel(item.label) }}</span>
            </router-link>
          </div>
        </div>
      </div>

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
import { ref, onMounted, onUnmounted, computed, h } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { useSettingsStore } from '../../stores/settings'
import { useModulesStore } from '../../stores/modules'
import SearchModal from '../ui/SearchModal.vue'

// Feather / Heroicon SVG helper definitions for clean aesthetics
const createIcon = (d) => {
  return () =>
    h(
      'svg',
      {
        class: 'w-4 h-4',
        fill: 'none',
        stroke: 'currentColor',
        viewBox: '0 0 24 24',
      },
      [
        h('path', {
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': '2',
          d,
        }),
      ]
    )
}

const Icons = {
  Colleges: createIcon('M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'),
  Programs: createIcon('M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'),
  Faculty: createIcon('M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'),
  Admissions: createIcon('M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'),
  Track: createIcon('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'),
  News: createIcon('M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'),
  Events: createIcon('M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'),
  Documents: createIcon('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'),
  Portal: createIcon('M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'),
  Default: createIcon('M13 10V3L4 14h7v7l9-11h-7z')
}

const itemIconMap = {
  '/colleges': Icons.Colleges,
  '/programs': Icons.Programs,
  '/faculty': Icons.Faculty,
  '/admissions': Icons.Admissions,
  '/admissions/track': Icons.Track,
  '/news': Icons.News,
  '/events': Icons.Events,
  '/documents': Icons.Documents,
  '/student-portal': Icons.Portal,
}

const route = useRoute()
const { t } = useI18n()
const localeStore = useLocaleStore()
const settingsStore = useSettingsStore()
const modulesStore = useModulesStore()
const mobileMenuOpen = ref(false)
const showSearchModal = ref(false)
const activeDropdown = ref(null)
const navRef = ref(null)
const openMobileGroups = ref(new Set(['academics', 'admissions', 'campus']))

let closeTimer = null

const publicNavItems = computed(() => {
  return modulesStore.getNavItems('public')
})

/**
 * Group enabled navigation items into logical semantic dropdown categories.
 */
const navGroups = computed(() => {
  const allItems = publicNavItems.value.map((item) => ({
    ...item,
    to: item.to || item.path,
    icon: itemIconMap[item.to || item.path] || Icons.Default,
  }))

  const groupsDef = [
    {
      id: 'academics',
      label: 'nav.academicsGroup',
      paths: ['/colleges', '/programs', '/faculty'],
    },
    {
      id: 'admissions',
      label: 'nav.admissionsGroup',
      paths: ['/admissions', '/admissions/track'],
    },
    {
      id: 'campus',
      label: 'nav.campusLifeGroup',
      paths: ['/news', '/events', '/documents'],
    },
    {
      id: 'portal',
      label: 'nav.studentPortal',
      paths: ['/student-portal'],
    },
  ]

  const result = []

  for (const g of groupsDef) {
    const matched = allItems.filter((i) => g.paths.includes(i.to))
    if (matched.length > 0) {
      result.push({
        id: g.id,
        label: g.label,
        items: matched,
      })
    }
  }

  // Any remaining custom or extension module links not matched in standard groups
  const categorizedPaths = groupsDef.flatMap((g) => g.paths)
  const leftover = allItems.filter((i) => !categorizedPaths.includes(i.to))

  for (const item of leftover) {
    result.push({
      id: item.id || item.to,
      label: item.label,
      items: [item],
    })
  }

  return result
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
  return route.path === path || route.path.startsWith(path + '/')
}

const isGroupActive = (group) => {
  if (!group || !group.items) return false
  return group.items.some((item) => isPublicRouteActive(item.to))
}

const openDropdown = (groupId) => {
  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }
  activeDropdown.value = groupId
}

const scheduleCloseDropdown = (groupId) => {
  if (closeTimer) clearTimeout(closeTimer)
  closeTimer = setTimeout(() => {
    if (activeDropdown.value === groupId) {
      activeDropdown.value = null
    }
  }, 180)
}

const toggleDropdown = (groupId) => {
  if (activeDropdown.value === groupId) {
    activeDropdown.value = null
  } else {
    activeDropdown.value = groupId
  }
}

const closeAllDropdowns = () => {
  if (closeTimer) clearTimeout(closeTimer)
  activeDropdown.value = null
}

const toggleMobileGroup = (groupId) => {
  if (openMobileGroups.value.has(groupId)) {
    openMobileGroups.value.delete(groupId)
  } else {
    openMobileGroups.value.add(groupId)
  }
}

const handleClickOutside = (e) => {
  if (navRef.value && !navRef.value.contains(e.target)) {
    closeAllDropdowns()
  }
}

const handleKeyboardShortcut = (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    showSearchModal.value = true
  }
  if (e.key === 'Escape') {
    closeAllDropdowns()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyboardShortcut)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyboardShortcut)
  document.removeEventListener('click', handleClickOutside)
  if (closeTimer) clearTimeout(closeTimer)
})
</script>
