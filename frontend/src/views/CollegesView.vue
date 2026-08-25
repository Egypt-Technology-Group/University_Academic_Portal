<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('colleges.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="gold" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('colleges.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('colleges.subtitle') }}
      </p>
    </div>

    <!-- Colleges Grid -->
    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <Card
        v-for="college in colleges"
        :key="college.id"
        padding="none"
        class="group flex flex-col justify-between"
      >
        <!-- College Image -->
        <div class="relative h-52 overflow-hidden bg-navy-950">
          <img
            :src="college.banner_image"
            :alt="getTranslated(college.name, localeStore.locale)"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/40 to-transparent"></div>
          
          <div class="absolute top-4 start-4 flex gap-2">
            <Badge variant="gold" size="sm" rounded="full">
              {{ college.programs_count }} {{ $t('colleges.programsCount') }}
            </Badge>
            <Badge variant="slate" size="sm" rounded="full">
              {{ college.departments_count }} {{ $t('colleges.departments') }}
            </Badge>
          </div>

          <div class="absolute bottom-4 start-4 end-4">
            <h2 class="text-xl font-bold text-white leading-snug">
              {{ getTranslated(college.name, localeStore.locale) }}
            </h2>
          </div>
        </div>

        <!-- College Body -->
        <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
          <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
            {{ getTranslated(college.about, localeStore.locale) }}
          </p>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <div class="text-xs text-slate-500">
              <span class="font-bold text-slate-700">{{ $t('colleges.dean') }}:</span>
              {{ getTranslated(college.dean_name, localeStore.locale) }}
            </div>
            
            <Button
              :to="`/colleges/${college.slug}`"
              variant="primary"
              size="sm"
              rounded="lg"
            >
              {{ $t('colleges.explore') }}
            </Button>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import Card from '../components/ui/Card.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'

const localeStore = useLocaleStore()
const colleges = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    colleges.value = await api.getColleges()
  } catch (e) {
    console.error('Failed to load colleges:', e)
  } finally {
    loading.value = false
  }
})
</script>
