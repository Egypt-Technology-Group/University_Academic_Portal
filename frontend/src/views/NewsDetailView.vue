<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs
      :items="[
        { label: $t('news.title'), to: '/news' },
        { label: getTranslated(article?.title, localeStore.locale) || $t('common.loading') },
      ]"
    />

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <div v-else-if="article" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      <!-- Main Article Content (8 cols) -->
      <article class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 shadow-academic border border-slate-200/80 space-y-8">
        <!-- Meta Header -->
        <div class="space-y-4">
          <div class="flex flex-wrap items-center gap-3">
            <Badge variant="primary" size="sm" rounded="md">
              {{ getTranslated(article.category?.name, localeStore.locale) }}
            </Badge>
            <span class="text-xs text-slate-400">
              📅 {{ formatDate(article.published_at) }}
            </span>
            <span class="text-xs text-slate-400">
              👁 {{ article.views_count }} {{ $t('news.views') }}
            </span>
          </div>

          <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-navy-950 leading-tight">
            {{ getTranslated(article.title, localeStore.locale) }}
          </h1>

          <p class="text-base text-slate-600 font-medium leading-relaxed bg-slate-50 p-4 rounded-xl border-s-4 border-s-gold-500">
            {{ getTranslated(article.excerpt, localeStore.locale) }}
          </p>
        </div>

        <!-- Featured Image -->
        <div class="rounded-2xl overflow-hidden shadow-sm bg-slate-100 max-h-[420px]">
          <img
            :src="article.featured_image"
            :alt="getTranslated(article.title, localeStore.locale)"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Article Body with Rich Text Support -->
        <div
          class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm sm:text-base space-y-4"
          v-html="getTranslated(article.body, localeStore.locale)"
        ></div>

        <!-- Social Share & Actions Bar -->
        <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase">{{ $t('news.share') }}:</span>
            
            <button
              type="button"
              class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors flex items-center justify-center text-xs font-bold"
              @click="shareOnWhatsApp"
            >
              WA
            </button>

            <button
              type="button"
              class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors flex items-center justify-center text-xs font-bold"
              @click="shareOnLinkedIn"
            >
              in
            </button>

            <button
              type="button"
              class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors text-xs font-medium"
              @click="copyUrl"
            >
              {{ copied ? $t('news.linkCopied') : $t('news.copyLink') }}
            </button>
          </div>

          <router-link
            to="/news"
            class="text-xs font-bold text-navy-900 hover:text-gold-600 transition-colors inline-flex items-center gap-1"
          >
            ← {{ $t('news.backToNews') }}
          </router-link>
        </div>
      </article>

      <!-- Sidebar: Related Articles (4 cols) -->
      <aside class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-academic border border-slate-200/80 space-y-4">
          <h3 class="text-lg font-bold text-navy-950 pb-3 border-b border-slate-100">
            {{ $t('news.relatedNews') }}
          </h3>

          <div class="space-y-4">
            <div
              v-for="rel in relatedArticles"
              :key="rel.id"
              class="group block space-y-1.5 pb-4 border-b border-slate-100 last:border-0 last:pb-0"
            >
              <span class="text-[11px] text-slate-400">
                {{ formatDate(rel.published_at) }}
              </span>
              <h4 class="text-xs sm:text-sm font-bold text-navy-950 group-hover:text-gold-600 line-clamp-2 leading-snug transition-colors">
                <router-link :to="`/news/${rel.slug}`">
                  {{ getTranslated(rel.title, localeStore.locale) }}
                </router-link>
              </h4>
            </div>
          </div>
        </div>

        <!-- Quick Admissions CTA -->
        <div class="bg-navy-950 text-white rounded-3xl p-6 text-center space-y-3">
          <Badge variant="gold" size="xs">{{ $t('app.shortName') }}</Badge>
          <h4 class="font-bold text-base">{{ $t('admissions.title') }}</h4>
          <p class="text-xs text-slate-300">{{ $t('hero.slide2_subtitle') }}</p>
          <Button to="/admissions" variant="gold" size="sm" rounded="xl" block>
            {{ $t('nav.applyNow') }}
          </Button>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import { formatStandardDate } from '../utils/dateFormat'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'

const route = useRoute()
const localeStore = useLocaleStore()

const article = ref(null)
const relatedArticles = ref([])
const loading = ref(true)
const copied = ref(false)

const loadArticle = async () => {
  loading.value = true
  try {
    const slug = route.params.slug
    const res = await api.getNewsArticle(slug)
    article.value = res.article
    relatedArticles.value = res.related || []
  } catch (e) {
    console.error('Failed to load news article:', e)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => formatStandardDate(dateStr, localeStore.locale)

const copyUrl = () => {
  navigator.clipboard.writeText(window.location.href)
  copied.value = true
  setTimeout(() => {
    copied.value = false
  }, 3000)
}

const shareOnWhatsApp = () => {
  const url = encodeURIComponent(window.location.href)
  const title = encodeURIComponent(getTranslated(article.value?.title, localeStore.locale))
  window.open(`https://api.whatsapp.com/send?text=${title}%20${url}`, '_blank')
}

const shareOnLinkedIn = () => {
  const url = encodeURIComponent(window.location.href)
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank')
}

watch(() => route.params.slug, loadArticle)
onMounted(loadArticle)
</script>
