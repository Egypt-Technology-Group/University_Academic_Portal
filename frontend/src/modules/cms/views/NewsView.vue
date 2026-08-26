<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('news.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="primary" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('news.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('news.subtitle') }}
      </p>
    </div>

    <!-- Search & Category Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-academic border border-slate-200/80 space-y-6">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Categories Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2">
          <button
            v-for="cat in categories"
            :key="cat.slug"
            type="button"
            :class="[
              'px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all cursor-pointer',
              selectedCategory === cat.slug
                ? 'bg-navy-900 text-white shadow-sm'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
            ]"
            @click="selectedCategory = cat.slug"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full md:w-72">
          <svg class="w-4 h-4 text-slate-400 absolute start-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('news.searchNews')"
            class="w-full ps-10 pe-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-navy-800 outline-none transition-all"
          />
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <ErrorState
      v-else-if="error"
      :message="error"
      @retry="loadNewsData"
    />

    <EmptyState
      v-else-if="filteredNews.length === 0"
      :title="$t('news.noNewsFound')"
    />

    <div v-else class="space-y-10">
      <!-- News Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card
          v-for="article in paginatedNews"
          :key="article.id"
          padding="none"
          class="group flex flex-col justify-between"
        >
          <!-- Article Image -->
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

          <!-- Article Content -->
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
              <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed pt-1">
                {{ getTranslated(article.excerpt, localeStore.locale) }}
              </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
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

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        @change="currentPage = $event"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../../stores/locale'
import { getTranslated } from '../../../services/api'
import cmsApi from '../services/cmsApi'
import { formatStandardDate } from '../../../utils/dateFormat'
import Breadcrumbs from '../../../components/ui/Breadcrumbs.vue'
import Badge from '../../../components/ui/Badge.vue'
import Card from '../../../components/ui/Card.vue'
import Pagination from '../../../components/ui/Pagination.vue'
import LoadingSpinner from '../../../components/ui/LoadingSpinner.vue'
import EmptyState from '../../../components/ui/EmptyState.vue'
import ErrorState from '../../../components/ui/ErrorState.vue'

const { t } = useI18n()
const localeStore = useLocaleStore()

const news = ref([])
const loading = ref(true)
const error = ref('')
const selectedCategory = ref('all')
const searchQuery = ref('')
const currentPage = ref(1)
const perPage = 6

const categories = computed(() => [
  { slug: 'all', label: t('news.categoryAll') },
  { slug: 'research-innovation', label: t('news.categoryResearch') },
  { slug: 'partnerships-events', label: t('news.categoryEvents') },
  { slug: 'academic-affairs', label: t('news.categoryAcademic') },
])

const filteredNews = computed(() => {
  return news.value.filter((n) => {
    if (selectedCategory.value !== 'all') {
      if (n.category?.slug !== selectedCategory.value) return false
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const titleAr = (n.title?.ar || '').toLowerCase()
      const titleEn = (n.title?.en || '').toLowerCase()
      const excerptAr = (n.excerpt?.ar || '').toLowerCase()
      const excerptEn = (n.excerpt?.en || '').toLowerCase()
      if (!titleAr.includes(q) && !titleEn.includes(q) && !excerptAr.includes(q) && !excerptEn.includes(q)) {
        return false
      }
    }
    return true
  })
})

const totalPages = computed(() => {
  return Math.ceil(filteredNews.value.length / perPage) || 1
})

const paginatedNews = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredNews.value.slice(start, start + perPage)
})

const formatDate = (dateStr) => formatStandardDate(dateStr, localeStore.locale)

const loadNewsData = async () => {
  loading.value = true
  error.value = ''
  try {
    news.value = await cmsApi.getNews()
  } catch (e) {
    error.value = e.message || (localeStore.isRtl ? 'تعذر جلب الأخبار من الخادم.' : 'Failed to fetch news.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadNewsData()
})
</script>
