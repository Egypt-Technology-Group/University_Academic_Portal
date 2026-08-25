<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight flex items-center gap-2.5">
          <School class="w-8 h-8 text-gold-500" />
          <span>{{ localeStore.isRtl ? 'إدارة الهيكل الأكاديمي والبرامج واللوائح' : 'Academic Structure & Degree Programs' }}</span>
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ localeStore.isRtl ? 'إدارة شاملة للكليات والمعاهد، الأقسام العلمية، البرامج والدرجات الأكاديمية، متطلبات القبول، والمصروفات الدراسية' : 'Full CRUD management for colleges, institutes, academic departments, degree programs, curricula, admission rules, and tuition fees' }}
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex flex-wrap items-center gap-2.5">
        <button
          v-if="activeTab === 'colleges'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewCollegeModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة كلية / معهد جديد' : 'New College / Institute' }}</span>
        </button>

        <button
          v-if="activeTab === 'departments'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewDepartmentModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة قسم علمي جديد' : 'New Department' }}</span>
        </button>

        <button
          v-if="activeTab === 'programs'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewProgramModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة برنامج / درجة دراسية' : 'New Degree Program' }}</span>
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200/80 shadow-xs flex flex-wrap gap-1.5">
      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'colleges' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'colleges'"
      >
        <Building2 class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'الكليات والمعاهد' : 'Colleges & Institutes' }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'colleges' ? 'bg-gold-500 text-navy-950 font-black' : 'bg-slate-200 text-slate-700'">
          {{ collegesList.length }}
        </span>
      </button>

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'departments' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'departments'"
      >
        <Layers class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'الأقسام العلمية' : 'Academic Departments' }}</span>
      </button>

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'programs' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'programs'"
      >
        <GraduationCap class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'البرامج الأكاديمية والدرجات العلمية' : 'Degree Programs' }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'programs' ? 'bg-gold-500 text-navy-950 font-black' : 'bg-slate-200 text-slate-700'">
          {{ programsList.length }}
        </span>
      </button>
    </div>

    <!-- TAB 1: COLLEGES & INSTITUTES -->
    <div v-if="activeTab === 'colleges'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="col in collegesList"
          :key="col.id"
          class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition-all group"
        >
          <div>
            <div class="relative h-40 overflow-hidden bg-slate-100">
              <img
                :src="col.banner_image || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=800&q=80'"
                :alt="getTranslated(col.name, localeStore.locale)"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-transparent"></div>
              <div class="absolute bottom-3 inset-x-4 flex items-center justify-between text-white">
                <span class="text-[10px] font-bold uppercase tracking-wider bg-gold-500/90 text-navy-950 px-2 py-0.5 rounded-md font-mono">
                  Order: {{ col.sort_order || 0 }}
                </span>
                <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-md', col.is_active !== false ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white']">
                  {{ col.is_active !== false ? 'Active' : 'Hidden' }}
                </span>
              </div>
            </div>

            <div class="p-5 space-y-3">
              <h3 class="font-black text-navy-950 text-base line-clamp-1">
                {{ getTranslated(col.name, localeStore.locale) }}
              </h3>
              <p class="text-xs text-slate-500 line-clamp-2">
                {{ getTranslated(col.about, localeStore.locale) || 'مؤسسة تعليمية رائدة تقدم برامج أكاديمية متميزة مواكبة لسوق العمل الدولي.' }}
              </p>

              <div class="text-[11px] text-slate-600 font-medium pt-2 border-t border-slate-100 flex items-center gap-1.5">
                <UserCheck class="w-3.5 h-3.5 text-gold-500 shrink-0" />
                <span>{{ localeStore.isRtl ? 'عميد الكلية:' : 'Dean:' }} <strong>{{ getTranslated(col.dean_name, localeStore.locale) || 'أ.د. عميد الكلية' }}</strong></span>
              </div>
            </div>
          </div>

          <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <div class="text-[11px] font-mono text-slate-500">
              ID: {{ col.id }}
            </div>
            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="p-1.5 rounded-lg text-slate-500 hover:text-navy-900 hover:bg-slate-200 transition-colors cursor-pointer"
                title="Edit College"
                @click="openEditCollegeModal(col)"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button
                type="button"
                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                title="Delete College"
                @click="handleDeleteCollege(col.id)"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: DEPARTMENTS -->
    <div v-if="activeTab === 'departments'" class="space-y-4">
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'القسم العلمي' : 'Department Name' }}</th>
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'الكلية التابع لها' : 'Affiliated College' }}</th>
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'رئيس القسم' : 'Head of Department' }}</th>
              <th class="py-3.5 px-4 text-end">{{ localeStore.isRtl ? 'الإجراءات' : 'Actions' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="dept in sampleDepartments" :key="dept.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="py-3.5 px-4">
                <div class="font-bold text-navy-950 text-sm">{{ getTranslated(dept.name, localeStore.locale) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ getTranslated(dept.description, localeStore.locale) }}</div>
              </td>
              <td class="py-3.5 px-4">
                <span class="inline-block text-[11px] font-bold text-navy-900 bg-navy-50 px-2 py-0.5 rounded border border-navy-100">
                  {{ getCollegeName(dept.college_id) }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-slate-700 font-medium">
                {{ getTranslated(dept.head_name, localeStore.locale) || 'أ.د. رئيس مجلس القسم' }}
              </td>
              <td class="py-3.5 px-4 text-end whitespace-nowrap">
                <button
                  type="button"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                  @click="handleDeleteDepartment(dept.id)"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: DEGREE PROGRAMS -->
    <div v-if="activeTab === 'programs'" class="space-y-4">
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
          <input
            v-model="searchProgram"
            type="text"
            :placeholder="localeStore.isRtl ? 'البحث باسم البرنامج الأكاديمي أو الكود...' : 'Search program by title or code...'"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm focus:bg-white focus:border-navy-900"
          />
        </div>

        <div class="w-full md:w-52 shrink-0">
          <select
            v-model="filterDegree"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
          >
            <option value="all">{{ localeStore.isRtl ? 'جميع الدرجات العلمية' : 'All Degree Levels' }}</option>
            <option value="bachelor">Bachelor (بكالوريوس)</option>
            <option value="master">Master (ماجستير)</option>
            <option value="doctorate">Doctorate (دكتوراه)</option>
            <option value="diploma">Diploma (دبلوم عالي)</option>
          </select>
        </div>
      </div>

      <!-- Programs Grid / Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-start text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'البرنامج الأكاديمي' : 'Degree Program' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الدرجة' : 'Degree Level' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الساعات والسنوات' : 'Credits & Duration' }}</th>
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'المصروفات السنوية' : 'Tuition Fees' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الحالة' : 'Status' }}</th>
                <th class="py-3.5 px-4 text-end">{{ localeStore.isRtl ? 'الإجراءات' : 'Actions' }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="prog in filteredPrograms" :key="prog.id" class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3.5 px-4">
                  <div class="font-bold text-navy-950 text-sm">{{ getTranslated(prog.name, localeStore.locale) }}</div>
                  <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ prog.slug }}</div>
                </td>

                <td class="py-3.5 px-4 text-center">
                  <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-gold-50 text-gold-900 border border-gold-200 uppercase font-mono">
                    {{ prog.degree_level || 'Bachelor' }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center font-mono">
                  <div class="font-bold text-navy-950">{{ prog.credit_hours || 136 }} Cr Hrs</div>
                  <div class="text-[10px] text-slate-400">{{ prog.duration_years || 4 }} Years</div>
                </td>

                <td class="py-3.5 px-4">
                  <div class="text-xs font-bold text-slate-700">{{ getTranslated(prog.tuition_fees, localeStore.locale) || '55,000 EGP / Year' }}</div>
                </td>

                <td class="py-3.5 px-4 text-center">
                  <span :class="['text-[10px] font-bold px-2.5 py-0.5 rounded-full', prog.is_active !== false ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600']">
                    {{ prog.is_active !== false ? 'Active' : 'Draft' }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                      @click="handleDeleteProgram(prog.id)"
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
    </div>

    <!-- MODAL: CREATE/EDIT COLLEGE -->
    <Modal
      v-model="isCollegeModalOpen"
      :title="isEditingCollege ? (localeStore.isRtl ? 'تعديل بيانات الكلية' : 'Edit College Details') : (localeStore.isRtl ? 'إضافة كلية أو معهد جديد' : 'New College / Institute')"
      size="lg"
      @close="isCollegeModalOpen = false"
    >
      <form @submit.prevent="submitCollegeForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم الكلية بالعربية' : 'College Name (Ar)' }} *</label>
            <input v-model="collegeForm.name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="كلية علوم الحاسب والذكاء الاصطناعي" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">College Name (En) *</label>
            <input v-model="collegeForm.name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="Faculty of Computer Science & AI" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم العميد (عربي)' : 'Dean Name (Ar)' }}</label>
            <input v-model="collegeForm.dean_name_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="أ.د. عصام النجار" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Dean Name (En)</label>
            <input v-model="collegeForm.dean_name_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="Prof. Dr. Essam El-Naggar" />
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'رابط صورة الغلاف أو البانر' : 'Banner Image URL' }}</label>
          <input v-model="collegeForm.banner_image" type="url" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" placeholder="https://images.unsplash.com/..." />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'نبذة عن الكلية (عربي)' : 'About College (Ar)' }}</label>
            <textarea v-model="collegeForm.about_ar" rows="3" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="نبذة تعريفية شاملة..."></textarea>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">About College (En)</label>
            <textarea v-model="collegeForm.about_en" rows="3" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="Comprehensive overview..."></textarea>
          </div>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isCollegeModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitCollegeForm">{{ localeStore.isRtl ? 'حفظ الكلية' : 'Save College' }}</button>
      </template>
    </Modal>

    <!-- MODAL: CREATE PROGRAM -->
    <Modal
      v-model="isProgramModalOpen"
      :title="localeStore.isRtl ? 'إضافة برنامج أكاديمي ولائحة دراسية' : 'Add Degree Program & Curriculum'"
      size="lg"
      @close="isProgramModalOpen = false"
    >
      <form @submit.prevent="submitProgramForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم البرنامج بالعربية' : 'Program Name (Ar)' }} *</label>
            <input v-model="programForm.name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="بكالوريوس علوم البيانات والذكاء الاصطناعي" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Program Name (En) *</label>
            <input v-model="programForm.name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="B.Sc. Data Science & Artificial Intelligence" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الدرجة العلمية' : 'Degree Level' }}</label>
            <select v-model="programForm.degree_level" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs">
              <option value="bachelor">Bachelor (بكالوريوس)</option>
              <option value="master">Master (ماجستير)</option>
              <option value="doctorate">Doctorate (دكتوراه)</option>
              <option value="diploma">Diploma (دبلوم)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'سنوات الدراسة' : 'Duration (Years)' }}</label>
            <input v-model="programForm.duration_years" type="number" min="1" max="8" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الساعات المعتمدة' : 'Credit Hours' }}</label>
            <input v-model="programForm.credit_hours" type="number" min="10" max="300" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'المصروفات الدراسية (عربي)' : 'Tuition Fees (Ar)' }}</label>
            <input v-model="programForm.tuition_fees_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="60,000 جنيه مصري / العام" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Tuition Fees (En)</label>
            <input v-model="programForm.tuition_fees_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="60,000 EGP / Academic Year" />
          </div>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isProgramModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitProgramForm">{{ localeStore.isRtl ? 'حفظ البرنامج' : 'Save Program' }}</button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import Modal from '../../components/ui/Modal.vue'
import {
  School,
  Building2,
  Layers,
  GraduationCap,
  Plus,
  Search,
  Trash2,
  Edit3,
  UserCheck,
} from 'lucide-vue-next'

const localeStore = useLocaleStore()
const activeTab = ref('colleges')

const collegesList = ref([])
const programsList = ref([])
const searchProgram = ref('')
const filterDegree = ref('all')

const sampleDepartments = ref([
  {
    id: 1,
    college_id: 1,
    name: { ar: 'قسم علوم الحاسب ونظم المعلومات', en: 'Computer Science & Information Systems' },
    head_name: { ar: 'أ.د. عمرو عبد السلام', en: 'Prof. Dr. Amr Abdelsalam' },
    description: { ar: 'يغطي مسارات الخوارزميات وهياكل البيانات وتطوير البرمجيات المتقدمة.', en: 'Covers algorithms, data structures, and advanced software engineering.' }
  },
  {
    id: 2,
    college_id: 1,
    name: { ar: 'قسم الذكاء الاصطناعي والروبوتات', en: 'Artificial Intelligence & Robotics' },
    head_name: { ar: 'أ.د. شريف زكريا', en: 'Prof. Dr. Sherif Zakaria' },
    description: { ar: 'متخصص في التعلم العميق، ومعالجة اللغات الطبيعية، والنظم المستقلة.', en: 'Specialized in deep learning, NLP, and autonomous intelligent systems.' }
  },
  {
    id: 3,
    college_id: 2,
    name: { ar: 'قسم الصيدلانيات والتكنولوجيا الصيدلية', en: 'Pharmaceutics & Pharmaceutical Technology' },
    head_name: { ar: 'أ.د. سحر عبد الحميد', en: 'Prof. Dr. Sahar Abdelhamid' },
    description: { ar: 'تصميم وتطوير الأشكال الصيدلية وأنظمة إيصال الدواء المستهدفة.', en: 'Design and delivery of targeted dosage forms and novel therapeutics.' }
  }
])

const isCollegeModalOpen = ref(false)
const isEditingCollege = ref(false)
const editingCollegeId = ref(null)
const collegeForm = reactive({
  name_ar: '',
  name_en: '',
  dean_name_ar: '',
  dean_name_en: '',
  about_ar: '',
  about_en: '',
  banner_image: ''
})

const isProgramModalOpen = ref(false)
const programForm = reactive({
  department_id: 1,
  name_ar: '',
  name_en: '',
  degree_level: 'bachelor',
  duration_years: 4,
  credit_hours: 136,
  tuition_fees_ar: '55,000 جنيه مصري / العام الدراسي',
  tuition_fees_en: '55,000 EGP / Academic Year'
})

const filteredPrograms = computed(() => {
  let list = [...programsList.value]
  if (filterDegree.value !== 'all') {
    list = list.filter((p) => p.degree_level === filterDegree.value)
  }
  if (searchProgram.value.trim()) {
    const q = searchProgram.value.trim().toLowerCase()
    list = list.filter((p) =>
      (p.name?.ar && p.name.ar.toLowerCase().includes(q)) ||
      (p.name?.en && p.name.en.toLowerCase().includes(q)) ||
      (p.slug && p.slug.toLowerCase().includes(q))
    )
  }
  return list
})

const getCollegeName = (colId) => {
  const col = collegesList.value.find((c) => c.id === colId)
  if (col) return getTranslated(col.name, localeStore.locale)
  return localeStore.isRtl ? 'كلية الهندسة والتكنولوجيا' : 'Faculty of Engineering'
}

const loadData = async () => {
  try {
    const cols = await api.getColleges()
    collegesList.value = cols || []
    const progs = await api.getPrograms()
    programsList.value = progs || []
  } catch (err) {
    console.error(err)
  }
}

const openNewCollegeModal = () => {
  isEditingCollege.value = false
  editingCollegeId.value = null
  collegeForm.name_ar = ''
  collegeForm.name_en = ''
  collegeForm.dean_name_ar = ''
  collegeForm.dean_name_en = ''
  collegeForm.about_ar = ''
  collegeForm.about_en = ''
  collegeForm.banner_image = ''
  isCollegeModalOpen.value = true
}

const openEditCollegeModal = (col) => {
  isEditingCollege.value = true
  editingCollegeId.value = col.id
  collegeForm.name_ar = col.name?.ar || ''
  collegeForm.name_en = col.name?.en || ''
  collegeForm.dean_name_ar = col.dean_name?.ar || ''
  collegeForm.dean_name_en = col.dean_name?.en || ''
  collegeForm.about_ar = col.about?.ar || ''
  collegeForm.about_en = col.about?.en || ''
  collegeForm.banner_image = col.banner_image || ''
  isCollegeModalOpen.value = true
}

const submitCollegeForm = async () => {
  if (isEditingCollege.value) {
    await api.updateCollege(editingCollegeId.value, { ...collegeForm })
  } else {
    const created = await api.createCollege({ ...collegeForm })
    collegesList.value.unshift(created)
  }
  isCollegeModalOpen.value = false
}

const handleDeleteCollege = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'هل أنت متأكد من حذف هذه الكلية نهائياً؟' : 'Are you sure you want to delete this college?')) {
    await api.deleteCollege(id)
    collegesList.value = collegesList.value.filter((c) => c.id !== id)
  }
}

const openNewDepartmentModal = () => {
  const nameAr = prompt(localeStore.isRtl ? 'اسم القسم العلمي بالعربية:' : 'Department name (Arabic):', 'قسم هندسة النظم الذكية')
  if (!nameAr) return
  const nameEn = prompt('Department name (English):', 'Department of Intelligent Systems')
  if (!nameEn) return
  sampleDepartments.value.unshift({
    id: Date.now(),
    college_id: collegesList.value[0]?.id || 1,
    name: { ar: nameAr, en: nameEn },
    head_name: { ar: 'أ.د. رئيس القسم', en: 'Prof. Dr. Head of Dept' },
    description: { ar: 'قسم أكاديمي متقدم.', en: 'Advanced academic department.' }
  })
}

const handleDeleteDepartment = (id) => {
  if (window.confirm(localeStore.isRtl ? 'حذف القسم العلمي؟' : 'Delete department?')) {
    sampleDepartments.value = sampleDepartments.value.filter((d) => d.id !== id)
  }
}

const openNewProgramModal = () => {
  programForm.name_ar = ''
  programForm.name_en = ''
  programForm.degree_level = 'bachelor'
  programForm.duration_years = 4
  programForm.credit_hours = 136
  isProgramModalOpen.value = true
}

const submitProgramForm = async () => {
  const created = await api.createProgram({ ...programForm })
  programsList.value.unshift(created)
  isProgramModalOpen.value = false
}

const handleDeleteProgram = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'هل تريد حذف هذا البرنامج الأكاديمي؟' : 'Delete this program?')) {
    await api.deleteProgram(id)
    programsList.value = programsList.value.filter((p) => p.id !== id)
  }
}

onMounted(() => {
  loadData()
})
</script>
