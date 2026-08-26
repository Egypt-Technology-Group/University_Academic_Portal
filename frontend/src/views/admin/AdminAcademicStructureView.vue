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

        <button
          v-if="activeTab === 'faculty'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewFacultyModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة عضو هيئة تدريس' : 'Add Faculty Member' }}</span>
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

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'faculty' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'faculty'"
      >
        <Users class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'هيئة التدريس والباحثين' : 'Faculty & Researchers' }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'faculty' ? 'bg-gold-500 text-navy-950 font-black' : 'bg-slate-200 text-slate-700'">
          {{ facultyList.length }}
        </span>
      </button>
    </div>

    <!-- TAB 1: COLLEGES & INSTITUTES -->
    <div v-if="activeTab === 'colleges'" class="space-y-4">
      <EmptyState
        v-if="collegesList.length === 0"
        :title="localeStore.isRtl ? 'لا توجد كليات أو معاهد مسجلة' : 'No colleges or institutes registered'"
        :description="localeStore.isRtl ? 'استخدم زر إضافة كلية جديد بالأعلى لإنشاء وتوثيق كلية أو معهد دراسي.' : 'Use the top action button to register a new college or institute.'"
      />
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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
                  class="p-1.5 rounded-lg text-slate-500 hover:text-navy-900 hover:bg-slate-100 transition-colors cursor-pointer me-1"
                  @click="openEditDepartmentModal(dept)"
                >
                  <Edit3 class="w-4 h-4" />
                </button>
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
        <EmptyState
          v-if="filteredPrograms.length === 0"
          :title="localeStore.isRtl ? 'لا توجد برامج أكاديمية مطابقة' : 'No degree programs found'"
          :description="localeStore.isRtl ? 'لم يتم العثور على برامج دراسية تطابق معايير البحث أو التصفية الحالية.' : 'No academic programs match the current search or degree level filter.'"
        />
        <div v-else class="overflow-x-auto">
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
                      class="p-1.5 rounded-lg text-slate-500 hover:text-navy-900 hover:bg-slate-100 transition-colors cursor-pointer"
                      @click="openEditProgramModal(prog)"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
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

    <!-- MODAL: CREATE/EDIT PROGRAM -->
    <Modal
      v-model="isProgramModalOpen"
      :title="isEditingProgram ? (localeStore.isRtl ? 'تعديل البرنامج الأكاديمي والدرجة العلمية' : 'Edit Degree Program') : (localeStore.isRtl ? 'إضافة برنامج دراسي جديد' : 'New Degree Program')"
      size="lg"
      @close="isProgramModalOpen = false"
    >
      <form @submit.prevent="submitProgramForm" class="space-y-4 text-start text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'القسم العلمي التابع له البرنامج' : 'Affiliated Department' }} *</label>
          <select v-model="programForm.department_id" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold bg-white">
            <option v-for="d in sampleDepartments" :key="d.id" :value="d.id">
              {{ getTranslated(d.name, localeStore.locale) }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم البرنامج (عربي)' : 'Program Name (Ar)' }} *</label>
            <input v-model="programForm.name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="بكالوريوس هندسة الذكاء الاصطناعي" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Program Name (En) *</label>
            <input v-model="programForm.name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="B.Sc. in Artificial Intelligence Engineering" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'المستوى / الدرجة' : 'Degree Level' }} *</label>
            <select v-model="programForm.degree_level" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs bg-white">
              <option value="bachelor">Bachelor (بكالوريوس)</option>
              <option value="master">Master (ماجستير)</option>
              <option value="doctorate">Doctorate (دكتوراه)</option>
              <option value="diploma">Diploma (دبلوم)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'سنوات الدراسة' : 'Duration (Years)' }} *</label>
            <input v-model.number="programForm.duration_years" type="number" min="1" max="7" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الساعات المعتمدة' : 'Credit Hours' }} *</label>
            <input v-model.number="programForm.credit_hours" type="number" min="30" max="300" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'المصروفات السنوية (عربي)' : 'Tuition Fees (Ar)' }}</label>
            <input v-model="programForm.tuition_fees_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="55,000 جنيه مصري / العام الدراسي" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Tuition Fees (En)</label>
            <input v-model="programForm.tuition_fees_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="55,000 EGP / Academic Year" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'شروط القبول (عربي)' : 'Admission Requirements (Ar)' }}</label>
            <textarea v-model="programForm.admission_requirements_ar" rows="2" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="شهادة الثانوية العامة شعبة علمي رياضة أو ما يعادلها..."></textarea>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Admission Requirements (En)</label>
            <textarea v-model="programForm.admission_requirements_en" rows="2" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="High school diploma (Math section) or equivalent..."></textarea>
          </div>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isProgramModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitProgramForm">{{ localeStore.isRtl ? 'حفظ البرنامج' : 'Save Program' }}</button>
      </template>
    </Modal>

    <!-- MODAL: CREATE/EDIT DEPARTMENT -->
    <Modal
      v-model="isDepartmentModalOpen"
      :title="isEditingDepartment ? (localeStore.isRtl ? 'تعديل بيانات القسم العلمي' : 'Edit Department Details') : (localeStore.isRtl ? 'إضافة قسم علمي جديد' : 'New Academic Department')"
      size="md"
      @close="isDepartmentModalOpen = false"
    >
      <form @submit.prevent="submitDepartmentForm" class="space-y-4 text-start text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الكلية التابع لها القسم' : 'Affiliated College' }} *</label>
          <select v-model="departmentForm.college_id" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold bg-white">
            <option v-for="c in collegesList" :key="c.id" :value="c.id">
              {{ getTranslated(c.name, localeStore.locale) }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم القسم (عربي)' : 'Department Name (Ar)' }} *</label>
            <input v-model="departmentForm.name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="قسم الذكاء الاصطناعي" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Department Name (En) *</label>
            <input v-model="departmentForm.name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="Department of Artificial Intelligence" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'رئيس مجلس القسم (عربي)' : 'Head of Dept (Ar)' }}</label>
            <input v-model="departmentForm.head_name_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="أ.د. حسام عادل" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Head of Dept (En)</label>
            <input v-model="departmentForm.head_name_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="Prof. Dr. Hossam Adel" />
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'نبذة عن القسم العلمي' : 'Department Description' }}</label>
          <textarea v-model="departmentForm.description_ar" rows="2.5" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="الخطة التدريسية ومجالات التخصص..."></textarea>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isDepartmentModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitDepartmentForm">{{ localeStore.isRtl ? 'حفظ القسم' : 'Save Department' }}</button>
      </template>
    </Modal>

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

        <!-- College Banner Image Upload -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'صورة الغلاف أو البانر للكلية' : 'College Banner Photo' }}</label>
          <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
            <img
              :src="collegeBannerPreview || collegeForm.banner_image || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=400&q=80'"
              class="w-16 h-12 rounded-lg object-cover border border-slate-200 shadow-xs shrink-0"
            />
            <div class="flex-1 min-w-0">
              <input
                ref="collegeBannerFileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleCollegeBannerSelect"
              />
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-navy-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 border border-slate-300"
                @click="$refs.collegeBannerFileInput.click()"
              >
                <Upload class="w-3.5 h-3.5 text-gold-600" />
                <span>{{ localeStore.isRtl ? 'اختيار بانر من جهازك' : 'Choose Banner from Device' }}</span>
              </button>
              <div v-if="collegeSelectedFile" class="text-[10px] text-emerald-700 font-mono mt-1 truncate">
                ✓ {{ collegeSelectedFile.name }} ({{ (collegeSelectedFile.size / 1024).toFixed(0) }} KB)
              </div>
            </div>
          </div>
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

    <!-- TAB 4: FACULTY & RESEARCHERS -->
    <div v-if="activeTab === 'faculty'" class="space-y-4">
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
          <input
            v-model="searchFaculty"
            type="text"
            :placeholder="localeStore.isRtl ? 'البحث بالاسم، اللقب الأكاديمي، أو الاهتمامات البحثية...' : 'Search faculty by name, title, research...'"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm focus:bg-white focus:border-navy-900"
          />
        </div>
      </div>

      <EmptyState
        v-if="filteredFaculty.length === 0"
        :title="localeStore.isRtl ? 'لا يوجد أعضاء هيئة تدريس مطابقين' : 'No faculty members found'"
        :description="localeStore.isRtl ? 'لم يتم العثور على باحثين أو أساتذة يطابقون استعلام البحث.' : 'No faculty members match the current search query.'"
      />
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="fac in filteredFaculty"
          :key="fac.id"
          class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4 flex flex-col justify-between hover:shadow-md transition-all"
        >
          <div class="space-y-3">
            <div class="flex items-start gap-3.5">
              <img
                :src="fac.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'"
                :alt="fac.name"
                class="w-14 h-14 rounded-2xl object-cover ring-2 ring-gold-500/30 shrink-0"
              />
              <div class="flex-1 min-w-0">
                <h4 class="font-black text-navy-950 text-sm truncate">{{ fac.name }}</h4>
                <div class="text-[11px] font-bold text-gold-600 truncate mt-0.5">
                  {{ getTranslated(fac.academic_title, localeStore.locale) }}
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">{{ fac.email }}</div>
              </div>
            </div>

            <p class="text-xs text-slate-600 line-clamp-2">
              {{ getTranslated(fac.bio, localeStore.locale) || 'أستاذ باحث متخصص في النظم الحديثة وتطبيقات الذكاء الاصطناعي.' }}
            </p>

            <div v-if="fac.research_interests" class="text-[10px] text-slate-500 bg-slate-50 p-2 rounded-xl border border-slate-100 line-clamp-1">
              🔬 {{ getTranslated(fac.research_interests, localeStore.locale) }}
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span v-if="fac.is_featured" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gold-100 text-gold-900 border border-gold-200">
                ★ Featured
              </span>
            </div>
            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="p-1.5 rounded-lg text-slate-500 hover:text-navy-900 hover:bg-slate-100 transition-colors cursor-pointer"
                @click="openEditFacultyModal(fac)"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button
                type="button"
                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                @click="handleDeleteFaculty(fac.id)"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: CREATE/EDIT FACULTY -->
    <Modal
      v-model="isFacultyModalOpen"
      :title="isEditingFaculty ? (localeStore.isRtl ? 'تعديل بيانات عضو هيئة التدريس' : 'Edit Faculty Profile') : (localeStore.isRtl ? 'إضافة عضو هيئة تدريس جديد' : 'Add Faculty Member')"
      size="lg"
      @close="isFacultyModalOpen = false"
    >
      <form @submit.prevent="submitFacultyForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <EnterpriseFormField
            v-model="facultyForm.name_ar"
            type="text"
            :label="localeStore.isRtl ? 'الاسم بالكامل (عربي)' : 'Full Name (Ar)'"
            required
            col-span="6"
            placeholder="أ.د. حسام عادل الشافعي"
          />
          <EnterpriseFormField
            v-model="facultyForm.name_en"
            type="text"
            label="Full Name (En)"
            required
            col-span="6"
            placeholder="Prof. Dr. Hossam Adel El-Shafei"
          />
          <EnterpriseFormField
            v-model="facultyForm.academic_title_ar"
            type="text"
            :label="localeStore.isRtl ? 'الدرجة الأكاديمية (عربي)' : 'Academic Rank (Ar)'"
            required
            col-span="6"
            placeholder="أستاذ ورئيس قسم الذكاء الاصطناعي"
          />
          <EnterpriseFormField
            v-model="facultyForm.academic_title_en"
            type="text"
            label="Academic Rank (En)"
            required
            col-span="6"
            placeholder="Professor & Chair of AI Dept"
          />
          <EnterpriseFormField
            v-model="facultyForm.email"
            type="email"
            :label="localeStore.isRtl ? 'البريد الإلكتروني الجامعي' : 'University Email'"
            required
            col-span="6"
            placeholder="h.adel@university.edu.eg"
          />
          <EnterpriseFormField
            type="image"
            :label="localeStore.isRtl ? 'الصورة الشخصية لعضو هيئة التدريس' : 'Faculty Profile Photo'"
            col-span="6"
            :preview-url="facultyAvatarPreview || facultyForm.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'"
            :button-text="localeStore.isRtl ? 'رفع صورة من جهازك' : 'Upload from Device'"
            @file-selected="handleFacultyAvatarSelect"
          />
          <EnterpriseFormField
            v-model="facultyForm.research_interests_ar"
            type="text"
            :label="localeStore.isRtl ? 'الاهتمامات البحثية' : 'Research Interests'"
            col-span="12"
            placeholder="التعلم العميق، الرؤية الحاسوبية، الروبوتات الطبية..."
          />
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isFacultyModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitFacultyForm">{{ localeStore.isRtl ? 'حفظ البيانات' : 'Save Faculty' }}</button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import { api, getTranslated } from '../../services/api'
import Modal from '../../components/ui/Modal.vue'
import EmptyState from '../../components/ui/EmptyState.vue'
import EnterpriseFormField from '../../components/ui/EnterpriseFormField.vue'
import {
  School,
  Building2,
  Layers,
  GraduationCap,
  Users,
  Plus,
  Search,
  Trash2,
  Edit3,
  UserCheck,
  Upload,
  X,
} from 'lucide-vue-next'

const localeStore = useLocaleStore()
const activeTab = ref('colleges')

const collegesList = ref([])
const programsList = ref([])
const facultyList = ref([])
const searchProgram = ref('')
const searchFaculty = ref('')
const filterDegree = ref('all')

// Utility to compress images in client memory before upload
const compressImage = (file, maxWidth = 400, quality = 0.75) => {
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

const collegeSelectedFile = ref(null)
const collegeBannerPreview = ref('')

const handleCollegeBannerSelect = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  collegeSelectedFile.value = file
  const compressed = await compressImage(file, 800, 0.7)
  collegeBannerPreview.value = compressed
  collegeForm.banner_image = compressed
}

const facultySelectedFile = ref(null)
const facultyAvatarPreview = ref('')

const handleFacultyAvatarSelect = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  facultySelectedFile.value = file
  const compressed = await compressImage(file, 300, 0.7)
  facultyAvatarPreview.value = compressed
  facultyForm.avatar = compressed
}

const isFacultyModalOpen = ref(false)
const isEditingFaculty = ref(false)
const editingFacultyId = ref(null)
const facultyForm = reactive({
  department_id: 1,
  name_ar: '',
  name_en: '',
  academic_title_ar: '',
  academic_title_en: '',
  email: '',
  avatar: '',
  research_interests_ar: '',
  research_interests_en: '',
  bio_ar: '',
  bio_en: '',
  is_featured: false,
})

const filteredFaculty = computed(() => {
  let list = [...facultyList.value]
  if (searchFaculty.value.trim()) {
    const q = searchFaculty.value.trim().toLowerCase()
    list = list.filter((f) =>
      f.name?.toLowerCase().includes(q) ||
      f.email?.toLowerCase().includes(q) ||
      (f.academic_title?.ar && f.academic_title.ar.toLowerCase().includes(q)) ||
      (f.academic_title?.en && f.academic_title.en.toLowerCase().includes(q))
    )
  }
  return list
})

const openNewFacultyModal = () => {
  isEditingFaculty.value = false
  editingFacultyId.value = null
  facultySelectedFile.value = null
  facultyAvatarPreview.value = ''
  facultyForm.department_id = sampleDepartments.value[0]?.id || 1
  facultyForm.name_ar = ''
  facultyForm.name_en = ''
  facultyForm.academic_title_ar = ''
  facultyForm.academic_title_en = ''
  facultyForm.email = ''
  facultyForm.avatar = ''
  facultyForm.research_interests_ar = ''
  isFacultyModalOpen.value = true
}

const openEditFacultyModal = (fac) => {
  isEditingFaculty.value = true
  editingFacultyId.value = fac.id
  facultySelectedFile.value = null
  facultyAvatarPreview.value = ''
  facultyForm.name_ar = fac.name || ''
  facultyForm.name_en = fac.name || ''
  facultyForm.academic_title_ar = fac.academic_title?.ar || ''
  facultyForm.academic_title_en = fac.academic_title?.en || ''
  facultyForm.email = fac.email || ''
  facultyForm.avatar = fac.avatar || ''
  facultyForm.research_interests_ar = fac.research_interests?.ar || ''
  isFacultyModalOpen.value = true
}

const submitFacultyForm = async () => {
  if (isEditingFaculty.value) {
    const updated = await api.updateFaculty(editingFacultyId.value, { ...facultyForm })
    const idx = facultyList.value.findIndex((f) => f.id === editingFacultyId.value)
    if (idx !== -1) {
      facultyList.value[idx] = {
        ...facultyList.value[idx],
        name: facultyForm.name_en || facultyForm.name_ar || facultyList.value[idx].name,
        academic_title: { ar: facultyForm.academic_title_ar, en: facultyForm.academic_title_en },
        email: facultyForm.email,
        avatar: facultyForm.avatar || facultyList.value[idx].avatar,
        research_interests: { ar: facultyForm.research_interests_ar, en: facultyForm.research_interests_en },
        ...updated,
      }
    }
  } else {
    const created = await api.createFaculty({ ...facultyForm })
    facultyList.value.unshift(created)
  }
  isFacultyModalOpen.value = false
}

const handleDeleteFaculty = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'حذف عضو هيئة التدريس؟' : 'Delete faculty profile?')) {
    await api.deleteFaculty(id)
    facultyList.value = facultyList.value.filter((f) => f.id !== id)
  }
}

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
const isEditingProgram = ref(false)
const editingProgramId = ref(null)
const programForm = reactive({
  department_id: 1,
  name_ar: '',
  name_en: '',
  degree_level: 'bachelor',
  duration_years: 4,
  credit_hours: 136,
  tuition_fees_ar: '55,000 جنيه مصري / العام الدراسي',
  tuition_fees_en: '55,000 EGP / Academic Year',
  admission_requirements_ar: '',
  admission_requirements_en: '',
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
    const facs = await api.getFaculty()
    facultyList.value = facs || []
  } catch (err) {
    console.error(err)
  }
}

const openNewCollegeModal = () => {
  isEditingCollege.value = false
  editingCollegeId.value = null
  collegeSelectedFile.value = null
  collegeBannerPreview.value = ''
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
  collegeSelectedFile.value = null
  collegeBannerPreview.value = ''
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

const isDepartmentModalOpen = ref(false)
const isEditingDepartment = ref(false)
const editingDepartmentId = ref(null)
const departmentForm = reactive({
  college_id: 1,
  name_ar: '',
  name_en: '',
  head_name_ar: '',
  head_name_en: '',
  description_ar: '',
  description_en: '',
})

const openNewDepartmentModal = () => {
  isEditingDepartment.value = false
  editingDepartmentId.value = null
  departmentForm.college_id = collegesList.value[0]?.id || 1
  departmentForm.name_ar = ''
  departmentForm.name_en = ''
  departmentForm.head_name_ar = ''
  departmentForm.head_name_en = ''
  departmentForm.description_ar = ''
  departmentForm.description_en = ''
  isDepartmentModalOpen.value = true
}

const openEditDepartmentModal = (dept) => {
  isEditingDepartment.value = true
  editingDepartmentId.value = dept.id
  departmentForm.college_id = dept.college_id || collegesList.value[0]?.id || 1
  departmentForm.name_ar = dept.name?.ar || ''
  departmentForm.name_en = dept.name?.en || ''
  departmentForm.head_name_ar = dept.head_name?.ar || ''
  departmentForm.head_name_en = dept.head_name?.en || ''
  departmentForm.description_ar = dept.description?.ar || ''
  departmentForm.description_en = dept.description?.en || ''
  isDepartmentModalOpen.value = true
}

const submitDepartmentForm = async () => {
  if (isEditingDepartment.value) {
    await api.updateDepartment(editingDepartmentId.value, { ...departmentForm })
    const idx = sampleDepartments.value.findIndex((d) => d.id === editingDepartmentId.value)
    if (idx !== -1) {
      sampleDepartments.value[idx].college_id = departmentForm.college_id
      sampleDepartments.value[idx].name = { ar: departmentForm.name_ar, en: departmentForm.name_en }
      sampleDepartments.value[idx].head_name = { ar: departmentForm.head_name_ar, en: departmentForm.head_name_en }
      sampleDepartments.value[idx].description = { ar: departmentForm.description_ar, en: departmentForm.description_en }
    }
  } else {
    const created = await api.createDepartment({ ...departmentForm })
    sampleDepartments.value.unshift(created)
  }
  isDepartmentModalOpen.value = false
}

const handleDeleteDepartment = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'حذف القسم العلمي؟' : 'Delete department?')) {
    await api.deleteDepartment(id)
    sampleDepartments.value = sampleDepartments.value.filter((d) => d.id !== id)
  }
}

const openNewProgramModal = () => {
  isEditingProgram.value = false
  editingProgramId.value = null
  programForm.department_id = sampleDepartments.value[0]?.id || 1
  programForm.name_ar = ''
  programForm.name_en = ''
  programForm.degree_level = 'bachelor'
  programForm.duration_years = 4
  programForm.credit_hours = 136
  programForm.tuition_fees_ar = '55,000 جنيه مصري / العام الدراسي'
  programForm.tuition_fees_en = '55,000 EGP / Academic Year'
  programForm.admission_requirements_ar = ''
  programForm.admission_requirements_en = ''
  isProgramModalOpen.value = true
}

const openEditProgramModal = (prog) => {
  isEditingProgram.value = true
  editingProgramId.value = prog.id
  programForm.department_id = prog.department_id || sampleDepartments.value[0]?.id || 1
  programForm.name_ar = prog.name?.ar || ''
  programForm.name_en = prog.name?.en || ''
  programForm.degree_level = prog.degree_level || 'bachelor'
  programForm.duration_years = prog.duration_years || 4
  programForm.credit_hours = prog.credit_hours || 136
  programForm.tuition_fees_ar = prog.tuition_fees?.ar || '55,000 جنيه مصري / العام الدراسي'
  programForm.tuition_fees_en = prog.tuition_fees?.en || '55,000 EGP / Academic Year'
  programForm.admission_requirements_ar = Array.isArray(prog.admission_requirements?.ar)
    ? prog.admission_requirements.ar.join(', ')
    : (prog.admission_requirements?.ar || '')
  programForm.admission_requirements_en = Array.isArray(prog.admission_requirements?.en)
    ? prog.admission_requirements.en.join(', ')
    : (prog.admission_requirements?.en || '')
  isProgramModalOpen.value = true
}

const submitProgramForm = async () => {
  if (isEditingProgram.value) {
    const updated = await api.updateProgram(editingProgramId.value, { ...programForm })
    const idx = programsList.value.findIndex((p) => p.id === editingProgramId.value)
    if (idx !== -1) {
      programsList.value[idx] = {
        ...programsList.value[idx],
        name: { ar: programForm.name_ar, en: programForm.name_en },
        degree_level: programForm.degree_level,
        duration_years: programForm.duration_years,
        credit_hours: programForm.credit_hours,
        tuition_fees: { ar: programForm.tuition_fees_ar, en: programForm.tuition_fees_en },
        ...updated,
      }
    }
  } else {
    const created = await api.createProgram({ ...programForm })
    programsList.value.unshift(created)
  }
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
