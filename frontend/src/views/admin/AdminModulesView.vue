<template>
  <div class="space-y-6" :dir="localeStore.dir">
    <!-- Header: Title, Global Health Indicator, Refresh Button -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
      <div class="space-y-1">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-2xl bg-navy-950 text-gold-400 flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
            <Blocks class="w-6 h-6 text-gold-400" />
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-xl sm:text-2xl font-black text-navy-950 tracking-tight">
                {{ $t('admin.modules.title') }}
              </h1>
              <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-full bg-navy-100 text-navy-800 border border-navy-200">
                v2.5.0 Modular Core
              </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
              {{ $t('admin.modules.subtitle') }}
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <!-- Global Operational Status Pill -->
        <div
          class="px-3.5 py-2 rounded-xl text-xs font-bold border inline-flex items-center gap-2 shadow-xs"
          :class="isAllOperational ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'"
        >
          <span class="relative flex h-2.5 w-2.5">
            <span
              class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
              :class="isAllOperational ? 'bg-emerald-400' : 'bg-amber-400'"
            ></span>
            <span
              class="relative inline-flex rounded-full h-2.5 w-2.5"
              :class="isAllOperational ? 'bg-emerald-500' : 'bg-amber-500'"
            ></span>
          </span>
          <span>
            {{ isAllOperational
              ? $t('admin.modules.allOperational', { active: enabledCount, total: totalModulesCount })
              : $t('admin.modules.partiallyActive', { active: enabledCount, total: totalModulesCount })
            }}
          </span>
        </div>

        <!-- Refresh Button -->
        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all inline-flex items-center gap-2 border border-slate-200 shadow-xs cursor-pointer active:scale-95"
          :disabled="isRefreshing"
          @click="handleRefreshModules"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin text-navy-900': isRefreshing }" />
          <span>{{ isRefreshing ? $t('admin.modules.refreshing') : $t('admin.modules.refresh') }}</span>
        </button>
      </div>
    </div>

    <!-- Enterprise Cryptographic License & Subscription Entitlement Control Ribbon -->
    <div class="bg-navy-950 text-white rounded-3xl p-5 sm:p-6 shadow-md border border-navy-900 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-start sm:items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-gold-500/20 text-gold-400 border border-gold-500/30 flex items-center justify-center shrink-0">
          <ShieldCheck class="w-6 h-6 text-gold-400" />
        </div>
        <div class="space-y-1">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs uppercase tracking-wider font-mono font-bold text-gold-400">
              {{ localeStore.isRtl ? 'الترخيص المؤسسي وحالة الاشتراك' : 'Enterprise Subscription & Licensing' }}
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
              {{ entitlementState?.entitlement ? (entitlementState.entitlement.tier.toUpperCase()) : 'DEVELOPMENT EVALUATION' }}
            </span>
          </div>
          <p class="text-xs text-slate-300">
            {{ localeStore.isRtl
              ? 'تفعيل الموديولات يخضع لشهادة الترخيص الرقمية الموقعة من المزود وفق باقة الاشتراك.'
              : 'Module activation is strictly verified against cryptographically signed vendor subscription entitlements.'
            }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <button
          type="button"
          class="px-4 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-navy-950 font-black text-xs transition-all shadow-sm flex items-center gap-1.5 cursor-pointer active:scale-95"
          @click="openLicenseModal"
        >
          <KeyRound class="w-3.5 h-3.5" />
          <span>{{ localeStore.isRtl ? 'تطبيق ترخيص جديد (Vendor License)' : 'Apply Vendor License Certificate' }}</span>
        </button>
      </div>
    </div>

    <!-- KPI Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Modules -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden group">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ $t('admin.modules.kpiTotal') }}</span>
          <div class="w-8 h-8 rounded-lg bg-navy-50 text-navy-900 flex items-center justify-center">
            <Boxes class="w-4 h-4" />
          </div>
        </div>
        <div class="text-3xl font-black text-navy-950 font-mono">
          {{ totalModulesCount }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ $t('admin.modules.kpiTotalSub') }}
        </div>
      </div>

      <!-- Active Modules -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden group">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ $t('admin.modules.kpiActive') }}</span>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <CheckCircle2 class="w-4 h-4" />
          </div>
        </div>
        <div class="text-3xl font-black text-emerald-600 font-mono">
          {{ enabledCount }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ $t('admin.modules.kpiActiveSub') }}
        </div>
      </div>

      <!-- Suspended Modules -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden group">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ $t('admin.modules.kpiSuspended') }}</span>
          <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
            <XCircle class="w-4 h-4" />
          </div>
        </div>
        <div class="text-3xl font-black font-mono" :class="suspendedCount > 0 ? 'text-amber-600' : 'text-slate-400'">
          {{ suspendedCount }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ $t('admin.modules.kpiSuspendedSub') }}
        </div>
      </div>

      <!-- Total Managed Tables -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden group">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ $t('admin.modules.kpiTables') }}</span>
          <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <Database class="w-4 h-4" />
          </div>
        </div>
        <div class="text-3xl font-black text-indigo-600 font-mono">
          {{ totalManagedTablesCount }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ $t('admin.modules.kpiTablesSub') }}
        </div>
      </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
      <!-- Search Input -->
      <div class="relative w-full md:w-96">
        <Search class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2" :class="localeStore.isRtl ? 'right-3.5' : 'left-3.5'" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('admin.modules.searchPlaceholder')"
          class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl py-2.5 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500"
          :class="localeStore.isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4'"
        />
        <button
          v-if="searchQuery"
          type="button"
          class="absolute top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"
          :class="localeStore.isRtl ? 'left-2.5' : 'right-2.5'"
          @click="searchQuery = ''"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Filter Tabs -->
      <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 no-scrollbar">
        <button
          v-for="tab in filterTabs"
          :key="tab.id"
          type="button"
          class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-1.5"
          :class="activeFilter === tab.id
            ? 'bg-navy-950 text-white shadow-xs'
            : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-navy-950'"
          @click="activeFilter = tab.id"
        >
          <span>{{ tab.label }}</span>
          <span
            class="text-[10px] px-1.5 py-0.2 rounded-full font-mono font-bold"
            :class="activeFilter === tab.id ? 'bg-gold-500 text-navy-950' : 'bg-slate-200 text-slate-700'"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>
    </div>

    <!-- Empty State if No Modules Match -->
    <div
      v-if="filteredModules.length === 0"
      class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-3"
    >
      <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
        <Search class="w-7 h-7" />
      </div>
      <h3 class="text-base font-bold text-navy-950">
        {{ $t('admin.modules.noModulesFound') }}
      </h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">
        {{ $t('admin.modules.noModulesFoundDesc') }}
      </p>
      <button
        type="button"
        class="text-xs font-bold text-navy-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors cursor-pointer"
        @click="resetFilters"
      >
        {{ localeStore.isRtl ? 'إعادة ضبط التصفية' : 'Reset Filters' }}
      </button>
    </div>

    <!-- Micro-Module Grid Cards -->
    <div v-else class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      <div
        v-for="mod in filteredModules"
        :key="mod.id"
        class="bg-white rounded-3xl border transition-all duration-300 flex flex-col justify-between shadow-xs hover:shadow-md relative overflow-hidden group"
        :class="mod.is_enabled ? 'border-slate-200/90' : 'border-amber-200/80 bg-amber-50/20'"
      >
        <!-- Card Top Indicator Bar -->
        <div
          class="h-1.5 w-full"
          :class="mod.is_enabled ? 'bg-gradient-to-r from-navy-900 via-gold-500 to-emerald-500' : 'bg-slate-300'"
        ></div>

        <!-- Card Header: Icon, Name, ID, Version, Status Switch -->
        <div class="p-5 sm:p-6 space-y-4 flex-1">
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3.5">
              <!-- Module Category Icon -->
              <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs transition-transform group-hover:scale-105"
                :class="getModuleThemeClasses(mod.id, mod.is_enabled)"
              >
                <component :is="resolveModuleIcon(mod.id)" class="w-6 h-6" />
              </div>

              <!-- Module Title & Metadata -->
              <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <h2 class="text-base sm:text-lg font-black text-navy-950 tracking-tight leading-snug">
                    {{ resolveLocalizedName(mod) }}
                  </h2>
                  <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                    v{{ mod.version || '1.0.0' }}
                  </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 font-mono">
                  <span>ID:</span>
                  <code class="text-navy-900 font-bold bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/60">{{ mod.id }}</code>
                </div>
              </div>
            </div>

            <!-- Activation Toggle Switch / Entitlement Indicator -->
            <div class="flex flex-col items-end gap-1.5 shrink-0">
              <div v-if="!isModuleEntitled(mod.id)" class="flex flex-col items-end gap-1">
                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase font-mono px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                  <Lock class="w-3 h-3" />
                  <span>{{ localeStore.isRtl ? 'غير مرخص' : 'Unlicensed' }}</span>
                </span>
                <span class="text-[10px] text-slate-400 font-medium">
                  {{ localeStore.isRtl ? 'يتطلب ترقية الاشتراك' : 'Requires Upgrade' }}
                </span>
              </div>
              <button
                v-else
                type="button"
                role="switch"
                :aria-checked="mod.is_enabled"
                :disabled="togglingId === mod.id || isRefreshing"
                @click="handleToggleClick(mod)"
                class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 select-none cursor-pointer"
                :class="[
                  mod.is_enabled ? 'bg-emerald-600' : 'bg-slate-300',
                  (togglingId === mod.id || isRefreshing) ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <span class="sr-only">{{ resolveLocalizedName(mod) }}</span>
                <span
                  class="inline-block h-5 w-5 rounded-full bg-white transition-transform duration-200 shadow-sm transform"
                  :class="[
                    localeStore.isRtl
                      ? (mod.is_enabled ? '-translate-x-6 mr-0.5' : 'translate-x-0 mr-0.5')
                      : (mod.is_enabled ? 'translate-x-6 ml-0.5' : 'translate-x-0 ml-0.5')
                  ]"
                ></span>
              </button>

              <!-- Loading spinner or text indicator -->
              <div v-if="isModuleEntitled(mod.id)" class="text-[11px] font-bold flex items-center gap-1">
                <RefreshCw v-if="togglingId === mod.id" class="w-3 h-3 animate-spin text-navy-900" />
                <span
                  v-else
                  :class="mod.is_enabled ? 'text-emerald-700' : 'text-slate-400'"
                >
                  {{ mod.is_enabled ? $t('admin.modules.statusActive') : $t('admin.modules.statusInactive') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ resolveLocalizedDesc(mod) }}
          </p>

          <!-- Owned Database Tables Section -->
          <div class="space-y-1.5 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
              <span class="flex items-center gap-1.5">
                <Database class="w-3.5 h-3.5 text-indigo-500" />
                <span>{{ $t('admin.modules.ownedTables') }}</span>
              </span>
              <span class="font-mono text-slate-400 font-semibold">
                {{ (mod.ownedTables || mod.owned_tables || []).length }} tables
              </span>
            </div>
            <div class="flex flex-wrap gap-1.5">
              <span
                v-for="table in (mod.ownedTables || mod.owned_tables || [])"
                :key="table"
                class="inline-flex items-center gap-1 text-[11px] font-mono font-semibold px-2 py-0.5 rounded-lg bg-indigo-50/80 text-indigo-800 border border-indigo-200/60"
              >
                <Table2 class="w-3 h-3 text-indigo-500" />
                <span>{{ table }}</span>
              </span>
              <span
                v-if="!(mod.ownedTables || mod.owned_tables || []).length"
                class="text-xs text-slate-400 italic"
              >
                {{ localeStore.isRtl ? 'لا توجد جداول بيانات معزولة' : 'No isolated tables' }}
              </span>
            </div>
          </div>

          <!-- Dependency Tree & Dependents Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <!-- Dependencies (Requires) -->
            <div class="bg-slate-50/80 p-3 rounded-2xl border border-slate-200/60 space-y-1.5">
              <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1.5">
                <GitFork class="w-3.5 h-3.5 text-navy-800" />
                <span>{{ $t('admin.modules.dependencies') }}</span>
              </div>
              <div class="space-y-1">
                <template v-if="(mod.dependencies || []).length > 0">
                  <div
                    v-for="depId in mod.dependencies"
                    :key="depId"
                    class="flex items-center justify-between text-xs px-2 py-1 rounded-lg bg-white border border-slate-200 shadow-2xs"
                  >
                    <span class="font-mono font-bold text-navy-950 truncate max-w-[130px]">{{ depId }}</span>
                    <span
                      class="text-[10px] font-bold px-1.5 py-0.2 rounded-full font-mono flex items-center gap-1"
                      :class="modulesStore.isModuleEnabled(depId) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="modulesStore.isModuleEnabled(depId) ? 'bg-emerald-600' : 'bg-rose-600'"></span>
                      <span>{{ modulesStore.isModuleEnabled(depId) ? 'Active' : 'Offline' }}</span>
                    </span>
                  </div>
                </template>
                <div v-else class="text-[11px] text-slate-400 italic py-0.5">
                  {{ $t('admin.modules.noDependencies') }}
                </div>
              </div>
            </div>

            <!-- Dependents (Required by) -->
            <div class="bg-slate-50/80 p-3 rounded-2xl border border-slate-200/60 space-y-1.5">
              <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1.5">
                <Layers class="w-3.5 h-3.5 text-gold-600" />
                <span>{{ $t('admin.modules.dependents') }}</span>
              </div>
              <div class="space-y-1">
                <template v-if="getDependentsList(mod.id).length > 0">
                  <div
                    v-for="dep in getDependentsList(mod.id)"
                    :key="dep.id"
                    class="flex items-center justify-between text-xs px-2 py-1 rounded-lg bg-white border border-slate-200 shadow-2xs"
                  >
                    <span class="font-mono font-bold text-navy-950 truncate max-w-[130px]">{{ dep.id }}</span>
                    <span
                      class="text-[10px] font-bold px-1.5 py-0.2 rounded-full font-mono flex items-center gap-1"
                      :class="modulesStore.isModuleEnabled(dep.id) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="modulesStore.isModuleEnabled(dep.id) ? 'bg-emerald-600' : 'bg-slate-400'"></span>
                      <span>{{ modulesStore.isModuleEnabled(dep.id) ? 'Dependent' : 'Offline' }}</span>
                    </span>
                  </div>
                </template>
                <div v-else class="text-[11px] text-slate-400 italic py-0.5">
                  {{ $t('admin.modules.noDependents') }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Footer: Inspect Details CTA -->
        <div class="px-5 sm:px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3">
          <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <ShieldCheck class="w-3.5 h-3.5 text-emerald-600" />
            <span>{{ isCoreProvider(mod.id) ? $t('admin.modules.coreProvider') : (localeStore.isRtl ? 'موديول تطبيقي' : 'Application Plugin') }}</span>
          </div>

          <button
            type="button"
            class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-navy-950 hover:text-white text-navy-950 text-xs font-bold transition-all border border-slate-200 shadow-2xs inline-flex items-center gap-1.5 cursor-pointer active:scale-95"
            @click="openInspectModal(mod)"
          >
            <Sliders class="w-3.5 h-3.5 text-gold-500" />
            <span>{{ $t('admin.modules.inspect') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Deep Inspection Modal / Drawer -->
    <div
      v-if="selectedModuleForInspection"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/60 backdrop-blur-xs transition-opacity"
      @click.self="selectedModuleForInspection = null"
    >
      <div
        class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200"
        :dir="localeStore.dir"
      >
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between gap-3 shrink-0">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-xs"
              :class="getModuleThemeClasses(selectedModuleForInspection.id, selectedModuleForInspection.is_enabled)"
            >
              <component :is="resolveModuleIcon(selectedModuleForInspection.id)" class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base sm:text-lg font-black text-navy-950 tracking-tight">
                {{ $t('admin.modules.inspectModalTitle', { name: resolveLocalizedName(selectedModuleForInspection) }) }}
              </h3>
              <p class="text-xs text-slate-500 font-medium">
                {{ $t('admin.modules.inspectModalSubtitle') }}
              </p>
            </div>
          </div>
          <button
            type="button"
            class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center transition-colors"
            @click="selectedModuleForInspection = null"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
          <!-- Backend Live Dependency Check Results -->
          <div class="p-4 rounded-2xl border bg-slate-50/80 space-y-3" :class="inspectLoading ? 'animate-pulse' : 'border-slate-200'">
            <div class="flex items-center justify-between text-xs font-bold">
              <span class="flex items-center gap-1.5 text-navy-950">
                <ShieldCheck class="w-4 h-4 text-emerald-600" />
                <span>{{ localeStore.isRtl ? 'فحص جاهزية الخادم وحالة التبعيات (Server Graph Status)' : 'Server Graph Dependency Verdict' }}</span>
              </span>
              <span v-if="inspectLoading" class="text-xs text-slate-500 font-normal flex items-center gap-1">
                <RefreshCw class="w-3 h-3 animate-spin" />
                <span>{{ localeStore.isRtl ? 'جاري الفحص من الخادم...' : 'Validating...' }}</span>
              </span>
            </div>

            <div v-if="!inspectLoading && inspectData" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs">
              <!-- Can Enable Verdict -->
              <div
                class="p-3 rounded-xl border flex items-start gap-2.5"
                :class="inspectData.can_enable ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-rose-50/70 border-rose-200 text-rose-900'"
              >
                <CheckCircle2 v-if="inspectData.can_enable" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                <AlertTriangle v-else class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                <div class="space-y-0.5">
                  <div class="font-bold">
                    {{ inspectData.can_enable ? $t('admin.modules.canEnable') : $t('admin.modules.cannotEnable') }}
                  </div>
                  <div v-if="inspectData.enable_block_reason" class="text-[11px] text-rose-700">
                    {{ inspectData.enable_block_reason }}
                  </div>
                </div>
              </div>

              <!-- Can Disable Verdict -->
              <div
                class="p-3 rounded-xl border flex items-start gap-2.5"
                :class="inspectData.can_disable ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-amber-50/70 border-amber-200 text-amber-900'"
              >
                <CheckCircle2 v-if="inspectData.can_disable" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                <AlertTriangle v-else class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div class="space-y-0.5">
                  <div class="font-bold">
                    {{ inspectData.can_disable ? $t('admin.modules.canDisable') : $t('admin.modules.cannotDisable') }}
                  </div>
                  <div v-if="inspectData.disable_block_reason" class="text-[11px] text-amber-800">
                    {{ inspectData.disable_block_reason }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section: Registered Endpoints & Routes -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
              <ExternalLink class="w-3.5 h-3.5 text-navy-900" />
              <span>{{ $t('admin.modules.modalSectionRoutes') }}</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Public Routes -->
              <div class="p-3.5 rounded-2xl border border-slate-200 bg-white space-y-2">
                <div class="text-xs font-bold text-slate-700 flex items-center justify-between">
                  <span>{{ $t('admin.modules.publicRoutes') }}</span>
                  <span class="text-[10px] font-mono bg-slate-100 px-1.5 py-0.2 rounded font-bold">
                    {{ (selectedModuleForInspection.publicRoutes || []).length }}
                  </span>
                </div>
                <div class="space-y-1 max-h-36 overflow-y-auto no-scrollbar">
                  <div
                    v-for="r in (selectedModuleForInspection.publicRoutes || [])"
                    :key="r.path"
                    class="text-xs font-mono p-1.5 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between"
                  >
                    <span class="text-navy-950 font-semibold truncate">{{ r.path }}</span>
                    <span class="text-[10px] text-slate-400">{{ r.name }}</span>
                  </div>
                  <div v-if="!(selectedModuleForInspection.publicRoutes || []).length" class="text-xs text-slate-400 italic">
                    {{ $t('admin.modules.noRoutesRegistered') }}
                  </div>
                </div>
              </div>

              <!-- Admin Routes -->
              <div class="p-3.5 rounded-2xl border border-slate-200 bg-white space-y-2">
                <div class="text-xs font-bold text-slate-700 flex items-center justify-between">
                  <span>{{ $t('admin.modules.adminRoutes') }}</span>
                  <span class="text-[10px] font-mono bg-slate-100 px-1.5 py-0.2 rounded font-bold">
                    {{ (selectedModuleForInspection.adminRoutes || []).length }}
                  </span>
                </div>
                <div class="space-y-1 max-h-36 overflow-y-auto no-scrollbar">
                  <div
                    v-for="r in (selectedModuleForInspection.adminRoutes || [])"
                    :key="r.path"
                    class="text-xs font-mono p-1.5 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between"
                  >
                    <span class="text-navy-950 font-semibold truncate">{{ r.path }}</span>
                    <span class="text-[10px] text-slate-400">{{ r.name }}</span>
                  </div>
                  <div v-if="!(selectedModuleForInspection.adminRoutes || []).length" class="text-xs text-slate-400 italic">
                    {{ $t('admin.modules.noRoutesRegistered') }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section: Owned Tables Full View -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
              <Database class="w-3.5 h-3.5 text-indigo-600" />
              <span>{{ $t('admin.modules.modalSectionTables') }}</span>
            </h4>
            <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
              <div class="flex flex-wrap gap-2">
                <div
                  v-for="tbl in (selectedModuleForInspection.ownedTables || selectedModuleForInspection.owned_tables || [])"
                  :key="tbl"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 font-mono text-xs font-bold"
                >
                  <Table2 class="w-3.5 h-3.5 text-indigo-600" />
                  <span>{{ tbl }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
          <button
            type="button"
            class="px-5 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95"
            @click="selectedModuleForInspection = null"
          >
            {{ $t('admin.modules.close') }}
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: APPLY VENDOR LICENSE CERTIFICATE -->
    <div
      v-if="isLicenseModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/60 backdrop-blur-xs animate-in fade-in duration-200"
      @click.self="isLicenseModalOpen = false"
    >
      <div
        class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] text-start"
        :dir="localeStore.dir"
      >
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-navy-950 text-white flex items-center justify-between shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gold-500/20 text-gold-400 border border-gold-500/30 flex items-center justify-center font-bold">
              <KeyRound class="w-5 h-5 text-gold-400" />
            </div>
            <div>
              <h3 class="text-base sm:text-lg font-black tracking-tight">
                {{ localeStore.isRtl ? 'تطبيق شهادة ترخيص الموديولات (Vendor License Certificate)' : 'Apply Vendor License Certificate' }}
              </h3>
              <p class="text-xs text-slate-300 font-medium">
                {{ localeStore.isRtl ? 'إدخال حزمة الترخيص الموقعة رقمياً من المزود لتفعيل الموديولات المستحقة' : 'Paste cryptographically signed license payload issued by Egypt Technology Group' }}
              </p>
            </div>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
            @click="isLicenseModalOpen = false"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 overflow-y-auto">
          <div class="p-3.5 bg-blue-50 border border-blue-200 text-blue-900 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
              <ShieldCheck class="w-4 h-4 text-blue-700" />
              <span>{{ localeStore.isRtl ? 'حماية التراخيص المشفرة (Defense-in-Depth)' : 'Cryptographic Signature Enforcement' }}</span>
            </div>
            <p>
              {{ localeStore.isRtl
                ? 'يتم التحقق من سلامة التوقيع الرقمي للمزود على الخادم. أي تعديل أو تلاعب في الحقول سيفشل التحقق فوراً.'
                : 'Server-side asymmetric verification validates the HMAC/RSA vendor signature against the root public key.'
              }}
            </p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              {{ localeStore.isRtl ? 'حزمة الترخيص الموقعة (Signed JSON License Package)' : 'Signed License JSON Package' }}
            </label>
            <textarea
              v-model="licensePackageInput"
              rows="6"
              class="w-full font-mono text-xs p-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-navy-950 focus:border-navy-950"
              placeholder='{ "payload": { "client_id": "...", "tier": "enterprise", "licensed_modules": [...] }, "signature": "...", "algorithm": "HMAC-SHA256" }'
            ></textarea>
          </div>

          <!-- Live Verification Preview if provided -->
          <div v-if="licenseVerificationResult" class="p-3.5 rounded-2xl border text-xs" :class="licenseVerificationResult.valid ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
            <div class="font-bold mb-1">{{ licenseVerificationResult.valid ? 'Valid Vendor Signature' : 'Invalid License' }}</div>
            <div v-if="licenseVerificationResult.valid" class="space-y-0.5 font-mono text-[11px]">
              <div>Tier: <span class="font-bold">{{ licenseVerificationResult.data.tier }}</span></div>
              <div>Licensed: <span class="font-bold">{{ (licenseVerificationResult.data.licensed_modules || []).join(', ') }}</span></div>
              <div>Valid Until: <span class="font-bold">{{ licenseVerificationResult.data.valid_until || 'Indefinite' }}</span></div>
            </div>
            <div v-else class="text-[11px]">{{ licenseVerificationResult.message }}</div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
          <button
            type="button"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200"
            @click="isLicenseModalOpen = false"
          >
            {{ $t('common.cancel') }}
          </button>
          <button
            type="button"
            class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white text-xs font-bold transition-all shadow-md inline-flex items-center gap-2 cursor-pointer"
            :disabled="isApplyingLicense || !licensePackageInput.trim()"
            @click="handleApplyLicense"
          >
            <RefreshCw v-if="isApplyingLicense" class="w-3.5 h-3.5 animate-spin" />
            <KeyRound v-else class="w-3.5 h-3.5" />
            <span>{{ localeStore.isRtl ? 'اعتماد وتطبيق الترخيص' : 'Verify & Apply License' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { useModulesStore } from '../../stores/modules'
import { useToast } from '../../composables/useToast'
import { useDialog } from '../../composables/useDialog'
import { modulesApi } from '../../services/modulesApi'
import { moduleRegistry } from '../../core/modules/moduleRegistry'
import { vendorEntitlementApi } from '../../services/vendorEntitlementApi'
import {
  Blocks,
  Boxes,
  CheckCircle2,
  XCircle,
  Database,
  Search,
  X,
  RefreshCw,
  GitFork,
  Layers,
  ShieldCheck,
  Sliders,
  Table2,
  AlertTriangle,
  ExternalLink,
  School,
  UserCheck,
  GraduationCap,
  Newspaper,
  Calendar,
  FolderArchive,
  Award,
  KeyRound,
  Lock,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()
const modulesStore = useModulesStore()
const toast = useToast()
const dialog = useDialog()

// State
const searchQuery = ref('')
const activeFilter = ref('all') // 'all' | 'active' | 'inactive' | 'has-deps' | 'core'
const isRefreshing = ref(false)
const togglingId = ref(null)
const selectedModuleForInspection = ref(null)
const inspectLoading = ref(false)
const inspectData = ref(null)

// Vendor Entitlement State
const entitlementState = ref(null)
const isLicenseModalOpen = ref(false)
const licensePackageInput = ref('')
const isApplyingLicense = ref(false)
const licenseVerificationResult = ref(null)

const isModuleEntitled = (modId) => {
  if (!entitlementState.value?.entitlement || !entitlementState.value.entitlement.licensed_modules) {
    return false // STRICT: Unlicensed if no verified vendor license is installed
  }
  const licensed = (entitlementState.value.entitlement.licensed_modules || []).map((m) =>
    m.replace(/_/g, '-').toLowerCase()
  )
  return licensed.includes(modId.replace(/_/g, '-').toLowerCase())
}

const fetchEntitlementStatus = async () => {
  try {
    const data = await vendorEntitlementApi.getStatus()
    entitlementState.value = data
  } catch (err) {
    console.warn('[AdminModulesView] Could not fetch vendor entitlement status:', err)
  }
}

const openLicenseModal = () => {
  licensePackageInput.value = ''
  licenseVerificationResult.value = null
  isLicenseModalOpen.value = true
}

const handleApplyLicense = async () => {
  try {
    let parsed
    try {
      parsed = JSON.parse(licensePackageInput.value)
    } catch (e) {
      toast.error(localeStore.isRtl ? 'صيغة JSON غير صالحة' : 'Invalid JSON format in license package')
      return
    }

    isApplyingLicense.value = true
    const result = await vendorEntitlementApi.applyLicense(parsed)
    toast.success(
      localeStore.isRtl ? 'تم تطبيق واعتماد ترخيص المزود بنجاح' : 'Vendor license applied and verified successfully'
    )
    isLicenseModalOpen.value = false
    await fetchEntitlementStatus()
    await modulesStore.fetchModules(true)
  } catch (err) {
    console.error('[AdminModulesView] Apply license failed:', err)
    toast.error(err?.message || (localeStore.isRtl ? 'فشل تطبيق الترخيص' : 'Failed to apply license'))
  } finally {
    isApplyingLicense.value = false
  }
}

// Icon Map per module ID
const moduleIconMap = {
  'academic-structure': School,
  admissions: UserCheck,
  'academic-services': GraduationCap,
  cms: Newspaper,
  events: Calendar,
  documents: FolderArchive,
  results: Award,
}

const resolveModuleIcon = (id) => {
  return moduleIconMap[id] || Blocks
}

// Module Theme styles
const getModuleThemeClasses = (id, isEnabled) => {
  if (!isEnabled) {
    return 'bg-slate-100 text-slate-400'
  }
  const themes = {
    'academic-structure': 'bg-emerald-100 text-emerald-800',
    admissions: 'bg-amber-100 text-amber-800',
    'academic-services': 'bg-sky-100 text-sky-800',
    cms: 'bg-purple-100 text-purple-800',
    events: 'bg-rose-100 text-rose-800',
    documents: 'bg-teal-100 text-teal-800',
    results: 'bg-gold-100 text-gold-900',
  }
  return themes[id] || 'bg-navy-100 text-navy-800'
}

// Localized helper
const resolveLocalizedName = (mod) => {
  if (!mod?.name) return mod?.id || ''
  if (typeof mod.name === 'string') return mod.name
  return mod.name[localeStore.locale] || mod.name.ar || mod.name.en || mod.id
}

const resolveLocalizedDesc = (mod) => {
  if (!mod?.description) return ''
  if (typeof mod.description === 'string') return mod.description
  return mod.description[localeStore.locale] || mod.description.ar || mod.description.en || ''
}

// Computed KPIs
const allModules = computed(() => modulesStore.allModules)
const totalModulesCount = computed(() => allModules.value.length)
const enabledCount = computed(() => allModules.value.filter((m) => m.is_enabled).length)
const suspendedCount = computed(() => totalModulesCount.value - enabledCount.value)
const isAllOperational = computed(() => totalModulesCount.value > 0 && enabledCount.value === totalModulesCount.value)

const totalManagedTablesCount = computed(() => {
  const tableSet = new Set()
  allModules.value.forEach((m) => {
    const tables = m.ownedTables || m.owned_tables || []
    tables.forEach((tbl) => tableSet.add(tbl))
  })
  return tableSet.size
})

// Dependents lookup helper
const getDependentsList = (id) => {
  return moduleRegistry.getDependents(id)
}

const isCoreProvider = (id) => {
  return getDependentsList(id).length > 0
}

// Filter Tabs
const filterTabs = computed(() => [
  { id: 'all', label: t('admin.modules.filterAll'), count: totalModulesCount.value },
  { id: 'active', label: t('admin.modules.filterActive'), count: enabledCount.value },
  { id: 'inactive', label: t('admin.modules.filterInactive'), count: suspendedCount.value },
  {
    id: 'has-deps',
    label: t('admin.modules.filterHasDeps'),
    count: allModules.value.filter((m) => (m.dependencies || []).length > 0).length,
  },
  {
    id: 'core',
    label: t('admin.modules.filterCore'),
    count: allModules.value.filter((m) => isCoreProvider(m.id)).length,
  },
])

// Filtered and Searched Modules List
const filteredModules = computed(() => {
  let list = allModules.value

  // Apply tab filter
  if (activeFilter.value === 'active') {
    list = list.filter((m) => m.is_enabled)
  } else if (activeFilter.value === 'inactive') {
    list = list.filter((m) => !m.is_enabled)
  } else if (activeFilter.value === 'has-deps') {
    list = list.filter((m) => (m.dependencies || []).length > 0)
  } else if (activeFilter.value === 'core') {
    list = list.filter((m) => isCoreProvider(m.id))
  }

  // Apply search query
  const query = searchQuery.value.trim().toLowerCase()
  if (query) {
    list = list.filter((m) => {
      const nameAr = m.name?.ar?.toLowerCase() || ''
      const nameEn = m.name?.en?.toLowerCase() || ''
      const descAr = m.description?.ar?.toLowerCase() || ''
      const descEn = m.description?.en?.toLowerCase() || ''
      const id = m.id?.toLowerCase() || ''
      const tables = (m.ownedTables || m.owned_tables || []).join(' ').toLowerCase()

      return (
        id.includes(query) ||
        nameAr.includes(query) ||
        nameEn.includes(query) ||
        descAr.includes(query) ||
        descEn.includes(query) ||
        tables.includes(query)
      )
    })
  }

  return list
})

const resetFilters = () => {
  searchQuery.value = ''
  activeFilter.value = 'all'
}

// Refresh Modules from Backend
const handleRefreshModules = async () => {
  isRefreshing.value = true
  try {
    await modulesStore.fetchModules(true)
    toast.success(
      localeStore.isRtl ? 'تم تحديث حالة الموديولات بنجاح' : 'Module states refreshed from server'
    )
  } catch (err) {
    console.error('[AdminModulesView] Refresh failed:', err)
    toast.error(err?.message || (localeStore.isRtl ? 'فشل في تحديث حالة الموديولات' : 'Failed to refresh modules'))
  } finally {
    isRefreshing.value = false
  }
}

// Toggle Module Handler with Safety Confirmation Dialogs & Conflict Notification
const handleToggleClick = async (mod) => {
  const targetState = !mod.is_enabled
  const modName = resolveLocalizedName(mod)

  // Safety Confirmation when disabling
  if (!targetState) {
    const activeDependents = getDependentsList(mod.id).filter((d) => modulesStore.isModuleEnabled(d.id))
    const hasActiveDependents = activeDependents.length > 0

    const confirmed = await dialog.confirm({
      title: t('admin.modules.confirmDisableTitle'),
      message: hasActiveDependents
        ? t('admin.modules.confirmDisableConflictPrompt', { name: modName })
        : t('admin.modules.confirmDisablePrompt', { name: modName }),
      confirmText: t('admin.modules.confirmBtn'),
      cancelText: t('admin.modules.cancelBtn'),
      variant: hasActiveDependents ? 'danger' : 'warning',
    })

    if (!confirmed) {
      return
    }
  } else {
    // Safety check when enabling: check if dependencies are satisfied
    const depCheck = modulesStore.canEnableModule(mod.id)
    if (!depCheck.valid && depCheck.missingDependencies?.length > 0) {
      const missingNames = depCheck.missingDependencies.join(', ')
      const proceed = await dialog.confirm({
        title: t('admin.modules.confirmEnableTitle'),
        message: localeStore.isRtl
          ? `تنبيه: موديول [${modName}] يتطلب الموديولات التالية غير المفعلة: (${missingNames}). قد يرفض الخادم التفعيل حتى يتم تشغيلها أولاً.`
          : `Warning: Module [${modName}] requires missing dependencies: (${missingNames}). The server may reject activation until they are enabled.`,
        confirmText: t('admin.modules.confirmBtn'),
        cancelText: t('admin.modules.cancelBtn'),
        variant: 'warning',
      })

      if (!proceed) return
    }
  }

  // Execute Toggle Request
  togglingId.value = mod.id
  try {
    const res = await modulesStore.toggleModule(mod.id, targetState)
    const successMsg = res?.message || t('admin.modules.toggleSuccess', { id: mod.id })
    toast.success(successMsg)

  } catch (err) {
    console.error(`[AdminModulesView] Error toggling module ${mod.id}:`, err)
    
    // Check for HTTP 409 conflict or conflict error
    const isConflict = err?.status === 409 || err?.response?.status === 409 || modulesStore.conflictError
    const conflictMsg = err?.response?.data?.message || err?.message || t('admin.modules.toggleError')

    if (isConflict) {
      toast.error(conflictMsg, localeStore.isRtl ? 'تعارض في الاعتماديات (409 Conflict)' : 'Dependency Conflict (409)')
      await dialog.alert({
        title: localeStore.isRtl ? 'تعذر تنفيذ الإجراء - تعارض في الاعتماديات' : 'Action Blocked: Dependency Conflict',
        message: conflictMsg,
        variant: 'danger',
        confirmText: t('admin.modules.close'),
      })
    } else {
      toast.error(conflictMsg)
    }

  } finally {
    togglingId.value = null
  }
}

// Deep Inspection Modal Handler
const openInspectModal = async (mod) => {
  selectedModuleForInspection.value = mod
  inspectLoading.value = true
  inspectData.value = null

  try {
    const data = await modulesApi.getModuleDependencies(mod.id)
    inspectData.value = data
  } catch (err) {
    console.warn('[AdminModulesView] Failed to load server dependency inspection:', err)
    inspectData.value = {
      id: mod.id,
      can_enable: mod.is_enabled || (mod.dependencies || []).every((d) => modulesStore.isModuleEnabled(d)),
      can_disable: getDependentsList(mod.id).every((d) => !modulesStore.isModuleEnabled(d.id)),
      dependencies: mod.dependencies || [],
      dependents: getDependentsList(mod.id),
    }
  } finally {
    inspectLoading.value = false
  }
}

onMounted(async () => {
  await fetchEntitlementStatus()
  if (!modulesStore.initialized) {
    await modulesStore.fetchModules()
  }
})
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
