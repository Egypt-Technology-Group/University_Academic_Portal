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
      <EmptyState
        v-else-if="filteredEvents.length === 0"
        :title="$t('admin.events.noEventsFound')"
      />

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
                    :src="ev.cover_image || ev.banner_image || 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80'"
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
                <span class="text-slate-600 font-medium line-clamp-1">
                  {{ getTranslated(ev.location || ev.venue, localeStore.locale) }}
                </span>
              </td>

              <!-- DateTime -->
              <td class="py-3.5 px-4 text-slate-500 font-mono">
                {{ ev.start_time ? ev.start_time.slice(0, 10) : ev.event_date }}
                <span class="text-slate-400 text-[10px] block">
                  {{ ev.start_time ? ev.start_time.slice(11, 16) : '' }}
                </span>
              </td>

              <!-- Capacity -->
              <td class="py-3.5 px-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                  {{ ev.capacity || 200 }}
                </span>
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-end">
                <div class="inline-flex items-center gap-1.5">
                  <a
                    href="/events"
                    target="_blank"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-navy-950 hover:bg-slate-100 transition-colors"
                    :title="$t('common.view')"
                  >
                    <ExternalLink class="w-4 h-4" />
                  </a>
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-navy-950 hover:bg-slate-100 transition-colors cursor-pointer"
                    :title="$t('common.edit')"
                    @click="openEditEventModal(ev)"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>
                  <button
                    type="button"
                    class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                    :title="$t('common.delete')"
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
      :title="isEditingEvent ? $t('admin.events.editEventTitle') : $t('admin.events.addEventTitle')"
      max-width="2xl"
    >
      <form @submit.prevent="submitForm" class="space-y-4 text-start">
        <div class="grid grid-cols-12 gap-3">
          <EnterpriseFormField
            v-model="form.title_ar"
            :label="$t('admin.events.labelTitleAr')"
            required
            col-span="6"
            placeholder="مثال: هاكاثون الابتكار والذكاء الاصطناعي 2025"
          />
          <EnterpriseFormField
            v-model="form.title_en"
            :label="$t('admin.events.labelTitleEn')"
            required
            col-span="6"
            placeholder="e.g. AI & Robotics Innovation Hackathon 2025"
          />

          <!-- Device Image File Upload -->
          <div class="col-span-12 space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
              {{ $t('admin.events.labelCoverImage') }}
            </label>
            <div class="flex items-center gap-2">
              <label class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-slate-100 cursor-pointer text-xs text-slate-600 transition-colors">
                <Upload class="w-4 h-4 text-slate-400" />
                <span class="truncate max-w-[180px]">{{ eventSelectedFile ? eventSelectedFile.name : (localeStore.isRtl ? 'اختر صورة البانر من جهازك' : 'Choose banner image') }}</span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleEventImageSelect"
                />
              </label>
              <div v-if="eventImagePreview" class="relative w-12 h-9 rounded-lg overflow-hidden border border-slate-200 shrink-0">
                <img :src="eventImagePreview" class="w-full h-full object-cover" />
                <button
                  type="button"
                  class="absolute inset-0 bg-black/40 text-white flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity"
                  @click="eventImagePreview = ''; form.banner_image = ''; form.cover_image = ''; eventSelectedFile = null;"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>

          <EnterpriseFormField
            v-model="form.event_date"
            type="date"
            :label="$t('admin.events.labelDate')"
            required
            col-span="4"
          />
          <EnterpriseFormField
            v-model="form.start_time"
            type="time"
            :label="$t('admin.events.labelStartTime')"
            required
            col-span="4"
          />
          <EnterpriseFormField
            v-model="form.end_time"
            type="time"
            :label="$t('admin.events.labelEndTime')"
            col-span="4"
          />

          <EnterpriseFormField
            v-model="form.venue_ar"
            :label="$t('admin.events.labelVenueAr')"
            required
            col-span="8"
            placeholder="مثال: المدرج المركزي - كلية الهندسة"
          />
          <EnterpriseFormField
            v-model="form.capacity"
            type="number"
            :label="$t('admin.events.labelCapacity')"
            col-span="4"
            placeholder="200"
          />

          <EnterpriseFormField
            v-model="form.description_ar"
            type="textarea"
            :label="$t('admin.events.labelDescAr')"
            required
            col-span="12"
            :rows="3"
            placeholder="وصف الفعالية، الأجندة، وشروط الحضور والمشاركة..."
          />
          <EnterpriseFormField
            v-model="form.description_en"
            type="textarea"
            :label="$t('admin.events.labelDescEn')"
            col-span="12"
            :rows="3"
            placeholder="Event description, agenda, speaker details, and requirements..."
          />
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
          {{ isEditingEvent ? $t('common.saveChanges') : $t('admin.events.addEvent') }}
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleStore } from '../../../stores/locale'
import { getTranslated } from '../../../services/api'
import eventsApi from '../services/eventsApi'
import Modal from '../../../components/ui/Modal.vue'
import EmptyState from '../../../components/ui/EmptyState.vue'
import EnterpriseFormField from '../../../components/ui/EnterpriseFormField.vue'
import { useDialog } from '../../../composables/useDialog'
import { useToast } from '../../../composables/useToast'
import {
  Plus,
  Search,
  ExternalLink,
  Edit3,
  Trash2,
  Upload,
  X,
} from 'lucide-vue-next'

const { t } = useI18n()
const localeStore = useLocaleStore()
const dialog = useDialog()
const toast = useToast()

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
  cover_image: '',
  event_date: '2025-10-20',
  start_time: '10:00',
  end_time: '14:00',
  venue_ar: 'المدرج المركزي - كلية الحاسبات',
  capacity: 200,
  description_ar: '',
  description_en: '',
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
  form.cover_image = compressed
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
      (e.venue?.ar && e.venue.ar.toLowerCase().includes(q)) ||
      (e.location?.ar && e.location.ar.toLowerCase().includes(q))
    )
  }

  return list
})

const loadEvents = async () => {
  isLoading.value = true
  try {
    const data = await eventsApi.getEvents()
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
  form.cover_image = ''
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
  eventImagePreview.value = ev.cover_image || ev.banner_image || ''
  form.title_ar = ev.title?.ar || ev.title || ''
  form.title_en = ev.title?.en || ev.title || ''
  form.banner_image = ev.cover_image || ev.banner_image || ''
  form.cover_image = ev.cover_image || ev.banner_image || ''
  form.description_ar = ev.description?.ar || ev.description || ''
  form.description_en = ev.description?.en || ev.description || ''
  form.event_date = ev.event_date || ev.start_time?.slice(0, 10) || new Date().toISOString().slice(0, 10)
  form.start_time = ev.start_time ? ev.start_time.slice(11, 16) || '10:00' : '10:00'
  form.end_time = ev.end_time ? ev.end_time.slice(11, 16) || '14:00' : '14:00'
  form.venue_ar = ev.venue?.ar || ev.location?.ar || ev.venue || ev.location || 'المدرج المركزي - كلية الحاسبات'
  form.capacity = ev.capacity || 200
  isModalOpen.value = true
}

const submitForm = async () => {
  if (!form.title_ar || !form.event_date || !form.venue_ar) {
    toast.warning(
      localeStore.isRtl ? 'يرجى إدخال عنوان وتاريخ ومكان الفعالية للمتابعة.' : 'Please fill in the title, date, and venue.',
      localeStore.isRtl ? 'حقول إلزامية' : 'Required Fields'
    )
    return
  }

  try {
    if (isEditingEvent.value) {
      const updated = await eventsApi.updateEvent(editingEventId.value, { ...form })
      const idx = eventsList.value.findIndex((e) => e.id === editingEventId.value)
      if (idx !== -1) {
        eventsList.value[idx] = {
          ...eventsList.value[idx],
          title: { ar: form.title_ar, en: form.title_en },
          description: { ar: form.description_ar, en: form.description_en },
          venue: { ar: form.venue_ar, en: form.venue_en || form.venue_ar },
          location: { ar: form.venue_ar, en: form.venue_en || form.venue_ar },
          event_date: form.event_date,
          start_time: form.start_time,
          end_time: form.end_time,
          capacity: form.capacity,
          banner_image: form.banner_image || eventsList.value[idx].banner_image,
          cover_image: form.cover_image || eventsList.value[idx].cover_image,
          ...updated,
        }
      }
      toast.success(
        localeStore.isRtl ? 'تم تحديث بيانات الفعالية بنجاح.' : 'Event updated successfully.',
        localeStore.isRtl ? 'تم التحديث' : 'Event Updated'
      )
    } else {
      const created = await eventsApi.createEvent({ ...form })
      eventsList.value.unshift(created)
      toast.success(
        localeStore.isRtl ? 'تم جدولة ونشر الفعالية بنجاح.' : 'Event created and published successfully.',
        localeStore.isRtl ? 'تم الحفظ' : 'Event Created'
      )
    }
    isModalOpen.value = false
  } catch (err) {
    toast.error(
      localeStore.isRtl ? 'تعذر حفظ الفعالية، يرجى المحاولة لاحقاً.' : 'Failed to save event.',
      localeStore.isRtl ? 'خطأ في الحفظ' : 'Save Error'
    )
  }
}

const handleDeleteEvent = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف الفعالية' : 'Delete Event',
    message: t('admin.events.confirmDeleteEvent') || (localeStore.isRtl ? 'هل أنت متأكد من حذف هذه الفعالية؟' : 'Are you sure you want to delete this event?'),
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    await eventsApi.deleteEvent(id)
    eventsList.value = eventsList.value.filter((e) => e.id !== id)
    toast.info(
      localeStore.isRtl ? 'تم حذف الفعالية بنجاح.' : 'Event deleted successfully.',
      localeStore.isRtl ? 'تم الحذف' : 'Deleted'
    )
  }
}

onMounted(() => {
  loadEvents()
})
</script>
