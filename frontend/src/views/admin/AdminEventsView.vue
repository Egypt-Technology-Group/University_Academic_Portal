<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight">
          {{ $t('admin.events.title') }}
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ $t('admin.events.subtitle') }}
        </p>
      </div>

      <button
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
        @click="openNewEventModal"
      >
        <Plus class="w-4 h-4 text-gold-400" />
        <span>{{ $t('admin.events.addEvent') }}</span>
      </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-3">
      <div class="relative flex-1 w-full">
        <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('admin.events.searchPlaceholder')"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:border-navy-900 focus:ring-1 focus:ring-navy-900"
        />
      </div>

      <div class="w-full sm:w-56 shrink-0">
        <select
          v-model="categoryFilter"
          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
        >
          <option value="all">{{ $t('admin.events.allCategories') }}</option>
          <option value="conference">{{ $t('admin.events.catConference') }}</option>
          <option value="workshop">{{ $t('admin.events.catWorkshop') }}</option>
          <option value="seminar">{{ $t('admin.events.catSeminar') }}</option>
          <option value="campus">{{ $t('admin.events.catCampus') }}</option>
        </select>
      </div>
    </div>

    <!-- Events Grid / Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="py-16 text-center text-slate-400">
        <div class="w-8 h-8 border-2 border-navy-900 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <div class="text-xs font-bold">{{ $t('common.loading') }}</div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredEvents.length === 0" class="py-16 text-center text-slate-500">
        <CalendarX class="w-12 h-12 text-slate-300 mx-auto mb-3" />
        <div class="text-sm font-bold text-navy-950">{{ $t('admin.events.noEventsFound') }}</div>
      </div>

      <!-- Events List Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">{{ $t('admin.events.colEvent') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.events.colVenue') }}</th>
              <th class="py-3.5 px-4 text-start">{{ $t('admin.events.colDateTime') }}</th>
              <th class="py-3.5 px-4 text-center">{{ $t('admin.events.colCapacity') }}</th>
              <th class="py-3.5 px-4 text-end">{{ $t('admin.events.colActions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="ev in filteredEvents"
              :key="ev.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- Thumbnail & Title -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3.5">
                  <img
                    :src="ev.banner_image"
                    :alt="getTranslated(ev.title, localeStore.locale)"
                    class="w-14 h-10 rounded-lg object-cover border border-slate-200 shrink-0"
                  />
                  <div class="max-w-md">
                    <div class="font-bold text-navy-950 text-sm line-clamp-1">
                      {{ getTranslated(ev.title, localeStore.locale) }}
                    </div>
                    <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                      {{ getTranslated(ev.description, localeStore.locale) }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Venue -->
              <td class="py-3.5 px-4">
                <div class="font-medium text-slate-800 flex items-center gap-1.5">
                  <MapPin class="w-3.5 h-3.5 text-gold-600 shrink-0" />
                  <span class="truncate max-w-[180px]">{{ getTranslated(ev.venue, localeStore.locale) }}</span>
                </div>
              </td>

              <!-- Date & Time -->
              <td class="py-3.5 px-4 font-mono text-slate-600">
                <div class="font-bold text-navy-950">{{ formatStandardDate(ev.event_date || ev.start_time, localeStore.locale) }}</div>
                <div class="text-[10px] text-slate-500 font-semibold">{{ formatTimeRange(ev.start_time, ev.end_time, localeStore.locale) }}</div>
              </td>

              <!-- Capacity -->
              <td class="py-3.5 px-4 text-center font-mono">
                <span class="inline-block font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                  {{ ev.registered_count || 0 }} / {{ ev.capacity }}
                </span>
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-navy-900 hover:bg-slate-100 transition-colors"
                    title="Edit Event"
                    @click="openEditEventModal(ev)"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                    title="Delete Event"
                    @click="handleDeleteEvent(ev.id)"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: ADD / EDIT EVENT -->
    <Modal
      v-model="isModalOpen"
      :title="isEditingEvent ? (localeStore.isRtl ? 'تعديل بيانات الفعالية والمؤتمر' : 'Edit Event / Conference') : $t('admin.events.modalTitle')"
      max-width="2xl"
      @close="isModalOpen = false"
    >
      <form @submit.prevent="submitForm" class="space-y-4 text-start">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelTitleAr') }} *
            </label>
            <input
              v-model="form.title_ar"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="مثال: مؤتمر الروبوتات والذكاء الاصطناعي..."
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelTitleEn') }} *
            </label>
            <input
              v-model="form.title_en"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="e.g. AI & Robotics Conference..."
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelDate') }} *
            </label>
            <input
              v-model="form.event_date"
              type="date"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelStartTime') }}
            </label>
            <input
              v-model="form.start_time"
              type="time"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelEndTime') }}
            </label>
            <input
              v-model="form.end_time"
              type="time"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelVenue') }} *
            </label>
            <input
              v-model="form.venue_ar"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="القاعة الكبرى - الحرم الجامعي"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $t('admin.events.labelCapacity') }}
            </label>
            <input
              v-model="form.capacity"
              type="number"
              min="1"
              class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
              placeholder="250"
            />
          </div>
        </div>

        <!-- Event Cover Banner Image Upload -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            {{ localeStore.isRtl ? 'صورة بوستر أو غلاف الفعالية' : 'Event Cover / Banner Photo' }}
          </label>
          <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
            <img
              :src="eventImagePreview || form.banner_image || 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=400&q=80'"
              class="w-16 h-12 rounded-lg object-cover border border-slate-200 shadow-xs shrink-0"
            />
            <div class="flex-1 min-w-0">
              <input
                ref="eventFileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleEventImageSelect"
              />
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 border border-slate-300"
                @click="$refs.eventFileInput.click()"
              >
                <Upload class="w-3.5 h-3.5 text-gold-600" />
                <span>{{ localeStore.isRtl ? 'اختيار بوستر من جهازك' : 'Choose Poster Image from Device' }}</span>
              </button>
              <div v-if="eventSelectedFile" class="text-[10px] text-emerald-700 font-mono mt-1 truncate">
                ✓ {{ eventSelectedFile.name }} ({{ (eventSelectedFile.size / 1024).toFixed(0) }} KB)
              </div>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            {{ $t('admin.events.labelDescriptionAr') }}
          </label>
          <textarea
            v-model="form.description_ar"
            rows="3"
            class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:border-navy-900"
            placeholder="وصف الفعالية وأهدافها..."
          ></textarea>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200"
          @click="isModalOpen = false"
        >
          {{ $t('common.cancel') }}
        </button>

        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md"
          @click="submitForm"
        >
          {{ $t('admin.events.addEvent') }}
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import { formatStandardDate, formatTimeRange } from '../../utils/dateFormat'
import Modal from '../../components/ui/Modal.vue'
import {
  Plus,
  Search,
  MapPin,
  Edit3,
  Trash2,
  CalendarX,
  Upload,
  X,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()

const eventsList = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const categoryFilter = ref('all')
const isModalOpen = ref(false)
const isEditingEvent = ref(false)
const editingEventId = ref(null)

const eventSelectedFile = ref(null)
const eventImagePreview = ref('')

const form = reactive({
  title_ar: '',
  title_en: '',
  banner_image: '',
  event_date: '2025-10-20',
  start_time: '10:00',
  end_time: '14:00',
  venue_ar: 'المدرج المركزي - كلية الحاسبات',
  capacity: 200,
  description_ar: '',
})

const compressImage = (file, maxWidth = 800, quality = 0.75) => {
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (event) => {
      const img = new Image()
      img.src = event.target.result
      img.onload = () => {
        const elem = document.createElement('canvas')
        let width = img.width
        let height = img.height
        if (width > maxWidth) {
          height = Math.round((height * maxWidth) / width)
          width = maxWidth
        }
        elem.width = width
        elem.height = height
        const ctx = elem.getContext('2d')
        ctx.drawImage(img, 0, 0, width, height)
        resolve(elem.toDataURL('image/jpeg', quality))
      }
    }
  })
}

const handleEventImageSelect = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  eventSelectedFile.value = file
  const compressed = await compressImage(file, 800, 0.7)
  eventImagePreview.value = compressed
  form.banner_image = compressed
}

const filteredEvents = computed(() => {
  let list = [...eventsList.value]

  if (categoryFilter.value !== 'all') {
    list = list.filter((e) => e.category === categoryFilter.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase()
    list = list.filter((e) =>
      (e.title?.ar && e.title.ar.toLowerCase().includes(q)) ||
      (e.title?.en && e.title.en.toLowerCase().includes(q)) ||
      (e.venue?.ar && e.venue.ar.toLowerCase().includes(q))
    )
  }

  return list
})

const loadEvents = async () => {
  isLoading.value = true
  try {
    const data = await api.getEvents()
    eventsList.value = data || []
  } catch (e) {
    console.error('Failed to load events', e)
  } finally {
    isLoading.value = false
  }
}

const openNewEventModal = () => {
  isEditingEvent.value = false
  editingEventId.value = null
  eventSelectedFile.value = null
  eventImagePreview.value = ''
  form.title_ar = ''
  form.title_en = ''
  form.banner_image = ''
  form.description_ar = ''
  form.description_en = ''
  form.event_date = new Date().toISOString().slice(0, 10)
  form.start_time = '10:00'
  form.end_time = '14:00'
  form.venue_ar = 'المدرج المركزي - كلية الحاسبات'
  form.capacity = 200
  isModalOpen.value = true
}

const openEditEventModal = (ev) => {
  isEditingEvent.value = true
  editingEventId.value = ev.id
  eventSelectedFile.value = null
  eventImagePreview.value = ev.banner_image || ''
  form.title_ar = ev.title?.ar || ev.title || ''
  form.title_en = ev.title?.en || ev.title || ''
  form.banner_image = ev.banner_image || ''
  form.description_ar = ev.description?.ar || ev.description || ''
  form.description_en = ev.description?.en || ev.description || ''
  form.event_date = ev.event_date || ev.start_time?.slice(0, 10) || new Date().toISOString().slice(0, 10)
  form.start_time = ev.start_time ? ev.start_time.slice(11, 16) || '10:00' : '10:00'
  form.end_time = ev.end_time ? ev.end_time.slice(11, 16) || '14:00' : '14:00'
  form.venue_ar = ev.venue?.ar || ev.location?.ar || ev.venue || 'المدرج المركزي - كلية الحاسبات'
  form.capacity = ev.capacity || 200
  isModalOpen.value = true
}

const submitForm = async () => {
  if (!form.title_ar || !form.event_date || !form.venue_ar) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }

  try {
    if (isEditingEvent.value) {
      const updated = await api.updateEvent(editingEventId.value, { ...form })
      const idx = eventsList.value.findIndex((e) => e.id === editingEventId.value)
      if (idx !== -1) {
        eventsList.value[idx] = {
          ...eventsList.value[idx],
          title: { ar: form.title_ar, en: form.title_en },
          description: { ar: form.description_ar, en: form.description_en },
          venue: { ar: form.venue_ar, en: form.venue_en || form.venue_ar },
          event_date: form.event_date,
          start_time: form.start_time,
          end_time: form.end_time,
          capacity: form.capacity,
          banner_image: form.banner_image || eventsList.value[idx].banner_image,
          ...updated,
        }
      }
    } else {
      const created = await api.createEvent({ ...form })
      eventsList.value.unshift(created)
    }
    isModalOpen.value = false
  } catch (err) {
    alert('Failed to save event')
  }
}

const handleDeleteEvent = async (id) => {
  if (window.confirm(t('admin.events.confirmDeleteEvent'))) {
    await api.deleteEvent(id)
    eventsList.value = eventsList.value.filter((e) => e.id !== id)
  }
}

onMounted(() => {
  loadEvents()
})
</script>
