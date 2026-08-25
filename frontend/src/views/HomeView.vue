<template>
  <div class="space-y-16 lg:space-y-24 pb-20">
    <!-- HERO SLIDER SECTION -->
    <section class="relative overflow-hidden bg-navy-950 text-white min-h-[580px] lg:min-h-[640px] flex items-center">
      <!-- Background Decorative Grid & Gradients -->
      <div class="absolute inset-0 bg-hero-gradient opacity-90"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#d4af37_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-navy-500/20 rounded-full blur-3xl pointer-events-none"></div>

      <!-- Slide Content -->
      <div class="relative max-w-7xl mx-auto px-4 sm:px-8 py-16 w-full z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <!-- Slide Text & CTAs -->
          <div class="lg:col-span-7 space-y-6 text-start">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gold-500/15 border border-gold-500/30 text-gold-300 text-xs sm:text-sm font-semibold tracking-wide backdrop-blur-sm">
              <span class="w-2 h-2 rounded-full bg-gold-400 animate-ping"></span>
              {{ activeSlide.badge }}
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight">
              {{ activeSlide.title }}
            </h1>

            <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed font-normal">
              {{ activeSlide.subtitle }}
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2">
              <Button
                :to="activeSlide.ctaLink"
                variant="gold"
                size="lg"
                rounded="xl"
              >
                {{ activeSlide.ctaText }}
                <template #trailingIcon>
                  <svg class="w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </template>
              </Button>

              <Button
                :to="activeSlide.secondaryLink"
                variant="white"
                size="lg"
                rounded="xl"
              >
                {{ activeSlide.secondaryText }}
              </Button>
            </div>
          </div>

          <!-- Hero Visual / Floating Stats Card -->
          <div class="lg:col-span-5 hidden lg:block">
            <div class="relative">
              <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white/10 relative group">
                <img
                  :src="activeSlide.image"
                  :alt="activeSlide.title"
                  class="w-full h-96 object-cover transform transition-transform duration-700 group-hover:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-transparent"></div>
              </div>

              <!-- Floating Fast Facts Badge -->
              <div class="absolute -bottom-6 -start-6 bg-white text-navy-950 p-4 sm:p-5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gold-100 text-gold-700 flex items-center justify-center font-black text-xl">
                  ★
                </div>
                <div class="text-start">
                  <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $t('stats.employment') }}</div>
                  <div class="text-2xl font-black text-navy-950">96.8%</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slider Pagination Indicators -->
        <div class="flex items-center gap-2 pt-12">
          <button
            v-for="(slide, index) in slides"
            :key="index"
            type="button"
            :class="[
              'h-2.5 rounded-full transition-all duration-300',
              currentSlideIndex === index ? 'w-8 bg-gold-400' : 'w-2.5 bg-white/30 hover:bg-white/50'
            ]"
            @click="currentSlideIndex = index"
          ></button>
        </div>
      </div>
    </section>

    <!-- KEY METRICS & STATISTICS COUNTER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8">
      <div class="bg-gradient-to-br from-navy-900 to-navy-950 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-navy-800">
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $t('stats.title') }}</h2>
          <p class="text-sm text-slate-300">{{ $t('stats.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 sm:gap-8 text-center divide-y md:divide-y-0 divide-slate-800/60">
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-gold-400 tracking-tight">{{ $t('stats.students_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.students') }}</div>
          </div>
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-emerald-400 tracking-tight">{{ $t('stats.faculty_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.faculty') }}</div>
          </div>
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-gold-400 tracking-tight">{{ $t('stats.programs_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.programs') }}</div>
          </div>
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-emerald-400 tracking-tight">{{ $t('stats.employment_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.employment') }}</div>
          </div>
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-gold-400 tracking-tight">{{ $t('stats.research_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.research') }}</div>
          </div>
          <div class="pt-4 md:pt-0 space-y-1">
            <div class="text-3xl sm:text-4xl font-black text-emerald-400 tracking-tight">{{ $t('stats.partners_val') }}</div>
            <div class="text-xs sm:text-sm font-medium text-slate-300">{{ $t('stats.partners') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED COLLEGES SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <div class="text-xs font-bold uppercase tracking-wider text-gold-600 mb-1">
            {{ $t('colleges.title') }}
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-navy-950">
            {{ $t('home.featuredColleges') }}
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            {{ $t('home.featuredCollegesSub') }}
          </p>
        </div>
        <router-link
          to="/colleges"
          class="inline-flex items-center gap-1.5 text-sm font-bold text-navy-900 hover:text-gold-600 transition-colors shrink-0"
        >
          {{ $t('home.viewAllColleges') }}
          <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card
          v-for="college in colleges.slice(0, 3)"
          :key="college.id"
          padding="none"
          class="group"
        >
          <!-- College Cover Image -->
          <div class="relative h-48 overflow-hidden bg-navy-950">
            <img
              :src="college.banner_image"
              :alt="getTranslated(college.name, localeStore.locale)"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/40 to-transparent"></div>
            <div class="absolute top-4 start-4">
              <Badge variant="gold" size="sm" rounded="full">
                {{ college.programs_count }} {{ $t('colleges.programsCount') }}
              </Badge>
            </div>
            <div class="absolute bottom-4 start-4 end-4">
              <h3 class="text-lg font-bold text-white leading-snug">
                {{ getTranslated(college.name, localeStore.locale) }}
              </h3>
            </div>
          </div>

          <!-- College Body Info -->
          <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
            <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
              {{ getTranslated(college.about, localeStore.locale) }}
            </p>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
              <div class="text-xs text-slate-500">
                <span class="font-semibold text-slate-700">{{ $t('colleges.dean') }}:</span>
                {{ getTranslated(college.dean_name, localeStore.locale) }}
              </div>
              <router-link
                :to="`/colleges/${college.slug}`"
                class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors inline-flex items-center gap-1"
              >
                {{ $t('common.viewDetails') }}
                <svg class="w-3.5 h-3.5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>
          </div>
        </Card>
      </div>
    </section>

    <!-- FEATURED DEGREE PROGRAMS -->
    <section class="bg-slate-100/70 py-16 border-y border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
          <div>
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1">
              {{ $t('programs.title') }}
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-navy-950">
              {{ $t('home.featuredPrograms') }}
            </h2>
            <p class="text-sm text-slate-600 mt-1">
              {{ $t('home.featuredProgramsSub') }}
            </p>
          </div>
          <router-link
            to="/programs"
            class="inline-flex items-center gap-1.5 text-sm font-bold text-navy-900 hover:text-emerald-600 transition-colors shrink-0"
          >
            {{ $t('home.viewAllPrograms') }}
            <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="program in programs.slice(0, 3)"
            :key="program.id"
            padding="lg"
            class="hover:border-gold-300"
          >
            <div class="flex items-center justify-between gap-2 mb-3">
              <Badge variant="subtle" size="sm">
                {{ $t(`programs.${program.degree_level}`) || program.degree_level }}
              </Badge>
              <span class="text-xs text-slate-500 font-semibold">
                {{ program.credit_hours }} {{ $t('programs.creditHours') }}
              </span>
            </div>

            <h3 class="text-lg font-bold text-navy-950 mb-2 leading-snug">
              {{ getTranslated(program.name, localeStore.locale) }}
            </h3>

            <p class="text-xs text-slate-500 mb-4">
              {{ getTranslated(program.college_name, localeStore.locale) }}
            </p>

            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-6">
              {{ getTranslated(program.overview, localeStore.locale) }}
            </p>

            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
              <router-link
                :to="`/programs/${program.slug}`"
                class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors"
              >
                {{ $t('programs.overview') }} →
              </router-link>

              <router-link
                :to="`/admissions?program_id=${program.id}`"
                class="px-3 py-1.5 text-xs font-bold text-navy-950 bg-gold-400 hover:bg-gold-300 rounded-lg transition-colors"
              >
                {{ $t('programs.applyForProgram') }}
              </router-link>
            </div>
          </Card>
        </div>
      </div>
    </section>

    <!-- PRESIDENT'S WELCOME MESSAGE -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8">
      <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-academic border border-slate-200/80">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
          <div class="lg:col-span-4 text-center">
            <div class="relative inline-block">
              <img
                src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80"
                alt="President"
                class="w-48 h-48 sm:w-56 sm:h-56 rounded-3xl object-cover shadow-lg border-4 border-gold-400/50 mx-auto"
              />
              <div class="absolute -bottom-3 inset-x-0 mx-auto w-fit bg-navy-900 text-gold-400 text-xs font-bold px-3 py-1 rounded-full shadow">
                {{ $t('home.presidentTitle') }}
              </div>
            </div>
            <h4 class="text-lg font-bold text-navy-950 mt-6">{{ $t('home.presidentName') }}</h4>
            <p class="text-xs text-slate-500">Ph.D., Senior Member IEEE</p>
          </div>

          <div class="lg:col-span-8 space-y-4 text-start">
            <div class="inline-flex items-center gap-2 text-gold-600 text-xs font-bold uppercase tracking-wider">
              <span>❝</span> {{ $t('home.presidentMessage') }}
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-navy-950 leading-tight">
              {{ localeStore.isRtl ? 'بناء مستقبل التكنولوجيا بأيدي أبنائنا المبدعين' : 'Engineering the Future of Technology with Applied Excellence' }}
            </h3>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
              {{ $t('home.presidentSpeech') }}
            </p>
            <div class="pt-4 flex items-center gap-4">
              <router-link
                to="/colleges"
                class="text-sm font-bold text-navy-900 hover:text-gold-600 transition-colors inline-flex items-center gap-1.5"
              >
                {{ $t('hero.slide3_cta') }} →
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LATEST NEWS & EVENTS SECTION (TABS / GRID) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <div class="text-xs font-bold uppercase tracking-wider text-navy-600 mb-1">
            {{ $t('nav.news') }}
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-navy-950">
            {{ $t('home.latestNews') }}
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            {{ $t('home.latestNewsSub') }}
          </p>
        </div>
        <router-link
          to="/news"
          class="inline-flex items-center gap-1.5 text-sm font-bold text-navy-900 hover:text-gold-600 transition-colors shrink-0"
        >
          {{ $t('home.viewAllNews') }}
          <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <Card
          v-for="article in news.slice(0, 3)"
          :key="article.id"
          padding="none"
          class="group"
        >
          <div class="relative h-48 overflow-hidden bg-slate-100">
            <img
              :src="article.featured_image"
              :alt="getTranslated(article.title, localeStore.locale)"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute top-3 start-3">
              <Badge variant="primary" size="xs" rounded="md">
                {{ getTranslated(article.category?.name, localeStore.locale) }}
              </Badge>
            </div>
          </div>

          <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
            <div>
              <div class="text-[11px] text-slate-400 mb-2">
                {{ formatDate(article.published_at) }} • {{ article.views_count }} {{ $t('news.views') }}
              </div>
              <h3 class="text-base font-bold text-navy-950 group-hover:text-navy-800 line-clamp-2 leading-snug">
                <router-link :to="`/news/${article.slug}`">
                  {{ getTranslated(article.title, localeStore.locale) }}
                </router-link>
              </h3>
              <p class="text-xs text-slate-600 line-clamp-2 mt-2 leading-relaxed">
                {{ getTranslated(article.excerpt, localeStore.locale) }}
              </p>
            </div>

            <div class="pt-4 border-t border-slate-100">
              <router-link
                :to="`/news/${article.slug}`"
                class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors inline-flex items-center gap-1"
              >
                {{ $t('home.readMore') }}
                <svg class="w-3 h-3 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>
          </div>
        </Card>
      </div>
    </section>

    <!-- UPCOMING EVENTS CARDS -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <div class="text-xs font-bold uppercase tracking-wider text-gold-600 mb-1">
            {{ $t('nav.events') }}
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-navy-950">
            {{ $t('home.upcomingEvents') }}
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            {{ $t('home.upcomingEventsSub') }}
          </p>
        </div>
        <router-link
          to="/events"
          class="inline-flex items-center gap-1.5 text-sm font-bold text-navy-900 hover:text-gold-600 transition-colors shrink-0"
        >
          {{ $t('home.viewAllEvents') }}
          <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="event in events.slice(0, 2)"
          :key="event.id"
          class="bg-white rounded-2xl p-6 shadow-academic border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center gap-6 hover:shadow-academic-lg transition-all duration-300"
        >
          <!-- Date Badge -->
          <div class="w-20 h-20 rounded-2xl bg-navy-900 text-white flex flex-col items-center justify-center shrink-0 border border-navy-800">
            <span class="text-xs uppercase font-bold text-gold-400">{{ getEventMonth(event.start_time) }}</span>
            <span class="text-2xl font-black">{{ getEventDay(event.start_time) }}</span>
          </div>

          <!-- Event Details -->
          <div class="flex-1 space-y-2">
            <div class="text-xs font-semibold text-emerald-600">
              📍 {{ getTranslated(event.location, localeStore.locale) }}
            </div>
            <h3 class="text-base font-bold text-navy-950 leading-snug">
              {{ getTranslated(event.title, localeStore.locale) }}
            </h3>
            <p class="text-xs text-slate-500 line-clamp-2">
              {{ getTranslated(event.description, localeStore.locale) }}
            </p>
          </div>

          <!-- Action -->
          <router-link
            to="/events"
            class="px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-navy-900 hover:text-white text-navy-950 rounded-xl transition-colors shrink-0"
          >
            {{ $t('events.registerEvent') }}
          </router-link>
        </div>
      </div>
    </section>

    <!-- CALL TO ACTION BANNER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8">
      <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-800 text-white rounded-3xl p-8 sm:p-12 relative overflow-hidden text-center shadow-xl border border-navy-800">
        <div class="relative z-10 max-w-2xl mx-auto space-y-6">
          <Badge variant="gold" size="md" rounded="full">
            {{ $t('admissions.cycle') }}
          </Badge>
          <h2 class="text-2xl sm:text-4xl font-black tracking-tight">
            {{ localeStore.isRtl ? 'ابدأ مسيرتك الأكاديمية والمهنية معنا اليوم' : 'Empower Your Academic & Professional Journey Today' }}
          </h2>
          <p class="text-sm sm:text-base text-slate-300">
            {{ localeStore.isRtl ? 'سجل طلب التحاقك إلكترونياً بخطوات ميسرة واحجز مقعدك في أحد التخصصات المستقبلية.' : 'Submit your online application in easy steps and secure your seat in high-demand fields.' }}
          </p>
          <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <Button
              to="/admissions"
              variant="gold"
              size="lg"
              rounded="xl"
            >
              {{ $t('nav.applyNow') }}
            </Button>
            <Button
              to="/programs"
              variant="white"
              size="lg"
              rounded="xl"
            >
              {{ $t('hero.slide1_cta') }}
            </Button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Button from '../components/ui/Button.vue'
import Badge from '../components/ui/Badge.vue'
import Card from '../components/ui/Card.vue'

const localeStore = useLocaleStore()

const colleges = ref([])
const programs = ref([])
const news = ref([])
const events = ref([])

const currentSlideIndex = ref(0)
let sliderTimer = null

const slides = computed(() => [
  {
    badge: localeStore.isRtl ? 'الريادة والابتكار' : 'Innovation & Leadership',
    title: localeStore.isRtl
      ? 'الريادة في الذكاء الاصطناعي والتكنولوجيا الحديثة'
      : 'Pioneering AI & Advanced Engineering',
    subtitle: localeStore.isRtl
      ? 'برامج أكاديمية متطورة ومعتمدة دولياً تؤهلك لسوق العمل العالمي بأعلى المعايير المهنية.'
      : 'Internationally accredited, future-proof academic programs engineered to prepare you for global market leadership.',
    ctaText: localeStore.isRtl ? 'استكشف البرامج' : 'Explore Programs',
    ctaLink: '/programs',
    secondaryText: localeStore.isRtl ? 'قدم الآن' : 'Apply Now',
    secondaryLink: '/admissions',
    image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1000&q=80',
  },
  {
    badge: localeStore.isRtl ? 'القبول والتسجيل 2025/2026' : 'Admissions Open 2025/2026',
    title: localeStore.isRtl
      ? 'فتح باب القبول والتسجيل للعام الأكاديمي الجديد'
      : 'Admissions Open for Academic Year 2025/2026',
    subtitle: localeStore.isRtl
      ? 'انضم إلى نخبة الطلاب في كبرى كليات الهندسة، الحاسبات، وإدارة الأعمال التكنولوجية.'
      : 'Join thousands of ambitious students in prestigious Engineering, Computer Science, and Tech-Business faculties.',
    ctaText: localeStore.isRtl ? 'قدم طلب التحاقك الآن' : 'Apply for Admission',
    ctaLink: '/admissions',
    secondaryText: localeStore.isRtl ? 'متابعة الطلب' : 'Track Application',
    secondaryLink: '/admissions/track',
    image: 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1000&q=80',
  },
  {
    badge: localeStore.isRtl ? 'أبحاث ومعامل متطورة' : 'Cutting-edge Research',
    title: localeStore.isRtl
      ? 'بيئة بحثية متقدمة وشراكات صناعية رائدة'
      : 'Cutting-Edge Research Hub & Industrial Partnerships',
    subtitle: localeStore.isRtl
      ? 'معامل متخصصة فائقة التطور، حاضنات أعمال تكنولوجية، ومشاريع تخرج مرتبطة بالصناعة.'
      : 'State-of-the-art specialized laboratories, tech incubators, and direct enterprise-aligned graduation projects.',
    ctaText: localeStore.isRtl ? 'تعرف على كلياتنا' : 'Explore Our Colleges',
    ctaLink: '/colleges',
    secondaryText: localeStore.isRtl ? 'هيئة التدريس' : 'Meet Our Faculty',
    secondaryLink: '/faculty',
    image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1000&q=80',
  },
])

const activeSlide = computed(() => slides.value[currentSlideIndex.value] || slides.value[0])

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString(localeStore.isRtl ? 'ar-EG' : 'en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const getEventMonth = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString(localeStore.isRtl ? 'ar-EG' : 'en-US', { month: 'short' })
}

const getEventDay = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.getDate()
}

onMounted(async () => {
  // Load data
  try {
    const [cData, pData, nData, eData] = await Promise.all([
      api.getColleges(),
      api.getPrograms(),
      api.getNews({ per_page: 3 }),
      api.getEvents(),
    ])
    colleges.value = cData
    programs.value = pData
    news.value = nData
    events.value = eData
  } catch (e) {
    console.error('HomeView load error:', e)
  }

  // Auto rotate slides every 6s
  sliderTimer = setInterval(() => {
    currentSlideIndex.value = (currentSlideIndex.value + 1) % slides.value.length
  }, 6000)
})

onUnmounted(() => {
  if (sliderTimer) clearInterval(sliderTimer)
})
</script>
