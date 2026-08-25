<template>
  <div class="min-h-screen bg-slate-100 text-slate-800 flex antialiased selection:bg-gold-500 selection:text-white" :dir="localeStore.dir">
    <!-- Mobile Backdrop -->
    <div
      v-if="isMobileMenuOpen"
      class="fixed inset-0 z-40 bg-navy-950/60 backdrop-blur-xs lg:hidden transition-opacity"
      @click="isMobileMenuOpen = false"
    ></div>

    <!-- Admin Sidebar -->
    <aside
      :class="[
        'fixed top-0 bottom-0 z-50 flex flex-col bg-navy-950 text-white transition-all duration-300 shadow-2xl border-e border-navy-900/60 select-none',
        localeStore.isRtl ? 'right-0' : 'left-0',
        isCollapsed ? 'w-20' : 'w-72',
        isMobileMenuOpen ? 'translate-x-0' : (localeStore.isRtl ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0')
      ]"
    >
      <!-- Sidebar Brand Header -->
      <div class="h-20 flex items-center px-4 border-b border-navy-900/80 bg-navy-950/80 shrink-0 gap-3 justify-between">
        <router-link to="/admin/dashboard" class="flex items-center gap-3 overflow-hidden group">
          <!-- Crest Logo -->
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-navy-950 font-black shadow-gold-glow shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v7m-3-4l3-3 3 3" />
            </svg>
          </div>

          <div v-show="!isCollapsed" class="text-start transition-opacity duration-200 truncate">
            <div class="font-black text-base text-white tracking-wide leading-none flex items-center gap-1.5">
              <span>{{ $t('admin.sidebar.brandTitle') }}</span>
              <span class="text-[10px] bg-gold-500/20 text-gold-400 border border-gold-500/30 px-1.5 py-0.5 rounded font-mono font-bold">ADM</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium truncate mt-0.5">
              {{ $t('admin.sidebar.brandSubtitle') }}
            </div>
          </div>
        </router-link>

        <!-- Desktop Collapse Button -->
        <button
          type="button"
          class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-navy-900 transition-colors"
          :title="isCollapsed ? $t('admin.sidebar.expand') : $t('admin.sidebar.collapse')"
          @click="isCollapsed = !isCollapsed"
        >
          <ChevronRight v-if="(!localeStore.isRtl && isCollapsed) || (localeStore.isRtl && !isCollapsed)" class="w-4 h-4" />
          <ChevronLeft v-else class="w-4 h-4" />
        </button>

        <!-- Mobile Close Button -->
        <button
          type="button"
          class="lg:hidden text-slate-400 hover:text-white p-1"
          @click="isMobileMenuOpen = false"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- System Status Pill -->
      <div v-show="!isCollapsed" class="px-4 py-3 bg-navy-900/40 border-b border-navy-900/60 shrink-0">
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-slate-300 font-medium">{{ $t('admin.sidebar.systemOnline') }}</span>
          </div>
          <span class="text-[11px] text-gold-400/90 font-mono font-semibold bg-navy-950 px-2 py-0.5 rounded border border-navy-800">
            {{ $t('admin.sidebar.termFall2025') }}
          </span>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto no-scrollbar">
        <template v-for="(group, gIdx) in navigationMenu" :key="gIdx">
          <div v-if="!isCollapsed && group.title" class="px-3 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400/80">
            {{ group.title }}
          </div>

          <router-link
            v-for="item in group.items"
            :key="item.path"
            :to="item.path"
            :class="[
              'flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 group relative',
              isActiveRoute(item.path)
                ? 'bg-gradient-to-r from-navy-800 to-navy-700 text-gold-400 shadow-md border border-navy-700/60 font-bold'
                : 'text-slate-300 hover:text-white hover:bg-navy-900/70'
            ]"
            @click="isMobileMenuOpen = false"
          >
            <!-- Active Indicator Pill -->
            <span
              v-if="isActiveRoute(item.path)"
              :class="[
                'absolute top-2 bottom-2 w-1 bg-gold-400 rounded-full',
                localeStore.isRtl ? 'right-0' : 'left-0'
              ]"
            ></span>

            <component
              :is="item.icon"
              :class="[
                'w-5 h-5 shrink-0 transition-colors',
                isActiveRoute(item.path) ? 'text-gold-400' : 'text-slate-400 group-hover:text-slate-200'
              ]"
            />

            <span v-show="!isCollapsed" class="truncate flex-1 text-start">
              {{ item.label }}
            </span>

            <!-- Optional Badge -->
            <span
              v-if="!isCollapsed && item.badge"
              :class="[
                'text-xs px-2 py-0.5 rounded-full font-mono font-bold shrink-0',
                item.badgeVariant === 'warning' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-navy-800 text-slate-300'
              ]"
            >
              {{ item.badge }}
            </span>
          </router-link>
        </template>
      </nav>

      <!-- Sidebar Footer / Utility -->
      <div class="p-3 border-t border-navy-900/80 bg-navy-950/90 shrink-0 space-y-2">
        <!-- Quick Link to Public Portal -->
        <router-link
          to="/"
          class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-navy-900 transition-colors border border-navy-900"
          :title="$t('admin.sidebar.returnPublicPortal')"
        >
          <ExternalLink class="w-4 h-4 text-gold-400 shrink-0" />
          <span v-show="!isCollapsed" class="truncate">{{ $t('admin.sidebar.returnPublicPortal') }}</span>
        </router-link>

        <!-- User Quick Card -->
        <div
          v-show="!isCollapsed"
          class="flex items-center gap-3 p-2 rounded-xl bg-navy-900/60 border border-navy-800/80"
        >
          <img
            :src="authStore.userAvatar"
            :alt="authStore.userName"
            class="w-9 h-9 rounded-full object-cover ring-2 ring-gold-500/40 shrink-0"
          />
          <div class="overflow-hidden flex-1 text-start">
            <div class="text-xs font-bold text-white truncate">{{ authStore.userName }}</div>
            <div class="text-[11px] text-gold-400 font-medium truncate">{{ userRoleLabel }}</div>
          </div>
          <button
            type="button"
            class="text-slate-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-navy-800 transition-colors shrink-0"
            :title="$t('admin.header.logout')"
            @click="handleLogout"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Container with dynamic left/right margin for sidebar -->
    <div
      :class="[
        'flex-1 flex flex-col min-h-screen transition-all duration-300 w-full',
        localeStore.isRtl
          ? (isCollapsed ? 'lg:mr-20' : 'lg:mr-72')
          : (isCollapsed ? 'lg:ml-20' : 'lg:ml-72')
      ]"
    >
      <!-- Admin Top Bar Header -->
      <header class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between gap-4 shadow-xs">
        <!-- Left: Hamburger (Mobile) + Breadcrumbs -->
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-navy-950 hover:bg-slate-100 transition-colors"
            @click="isMobileMenuOpen = true"
          >
            <Menu class="w-6 h-6" />
          </button>

          <!-- Current Route Title / Location -->
          <div class="flex items-center gap-2">
            <h1 class="text-base sm:text-lg font-black text-navy-950 tracking-tight">
              {{ currentRouteTitle }}
            </h1>
          </div>
        </div>

        <!-- Right: Actions, Language Switch, User Profile -->
        <div class="flex items-center gap-2 sm:gap-4">
          <!-- Current Academic Date Badge -->
          <div class="hidden md:flex items-center gap-1.5 text-xs text-slate-500 font-medium bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200/60">
            <Calendar class="w-3.5 h-3.5 text-gold-500" />
            <span>{{ formattedCurrentDate }}</span>
          </div>

          <!-- Language Switcher Button -->
          <button
            type="button"
            class="flex items-center gap-1.5 text-xs font-bold text-navy-900 bg-slate-100 hover:bg-slate-200 px-2.5 py-1.5 rounded-lg border border-slate-200 transition-colors"
            @click="localeStore.toggleLocale"
          >
            <Globe class="w-3.5 h-3.5 text-gold-600" />
            <span>{{ localeStore.isRtl ? 'English' : 'العربية' }}</span>
          </button>

          <!-- Divider -->
          <div class="h-6 w-px bg-slate-200"></div>

          <!-- User Profile Dropdown / Quick Summary -->
          <div class="flex items-center gap-2.5">
            <img
              :src="authStore.userAvatar"
              :alt="authStore.userName"
              class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover ring-2 ring-navy-900/20"
            />
            <div class="hidden sm:block text-start leading-tight">
              <span class="block text-xs sm:text-sm font-bold text-navy-950 truncate max-w-[140px]">
                {{ authStore.userName }}
              </span>
              <span class="inline-block text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                {{ userRoleLabel }}
              </span>
            </div>

            <!-- Logout Button -->
            <button
              type="button"
              class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors"
              :title="$t('admin.header.logout')"
              @click="handleLogout"
            >
              <LogOut class="w-4 h-4 sm:w-5 sm:h-5" />
            </button>
          </div>
        </div>
      </header>

      <!-- Main Body Content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

      <!-- Admin Footer -->
      <footer class="border-t border-slate-200/80 bg-white px-6 py-4 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
          {{ $t('admin.footer.copyright') }}
        </div>
        <div class="flex items-center gap-4 text-slate-400">
          <span>{{ $t('admin.footer.portalVersion') }}</span>
          <span>•</span>
          <span class="text-emerald-600 font-medium flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            {{ $t('admin.footer.allSystemsNominal') }}
          </span>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import { useLocaleStore } from '../../stores/locale'
import {
  LayoutDashboard,
  UserCheck,
  GraduationCap,
  Newspaper,
  Calendar,
  FolderArchive,
  Palette,
  ExternalLink,
  ChevronRight,
  ChevronLeft,
  Menu,
  X,
  Globe,
  LogOut,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const { t, locale } = useI18n()
const authStore = useAuthStore()
const localeStore = useLocaleStore()

const isCollapsed = ref(false)
const isMobileMenuOpen = ref(false)

const isActiveRoute = (path) => {
  if (path === '/admin/dashboard') {
    return route.path === '/admin/dashboard' || route.path === '/admin'
  }
  return route.path.startsWith(path)
}

const currentRouteTitle = computed(() => {
  if (route.path.includes('/admin/admissions')) return t('admin.nav.admissions')
  if (route.path.includes('/admin/cms')) return t('admin.nav.cms')
  if (route.path.includes('/admin/events')) return t('admin.nav.events')
  if (route.path.includes('/admin/documents')) return t('admin.nav.documents')
  if (route.path.includes('/admin/settings')) return t('admin.nav.settings')
  return t('admin.nav.dashboard')
})

const userRoleLabel = computed(() => {
  if (authStore.isSuperAdmin) return t('admin.roles.superAdmin')
  if (authStore.isAdmissionsOfficer) return t('admin.roles.admissionsOfficer')
  return t('admin.roles.admin')
})

const formattedCurrentDate = computed(() => {
  const now = new Date()
  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }
  return now.toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-US', options)
})

const navigationMenu = computed(() => [
  {
    title: t('admin.sidebar.groupOverview'),
    items: [
      {
        path: '/admin/dashboard',
        label: t('admin.nav.dashboard'),
        icon: LayoutDashboard,
      },
    ],
  },
  {
    title: t('admin.sidebar.groupAdmissions'),
    items: [
      {
        path: '/admin/admissions',
        label: t('admin.nav.admissions'),
        icon: UserCheck,
        badge: '14',
        badgeVariant: 'warning',
      },
      {
        path: '/admin/academic-services',
        label: localeStore.isRtl ? 'الخدمات الأكاديمية والطلابية' : 'Academic & Student Services',
        icon: GraduationCap,
      },
    ],
  },
  {
    title: t('admin.sidebar.groupContent'),
    items: [
      {
        path: '/admin/cms',
        label: t('admin.nav.cms'),
        icon: Newspaper,
      },
      {
        path: '/admin/events',
        label: t('admin.nav.events'),
        icon: Calendar,
      },
      {
        path: '/admin/documents',
        label: t('admin.nav.documents'),
        icon: FolderArchive,
      },
    ],
  },
  {
    title: t('admin.sidebar.groupSettings'),
    items: [
      {
        path: '/admin/settings',
        label: t('admin.nav.settings'),
        icon: Palette,
      },
    ],
  },
])

const handleLogout = async () => {
  if (window.confirm(t('admin.header.confirmLogoutPrompt'))) {
    await authStore.logout()
    router.push({ name: 'admin-login' })
  }
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
