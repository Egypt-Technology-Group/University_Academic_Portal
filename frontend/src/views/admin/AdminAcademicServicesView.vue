<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight flex items-center gap-2.5">
          <GraduationCap class="w-8 h-8 text-gold-500" />
          <span>{{ localeStore.isRtl ? 'الخدمات الأكاديمية والطلابية' : 'Academic & Student Services' }}</span>
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
          {{ localeStore.isRtl ? 'إدارة الطلبات الإلكترونية، استخراج الإفادات المعتمدة برمز التحقق، جداول الامتحانات والمراقبة، والخطط الدراسية' : 'Manage electronic student requests, verifiable official certificates, exam timetables, and degree study plans' }}
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex flex-wrap items-center gap-2.5">
        <button
          v-if="activeTab === 'requests'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewRequestModal"
        >
          <FileText class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'تسجيل طلب طالب جديد' : 'New Service Request' }}</span>
        </button>

        <button
          v-if="activeTab === 'statements'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openIssueStatementModal"
        >
          <ShieldCheck class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إصدار إفادة / شهادة معتمدة' : 'Issue Official Statement' }}</span>
        </button>

        <button
          v-if="activeTab === 'exams'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewExamModal"
        >
          <CalendarDays class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة موعد امتحان وقاعة' : 'Schedule Exam & Hall' }}</span>
        </button>

        <button
          v-if="activeTab === 'study_plans'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
          @click="openNewCourseModal"
        >
          <Plus class="w-4 h-4 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'إضافة مقرر دراسي للخطة' : 'Add Course to Plan' }}</span>
        </button>
      </div>
    </div>

    <!-- Academic Services Tabs -->
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200/80 shadow-xs flex flex-wrap gap-1.5">
      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'requests' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'requests'"
      >
        <FileText class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'الطلبات الإلكترونية والخدمات' : 'Student Service Requests' }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'requests' ? 'bg-gold-500 text-navy-950 font-black' : 'bg-slate-200 text-slate-700'">
          {{ requestsList.length }}
        </span>
      </button>

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'statements' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'statements'"
      >
        <ShieldCheck class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'الإفادات والشهادات المعتمدة (Verifiable)' : 'Verifiable Statements' }}</span>
      </button>

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'exams' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'exams'"
      >
        <CalendarDays class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'جداول الامتحانات والمراقبة' : 'Exam Timetables & Proctors' }}</span>
      </button>

      <button
        type="button"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer',
          activeTab === 'study_plans' ? 'bg-navy-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
        ]"
        @click="activeTab = 'study_plans'"
      >
        <BookOpenCheck class="w-4 h-4" />
        <span>{{ localeStore.isRtl ? 'الخطط الدراسية والمقررات' : 'Curriculum Study Plans' }}</span>
      </button>
    </div>

    <!-- TAB 1: STUDENT SERVICE REQUESTS -->
    <div v-if="activeTab === 'requests'" class="space-y-4">
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute inset-y-0 start-3.5 my-auto" />
          <input
            v-model="searchReq"
            type="text"
            :placeholder="localeStore.isRtl ? 'البحث برقم الطلب، كود الطالب، أو الاسم...' : 'Search by request #, student ID, name...'"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 ps-10 pe-4 py-2 text-xs sm:text-sm focus:bg-white focus:border-navy-900"
          />
        </div>

        <div class="w-full md:w-52 shrink-0">
          <select
            v-model="filterReqStatus"
            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-700 focus:bg-white focus:border-navy-900"
          >
            <option value="all">{{ localeStore.isRtl ? 'كل الحالات' : 'All Statuses' }}</option>
            <option value="pending">{{ localeStore.isRtl ? 'قيد الانتظار (Pending)' : 'Pending' }}</option>
            <option value="processing">{{ localeStore.isRtl ? 'قيد المراجعة والإعداد' : 'Processing' }}</option>
            <option value="approved">{{ localeStore.isRtl ? 'معتمد وجاهز للاستلام' : 'Approved & Ready' }}</option>
            <option value="rejected">{{ localeStore.isRtl ? 'مرفوض' : 'Rejected' }}</option>
          </select>
        </div>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer shrink-0"
          @click="exportRequestsCsv"
        >
          <Download class="w-3.5 h-3.5 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'تصدير CSV' : 'Export CSV' }}</span>
        </button>
      </div>

      <!-- Requests Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-start text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'رقم الطلب والنوع' : 'Request & Type' }}</th>
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'بيانات الطالب' : 'Student Details' }}</th>
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'الغرض والملاحظات الإدارية' : 'Purpose & Admin Notes' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الرسوم' : 'Fee Status' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'حالة الطلب' : 'Status' }}</th>
                <th class="py-3.5 px-4 text-end">{{ localeStore.isRtl ? 'الإجراءات' : 'Actions' }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="req in filteredRequests" :key="req.id" class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3.5 px-4">
                  <div class="font-mono font-bold text-navy-950 text-xs">{{ req.request_number }}</div>
                  <div class="inline-block text-[10px] font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded mt-0.5 border border-blue-100">
                    {{ getServiceLabel(req.service_type) }}
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <div class="font-bold text-navy-950">{{ req.student_name }}</div>
                  <div class="text-[11px] font-mono text-slate-400 mt-0.5">ID: {{ req.student_id_number }}</div>
                </td>

                <td class="py-3.5 px-4 max-w-xs">
                  <div class="text-xs text-slate-700 line-clamp-1">{{ getTranslated(req.purpose, localeStore.locale) }}</div>
                  <div v-if="req.admin_notes" class="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded mt-1 border border-amber-100">
                    📝 {{ req.admin_notes }}
                  </div>
                </td>

                <td class="py-3.5 px-4 text-center font-mono font-bold">
                  <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px] border border-emerald-200">
                    {{ req.fee_amount || 50 }} EGP ({{ localeStore.isRtl ? 'مسدد' : 'Paid' }})
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center">
                  <span :class="['text-[10px] font-bold px-2.5 py-1 rounded-full uppercase', getStatusClass(req.status)]">
                    {{ req.status }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      class="px-3 py-1.5 rounded-lg bg-navy-900 hover:bg-navy-950 text-white font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1"
                      @click="openReviewRequestModal(req)"
                    >
                      <CheckCircle class="w-3.5 h-3.5 text-gold-400" />
                      <span>{{ localeStore.isRtl ? 'معالجة' : 'Process' }}</span>
                    </button>
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                      title="Delete Request"
                      @click="handleDeleteRequest(req.id)"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: VERIFIABLE STATEMENTS & CERTIFICATES -->
    <div v-if="activeTab === 'statements'" class="space-y-4">
      <div class="bg-gradient-to-r from-navy-950 to-navy-900 rounded-3xl p-6 text-white border border-navy-800 shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-black">
            <QrCode class="w-4 h-4" />
            <span>{{ localeStore.isRtl ? 'نظام التحقق الرقمي المشفر بالباركود (Digital Seal QR)' : 'Cryptographic QR Verification System' }}</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-black">
            {{ localeStore.isRtl ? 'استخراج شهادات وإفادات قيد رسمية موثقة' : 'Issue Verifiable Official Academic Certificates' }}
          </h2>
          <p class="text-xs text-slate-300 max-w-xl">
            {{ localeStore.isRtl ? 'يتم تزويد كل إفادة بكود أمني فريد (SHA-256 Hash) ورمز QR فوري يسمح للجهات الحكومية والسفارات بالتحقق من صحة المستند.' : 'Each issued statement embeds a SHA-256 verification hash and live QR code for instant government and embassy validation.' }}
          </p>
        </div>

        <button
          type="button"
          class="px-5 py-3 rounded-xl bg-gold-500 hover:bg-gold-400 text-navy-950 font-black text-xs shadow-lg transition-all cursor-pointer shrink-0 inline-flex items-center gap-2"
          @click="openIssueStatementModal"
        >
          <Award class="w-4 h-4" />
          <span>{{ localeStore.isRtl ? 'إصدار شهادة جديدة الآن' : 'Issue New Certificate' }}</span>
        </button>
      </div>

      <!-- Sample Issued Statements Gallery -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="st in sampleStatements" :key="st.certificate_code" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
          <div class="flex items-center justify-between">
            <span class="font-mono font-bold text-xs text-navy-950 bg-slate-100 px-2 py-1 rounded border border-slate-200">
              {{ st.certificate_code }}
            </span>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
              ✓ {{ localeStore.isRtl ? 'موثق وصالح' : 'Active & Verified' }}
            </span>
          </div>

          <div>
            <h3 class="font-bold text-navy-950 text-sm">{{ getTranslated(st.title, localeStore.locale) }}</h3>
            <div class="text-xs text-slate-500 mt-1">
              <span>{{ localeStore.isRtl ? 'الطالب:' : 'Student:' }} <strong>{{ st.student_name }}</strong> (ID: {{ st.student_id_number }})</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">
              {{ localeStore.isRtl ? 'الجهة الموجه إليها:' : 'Addressed to:' }} {{ getTranslated(st.recipient_entity, localeStore.locale) }}
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100">
            <div class="text-[10px] text-slate-400 font-mono">
              Issuer: {{ st.signatory_name }}
            </div>
            <button
              type="button"
              class="px-3 py-1 rounded-lg bg-navy-50 text-navy-900 hover:bg-navy-100 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5"
              @click="printStatement(st)"
            >
              <Printer class="w-3.5 h-3.5 text-gold-600" />
              <span>{{ localeStore.isRtl ? 'طباعة الإفادة الرسمية' : 'Print Certificate' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: EXAM SCHEDULES & HALL INVIGILATION -->
    <div v-if="activeTab === 'exams'" class="space-y-4">
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-start text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'كود واسم المقرر' : 'Course Code & Title' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'نوع الامتحان' : 'Exam Type' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'التاريخ والتوقيت' : 'Date & Time' }}</th>
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'القاعة والمدرج' : 'Hall / Location' }}</th>
                <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'رئيس اللجنة والمراقبون' : 'Proctors & Invigilators' }}</th>
                <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'السعة' : 'Capacity' }}</th>
                <th class="py-3.5 px-4 text-end">{{ localeStore.isRtl ? 'الإجراءات' : 'Actions' }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="exam in examSchedulesList" :key="exam.id" class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3.5 px-4">
                  <div class="font-mono font-bold text-navy-950 text-sm">{{ exam.course_code }}</div>
                  <div class="text-xs text-slate-600 font-bold mt-0.5">{{ getTranslated(exam.course_name, localeStore.locale) }}</div>
                </td>

                <td class="py-3.5 px-4 text-center">
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-800 border border-purple-200 uppercase">
                    {{ exam.exam_type }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center font-mono">
                  <div class="font-bold text-navy-950">{{ exam.exam_date }}</div>
                  <div class="text-[10px] text-slate-400">{{ exam.start_time }} - {{ exam.end_time }}</div>
                </td>

                <td class="py-3.5 px-4">
                  <div class="font-bold text-navy-950">{{ getTranslated(exam.hall_location, localeStore.locale) }}</div>
                </td>

                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-800 text-xs">{{ getTranslated(exam.chief_invigilator, localeStore.locale) }}</div>
                  <div class="text-[10px] text-slate-400 mt-0.5">
                    {{ Array.isArray(exam.proctors_list) ? exam.proctors_list.join(', ') : 'TAs Assigned' }}
                  </div>
                </td>

                <td class="py-3.5 px-4 text-center font-mono font-bold text-navy-950">
                  {{ exam.seating_capacity || 80 }} Seats
                </td>

                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-navy-900 hover:bg-slate-100 transition-colors"
                      title="Edit Exam"
                      @click="openEditExamModal(exam)"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                      title="Delete Exam"
                      @click="handleDeleteExam(exam.id)"
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

    <!-- TAB 4: STUDY PLANS & CURRICULUM -->
    <div v-if="activeTab === 'study_plans'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="lvl in [1, 2, 3, 4]" :key="lvl" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs space-y-3">
          <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 class="font-black text-navy-950 text-sm">{{ localeStore.isRtl ? 'المستوى الدراسي ' + lvl : 'Academic Level ' + lvl }}</h4>
            <span class="text-[10px] font-mono bg-gold-100 text-gold-900 px-1.5 py-0.5 rounded font-bold">
              {{ getLevelCourses(lvl).reduce((sum, c) => sum + (c.credits || 3), 0) }} Credits
            </span>
          </div>
          <ul class="space-y-2 text-xs text-slate-600">
            <li
              v-for="course in getLevelCourses(lvl)"
              :key="course.id"
              class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors"
            >
              <div class="flex-1 min-w-0 me-2">
                <span class="font-mono font-bold text-navy-900 text-xs">{{ course.code }}</span>
                <div class="text-[11px] text-slate-600 font-medium truncate">{{ getTranslated(course.name, localeStore.locale) }}</div>
              </div>
              <div class="flex items-center gap-1 shrink-0">
                <span class="font-mono text-[10px] bg-white px-1.5 py-0.5 rounded border border-slate-200 font-bold me-1">{{ course.credits || 3 }} Cr</span>
                <button
                  type="button"
                  class="p-1 rounded text-slate-400 hover:text-navy-900 hover:bg-white"
                  title="Edit Course"
                  @click="openEditCourseModal(course)"
                >
                  <Edit3 class="w-3.5 h-3.5" />
                </button>
                <button
                  type="button"
                  class="p-1 rounded text-slate-400 hover:text-red-600 hover:bg-white"
                  title="Delete Course"
                  @click="handleDeleteCourse(course.id)"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- MODAL: REVIEW REQUEST -->
    <Modal v-model="isReviewModalOpen" :title="localeStore.isRtl ? 'معالجة وتحديث حالة طلب الطالب' : 'Process Student Service Request'" size="md" @close="isReviewModalOpen = false">
      <div v-if="activeRequest" class="space-y-4 text-start text-xs">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
          <div class="flex items-center justify-between">
            <span class="font-mono font-bold text-navy-950">{{ activeRequest.request_number }}</span>
            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ getServiceLabel(activeRequest.service_type) }}</span>
          </div>
          <div class="font-bold text-navy-950 text-sm">{{ activeRequest.student_name }} (ID: {{ activeRequest.student_id_number }})</div>
          <div class="text-slate-600 text-xs">{{ getTranslated(activeRequest.purpose, localeStore.locale) }}</div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'تحديث حالة الطلب' : 'Update Status' }}</label>
          <select v-model="reviewForm.status" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm font-bold">
            <option value="pending">Pending (قيد الانتظار)</option>
            <option value="processing">Processing (قيد التنفيذ والمراجعة)</option>
            <option value="approved">Approved & Ready for Pickup (معتمد وجاهز للاستلام)</option>
            <option value="rejected">Rejected (مرفوض)</option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الملاحظات الإدارية وتوجيهات الاستلام' : 'Administrative Notes' }}</label>
          <textarea v-model="reviewForm.admin_notes" rows="3" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm" placeholder="تم اعتماد الطلب، يرجى التوجه لشؤون الطلاب بالمبنى الرئيسي..."></textarea>
        </div>
      </div>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isReviewModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="saveRequestReview">{{ localeStore.isRtl ? 'حفظ القرار' : 'Save Decision' }}</button>
      </template>
    </Modal>

    <!-- MODAL: ISSUE OFFICIAL STATEMENT -->
    <Modal v-model="isStatementModalOpen" :title="localeStore.isRtl ? 'إصدار إفادة قيد رسمية موثقة' : 'Issue Verifiable Statement'" size="lg" @close="isStatementModalOpen = false">
      <form @submit.prevent="submitStatementForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'كود الطالب الجامعي' : 'Student ID Number' }} *</label>
            <input v-model="statementForm.student_id_number" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" placeholder="20241001" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم الطالب الرباعي' : 'Student Full Name' }} *</label>
            <input v-model="statementForm.student_name" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="يوسف أحمد حسن" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الرقم القومي / جواز السفر' : 'National ID / Passport' }} *</label>
            <input v-model="statementForm.national_id" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" placeholder="30405150102233" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'نوع الإفادة' : 'Statement Type' }}</label>
            <select v-model="statementForm.statement_type" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs">
              <option value="official_enrollment">إفادة قيد بكالوريوس رسمية (Enrollment Certificate)</option>
              <option value="completion_statement">شهادة تخرج مؤقتة (Graduation Certificate)</option>
              <option value="english_proficiency">شهادة دراسة باللغة الإنجليزية (Medium of Instruction)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الجهة الموجه إليها (عربي)' : 'Addressed Entity (Ar)' }}</label>
            <input v-model="statementForm.recipient_entity_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="إلى من يهمه الأمر / نقابة المهندسين" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Addressed Entity (En)</label>
            <input v-model="statementForm.recipient_entity_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="To Whom It May Concern / Embassy" />
          </div>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isStatementModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitStatementForm">{{ localeStore.isRtl ? 'إصدار الوثيقة وتوليد QR' : 'Issue & Generate QR' }}</button>
      </template>
    </Modal>

    <!-- MODAL: SCHEDULE / EDIT EXAM -->
    <Modal
      v-model="isExamModalOpen"
      :title="isEditingExam ? (localeStore.isRtl ? 'تعديل موعد الامتحان ولجنة المراقبة' : 'Edit Exam Schedule & Invigilation') : (localeStore.isRtl ? 'جدولة امتحان جديد وتعيين القاعات' : 'Schedule Exam & Assign Halls')"
      size="lg"
      @close="isExamModalOpen = false"
    >
      <form @submit.prevent="submitExamForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'كود المقرر' : 'Course Code' }} *</label>
            <input v-model="examForm.course_code" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono font-bold" placeholder="CS301" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم المقرر (عربي)' : 'Course Name (Ar)' }} *</label>
            <input v-model="examForm.course_name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="الذكاء الاصطناعي وتعلم الآلة" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Course Name (En) *</label>
            <input v-model="examForm.course_name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="Artificial Intelligence & ML" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'نوع الامتحان' : 'Exam Type' }} *</label>
            <select v-model="examForm.exam_type" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs bg-white">
              <option value="midterm">Midterm (نصفي)</option>
              <option value="final">Final (نهائي)</option>
              <option value="practical">Practical (عملي)</option>
              <option value="oral">Oral (شفوي)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'تاريخ الامتحان' : 'Exam Date' }} *</label>
            <input v-model="examForm.exam_date" type="date" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'وقت البدء' : 'Start Time' }} *</label>
            <input v-model="examForm.start_time" type="time" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'وقت الانتهاء' : 'End Time' }} *</label>
            <input v-model="examForm.end_time" type="time" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'المدرج / القاعة (عربي)' : 'Hall Location (Ar)' }} *</label>
            <input v-model="examForm.hall_location_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="مدرج الدكتور مجدي يعقوب (مبنى أ)" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Hall Location (En) *</label>
            <input v-model="examForm.hall_location_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="Magdi Yacoub Auditorium (Hall A)" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'رئيس اللجنة (عربي)' : 'Chief Proctor (Ar)' }}</label>
            <input v-model="examForm.chief_invigilator_ar" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="أ.د. عصام النجار" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Chief Proctor (En)</label>
            <input v-model="examForm.chief_invigilator_en" type="text" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="Prof. Dr. Essam El-Naggar" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'سعة القاعة' : 'Seating Capacity' }}</label>
            <input v-model.number="examForm.seating_capacity" type="number" min="10" max="1000" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" />
          </div>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isExamModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitExamForm">{{ localeStore.isRtl ? 'حفظ الامتحان' : 'Save Exam Schedule' }}</button>
      </template>
    </Modal>

    <!-- MODAL: ADD STUDENT REQUEST -->
    <Modal
      v-model="isNewRequestModalOpen"
      :title="localeStore.isRtl ? 'تسجيل طلب طالب جديد' : 'New Student Service Request'"
      size="md"
      @close="isNewRequestModalOpen = false"
    >
      <form @submit.prevent="submitNewRequestForm" class="space-y-4 text-start text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'كود الطالب الجامعي' : 'Student ID Number' }} *</label>
          <input v-model="newRequestForm.student_id_number" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono" placeholder="20241001" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم الطالب الرباعي' : 'Student Full Name' }} *</label>
          <input v-model="newRequestForm.student_name" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="محمود سامي علي" />
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'نوع الخدمة المطلوبة' : 'Service Type' }} *</label>
          <select v-model="newRequestForm.service_type" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs bg-white font-bold">
            <option value="enrollment_cert">{{ localeStore.isRtl ? 'شهادة قيد رسمية' : 'Enrollment Certificate' }}</option>
            <option value="transcript">{{ localeStore.isRtl ? 'كشف درجات معتمد' : 'Official Transcript' }}</option>
            <option value="course_exemption">{{ localeStore.isRtl ? 'مقاصة ومعادلة مقررات' : 'Course Exemption' }}</option>
            <option value="postponement">{{ localeStore.isRtl ? 'تأجيل فصل دراسي' : 'Term Postponement' }}</option>
            <option value="id_card_replacement">{{ localeStore.isRtl ? 'بدل فاقد كارنيه' : 'ID Card Replacement' }}</option>
          </select>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'الغرض من الطلب' : 'Purpose' }}</label>
          <textarea v-model="newRequestForm.purpose_ar" rows="2" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs" placeholder="استخراج شهادة قيد موجهة إلى..."></textarea>
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isNewRequestModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitNewRequestForm">{{ localeStore.isRtl ? 'تقديم الطلب' : 'Submit Request' }}</button>
      </template>
    </Modal>

    <!-- MODAL: ADD / EDIT COURSE -->
    <Modal
      v-model="isCourseModalOpen"
      :title="isEditingCourse ? (localeStore.isRtl ? 'تعديل بيانات المقرر بالخطة الدراسية' : 'Edit Study Plan Course') : (localeStore.isRtl ? 'إضافة مقرر دراسي جديد للخطة' : 'Add Course to Curriculum')"
      size="md"
      @close="isCourseModalOpen = false"
    >
      <form @submit.prevent="submitCourseForm" class="space-y-4 text-start text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'كود المقرر' : 'Course Code' }} *</label>
            <input v-model="courseForm.code" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono font-bold" placeholder="CS201" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'المستوى الدراسي' : 'Academic Level' }} *</label>
            <select v-model.number="courseForm.level" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs bg-white font-bold">
              <option :value="1">Level 1 (المستوى الأول)</option>
              <option :value="2">Level 2 (المستوى الثاني)</option>
              <option :value="3">Level 3 (المستوى الثالث)</option>
              <option :value="4">Level 4 (المستوى الرابع)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'اسم المقرر (عربي)' : 'Course Name (Ar)' }} *</label>
            <input v-model="courseForm.name_ar" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="هياكل البيانات والخوارزميات" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Course Name (En) *</label>
            <input v-model="courseForm.name_en" type="text" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-bold" placeholder="Data Structures & Algorithms" />
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">{{ localeStore.isRtl ? 'عدد الساعات المعتمدة' : 'Credit Hours' }} *</label>
          <input v-model.number="courseForm.credits" type="number" min="1" max="6" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-mono font-bold" />
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isCourseModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitCourseForm">{{ localeStore.isRtl ? 'حفظ المقرر' : 'Save Course' }}</button>
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
  GraduationCap,
  FileText,
  ShieldCheck,
  CalendarDays,
  BookOpenCheck,
  Search,
  CheckCircle,
  QrCode,
  Award,
  Printer,
  Download,
  Plus,
  Edit3,
  Trash2
} from 'lucide-vue-next'

const localeStore = useLocaleStore()
const activeTab = ref('requests')

const requestsList = ref([])
const examSchedulesList = ref([])
const searchReq = ref('')
const filterReqStatus = ref('all')

const isReviewModalOpen = ref(false)
const activeRequest = ref(null)
const reviewForm = reactive({
  status: 'approved',
  admin_notes: ''
})

const isExamModalOpen = ref(false)
const isEditingExam = ref(false)
const editingExamId = ref(null)
const examForm = reactive({
  course_code: '',
  course_name_ar: '',
  course_name_en: '',
  exam_type: 'final',
  exam_date: new Date().toISOString().slice(0, 10),
  start_time: '09:00',
  end_time: '12:00',
  hall_location_ar: 'مدرج الدكتور مجدي يعقوب (مبنى أ)',
  hall_location_en: 'Magdi Yacoub Auditorium (Hall A)',
  chief_invigilator_ar: 'أ.د. عصام النجار',
  chief_invigilator_en: 'Prof. Dr. Essam El-Naggar',
  seating_capacity: 120
})

const isNewRequestModalOpen = ref(false)
const newRequestForm = reactive({
  student_id_number: '',
  student_name: '',
  service_type: 'enrollment_cert',
  purpose_ar: ''
})

const isCourseModalOpen = ref(false)
const isEditingCourse = ref(false)
const editingCourseId = ref(null)
const courseForm = reactive({
  code: '',
  name_ar: '',
  name_en: '',
  credits: 3,
  level: 1
})

const studyPlansCourses = ref([
  { id: 101, level: 1, code: 'CS101', name: { ar: 'مقدمة في علوم الحاسب والبرمجة', en: 'Intro to Computer Science & Programming' }, credits: 3 },
  { id: 102, level: 1, code: 'MATH101', name: { ar: 'التفاضل والتكامل والهندسة التحليلية', en: 'Calculus & Analytical Geometry' }, credits: 3 },
  { id: 103, level: 1, code: 'PHYS101', name: { ar: 'الفيزياء العامة وتطبيقاتها الهندسية', en: 'General Physics & Engineering Applications' }, credits: 3 },
  { id: 201, level: 2, code: 'CS201', name: { ar: 'هياكل البيانات والتحليل الخوارزمي', en: 'Data Structures & Algorithmic Analysis' }, credits: 3 },
  { id: 202, level: 2, code: 'MATH202', name: { ar: 'الرياضيات المتقطعة ونظرية المخططات', en: 'Discrete Mathematics & Graph Theory' }, credits: 3 },
  { id: 301, level: 3, code: 'AI301', name: { ar: 'أسس الذكاء الاصطناعي والتعلم الآلي', en: 'Foundations of AI & Machine Learning' }, credits: 3 },
  { id: 302, level: 3, code: 'CS302', name: { ar: 'تصميم نظم قواعد البيانات الموزعة', en: 'Distributed Database Systems Design' }, credits: 3 },
  { id: 401, level: 4, code: 'AI401', name: { ar: 'مشروع التخرج المتقدم (الجزء الأول)', en: 'Senior Capstone Project I' }, credits: 4 },
  { id: 402, level: 4, code: 'SEC402', name: { ar: 'أمن الفضاء السيبراني واختبار الاختراق', en: 'Cybersecurity & Penetration Testing' }, credits: 3 }
])

const getLevelCourses = (lvl) => {
  return studyPlansCourses.value.filter((c) => c.level === lvl)
}

const isStatementModalOpen = ref(false)
const statementForm = reactive({
  student_id_number: '20241001',
  student_name: 'Youssef Ahmed Hassan',
  national_id: '30405150102233',
  statement_type: 'official_enrollment',
  title_ar: 'إفادة قيد رسمية معتمدة لدرجة البكالوريوس',
  title_en: 'Official Certificate of Enrollment',
  recipient_entity_ar: 'إلى من يهمه الأمر',
  recipient_entity_en: 'To Whom It May Concern'
})

const sampleStatements = ref([
  {
    certificate_code: 'CERT-2025-EG892144',
    student_name: 'Youssef Ahmed Hassan',
    student_id_number: '20241001',
    title: { ar: 'إفادة قيد رسمية معتمدة لدرجة البكالوريوس', en: 'Official Certificate of Enrollment (B.Sc. AI)' },
    recipient_entity: { ar: 'إلى من يهمه الأمر / نقابة المهندسين', en: 'To Whom It May Concern / Syndicate' },
    signatory_name: 'Prof. Dr. Ahmed Mansour',
    issue_date: '2026-08-20'
  }
])

const filteredRequests = computed(() => {
  let list = [...requestsList.value]
  if (filterReqStatus.value !== 'all') {
    list = list.filter((r) => r.status === filterReqStatus.value)
  }
  if (searchReq.value.trim()) {
    const q = searchReq.value.trim().toLowerCase()
    list = list.filter((r) =>
      r.request_number?.toLowerCase().includes(q) ||
      r.student_name?.toLowerCase().includes(q) ||
      r.student_id_number?.includes(q)
    )
  }
  return list
})

const getServiceLabel = (type) => {
  const map = {
    enrollment_cert: localeStore.isRtl ? 'شهادة قيد رسمية' : 'Enrollment Cert',
    transcript: localeStore.isRtl ? 'كشف درجات معتمد' : 'Official Transcript',
    course_exemption: localeStore.isRtl ? 'مقاصة ومعادلة مقررات' : 'Course Exemption',
    postponement: localeStore.isRtl ? 'تأجيل فصل دراسي' : 'Term Postponement',
    id_card_replacement: localeStore.isRtl ? 'بدل فاقد كارنيه' : 'ID Card Replacement'
  }
  return map[type] || type
}

const getStatusClass = (status) => {
  if (status === 'approved') return 'bg-emerald-100 text-emerald-800'
  if (status === 'processing') return 'bg-blue-100 text-blue-800'
  if (status === 'rejected') return 'bg-red-100 text-red-800'
  return 'bg-amber-100 text-amber-800'
}

const loadData = async () => {
  try {
    const reqs = await api.getStudentRequests()
    requestsList.value = reqs || []
    const exams = await api.getExamSchedules()
    examSchedulesList.value = exams || []
  } catch (err) {
    console.error(err)
  }
}

const openReviewRequestModal = (req) => {
  activeRequest.value = req
  reviewForm.status = req.status || 'approved'
  reviewForm.admin_notes = req.admin_notes || ''
  isReviewModalOpen.value = true
}

const saveRequestReview = async () => {
  if (!activeRequest.value) return
  await api.updateStudentRequestStatus(activeRequest.value.id, {
    status: reviewForm.status,
    admin_notes: reviewForm.admin_notes,
    handled_by: 'Academic Affairs Admin'
  })
  activeRequest.value.status = reviewForm.status
  activeRequest.value.admin_notes = reviewForm.admin_notes
  isReviewModalOpen.value = false
}

const openNewRequestModal = () => {
  newRequestForm.student_id_number = ''
  newRequestForm.student_name = ''
  newRequestForm.service_type = 'enrollment_cert'
  newRequestForm.purpose_ar = ''
  isNewRequestModalOpen.value = true
}

const submitNewRequestForm = async () => {
  if (!newRequestForm.student_id_number || !newRequestForm.student_name) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }
  const created = await api.submitStudentRequest({ ...newRequestForm })
  requestsList.value.unshift(created)
  isNewRequestModalOpen.value = false
}

const handleDeleteRequest = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'هل تريد حذف هذا الطلب نهائياً؟' : 'Delete this student request?')) {
    await api.deleteStudentRequest(id)
    requestsList.value = requestsList.value.filter((r) => r.id !== id)
  }
}

const openNewExamModal = () => {
  isEditingExam.value = false
  editingExamId.value = null
  examForm.course_code = ''
  examForm.course_name_ar = ''
  examForm.course_name_en = ''
  examForm.exam_type = 'final'
  examForm.exam_date = new Date().toISOString().slice(0, 10)
  examForm.start_time = '09:00'
  examForm.end_time = '12:00'
  examForm.hall_location_ar = 'مدرج الدكتور مجدي يعقوب (مبنى أ)'
  examForm.hall_location_en = 'Magdi Yacoub Auditorium (Hall A)'
  examForm.chief_invigilator_ar = 'أ.د. عصام النجار'
  examForm.chief_invigilator_en = 'Prof. Dr. Essam El-Naggar'
  examForm.seating_capacity = 120
  isExamModalOpen.value = true
}

const openEditExamModal = (exam) => {
  isEditingExam.value = true
  editingExamId.value = exam.id
  examForm.course_code = exam.course_code || ''
  examForm.course_name_ar = exam.course_name?.ar || exam.course_name || ''
  examForm.course_name_en = exam.course_name?.en || exam.course_name || ''
  examForm.exam_type = exam.exam_type || 'final'
  examForm.exam_date = exam.exam_date || new Date().toISOString().slice(0, 10)
  examForm.start_time = exam.start_time ? exam.start_time.slice(0, 5) : '09:00'
  examForm.end_time = exam.end_time ? exam.end_time.slice(0, 5) : '12:00'
  examForm.hall_location_ar = exam.hall_location?.ar || exam.hall_location || 'مدرج الدكتور مجدي يعقوب (مبنى أ)'
  examForm.hall_location_en = exam.hall_location?.en || exam.hall_location || 'Magdi Yacoub Auditorium (Hall A)'
  examForm.chief_invigilator_ar = exam.chief_invigilator?.ar || exam.chief_invigilator || 'أ.د. عصام النجار'
  examForm.chief_invigilator_en = exam.chief_invigilator?.en || exam.chief_invigilator || 'Prof. Dr. Essam El-Naggar'
  examForm.seating_capacity = exam.seating_capacity || 120
  isExamModalOpen.value = true
}

const submitExamForm = async () => {
  if (!examForm.course_code || !examForm.course_name_ar || !examForm.exam_date) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }

  try {
    if (isEditingExam.value) {
      const updated = await api.updateExamSchedule(editingExamId.value, { ...examForm })
      const idx = examSchedulesList.value.findIndex((e) => e.id === editingExamId.value)
      if (idx !== -1) {
        examSchedulesList.value[idx] = {
          ...examSchedulesList.value[idx],
          course_code: examForm.course_code,
          course_name: { ar: examForm.course_name_ar, en: examForm.course_name_en },
          exam_type: examForm.exam_type,
          exam_date: examForm.exam_date,
          start_time: examForm.start_time,
          end_time: examForm.end_time,
          hall_location: { ar: examForm.hall_location_ar, en: examForm.hall_location_en },
          chief_invigilator: { ar: examForm.chief_invigilator_ar, en: examForm.chief_invigilator_en },
          seating_capacity: examForm.seating_capacity,
          ...updated,
        }
      }
    } else {
      const created = await api.storeExamSchedule({ ...examForm })
      examSchedulesList.value.unshift(created)
    }
    isExamModalOpen.value = false
  } catch (err) {
    alert('Failed to save exam schedule')
  }
}

const openNewCourseModal = () => {
  isEditingCourse.value = false
  editingCourseId.value = null
  courseForm.code = ''
  courseForm.name_ar = ''
  courseForm.name_en = ''
  courseForm.credits = 3
  courseForm.level = 1
  isCourseModalOpen.value = true
}

const openEditCourseModal = (course) => {
  isEditingCourse.value = true
  editingCourseId.value = course.id
  courseForm.code = course.code || ''
  courseForm.name_ar = course.name?.ar || course.name || ''
  courseForm.name_en = course.name?.en || course.name || ''
  courseForm.credits = course.credits || 3
  courseForm.level = course.level || 1
  isCourseModalOpen.value = true
}

const submitCourseForm = () => {
  if (!courseForm.code || !courseForm.name_ar) {
    alert('يرجى ملء الحقول الإلزامية')
    return
  }

  if (isEditingCourse.value) {
    const idx = studyPlansCourses.value.findIndex((c) => c.id === editingCourseId.value)
    if (idx !== -1) {
      studyPlansCourses.value[idx] = {
        ...studyPlansCourses.value[idx],
        code: courseForm.code,
        name: { ar: courseForm.name_ar, en: courseForm.name_en },
        credits: courseForm.credits,
        level: courseForm.level
      }
    }
  } else {
    studyPlansCourses.value.push({
      id: Date.now(),
      code: courseForm.code,
      name: { ar: courseForm.name_ar, en: courseForm.name_en },
      credits: courseForm.credits,
      level: courseForm.level
    })
  }
  isCourseModalOpen.value = false
}

const handleDeleteCourse = (id) => {
  if (window.confirm(localeStore.isRtl ? 'حذف هذا المقرر من الخطة الدراسية؟' : 'Delete this course from study plan?')) {
    studyPlansCourses.value = studyPlansCourses.value.filter((c) => c.id !== id)
  }
}

const handleDeleteExam = async (id) => {
  if (window.confirm(localeStore.isRtl ? 'حذف هذا الموعد من جدول الامتحانات؟' : 'Delete exam schedule entry?')) {
    await api.deleteExamSchedule(id)
    examSchedulesList.value = examSchedulesList.value.filter((e) => e.id !== id)
  }
}

const openIssueStatementModal = () => {
  isStatementModalOpen.value = true
}

const submitStatementForm = async () => {
  const statement = await api.issueOfficialStatement({ ...statementForm })
  sampleStatements.value.unshift(statement)
  isStatementModalOpen.value = false
  alert(localeStore.isRtl ? 'تم إصدار الوثيقة واعتمادها بنجاح برمز التحقق المشفر.' : 'Official Statement issued and signed.')
}

const printStatement = (st) => {
  window.print()
}

const exportRequestsCsv = () => {
  const headers = ['Request Number', 'Student Name', 'Student ID', 'Service Type', 'Status', 'Fee Paid', 'Admin Notes', 'Date']
  const rows = filteredRequests.value.map((r) => [
    r.request_number,
    `"${r.student_name}"`,
    `'${r.student_id_number}'`,
    getServiceLabel(r.service_type),
    r.status,
    r.is_fee_paid ? 'Yes' : 'No',
    `"${r.admin_notes || ''}"`,
    r.created_at || ''
  ])

  const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + [headers.join(','), ...rows.map((e) => e.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `EgyiTech_Student_Requests_${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

onMounted(() => {
  loadData()
})
</script>
