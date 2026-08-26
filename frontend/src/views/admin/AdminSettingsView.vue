<template>
  <div class="space-y-6 sm:space-y-8" :dir="localeStore.dir">
    <!-- Header with Quick Save & Reset Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500/10 text-gold-600 border border-gold-500/20 text-xs font-bold mb-2">
          <Palette class="w-3.5 h-3.5 text-gold-500" />
          <span>{{ $t('admin.settings.badge') }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.settings.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5 max-w-2xl">
          {{ $t('admin.settings.subtitle') }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition-colors cursor-pointer"
          :disabled="isResetting"
          @click="confirmResetDefaults"
        >
          <RotateCcw class="w-3.5 h-3.5 text-slate-400" :class="{ 'animate-spin': isResetting }" />
          <span>{{ $t('admin.settings.resetDefaults') }}</span>
        </button>

        <button
          type="button"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-navy-950 font-black text-xs sm:text-sm shadow-gold-glow transition-all cursor-pointer disabled:opacity-60"
          :disabled="isSaving"
          @click="saveAllSettings"
        >
          <div v-if="isSaving" class="w-4 h-4 border-2 border-navy-950 border-t-transparent rounded-full animate-spin"></div>
          <Save v-else class="w-4 h-4" />
          <span>{{ isSaving ? $t('admin.settings.saving') : $t('admin.settings.saveAll') }}</span>
        </button>
      </div>
    </div>

    <!-- Success / Error Alerts -->
    <div
      v-if="saveSuccess"
      class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center justify-between shadow-xs animate-fade-in"
    >
      <div class="flex items-center gap-3">
        <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
        <span class="text-sm font-bold">{{ $t('admin.settings.saveSuccessMsg') }}</span>
      </div>
      <button type="button" class="text-emerald-600 hover:text-emerald-900" @click="saveSuccess = false">
        ✕
      </button>
    </div>

    <!-- Category Tabs Navigation -->
    <div class="flex overflow-x-auto scrollbar-none gap-2 p-1.5 bg-slate-200/70 rounded-2xl border border-slate-300/50">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        :class="[
          'px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition-all cursor-pointer',
          activeTab === tab.id
            ? 'bg-navy-950 text-white shadow-md'
            : 'text-slate-600 hover:text-navy-950 hover:bg-white/60',
        ]"
        @click="activeTab = tab.id"
      >
        <component :is="tab.icon" class="w-4 h-4" :class="activeTab === tab.id ? 'text-gold-400' : 'text-slate-400'" />
        <span>{{ tab.label }}</span>
      </button>
    </div>

    <!-- TAB 1: IDENTITY & BRANDING -->
    <div v-if="activeTab === 'branding'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
            <Building2 class="w-5 h-5 text-gold-500" />
            <span>{{ $t('admin.settings.tabs.brandingTitle') }}</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $t('admin.settings.tabs.brandingDesc') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Arabic Name -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.nameAr') }} *
            </label>
            <input
              v-model="form.site_identity.name.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
            />
          </div>

          <!-- English Name -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.nameEn') }} *
            </label>
            <input
              v-model="form.site_identity.name.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
            />
          </div>

          <!-- Short Name AR -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.shortNameAr') }}
            </label>
            <input
              v-model="form.site_identity.short_name.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Short Name EN -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.shortNameEn') }}
            </label>
            <input
              v-model="form.site_identity.short_name.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Slogan AR -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.sloganAr') }}
            </label>
            <input
              v-model="form.site_identity.slogan.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Slogan EN -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.sloganEn') }}
            </label>
            <input
              v-model="form.site_identity.slogan.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Motto AR -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.mottoAr') }}
            </label>
            <input
              v-model="form.site_identity.motto.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Motto EN -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.mottoEn') }}
            </label>
            <input
              v-model="form.site_identity.motto.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Established Year -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.estYear') }}
            </label>
            <input
              v-model="form.site_identity.established_year"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- Custom Favicon URL -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.faviconUrl') }}
            </label>
            <input
              v-model="form.site_identity.favicon_url"
              type="url"
              dir="ltr"
              placeholder="https://example.com/favicon.png"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: THEME & COLOR PALETTE -->
    <div v-if="activeTab === 'theme'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
            <Palette class="w-5 h-5 text-gold-500" />
            <span>{{ $t('admin.settings.tabs.themeTitle') }}</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $t('admin.settings.tabs.themeDesc') }}
          </p>
        </div>

        <!-- Color Pickers Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Primary Academic Navy -->
          <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $t('admin.settings.fields.primaryNavy') }}
              </label>
              <div class="w-6 h-6 rounded-lg shadow-inner border border-slate-300" :style="{ backgroundColor: form.theme_colors.primary_color }"></div>
            </div>
            <div class="flex items-center gap-3">
              <input
                v-model="form.theme_colors.primary_color"
                type="color"
                class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent"
              />
              <input
                v-model="form.theme_colors.primary_color"
                type="text"
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs bg-white uppercase font-bold"
              />
            </div>
          </div>

          <!-- Academic Gold -->
          <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $t('admin.settings.fields.secondaryGold') }}
              </label>
              <div class="w-6 h-6 rounded-lg shadow-inner border border-slate-300" :style="{ backgroundColor: form.theme_colors.secondary_gold }"></div>
            </div>
            <div class="flex items-center gap-3">
              <input
                v-model="form.theme_colors.secondary_gold"
                type="color"
                class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent"
              />
              <input
                v-model="form.theme_colors.secondary_gold"
                type="text"
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs bg-white uppercase font-bold"
              />
            </div>
          </div>

          <!-- Accent Emerald -->
          <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $t('admin.settings.fields.accentEmerald') }}
              </label>
              <div class="w-6 h-6 rounded-lg shadow-inner border border-slate-300" :style="{ backgroundColor: form.theme_colors.accent_emerald }"></div>
            </div>
            <div class="flex items-center gap-3">
              <input
                v-model="form.theme_colors.accent_emerald"
                type="color"
                class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent"
              />
              <input
                v-model="form.theme_colors.accent_emerald"
                type="text"
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs bg-white uppercase font-bold"
              />
            </div>
          </div>
        </div>

        <!-- Presets Palette Bar -->
        <div class="pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
            {{ $t('admin.settings.fields.palettePresets') }}
          </label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <button
              type="button"
              class="p-3.5 rounded-2xl border border-slate-200 hover:border-gold-500 bg-white hover:bg-gold-50/20 text-start flex items-center justify-between transition-all cursor-pointer group"
              @click="applyPreset('#0A2540', '#C59B27', '#059669')"
            >
              <div>
                <div class="text-xs font-bold text-navy-950 group-hover:text-gold-600">Royal Academic (Default)</div>
                <div class="text-[11px] text-slate-400 font-mono">Navy + Gold + Emerald</div>
              </div>
              <div class="flex items-center -space-x-1">
                <span class="w-4 h-4 rounded-full bg-[#0A2540] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#C59B27] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#059669] inline-block border border-white"></span>
              </div>
            </button>

            <button
              type="button"
              class="p-3.5 rounded-2xl border border-slate-200 hover:border-blue-500 bg-white hover:bg-blue-50/20 text-start flex items-center justify-between transition-all cursor-pointer group"
              @click="applyPreset('#0F172A', '#3B82F6', '#10B981')"
            >
              <div>
                <div class="text-xs font-bold text-navy-950 group-hover:text-blue-600">Modern Tech University</div>
                <div class="text-[11px] text-slate-400 font-mono">Slate + Blue + Mint</div>
              </div>
              <div class="flex items-center -space-x-1">
                <span class="w-4 h-4 rounded-full bg-[#0F172A] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#3B82F6] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#10B981] inline-block border border-white"></span>
              </div>
            </button>

            <button
              type="button"
              class="p-3.5 rounded-2xl border border-slate-200 hover:border-amber-500 bg-white hover:bg-amber-50/20 text-start flex items-center justify-between transition-all cursor-pointer group"
              @click="applyPreset('#1E1B4B', '#F59E0B', '#06B6D4')"
            >
              <div>
                <div class="text-xs font-bold text-navy-950 group-hover:text-amber-600">Oxford Heritage</div>
                <div class="text-[11px] text-slate-400 font-mono">Indigo + Amber + Cyan</div>
              </div>
              <div class="flex items-center -space-x-1">
                <span class="w-4 h-4 rounded-full bg-[#1E1B4B] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#F59E0B] inline-block border border-white"></span>
                <span class="w-4 h-4 rounded-full bg-[#06B6D4] inline-block border border-white"></span>
              </div>
            </button>
          </div>
        </div>

        <!-- Typography Customization -->
        <div class="pt-6 border-t border-slate-100">
          <h3 class="text-sm font-black text-navy-950 mb-3 flex items-center gap-2">
            <span>🔤</span> {{ localeStore.isRtl ? 'الخطوط والطباعة الأكاديمية (Google Fonts)' : 'Typography & Web Fonts' }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                {{ localeStore.isRtl ? 'الخط العربي الأساسي' : 'Primary Arabic Font' }}
              </label>
              <select
                v-model="form.theme_colors.font_family_ar"
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-xs sm:text-sm font-medium focus:bg-white focus:border-navy-900"
              >
                <option value="Cairo">Cairo (الافتراضي - رسمي ورصين)</option>
                <option value="Tajawal">Tajawal (عصري وتقني)</option>
                <option value="Alexandria">Alexandria (هندسي وحديث)</option>
                <option value="Almarai">Almarai (مريح وواضح)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                {{ localeStore.isRtl ? 'الخط اللاتيني/الإنجليزي' : 'Primary English Font' }}
              </label>
              <select
                v-model="form.theme_colors.font_family_en"
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-xs sm:text-sm font-medium focus:bg-white focus:border-navy-900"
              >
                <option value="Inter">Inter (Default - Ultra Clean)</option>
                <option value="Plus Jakarta Sans">Plus Jakarta Sans (Modern Academic)</option>
                <option value="Outfit">Outfit (Tech & Bold)</option>
                <option value="Roboto">Roboto (Classic Neutral)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Custom CSS Injection Code Area -->
        <div class="pt-6 border-t border-slate-100">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-black text-navy-950 flex items-center gap-2">
              <span>💻</span> {{ localeStore.isRtl ? 'حقن كود CSS مخصص (Custom CSS Code)' : 'Advanced Custom CSS Injection' }}
            </h3>
            <span class="text-[11px] text-slate-400 font-mono">Real-time Injection</span>
          </div>
          <p class="text-xs text-slate-500 mb-3">
            {{ localeStore.isRtl ? 'أضف قواعد وتنسيقات CSS إضافية ليتم تطبيقها فوراً على البوابة بدون الحاجة لإعادة البناء.' : 'Inject custom CSS overrides to apply immediately to the portal without redeployment.' }}
          </p>
          <textarea
            v-model="form.custom_css.css_code"
            rows="4"
            dir="ltr"
            placeholder="/* Example: .hero-title { letter-spacing: -0.02em; } */"
            class="w-full rounded-2xl border border-slate-200 bg-slate-900 text-gold-300 font-mono text-xs p-4 focus:ring-2 focus:ring-gold-400 outline-none"
          ></textarea>
        </div>
      </div>
    </div>

    <!-- TAB 3: UNIVERSITY PRESIDENT & LEADERSHIP -->
    <div v-if="activeTab === 'president'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
            <UserCheck class="w-5 h-5 text-gold-500" />
            <span>{{ $t('admin.settings.tabs.presidentTitle') }}</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $t('admin.settings.tabs.presidentDesc') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- President Name AR -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentNameAr') }} *
            </label>
            <input
              v-model="form.president_message.name.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- President Name EN -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentNameEn') }} *
            </label>
            <input
              v-model="form.president_message.name.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- President Title AR -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentTitleAr') }}
            </label>
            <input
              v-model="form.president_message.title.ar"
              type="text"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- President Title EN -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentTitleEn') }}
            </label>
            <input
              v-model="form.president_message.title.en"
              type="text"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-navy-900"
            />
          </div>

          <!-- President Photo Upload -->
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentAvatar') }}
            </label>
            <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-200">
              <img
                :src="form.president_message.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80'"
                alt="President Avatar Preview"
                class="w-16 h-16 rounded-2xl object-cover border-2 border-gold-400 shadow-sm shrink-0"
              />
              <div class="flex-1 min-w-0">
                <input
                  ref="presidentAvatarInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handlePresidentAvatarSelect"
                />
                <button
                  type="button"
                  class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-2 border border-slate-300 shadow-xs"
                  @click="$refs.presidentAvatarInput.click()"
                >
                  <Upload class="w-4 h-4 text-gold-600" />
                  <span>{{ localeStore.isRtl ? 'اختيار صورة الرئيس من جهازك' : 'Choose Photo from Device' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Quote AR -->
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentQuoteAr') }}
            </label>
            <textarea
              v-model="form.president_message.quote.ar"
              rows="2"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-sm font-medium focus:bg-white focus:border-navy-900"
            ></textarea>
          </div>

          <!-- Quote EN -->
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentQuoteEn') }}
            </label>
            <textarea
              v-model="form.president_message.quote.en"
              rows="2"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-sm font-medium focus:bg-white focus:border-navy-900"
            ></textarea>
          </div>

          <!-- Full Speech AR -->
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentMessageAr') }}
            </label>
            <textarea
              v-model="form.president_message.message.ar"
              rows="4"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-sm font-medium focus:bg-white focus:border-navy-900"
            ></textarea>
          </div>

          <!-- Full Speech EN -->
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
              {{ $t('admin.settings.fields.presidentMessageEn') }}
            </label>
            <textarea
              v-model="form.president_message.message.en"
              rows="4"
              dir="ltr"
              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-sm font-medium focus:bg-white focus:border-navy-900"
            ></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: HOMEPAGE HERO SLIDER -->
    <div v-if="activeTab === 'hero'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
              <Sparkles class="w-5 h-5 text-gold-500" />
              <span>{{ $t('admin.settings.tabs.heroTitle') }}</span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
              {{ $t('admin.settings.tabs.heroDesc') }}
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer"
            @click="addNewSlide"
          >
            <Plus class="w-4 h-4 text-gold-400" />
            <span>{{ $t('admin.settings.fields.addSlide') }}</span>
          </button>
        </div>

        <div class="space-y-6">
          <div
            v-for="(slide, sIdx) in form.hero_slider.slides"
            :key="slide.id || sIdx"
            class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4 relative"
          >
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
              <span class="text-xs font-black uppercase text-gold-600 bg-gold-100/80 px-2.5 py-1 rounded-lg">
                {{ $t('admin.settings.fields.slideNum') }} #{{ sIdx + 1 }}
              </span>
              <button
                type="button"
                class="text-red-500 hover:text-red-700 text-xs font-bold cursor-pointer"
                :disabled="form.hero_slider.slides.length <= 1"
                @click="removeSlide(sIdx)"
              >
                {{ $t('common.delete') }}
              </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Badge (AR)</label>
                <input v-model="slide.badge.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Badge (EN)</label>
                <input v-model="slide.badge.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs" />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Title (AR) *</label>
                <input v-model="slide.title.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold" />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Title (EN) *</label>
                <input v-model="slide.title.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Subtitle (AR)</label>
                <textarea v-model="slide.subtitle.ar" rows="2" class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs"></textarea>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Subtitle (EN)</label>
                <textarea v-model="slide.subtitle.en" rows="2" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs"></textarea>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">CTA Button Text (AR)</label>
                <input v-model="slide.cta_text.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">CTA Link</label>
                <input v-model="slide.cta_link" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-mono" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'صورة الشريحة' : 'Slide Image' }}</label>
                <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-200">
                  <img :src="slide.image_url" class="w-16 h-12 rounded-lg object-cover border border-slate-200 shrink-0" />
                  <div class="flex-1 min-w-0">
                    <input
                      :ref="(el) => { if (el) slideFileInputs[index] = el }"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="(e) => handleSlideImageSelect(e, index)"
                    />
                    <button
                      type="button"
                      class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 border border-slate-300 shadow-xs"
                      @click="triggerSlideFile(index)"
                    >
                      <Upload class="w-3.5 h-3.5 text-gold-600" />
                      <span>{{ localeStore.isRtl ? 'اختيار صورة من جهازك' : 'Choose Image from Device' }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: CONTACT & SOCIAL CHANNELS -->
    <div v-if="activeTab === 'contact'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
            <PhoneCall class="w-5 h-5 text-gold-500" />
            <span>{{ $t('admin.settings.tabs.contactTitle') }}</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $t('admin.settings.tabs.contactDesc') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Hotline</label>
            <input v-model="form.contact_info.hotline" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Primary Phone</label>
            <input v-model="form.contact_info.phone" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Official Email</label>
            <input v-model="form.contact_info.email" type="email" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Admissions Email</label>
            <input v-model="form.contact_info.admissions_email" type="email" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Support Email</label>
            <input v-model="form.contact_info.support_email" type="email" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Working Hours (AR)</label>
            <input v-model="form.contact_info.working_hours.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm" />
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Address (AR)</label>
            <input v-model="form.contact_info.address.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm" />
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Address (EN)</label>
            <input v-model="form.contact_info.address.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm" />
          </div>
        </div>

        <!-- Social Media Channels -->
        <div class="pt-6 border-t border-slate-100">
          <h3 class="text-sm font-black text-navy-950 mb-4">{{ $t('admin.settings.fields.socialChannels') }}</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Facebook</label>
              <input v-model="form.social_links.facebook" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Twitter / X</label>
              <input v-model="form.social_links.twitter" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">LinkedIn</label>
              <input v-model="form.social_links.linkedin" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">YouTube</label>
              <input v-model="form.social_links.youtube" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Instagram</label>
              <input v-model="form.social_links.instagram" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1">Telegram</label>
              <input v-model="form.social_links.telegram" type="url" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-mono" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 6: BROADCAST BAR & FOOTER -->
    <div v-if="activeTab === 'broadcast'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
            <Megaphone class="w-5 h-5 text-gold-500" />
            <span>{{ $t('admin.settings.tabs.broadcastTitle') }}</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $t('admin.settings.tabs.broadcastDesc') }}
          </p>
        </div>

        <div class="space-y-4">
          <!-- Toggle Active State -->
          <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <input
              id="topBarToggle"
              v-model="form.top_announcement_bar.is_enabled"
              type="checkbox"
              class="w-5 h-5 rounded text-gold-500 focus:ring-gold-400"
            />
            <label for="topBarToggle" class="text-sm font-bold text-navy-950 cursor-pointer">
              {{ $t('admin.settings.fields.enableBroadcast') }}
            </label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Broadcast Text (AR)</label>
              <input v-model="form.top_announcement_bar.text.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Broadcast Text (EN)</label>
              <input v-model="form.top_announcement_bar.text.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Destination Link URL</label>
              <input v-model="form.top_announcement_bar.link_url" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2.5 text-sm font-mono" />
            </div>
          </div>
        </div>

        <!-- Footer Copyright & Accreditation -->
        <div class="pt-6 border-t border-slate-100 space-y-4">
          <h3 class="text-sm font-black text-navy-950">{{ $t('admin.settings.fields.footerConfig') }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Footer About Text (AR)</label>
              <textarea v-model="form.footer_info.about_text.ar" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs"></textarea>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Copyright (AR)</label>
              <input v-model="form.footer_info.copyright_text.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Copyright (EN)</label>
              <input v-model="form.footer_info.copyright_text.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 7: SITE STATISTICS & NUMERICAL COUNTERS -->
    <div v-if="activeTab === 'statistics'" class="space-y-6">
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-lg font-black text-navy-950 flex items-center gap-2">
              <BarChart3 class="w-5 h-5 text-gold-500" />
              <span>{{ localeStore.isRtl ? 'إدارة الإحصائيات والأرقام والمؤشرات العامة' : 'Site Statistics & Numerical Counters' }}</span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
              {{ localeStore.isRtl ? 'التحكم الديناميكي الكامل في أرقام ونسب ومؤشرات النجاح المعروضة في واجهة الموقع للزوار.' : 'Manage public stats, enrolled student counters, faculty numbers, employment rates, and research figures.' }}
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all cursor-pointer shrink-0"
            @click="addNewStatItem"
          >
            <Plus class="w-4 h-4 text-gold-400" />
            <span>{{ localeStore.isRtl ? 'إضافة مؤشر / إحصائية جديدة' : 'Add Metric Counter' }}</span>
          </button>
        </div>

        <!-- Section Headers Customization -->
        <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-4">
          <div class="text-xs font-black uppercase text-navy-950 tracking-wider">
            {{ localeStore.isRtl ? 'عناوين قسم الإحصائيات في الصفحة الرئيسية' : 'Section Headings' }}
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ localeStore.isRtl ? 'عنوان القسم (عربي)' : 'Section Title (AR)' }}</label>
              <input v-model="form.site_statistics.title.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium focus:ring-1 focus:ring-navy-900" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ localeStore.isRtl ? 'عنوان القسم (إنجليزي)' : 'Section Title (EN)' }}</label>
              <input v-model="form.site_statistics.title.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium focus:ring-1 focus:ring-navy-900" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ localeStore.isRtl ? 'العنوان الفرعي (عربي)' : 'Section Subtitle (AR)' }}</label>
              <input v-model="form.site_statistics.subtitle.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium focus:ring-1 focus:ring-navy-900" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ localeStore.isRtl ? 'العنوان الفرعي (إنجليزي)' : 'Section Subtitle (EN)' }}</label>
              <input v-model="form.site_statistics.subtitle.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium focus:ring-1 focus:ring-navy-900" />
            </div>
          </div>
        </div>

        <!-- Metrics Cards List -->
        <div v-if="!form.site_statistics?.items || form.site_statistics.items.length === 0" class="p-8 text-center bg-slate-50 border border-dashed border-slate-300 rounded-2xl space-y-3">
          <BarChart3 class="w-8 h-8 text-slate-400 mx-auto" />
          <div class="text-xs font-bold text-slate-600">
            {{ localeStore.isRtl ? 'لا توجد مؤشرات حالياً. انقر على زر إضافة مؤشر جديد بالأعلى لإضافة إحصائيات.' : 'No metric counters configured. Click "Add Metric Counter" above to create one.' }}
          </div>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="(item, idx) in form.site_statistics.items"
            :key="item.id || idx"
            class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-white hover:border-slate-300 transition-all shadow-xs space-y-4 relative group"
          >
            <!-- Card Header with Reorder, Active Toggle & Delete -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
              <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-navy-950 text-gold-400 font-bold text-xs flex items-center justify-center font-mono">
                  #{{ item.order || idx + 1 }}
                </span>
                <span class="text-xs font-bold text-navy-950">
                  {{ item.label?.ar || item.label?.en || `Metric #${idx + 1}` }}
                </span>
              </div>

              <div class="flex items-center gap-2">
                <!-- Reorder Up/Down -->
                <button
                  type="button"
                  title="Move Up"
                  class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 disabled:opacity-30 cursor-pointer"
                  :disabled="idx === 0"
                  @click="moveStatUp(idx)"
                >
                  <MoveUp class="w-3.5 h-3.5" />
                </button>
                <button
                  type="button"
                  title="Move Down"
                  class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 disabled:opacity-30 cursor-pointer"
                  :disabled="idx === form.site_statistics.items.length - 1"
                  @click="moveStatDown(idx)"
                >
                  <MoveDown class="w-3.5 h-3.5" />
                </button>

                <!-- Active Toggle -->
                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer">
                  <input
                    v-model="item.is_active"
                    type="checkbox"
                    class="w-4 h-4 rounded text-gold-500 focus:ring-gold-400"
                  />
                  <span>{{ item.is_active ? (localeStore.isRtl ? 'مفعل' : 'Active') : (localeStore.isRtl ? 'معطل' : 'Hidden') }}</span>
                </label>

                <!-- Delete Action -->
                <button
                  type="button"
                  class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition-colors cursor-pointer"
                  title="Delete Metric"
                  @click="removeStatItem(idx)"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Metric Inputs Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
              <!-- Label AR -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم المؤشر (عربي)' : 'Metric Label (AR)' }} *</label>
                <input v-model="item.label.ar" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-navy-900" />
              </div>

              <!-- Label EN -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم المؤشر (إنجليزي)' : 'Metric Label (EN)' }} *</label>
                <input v-model="item.label.en" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-navy-900" />
              </div>

              <!-- Value (e.g. 15,400+ or 96.8%) -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'القيمة المعروضة (Value)' : 'Display Value' }} *</label>
                <input v-model="item.value" type="text" dir="ltr" placeholder="e.g. 15,400+ or 96.8%" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2 text-xs font-mono font-bold text-navy-950 focus:bg-white focus:border-navy-900" />
              </div>

              <!-- Color Accent -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'لون التمييز (Color Accent)' : 'Color Theme' }}</label>
                <select v-model="item.color" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-xs font-bold cursor-pointer">
                  <option value="gold">Gold / ذهبي</option>
                  <option value="emerald">Emerald / زمردي</option>
                  <option value="sky">Sky Blue / سماوي</option>
                  <option value="white">White / أبيض</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useSettingsStore } from '../../stores/settings'
import { useLocaleStore } from '../../stores/locale'
import { useDialog } from '../../composables/useDialog'
import { useToast } from '../../composables/useToast'
import {
  Palette,
  Building2,
  UserCheck,
  Sparkles,
  PhoneCall,
  Megaphone,
  BarChart3,
  Hash,
  MoveUp,
  MoveDown,
  Trash2,
  Save,
  RotateCcw,
  Plus,
  CheckCircle2,
  Upload,
} from 'lucide-vue-next'

const settingsStore = useSettingsStore()
const localeStore = useLocaleStore()
const dialog = useDialog()
const toast = useToast()

const activeTab = ref('branding')
const isSaving = ref(false)
const isResetting = ref(false)
const saveSuccess = ref(false)
const slideFileInputs = ref([])

const handlePresidentAvatarSelect = (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    form.president_message.avatar_url = ev.target.result
  }
  reader.readAsDataURL(file)
}

const triggerSlideFile = (index) => {
  if (slideFileInputs.value[index]) {
    slideFileInputs.value[index].click()
  }
}

const handleSlideImageSelect = (e, index) => {
  const file = e.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    if (form.hero_slider?.slides && form.hero_slider.slides[index]) {
      form.hero_slider.slides[index].image_url = ev.target.result
    }
  }
  reader.readAsDataURL(file)
}

const tabs = computed(() => [
  { id: 'branding', label: localeStore.isRtl ? 'الهوية والشعار' : 'Branding & Identity', icon: Building2 },
  { id: 'theme', label: localeStore.isRtl ? 'الألوان والسمة' : 'Theme & Colors', icon: Palette },
  { id: 'president', label: localeStore.isRtl ? 'كلمة رئيس الجامعة' : 'President Message', icon: UserCheck },
  { id: 'hero', label: localeStore.isRtl ? 'بانر الواجهة الرئيسية' : 'Hero Slider', icon: Sparkles },
  { id: 'contact', label: localeStore.isRtl ? 'التواصل والشبكات' : 'Contact & Social', icon: PhoneCall },
  { id: 'broadcast', label: localeStore.isRtl ? 'الشريط العاجل والتذييل' : 'Broadcast & Footer', icon: Megaphone },
  { id: 'statistics', label: localeStore.isRtl ? 'الإحصائيات والأرقام' : 'Site Statistics & Counters', icon: BarChart3 },
])

// Local Form Reactive State cloned from settings store
const form = reactive({
  site_identity: {
    name: { ar: '', en: '' },
    short_name: { ar: '', en: '' },
    slogan: { ar: '', en: '' },
    motto: { ar: '', en: '' },
    logo_url: '',
    favicon_url: '',
    established_year: '',
    ...JSON.parse(JSON.stringify(settingsStore.siteIdentity || {}))
  },
  theme_colors: {
    primary_color: '#0A2540',
    primary_hover: '#0F3460',
    secondary_gold: '#C59B27',
    secondary_gold_light: '#D4AF37',
    accent_emerald: '#059669',
    background_slate: '#F8FAFC',
    dark_surface: '#091E33',
    font_family_ar: 'Cairo',
    font_family_en: 'Inter',
    header_style: 'classic',
    ...JSON.parse(JSON.stringify(settingsStore.themeColors || {}))
  },
  president_message: {
    name: { ar: '', en: '' },
    title: { ar: '', en: '' },
    avatar_url: '',
    quote: { ar: '', en: '' },
    message: { ar: '', en: '' },
    signature_url: '',
    ...JSON.parse(JSON.stringify(settingsStore.presidentMessage || {}))
  },
  hero_slider: {
    slides: [],
    ...JSON.parse(JSON.stringify(settingsStore.settings.hero_slider || { slides: [] }))
  },
  contact_info: {
    hotline: '',
    phone: '',
    phone_secondary: '',
    email: '',
    admissions_email: '',
    support_email: '',
    address: { ar: '', en: '' },
    working_hours: { ar: '', en: '' },
    google_maps_embed_url: '',
    ...JSON.parse(JSON.stringify(settingsStore.contactInfo || {}))
  },
  social_links: {
    facebook: '',
    twitter: '',
    linkedin: '',
    youtube: '',
    instagram: '',
    telegram: '',
    ...JSON.parse(JSON.stringify(settingsStore.socialLinks || {}))
  },
  footer_info: {
    about_text: { ar: '', en: '' },
    accreditation_text: { ar: '', en: '' },
    iso_text: { ar: '', en: '' },
    copyright_text: { ar: '', en: '' },
    ...JSON.parse(JSON.stringify(settingsStore.footerInfo || {}))
  },
  top_announcement_bar: {
    is_enabled: false,
    text: { ar: '', en: '' },
    link_url: '',
    badge: { ar: '', en: '' },
    ...JSON.parse(JSON.stringify(settingsStore.topAnnouncement || {}))
  },
  site_statistics: {
    title: { ar: '', en: '' },
    subtitle: { ar: '', en: '' },
    items: [],
    ...JSON.parse(JSON.stringify(settingsStore.siteStatistics || { title: { ar: '', en: '' }, subtitle: { ar: '', en: '' }, items: [] }))
  },
})

const applyPreset = (primary, secondary, accent) => {
  form.theme_colors.primary_color = primary
  form.theme_colors.secondary_gold = secondary
  form.theme_colors.accent_emerald = accent
}

const addNewSlide = () => {
  form.hero_slider.slides.push({
    id: Date.now(),
    badge: { ar: 'شريحة جديدة', en: 'New Feature' },
    title: { ar: 'عنوان الإعلان أو البرنامج', en: 'Feature Title' },
    subtitle: { ar: 'وصف تفصيلي للشريحة الأكاديمية أو الحدث الجامعي.', en: 'Description of the academic announcement.' },
    cta_text: { ar: 'اعرف المزيد', en: 'Learn More' },
    cta_link: '/programs',
    secondary_text: { ar: 'تواصل معنا', en: 'Contact Us' },
    secondary_link: '/admissions',
    image_url: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1000&q=80',
  })
}

const removeSlide = (index) => {
  if (form.hero_slider.slides.length > 1) {
    form.hero_slider.slides.splice(index, 1)
  }
}

// Statistics Counters Management
const addNewStatItem = () => {
  if (!form.site_statistics) {
    form.site_statistics = {
      title: { ar: 'جامعة إيجي تك في أرقام', en: 'EgyiTech at a Glance' },
      subtitle: { ar: 'إنجازات تبرز التميز الأكاديمي والريادة الوطنية والبحثية', en: 'Milestones demonstrating academic prestige, research excellence, and national impact' },
      items: []
    }
  }
  if (!Array.isArray(form.site_statistics.items)) {
    form.site_statistics.items = []
  }
  const nextOrder = form.site_statistics.items.length + 1
  form.site_statistics.items.push({
    id: `metric_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
    label: { ar: 'مؤشر أو إحصائية جديدة', en: 'New Metric Fact' },
    value: '100+',
    prefix: '',
    suffix: '+',
    icon: 'Users',
    color: 'gold',
    is_active: true,
    order: nextOrder,
  })
}

const removeStatItem = (index) => {
  if (form.site_statistics.items && form.site_statistics.items.length > 1) {
    form.site_statistics.items.splice(index, 1)
    reindexStatOrders()
  }
}

const moveStatUp = (index) => {
  if (index <= 0) return
  const temp = form.site_statistics.items[index]
  form.site_statistics.items[index] = form.site_statistics.items[index - 1]
  form.site_statistics.items[index - 1] = temp
  reindexStatOrders()
}

const moveStatDown = (index) => {
  if (index >= form.site_statistics.items.length - 1) return
  const temp = form.site_statistics.items[index]
  form.site_statistics.items[index] = form.site_statistics.items[index + 1]
  form.site_statistics.items[index + 1] = temp
  reindexStatOrders()
}

const reindexStatOrders = () => {
  if (form.site_statistics?.items) {
    form.site_statistics.items.forEach((item, idx) => {
      item.order = idx + 1
    })
  }
}

const saveAllSettings = async () => {
  isSaving.value = true
  saveSuccess.value = false
  try {
    await settingsStore.saveSettings(form)
    saveSuccess.value = true
    toast.success(
      localeStore.isRtl ? 'تم حفظ وتطبيق كافة إعدادات النظام بنجاح.' : 'All system settings have been saved and applied successfully.',
      localeStore.isRtl ? 'تم الحفظ' : 'Settings Saved'
    )
    setTimeout(() => {
      saveSuccess.value = false
    }, 4000)
  } catch (e) {
    console.error('Failed to save settings:', e)
    toast.error(
      localeStore.isRtl ? 'تعذر حفظ إعدادات النظام، يرجى المحاولة مرة أخرى.' : 'Failed to save settings. Please try again.',
      localeStore.isRtl ? 'خطأ في الحفظ' : 'Save Error'
    )
  } finally {
    isSaving.value = false
  }
}

const confirmResetDefaults = async () => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'استعادة الإعدادات الافتراضية' : 'Reset System Defaults',
    message: localeStore.isRtl ? 'هل أنت متأكد من رغبتك في استعادة الإعدادات الافتراضية للنظام من قاعدة البيانات؟ سيتم فقدان التعديلات غير المحفوظة.' : 'Are you sure you want to reset site settings to factory defaults? Any unsaved changes will be lost.',
    confirmText: localeStore.isRtl ? 'استعادة الافتراضي' : 'Reset to Defaults',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    isResetting.value = true
    try {
      await settingsStore.resetSettings()
      // Reload form
      Object.assign(form, {
        site_identity: JSON.parse(JSON.stringify(settingsStore.siteIdentity)),
        theme_colors: JSON.parse(JSON.stringify(settingsStore.themeColors)),
        president_message: JSON.parse(JSON.stringify(settingsStore.presidentMessage)),
        hero_slider: JSON.parse(JSON.stringify(settingsStore.settings.hero_slider || { slides: [] })),
        contact_info: JSON.parse(JSON.stringify(settingsStore.contactInfo)),
        social_links: JSON.parse(JSON.stringify(settingsStore.socialLinks)),
        footer_info: JSON.parse(JSON.stringify(settingsStore.footerInfo)),
        top_announcement_bar: JSON.parse(JSON.stringify(settingsStore.topAnnouncement)),
        site_statistics: JSON.parse(JSON.stringify(settingsStore.siteStatistics)),
      })
      toast.success(
        localeStore.isRtl ? 'تمت استعادة الإعدادات الافتراضية للنظام بنجاح.' : 'Site settings have been reset to factory defaults successfully.',
        localeStore.isRtl ? 'تمت الاستعادة' : 'Reset Complete'
      )
    } catch (e) {
      console.error('Failed to reset settings:', e)
      toast.error(
        localeStore.isRtl ? 'حدث خطأ أثناء استعادة الإعدادات الافتراضية.' : 'Failed to reset settings to defaults.',
        localeStore.isRtl ? 'خطأ' : 'Error'
      )
    } finally {
      isResetting.value = false
    }
  }
}

onMounted(async () => {
  await settingsStore.fetchAdminSettings()
  Object.assign(form, {
    site_identity: JSON.parse(JSON.stringify(settingsStore.siteIdentity)),
    theme_colors: JSON.parse(JSON.stringify(settingsStore.themeColors)),
    president_message: JSON.parse(JSON.stringify(settingsStore.presidentMessage)),
    hero_slider: JSON.parse(JSON.stringify(settingsStore.settings.hero_slider || { slides: [] })),
    contact_info: JSON.parse(JSON.stringify(settingsStore.contactInfo)),
    social_links: JSON.parse(JSON.stringify(settingsStore.socialLinks)),
    footer_info: JSON.parse(JSON.stringify(settingsStore.footerInfo)),
    top_announcement_bar: JSON.parse(JSON.stringify(settingsStore.topAnnouncement)),
    site_statistics: JSON.parse(JSON.stringify(settingsStore.siteStatistics || { title: { ar: '', en: '' }, subtitle: { ar: '', en: '' }, items: [] })),
  })
})
</script>
