<template>
  <footer class="bg-navy-950 text-slate-300 border-t border-navy-900 mt-auto">
    <!-- Accreditation & Institutional Badges Bar -->
    <div class="bg-navy-900/60 border-b border-navy-800/80 py-6 px-4 sm:px-8">
      <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-gold-500/10 border border-gold-500/30 flex items-center justify-center text-gold-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white">{{ $t('footer.accreditationBadge') }}</div>
            <div class="text-xs text-slate-400">National Authority for Quality Assurance (NAQAAE)</div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white">{{ $t('footer.isoBadge') }}</div>
            <div class="text-xs text-slate-400">Certified Higher Education Quality Management</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Footer Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 lg:py-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
        <!-- University Brand & Overview -->
        <div class="lg:col-span-4 space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-navy-900 to-navy-800 flex items-center justify-center text-gold-400 shadow-md border border-gold-500/30">
              <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v7m-3-4l3-3 3 3" />
              </svg>
            </div>
            <div>
              <span class="block font-black text-xl text-white tracking-tight leading-none">
                {{ $t('app.shortName') }}
              </span>
              <span class="block text-xs text-gold-400 font-medium">
                {{ localeStore.isRtl ? 'جامعة التكنولوجيا والعلوم التطبيقية' : 'University of Technology' }}
              </span>
            </div>
          </div>

          <p class="text-xs leading-relaxed text-slate-400">
            {{ $t('footer.aboutText') }}
          </p>

          <!-- Social Links -->
          <div class="flex items-center gap-3 pt-2">
            <a href="#" class="w-9 h-9 rounded-lg bg-navy-900 hover:bg-gold-500 hover:text-navy-950 text-slate-300 flex items-center justify-center transition-colors border border-navy-800">
              <span class="sr-only">Facebook</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="#" class="w-9 h-9 rounded-lg bg-navy-900 hover:bg-gold-500 hover:text-navy-950 text-slate-300 flex items-center justify-center transition-colors border border-navy-800">
              <span class="sr-only">LinkedIn</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
            </a>
            <a href="#" class="w-9 h-9 rounded-lg bg-navy-900 hover:bg-gold-500 hover:text-navy-950 text-slate-300 flex items-center justify-center transition-colors border border-navy-800">
              <span class="sr-only">YouTube</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
          </div>
        </div>

        <!-- Academic Faculties Quick Links -->
        <div class="lg:col-span-2 space-y-3">
          <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ $t('footer.academicLinks') }}</h4>
          <ul class="space-y-2 text-xs">
            <li>
              <router-link to="/colleges" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.colleges') }}
              </router-link>
            </li>
            <li>
              <router-link to="/programs" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.programs') }}
              </router-link>
            </li>
            <li>
              <router-link to="/faculty" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.faculty') }}
              </router-link>
            </li>
            <li>
              <router-link to="/documents" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.documents') }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Student Services & Portals Links -->
        <div class="lg:col-span-2 space-y-3">
          <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ $t('footer.studentServices') }}</h4>
          <ul class="space-y-2 text-xs">
            <li>
              <router-link to="/admissions" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.applyNow') }}
              </router-link>
            </li>
            <li>
              <router-link to="/admissions/track" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.trackApp') }}
              </router-link>
            </li>
            <li>
              <router-link to="/student-portal" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.studentPortal') }}
              </router-link>
            </li>
            <li>
              <router-link to="/events" class="hover:text-gold-400 transition-colors block">
                {{ $t('nav.events') }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Contact & Newsletter Form -->
        <div class="lg:col-span-4 space-y-4">
          <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ $t('footer.newsletterTitle') }}</h4>
          <p class="text-xs text-slate-400 leading-relaxed">
            {{ $t('footer.newsletterDesc') }}
          </p>

          <form @submit.prevent="handleSubscribe" class="space-y-2">
            <div class="flex gap-2">
              <input
                v-model="emailInput"
                type="email"
                required
                :placeholder="$t('footer.newsletterPlaceholder')"
                class="flex-1 px-3.5 py-2 text-xs bg-navy-900 border border-navy-800 rounded-xl text-white placeholder:text-slate-500 focus:outline-none focus:border-gold-500"
              />
              <button
                type="submit"
                class="px-4 py-2 text-xs font-bold bg-gold-500 text-navy-950 rounded-xl hover:bg-gold-400 transition-colors shrink-0 shadow-sm"
              >
                {{ $t('footer.subscribe') }}
              </button>
            </div>
            <p v-if="subscribed" class="text-xs text-emerald-400 font-medium">
              ✓ {{ $t('footer.subscribed') }}
            </p>
          </form>

          <div class="pt-2 space-y-1.5 text-xs text-slate-400 border-t border-navy-900">
            <div class="flex items-start gap-2">
              <svg class="w-4 h-4 text-gold-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ $t('footer.address') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span>{{ $t('footer.hotline') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Copyright & Legal Sub-footer -->
    <div class="border-t border-navy-900 bg-navy-950 py-4 px-4 sm:px-8 text-xs text-slate-500">
      <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-start">
        <p>{{ $t('footer.copyright') }}</p>
        <div class="flex items-center gap-4">
          <a href="#" class="hover:text-slate-400 transition-colors">{{ $t('footer.privacy') }}</a>
          <span>•</span>
          <a href="#" class="hover:text-slate-400 transition-colors">{{ $t('footer.terms') }}</a>
          <span>•</span>
          <a href="#" class="hover:text-slate-400 transition-colors">{{ $t('footer.sitemap') }}</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { useLocaleStore } from '../../stores/locale'

const localeStore = useLocaleStore()
const emailInput = ref('')
const subscribed = ref(false)

const handleSubscribe = () => {
  if (emailInput.value) {
    subscribed.value = true
    emailInput.value = ''
    setTimeout(() => {
      subscribed.value = false
    }, 4000)
  }
}
</script>
