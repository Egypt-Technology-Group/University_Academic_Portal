<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 space-y-10">
    <Breadcrumbs :items="[{ label: $t('events.title') }]" />

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
      <Badge variant="gold" size="md" rounded="full">
        {{ $t('app.shortName') }}
      </Badge>
      <h1 class="text-3xl sm:text-4xl font-black text-navy-950">
        {{ $t('events.title') }}
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        {{ $t('events.subtitle') }}
      </p>
    </div>

    <!-- Filter Buttons -->
    <div class="flex items-center justify-center gap-2">
      <button
        v-for="filter in filters"
        :key="filter.id"
        type="button"
        :class="[
          'px-5 py-2.5 text-xs sm:text-sm font-bold rounded-xl transition-colors cursor-pointer',
          activeFilter === filter.id
            ? 'bg-navy-900 text-white shadow-sm'
            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50',
        ]"
        @click="activeFilter = filter.id"
      >
        {{ filter.label }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <LoadingSpinner size="lg" :label="$t('common.loading')" />
    </div>

    <div v-else-if="events.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200 space-y-3">
      <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <p class="text-slate-600 font-semibold">{{ $t('events.noEvents') }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <Card
        v-for="event in events"
        :key="event.id"
        padding="none"
        class="group flex flex-col justify-between overflow-hidden"
      >
        <!-- Event Cover -->
        <div class="relative h-48 bg-navy-950 overflow-hidden">
          <img
            :src="event.cover_image"
            :alt="getTranslated(event.title, localeStore.locale)"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-transparent"></div>

          <!-- Date Block Badge -->
          <div class="absolute top-3 start-3 bg-white/95 backdrop-blur-sm rounded-xl p-2 text-center shadow-md min-w-[54px]">
            <span class="block text-[10px] font-bold uppercase text-gold-600 leading-none">
              {{ getMonth(event.start_time) }}
            </span>
            <span class="block text-xl font-black text-navy-950 leading-none mt-1">
              {{ getDay(event.start_time) }}
            </span>
          </div>
        </div>

        <!-- Event Body -->
        <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
          <div class="space-y-2">
            <div class="text-xs font-semibold text-emerald-600">
              📍 {{ getTranslated(event.location, localeStore.locale) }}
            </div>

            <h3 class="text-base font-bold text-navy-950 group-hover:text-navy-800 leading-snug">
              {{ getTranslated(event.title, localeStore.locale) }}
            </h3>

            <p class="text-xs text-slate-500 font-medium">
              🏢 {{ getTranslated(event.organizer, localeStore.locale) }}
            </p>

            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed pt-1">
              {{ getTranslated(event.description, localeStore.locale) }}
            </p>
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
            <button
              type="button"
              class="w-full py-2 text-xs font-bold bg-navy-900 hover:bg-navy-800 text-white rounded-xl transition-colors text-center"
              @click="openEventModal(event)"
            >
              {{ $t('events.registerEvent') }}
            </button>
          </div>
        </div>
      </Card>
    </div>

    <!-- Event Registration Modal -->
    <Modal v-model="showEventModal" :title="getTranslated(selectedEvent?.title, localeStore.locale)" max-width="lg">
      <div v-if="selectedEvent" class="space-y-4 text-start">
        <div class="p-4 bg-slate-50 rounded-xl space-y-1.5 text-xs text-slate-600 border border-slate-100">
          <div><strong class="text-navy-950">{{ $t('events.location') }}:</strong> {{ getTranslated(selectedEvent.location, localeStore.locale) }}</div>
          <div><strong class="text-navy-950">{{ $t('events.organizer') }}:</strong> {{ getTranslated(selectedEvent.organizer, localeStore.locale) }}</div>
        </div>

        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          {{ getTranslated(selectedEvent.description, localeStore.locale) }}
        </p>

        <form @submit.prevent="handleRegisterEvent" class="space-y-3 pt-2">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">{{ $t('admissions.firstName') }} & {{ $t('admissions.lastName') }}</label>
            <input
              v-model="regName"
              type="text"
              required
              class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-navy-800 outline-none"
            />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">{{ $t('admissions.email') }}</label>
            <input
              v-model="regEmail"
              type="email"
              required
              class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-navy-800 outline-none"
            />
          </div>

          <div v-if="regSuccess" class="p-3 bg-emerald-50 text-emerald-700 text-xs rounded-xl font-bold">
            ✓ {{ localeStore.isRtl ? 'تم تسجيل حضورك في الفعالية بنجاح وسنرسل لك التفاصيل على بريدك الإلكتروني.' : 'Attendance registered successfully! Confirmation email sent.' }}
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <Button type="submit" variant="gold" size="sm" rounded="lg">
              {{ $t('events.registerEvent') }}
            </Button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../stores/locale'
import { api, getTranslated } from '../services/api'
import Breadcrumbs from '../components/ui/Breadcrumbs.vue'
import Badge from '../components/ui/Badge.vue'
import Button from '../components/ui/Button.vue'
import Card from '../components/ui/Card.vue'
import Modal from '../components/ui/Modal.vue'
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'

const { t } = useI18n()
const localeStore = useLocaleStore()

const events = ref([])
const loading = ref(true)
const activeFilter = ref('upcoming')

const showEventModal = ref(false)
const selectedEvent = ref(null)
const regName = ref('')
const regEmail = ref('')
const regSuccess = ref(false)

const filters = computed(() => [
  { id: 'upcoming', label: t('events.upcoming') },
  { id: 'all', label: t('events.all') },
  { id: 'past', label: t('events.past') },
])

const openEventModal = (ev) => {
  selectedEvent.value = ev
  regSuccess.value = false
  showEventModal.value = true
}

const isSubmittingReg = ref(false)

const handleRegisterEvent = async () => {
  if (!selectedEvent.value || !regName.value || !regEmail.value) return
  isSubmittingReg.value = true
  try {
    await api.registerEvent(selectedEvent.value.id, {
      name: regName.value,
      email: regEmail.value,
    })
    regSuccess.value = true
    setTimeout(() => {
      showEventModal.value = false
      regName.value = ''
      regEmail.value = ''
    }, 2500)
  } catch (e) {
    console.error('Registration failed:', e)
  } finally {
    isSubmittingReg.value = false
  }
}

const getMonth = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString(localeStore.isRtl ? 'ar-EG' : 'en-US', { month: 'short' })
}

const getDay = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.getDate()
}

onMounted(async () => {
  try {
    events.value = await api.getEvents()
  } catch (e) {
    console.error('Failed to load events:', e)
  } finally {
    loading.value = false
  }
})
</script>
