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
        <EmptyState
          v-if="filteredRequests.length === 0"
          :title="localeStore.isRtl ? 'لا توجد طلبات إلكترونية مطابقة' : 'No service requests found'"
          :description="localeStore.isRtl ? 'لم يتم تقديم أي طلبات طلابية تطابق الفلترة المحددة حالياً.' : 'No student requests currently match the selected criteria.'"
        />
        <div v-else class="overflow-x-auto">
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
                  <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ formatStandardDate(req.created_at, localeStore.locale) }}</div>
                  <div class="inline-block text-[10px] font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded mt-1 border border-blue-100">
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
              {{ formatStandardDate(st.issue_date, localeStore.locale) }} • {{ st.signatory_name }}
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
        <EmptyState
          v-if="examSchedulesList.length === 0"
          :title="localeStore.isRtl ? 'لا توجد جداول امتحانات معلنة' : 'No exam schedules posted'"
          :description="localeStore.isRtl ? 'استخدم زر إضافة موعد امتحان بالأعلى لإدراج مقرر وقاعة ولجنة مراقبة.' : 'Click schedule exam button above to assign courses and halls.'"
        />
        <div v-else class="overflow-x-auto">
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
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-navy-950 text-sm">{{ exam.course_code }}</span>
                    <a
                      v-if="exam.timetable_document_path"
                      :href="exam.timetable_document_path"
                      target="_blank"
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gold-100 text-gold-950 text-[10px] font-bold hover:bg-gold-200 transition-colors"
                      title="View Attached Official Timetable"
                    >
                      <Download class="w-3 h-3" />
                      <span>PDF Asset</span>
                    </a>
                  </div>
                  <div class="text-xs text-slate-600 font-bold mt-0.5">{{ getTranslated(exam.course_name, localeStore.locale) }}</div>
                </td>

                <td class="py-3.5 px-4 text-center">
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-800 border border-purple-200 uppercase">
                    {{ exam.exam_type }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center font-mono">
                  <div class="font-bold text-navy-950">{{ formatStandardDate(exam.exam_date, localeStore.locale) }}</div>
                  <div class="text-[10px] text-slate-500 font-semibold">{{ formatTimeRange(exam.start_time, exam.end_time, localeStore.locale) }}</div>
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

    <!-- TAB 4: STUDY PLANS & CURRICULUM (HYBRID WORKFLOW) -->
    <div v-if="activeTab === 'study_plans'" class="space-y-4">
      <!-- Master Study Plan PDF Upload Ribbon (Hybrid Document Workflow) -->
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h3 class="text-base font-bold text-navy-950 flex items-center gap-2">
              <BookOpenCheck class="w-5 h-5 text-gold-500" />
              <span>{{ localeStore.isRtl ? 'لائحة الخطة الدراسية وتوصيف المقررات (Hybrid Document Mode)' : 'Master Curriculum Blueprint (Hybrid Document Mode)' }}</span>
            </h3>
            <p class="text-xs text-slate-500 mt-1">
              {{ localeStore.isRtl ? 'يمكنك إدخال المقررات يدوياً لكل مستوى، أو رفع ملف اللائحة والخطة المعتمدة (PDF / Excel) ليعتمد كوثيقة الخطة الرسمية مباشرة.' : 'You can enter courses level-by-level or upload an official accredited Curriculum Blueprint (PDF/Excel) as the primary document asset.' }}
            </p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <button
              type="button"
              class="px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-400 text-navy-950 font-bold text-xs shadow-sm transition-all cursor-pointer inline-flex items-center gap-1.5"
              @click="openCourseStudyPlanHybridModal"
            >
              <Upload class="w-3.5 h-3.5" />
              <span>{{ localeStore.isRtl ? 'رفع وثيقة اللائحة والخطة المعتمدة' : 'Upload Master Study Plan PDF' }}</span>
            </button>
            <button
              type="button"
              class="px-4 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-sm transition-all cursor-pointer inline-flex items-center gap-1.5"
              @click="openNewCourseModal"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>{{ localeStore.isRtl ? 'إضافة مقرر فردي' : 'Add Single Course' }}</span>
            </button>
          </div>
        </div>

        <!-- Master Document Asset Notice if uploaded -->
        <div v-if="masterStudyPlanFileUrl" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono">
              PDF
            </div>
            <div>
              <div class="text-xs font-bold text-navy-950">{{ masterStudyPlanFileName || 'Accredited Curriculum Blueprint' }}</div>
              <div class="text-[11px] text-emerald-800 font-medium">
                {{ localeStore.isRtl ? 'الوثيقة الرسمية المعتمدة للخطة الدراسية نشطة ومتاحة للتحميل.' : 'Accredited study plan file is currently active and primary.' }}
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <a
              :href="masterStudyPlanFileUrl"
              target="_blank"
              class="px-3 py-1.5 rounded-lg bg-white border border-emerald-300 text-navy-950 font-bold text-xs hover:bg-emerald-100 transition-colors inline-flex items-center gap-1"
            >
              <Download class="w-3.5 h-3.5 text-gold-600" />
              <span>{{ localeStore.isRtl ? 'تحميل' : 'Download' }}</span>
            </a>
            <button
              type="button"
              class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
              @click="removeMasterStudyPlanFile"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

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

    <!-- MODAL: ISSUE OFFICIAL STATEMENT (HYBRID DOCUMENT WORKFLOW) -->
    <Modal
      v-model="isStatementModalOpen"
      :title="localeStore.isRtl ? 'إصدار إفادة / شهادة معتمدة (Hybrid Workflow)' : 'Issue Verifiable Statement (Hybrid Workflow)'"
      size="lg"
      @close="isStatementModalOpen = false"
    >
      <div class="space-y-4 text-start text-xs">
        <HybridDocumentWorkflow
          v-model="statementWorkflowModel"
          mode="both"
          :existing-file-url="statementWorkflowModel.fileUrl"
          :existing-file-name="statementWorkflowModel.fileName"
          :structured-tab-label="localeStore.isRtl ? 'توليد إلكتروني معتمد' : 'Structured Credential Form'"
          :upload-tab-label="localeStore.isRtl ? 'رفع إفادة ممسوحة / مستند رقمي' : 'Direct Certificate Upload'"
          accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
          @file-selected="handleStatementFileSelected"
          @file-removed="handleStatementFileRemoved"
        >
          <template #structured-form>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
              <EnterpriseFormField
                v-model="statementForm.student_id_number"
                type="text"
                :label="localeStore.isRtl ? 'كود الطالب الجامعي' : 'Student ID Number'"
                required
                col-span="6"
                placeholder="20241001"
              />
              <EnterpriseFormField
                v-model="statementForm.student_name"
                type="text"
                :label="localeStore.isRtl ? 'اسم الطالب الرباعي' : 'Student Full Name'"
                required
                col-span="6"
                placeholder="يوسف أحمد حسن"
              />
              <EnterpriseFormField
                v-model="statementForm.national_id"
                type="text"
                :label="localeStore.isRtl ? 'الرقم القومي / جواز السفر' : 'National ID / Passport'"
                required
                col-span="6"
                placeholder="30405150102233"
              />
              <EnterpriseFormField
                v-model="statementForm.statement_type"
                type="select"
                :label="localeStore.isRtl ? 'نوع الإفادة' : 'Statement Type'"
                col-span="6"
                :options="[
                  { label: 'إفادة قيد بكالوريوس رسمية (Enrollment Certificate)', value: 'official_enrollment' },
                  { label: 'شهادة تخرج مؤقتة (Graduation Certificate)', value: 'completion_statement' },
                  { label: 'شهادة دراسة باللغة الإنجليزية (Medium of Instruction)', value: 'english_proficiency' }
                ]"
              />
              <EnterpriseFormField
                v-model="statementForm.recipient_entity_ar"
                type="text"
                :label="localeStore.isRtl ? 'الجهة الموجه إليها (عربي)' : 'Addressed Entity (Ar)'"
                col-span="6"
                placeholder="إلى من يهمه الأمر / نقابة المهندسين"
              />
              <EnterpriseFormField
                v-model="statementForm.recipient_entity_en"
                type="text"
                label="Addressed Entity (En)"
                col-span="6"
                placeholder="To Whom It May Concern / Embassy"
              />
            </div>
          </template>

          <template #file-meta-form>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
              <EnterpriseFormField
                v-model="statementForm.title_ar"
                type="text"
                :label="localeStore.isRtl ? 'عنوان الوثيقة أو الإفادة (عربي)' : 'Document Title (Ar)'"
                placeholder="شهادة تخرج معتمدة وموثقة"
              />
              <EnterpriseFormField
                v-model="statementForm.student_id_number"
                type="text"
                :label="localeStore.isRtl ? 'كود الطالب (اختياري للربط بالسجل)' : 'Student ID (Optional)'"
                placeholder="20241001"
              />
            </div>
          </template>
        </HybridDocumentWorkflow>
      </div>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 cursor-pointer" @click="isStatementModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md cursor-pointer inline-flex items-center gap-1.5" @click="submitStatementForm">
          <ShieldCheck class="w-4 h-4 text-gold-400" />
          <span>{{ statementWorkflowModel.mode === 'upload' ? (localeStore.isRtl ? 'اعتماد وحفظ المستند الرقمي' : 'Store & Certify Digital Asset') : (localeStore.isRtl ? 'إصدار الوثيقة وتوليد QR' : 'Issue & Generate QR') }}</span>
        </button>
      </template>
    </Modal>

    <!-- MODAL: SCHEDULE / EDIT EXAM (HYBRID DOCUMENT WORKFLOW) -->
    <Modal
      v-model="isExamModalOpen"
      :title="isEditingExam ? (localeStore.isRtl ? 'تعديل موعد الامتحان ولجنة المراقبة' : 'Edit Exam Schedule & Invigilation') : (localeStore.isRtl ? 'جدولة امتحان جديد أو رفع جدول كامل' : 'Schedule Exam or Upload Full Timetable')"
      size="lg"
      @close="isExamModalOpen = false"
    >
      <div class="space-y-4 text-start text-xs">
        <HybridDocumentWorkflow
          v-model="examWorkflowModel"
          mode="both"
          :existing-file-url="examWorkflowModel.fileUrl"
          :existing-file-name="examWorkflowModel.fileName"
          :structured-tab-label="localeStore.isRtl ? 'إدخال مقرر وموعد محدد' : 'Course-Level Entry'"
          :upload-tab-label="localeStore.isRtl ? 'رفع جدول الامتحانات العام (ملف PDF / Excel)' : 'Upload Master Timetable (PDF / Excel)'"
          accept=".pdf,.xls,.xlsx,.doc,.docx,.jpg,.jpeg,.png"
          @file-selected="handleExamFileSelected"
          @file-removed="handleExamFileRemoved"
        >
          <template #structured-form>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
              <EnterpriseFormField
                v-model="examForm.course_code"
                type="text"
                :label="localeStore.isRtl ? 'كود المقرر' : 'Course Code'"
                required
                col-span="4"
                placeholder="CS301"
              />
              <EnterpriseFormField
                v-model="examForm.course_name_ar"
                type="text"
                :label="localeStore.isRtl ? 'اسم المقرر (عربي)' : 'Course Name (Ar)'"
                required
                col-span="4"
                placeholder="الذكاء الاصطناعي وتعلم الآلة"
              />
              <EnterpriseFormField
                v-model="examForm.course_name_en"
                type="text"
                label="Course Name (En)"
                required
                col-span="4"
                placeholder="Artificial Intelligence & ML"
              />
              <EnterpriseFormField
                v-model="examForm.exam_type"
                type="select"
                :label="localeStore.isRtl ? 'نوع الامتحان' : 'Exam Type'"
                required
                col-span="3"
                :options="[
                  { label: 'Midterm (نصفي)', value: 'midterm' },
                  { label: 'Final (نهائي)', value: 'final' },
                  { label: 'Practical (عملي)', value: 'practical' },
                  { label: 'Oral (شفوي)', value: 'oral' }
                ]"
              />
              <EnterpriseFormField
                v-model="examForm.exam_date"
                type="date"
                :label="localeStore.isRtl ? 'تاريخ الامتحان' : 'Exam Date'"
                required
                col-span="3"
              />
              <EnterpriseFormField
                v-model="examForm.start_time"
                type="time"
                :label="localeStore.isRtl ? 'وقت البدء' : 'Start Time'"
                required
                col-span="3"
              />
              <EnterpriseFormField
                v-model="examForm.end_time"
                type="time"
                :label="localeStore.isRtl ? 'وقت الانتهاء' : 'End Time'"
                required
                col-span="3"
              />
              <EnterpriseFormField
                v-model="examForm.hall_location_ar"
                type="text"
                :label="localeStore.isRtl ? 'المدرج / القاعة (عربي)' : 'Hall Location (Ar)'"
                required
                col-span="6"
                placeholder="مدرج الدكتور مجدي يعقوب (مبنى أ)"
              />
              <EnterpriseFormField
                v-model="examForm.hall_location_en"
                type="text"
                label="Hall Location (En)"
                required
                col-span="6"
                placeholder="Magdi Yacoub Auditorium (Hall A)"
              />
              <EnterpriseFormField
                v-model="examForm.chief_invigilator_ar"
                type="text"
                :label="localeStore.isRtl ? 'رئيس اللجنة (عربي)' : 'Chief Proctor (Ar)'"
                col-span="4"
                placeholder="أ.د. عصام النجار"
              />
              <EnterpriseFormField
                v-model="examForm.chief_invigilator_en"
                type="text"
                label="Chief Proctor (En)"
                col-span="4"
                placeholder="Prof. Dr. Essam El-Naggar"
              />
              <EnterpriseFormField
                v-model="examForm.seating_capacity"
                type="number"
                :label="localeStore.isRtl ? 'سعة القاعة' : 'Seating Capacity'"
                :min="10"
                :max="1000"
                col-span="4"
              />
            </div>
          </template>

          <template #file-meta-form>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
              <EnterpriseFormField
                v-model="examForm.course_name_ar"
                type="text"
                :label="localeStore.isRtl ? 'اسم أو عنوان الجدول المرفوع (عربي)' : 'Timetable Title (Ar)'"
                placeholder="جدول الامتحانات النهائية للفصل الدراسي الثاني"
              />
              <EnterpriseFormField
                v-model="examForm.exam_type"
                type="select"
                :label="localeStore.isRtl ? 'فترة / نوع الامتحانات' : 'Exam Season'"
                :options="[
                  { label: 'Final Examinations (نهائي)', value: 'final' },
                  { label: 'Midterm Examinations (نصفي)', value: 'midterm' },
                  { label: 'Practical & Labs (عملي)', value: 'practical' }
                ]"
              />
            </div>
          </template>
        </HybridDocumentWorkflow>
      </div>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 cursor-pointer" @click="isExamModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md cursor-pointer inline-flex items-center gap-1.5" @click="submitExamForm">
          <CalendarDays class="w-4 h-4 text-gold-400" />
          <span>{{ examWorkflowModel.mode === 'upload' ? (localeStore.isRtl ? 'اعتماد ونشر ملف الجدول' : 'Publish Master Timetable File') : (localeStore.isRtl ? 'حفظ الموعد الأكاديمي' : 'Save Exam Schedule') }}</span>
        </button>
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
        <EnterpriseFormField
          v-model="newRequestForm.student_id_number"
          type="text"
          :label="localeStore.isRtl ? 'كود الطالب الجامعي' : 'Student ID Number'"
          required
          placeholder="20241001"
        />
        <EnterpriseFormField
          v-model="newRequestForm.student_name"
          type="text"
          :label="localeStore.isRtl ? 'اسم الطالب الرباعي' : 'Student Full Name'"
          required
          placeholder="محمود سامي علي"
        />
        <EnterpriseFormField
          v-model="newRequestForm.service_type"
          type="select"
          :label="localeStore.isRtl ? 'نوع الخدمة المطلوبة' : 'Service Type'"
          required
          :options="[
            { label: localeStore.isRtl ? 'شهادة قيد رسمية' : 'Enrollment Certificate', value: 'enrollment_cert' },
            { label: localeStore.isRtl ? 'كشف درجات معتمد' : 'Official Transcript', value: 'transcript' },
            { label: localeStore.isRtl ? 'مقاصة ومعادلة مقررات' : 'Course Exemption', value: 'course_exemption' },
            { label: localeStore.isRtl ? 'تأجيل فصل دراسي' : 'Term Postponement', value: 'postponement' },
            { label: localeStore.isRtl ? 'بدل فاقد كارنيه' : 'ID Card Replacement', value: 'id_card_replacement' }
          ]"
        />
        <EnterpriseFormField
          v-model="newRequestForm.purpose_ar"
          type="textarea"
          :label="localeStore.isRtl ? 'الغرض من الطلب' : 'Purpose'"
          :rows="2"
          placeholder="استخراج شهادة قيد موجهة إلى..."
        />
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
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <EnterpriseFormField
            v-model="courseForm.code"
            type="text"
            :label="localeStore.isRtl ? 'كود المقرر' : 'Course Code'"
            required
            col-span="6"
            placeholder="CS201"
          />
          <EnterpriseFormField
            v-model="courseForm.level"
            type="select"
            :label="localeStore.isRtl ? 'المستوى الدراسي' : 'Academic Level'"
            required
            col-span="6"
            :options="[
              { label: 'Level 1 (المستوى الأول)', value: 1 },
              { label: 'Level 2 (المستوى الثاني)', value: 2 },
              { label: 'Level 3 (المستوى الثالث)', value: 3 },
              { label: 'Level 4 (المستوى الرابع)', value: 4 }
            ]"
          />
          <EnterpriseFormField
            v-model="courseForm.name_ar"
            type="text"
            :label="localeStore.isRtl ? 'اسم المقرر (عربي)' : 'Course Name (Ar)'"
            required
            col-span="6"
            placeholder="هياكل البيانات والخوارزميات"
          />
          <EnterpriseFormField
            v-model="courseForm.name_en"
            type="text"
            label="Course Name (En)"
            required
            col-span="6"
            placeholder="Data Structures & Algorithms"
          />
          <EnterpriseFormField
            v-model="courseForm.credits"
            type="number"
            :label="localeStore.isRtl ? 'عدد الساعات المعتمدة' : 'Credit Hours'"
            required
            :min="1"
            :max="6"
            col-span="12"
          />
        </div>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isCourseModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitCourseForm">{{ localeStore.isRtl ? 'حفظ المقرر' : 'Save Course' }}</button>
      </template>
    </Modal>

    <!-- MODAL: UPLOAD MASTER CURRICULUM BLUEPRINT (HYBRID DOCUMENT WORKFLOW) -->
    <Modal
      v-model="isCurriculumUploadModalOpen"
      :title="localeStore.isRtl ? 'إدارة واعتماد وثيقة اللائحة والخطة الدراسية (Hybrid Workflow)' : 'Master Curriculum Blueprint Management (Hybrid Workflow)'"
      size="xl"
      @close="isCurriculumUploadModalOpen = false"
    >
      <form @submit.prevent="submitCurriculumBlueprintUpload" class="space-y-5 text-start text-xs">
        <HybridDocumentWorkflow
          v-model="curriculumWorkflowModel"
          structured-tab-label="مقررات وتوصيف الخطة (Curriculum Specs)"
          upload-tab-label="ملف اللائحة المعتمدة (Official PDF / Excel Blueprint)"
          @file-selected="handleCurriculumFileSelected"
          @file-removed="handleCurriculumFileRemoved"
        >
          <template #structured>
            <div class="space-y-4">
              <div class="p-3.5 bg-blue-50/70 border border-blue-200 text-blue-900 rounded-2xl text-xs">
                {{ localeStore.isRtl ? 'أدخل البيانات العامة للائحة والخطة الدراسية لتوليد فهرس معتمد للمقررات.' : 'Provide high-level degree specification parameters and credit distribution.' }}
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <EnterpriseFormField
                  v-model="curriculumPlanMeta.program_name_ar"
                  type="text"
                  :label="localeStore.isRtl ? 'اسم البرنامج الأكاديمي (عربي)' : 'Program Name (Ar)'"
                  required
                  col-span="6"
                  placeholder="بكالوريوس الذكاء الاصطناعي وهندسة البرمجيات"
                />
                <EnterpriseFormField
                  v-model="curriculumPlanMeta.program_name_en"
                  type="text"
                  :label="localeStore.isRtl ? 'اسم البرنامج (إنجليزي)' : 'Program Name (En)'"
                  required
                  col-span="6"
                  placeholder="B.Sc. in Artificial Intelligence & Software Eng."
                />
                <EnterpriseFormField
                  v-model="curriculumPlanMeta.total_credits"
                  type="number"
                  :label="localeStore.isRtl ? 'إجمالي الساعات المعتمدة' : 'Total Credit Hours'"
                  required
                  col-span="4"
                  placeholder="136"
                />
                <EnterpriseFormField
                  v-model="curriculumPlanMeta.degree_level"
                  type="select"
                  :label="localeStore.isRtl ? 'الدرجة العلمية' : 'Degree Level'"
                  col-span="4"
                  :options="[
                    { label: 'Bachelor (بكالوريوس)', value: 'bachelor' },
                    { label: 'Master (ماجستير)', value: 'master' },
                    { label: 'PhD (دكتوراه)', value: 'phd' }
                  ]"
                />
                <EnterpriseFormField
                  v-model="curriculumPlanMeta.effective_year"
                  type="text"
                  :label="localeStore.isRtl ? 'اللائحة المعتمدة من عام' : 'Effective Academic Year'"
                  col-span="4"
                  placeholder="2025/2026"
                />
              </div>
            </div>
          </template>
        </HybridDocumentWorkflow>
      </form>

      <template #footer>
        <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200" @click="isCurriculumUploadModalOpen = false">{{ $t('common.cancel') }}</button>
        <button type="button" class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md" @click="submitCurriculumBlueprintUpload">
          {{ localeStore.isRtl ? 'اعتماد وحفظ الخطة الدراسية' : 'Save & Publish Blueprint' }}
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useLocaleStore } from '../../../stores/locale'
import { api, getTranslated } from '../../../services/api'
import { formatStandardDate, formatStandardTime, formatTimeRange } from '../../../utils/dateFormat'
import Modal from '../../../components/ui/Modal.vue'
import EmptyState from '../../../components/ui/EmptyState.vue'
import EnterpriseFormField from '../../../components/ui/EnterpriseFormField.vue'
import HybridDocumentWorkflow from '../../../components/ui/HybridDocumentWorkflow.vue'
import { useDialog } from '../../../composables/useDialog'
import { useToast } from '../../../composables/useToast'
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
const dialog = useDialog()
const toast = useToast()
const activeTab = ref('requests')

const statementWorkflowModel = reactive({
  structuredData: {},
  file: null,
  fileUrl: '',
  fileName: '',
  mode: 'structured'
})

const handleStatementFileSelected = (file) => {
  statementWorkflowModel.file = file
  statementWorkflowModel.fileName = file.name
}

const handleStatementFileRemoved = () => {
  statementWorkflowModel.file = null
  statementWorkflowModel.fileUrl = ''
  statementWorkflowModel.fileName = ''
}

const examWorkflowModel = reactive({
  structuredData: {},
  file: null,
  fileUrl: '',
  fileName: '',
  mode: 'structured'
})

const handleExamFileSelected = (file) => {
  examWorkflowModel.file = file
  examWorkflowModel.fileName = file.name
}

const handleExamFileRemoved = () => {
  examWorkflowModel.file = null
  examWorkflowModel.fileUrl = ''
  examWorkflowModel.fileName = ''
}

const isCurriculumUploadModalOpen = ref(false)
const masterStudyPlanFileUrl = ref('')
const masterStudyPlanFileName = ref('')

const curriculumPlanMeta = reactive({
  program_name_ar: '',
  program_name_en: '',
  total_credits: 132,
  degree_level: 'bachelor',
  effective_year: ''
})

const curriculumWorkflowModel = reactive({
  structuredData: {},
  file: null,
  fileUrl: '',
  fileName: '',
  mode: 'structured'
})

const handleCurriculumFileSelected = (file) => {
  curriculumWorkflowModel.file = file
  curriculumWorkflowModel.fileName = file.name
}

const handleCurriculumFileRemoved = () => {
  curriculumWorkflowModel.file = null
  curriculumWorkflowModel.fileUrl = ''
  curriculumWorkflowModel.fileName = ''
}

const openCourseStudyPlanHybridModal = () => {
  curriculumWorkflowModel.file = null
  curriculumWorkflowModel.fileUrl = masterStudyPlanFileUrl.value
  curriculumWorkflowModel.fileName = masterStudyPlanFileName.value
  curriculumWorkflowModel.mode = masterStudyPlanFileUrl.value ? 'both' : 'structured'
  isCurriculumUploadModalOpen.value = true
}

const removeMasterStudyPlanFile = () => {
  masterStudyPlanFileUrl.value = ''
  masterStudyPlanFileName.value = ''
}

const submitCurriculumBlueprintUpload = async () => {
  if (curriculumWorkflowModel.file) {
    masterStudyPlanFileUrl.value = URL.createObjectURL(curriculumWorkflowModel.file)
    masterStudyPlanFileName.value = curriculumWorkflowModel.file.name
  } else if (curriculumWorkflowModel.fileUrl) {
    masterStudyPlanFileUrl.value = curriculumWorkflowModel.fileUrl
    masterStudyPlanFileName.value = curriculumWorkflowModel.fileName
  }
  isCurriculumUploadModalOpen.value = false
  await dialog.alert({
    title: localeStore.isRtl ? 'تم بنجاح' : 'Success',
    message: localeStore.isRtl ? 'تم تحديث واعتماد وثيقة اللائحة والخطة الدراسية بنجاح.' : 'Curriculum Blueprint saved successfully.',
    variant: 'success',
  })
}

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

const isNewRequestModalOpen = ref(false)
const newRequestForm = reactive({
  student_id_number: '',
  student_name: '',
  service_type: 'enrollment_cert',
  purpose_ar: ''
})

const isExamModalOpen = ref(false)
const isEditingExam = ref(false)
const editingExamId = ref(null)
const examForm = reactive({
  course_code: '',
  course_name_ar: '',
  course_name_en: '',
  exam_type: 'final',
  exam_date: '',
  start_time: '09:00',
  end_time: '12:00',
  hall_location_ar: '',
  hall_location_en: '',
  chief_invigilator_ar: '',
  chief_invigilator_en: '',
  seating_capacity: 100
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

const isStatementModalOpen = ref(false)
const statementForm = reactive({
  student_name: '',
  student_id_number: '',
  national_id: '',
  statement_type: 'official_enrollment',
  title_ar: 'إفادة قيد رسمية موجهة إلى نقابة المهندسين',
  title_en: 'Official Enrollment Verification Statement',
  recipient_entity_ar: '',
  recipient_entity_en: ''
})

const sampleStatements = ref([])

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
    const stmts = await api.getOfficialStatements()
    sampleStatements.value = stmts || []
  } catch (err) {
    console.error(err)
  }
}

const openReviewModal = (req) => {
  activeRequest.value = req
  reviewForm.status = req.status || 'approved'
  reviewForm.admin_notes = req.admin_notes || ''
  isReviewModalOpen.value = true
}

const submitReviewForm = async () => {
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
    toast.warning(
      localeStore.isRtl ? 'يرجى ملء جميع الحقول الإلزامية للمتابعة.' : 'Please fill in all required fields.',
      localeStore.isRtl ? 'حقول إلزامية' : 'Required Fields'
    )
    return
  }
  try {
    const created = await api.submitStudentRequest({ ...newRequestForm })
    requestsList.value.unshift(created)
    isNewRequestModalOpen.value = false
    toast.success(
      localeStore.isRtl ? 'تم تقديم الطلب الأكاديمي بنجاح.' : 'Student request submitted successfully.',
      localeStore.isRtl ? 'تم التقديم' : 'Request Submitted'
    )
  } catch (e) {
    toast.error(
      localeStore.isRtl ? 'تعذر تقديم الطلب الطلابي.' : 'Failed to submit request.',
      localeStore.isRtl ? 'خطأ' : 'Error'
    )
  }
}

const handleDeleteRequest = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف الطلب الطلابي' : 'Delete Student Request',
    message: localeStore.isRtl ? 'هل تريد حذف هذا الطلب نهائياً من سجلات البوابة؟' : 'Are you sure you want to delete this student request?',
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    await api.deleteStudentRequest(id)
    requestsList.value = requestsList.value.filter((r) => r.id !== id)
    toast.info(
      localeStore.isRtl ? 'تم حذف الطلب الطلابي بنجاح.' : 'Student request deleted.',
      localeStore.isRtl ? 'تم الحذف' : 'Deleted'
    )
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
  examForm.hall_location_ar = ''
  examForm.hall_location_en = ''
  examForm.chief_invigilator_ar = ''
  examForm.chief_invigilator_en = ''
  examForm.seating_capacity = 100

  examWorkflowModel.file = null
  examWorkflowModel.fileUrl = ''
  examWorkflowModel.fileName = ''
  examWorkflowModel.mode = 'structured'

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
  examForm.hall_location_ar = exam.hall_location?.ar || exam.hall_location || ''
  examForm.hall_location_en = exam.hall_location?.en || exam.hall_location || ''
  examForm.chief_invigilator_ar = exam.chief_invigilator?.ar || exam.chief_invigilator || ''
  examForm.chief_invigilator_en = exam.chief_invigilator?.en || exam.chief_invigilator || ''
  examForm.seating_capacity = exam.seating_capacity || 100

  examWorkflowModel.file = null
  examWorkflowModel.fileUrl = exam.timetable_document_path || ''
  examWorkflowModel.fileName = exam.timetable_file_name || (exam.timetable_document_path ? 'Timetable Document' : '')
  examWorkflowModel.mode = exam.workflow_mode || (exam.timetable_document_path ? 'upload' : 'structured')

  isExamModalOpen.value = true
}

const submitExamForm = async () => {
  try {
    const payload = {
      ...examForm,
      workflow_mode: examWorkflowModel.mode,
      timetable_document: examWorkflowModel.file
    }

    if (isEditingExam.value) {
      const updated = await api.updateExamSchedule(editingExamId.value, payload)
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
          timetable_document_path: updated?.timetable_document_path || examWorkflowModel.fileUrl,
          timetable_file_name: updated?.timetable_file_name || examWorkflowModel.fileName,
          ...updated,
        }
      }
      toast.success(
        localeStore.isRtl ? 'تم تحديث جدول الامتحان بنجاح.' : 'Exam schedule entry updated successfully.',
        localeStore.isRtl ? 'تم التحديث' : 'Exam Updated'
      )
    } else {
      const created = await api.storeExamSchedule(payload)
      examSchedulesList.value.unshift(created)
      toast.success(
        localeStore.isRtl ? 'تمت إضافة موعد الامتحان بنجاح.' : 'Exam schedule entry created successfully.',
        localeStore.isRtl ? 'تمت الإضافة' : 'Exam Added'
      )
    }
    isExamModalOpen.value = false
  } catch (err) {
    toast.error(
      localeStore.isRtl ? 'تعذر حفظ جدول الامتحان، يرجى التحقق من البيانات.' : 'Failed to save exam schedule.',
      localeStore.isRtl ? 'خطأ في الحفظ' : 'Save Error'
    )
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

const submitCourseForm = async () => {
  if (!courseForm.code || !courseForm.name_ar) {
    toast.warning(
      localeStore.isRtl ? 'يرجى إدخال كود واسم المقرر.' : 'Please enter course code and name.',
      localeStore.isRtl ? 'حقول إلزامية' : 'Required Fields'
    )
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
    toast.success(
      localeStore.isRtl ? 'تم تحديث بيانات المقرر بنجاح.' : 'Course updated successfully.',
      localeStore.isRtl ? 'تم التحديث' : 'Course Updated'
    )
  } else {
    studyPlansCourses.value.push({
      id: Date.now(),
      code: courseForm.code,
      name: { ar: courseForm.name_ar, en: courseForm.name_en },
      credits: courseForm.credits,
      level: courseForm.level
    })
    toast.success(
      localeStore.isRtl ? 'تمت إضافة المقرر إلى الخطة الدراسية.' : 'Course added to study plan.',
      localeStore.isRtl ? 'تمت الإضافة' : 'Course Added'
    )
  }
  isCourseModalOpen.value = false
}

const handleDeleteCourse = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف المقرر' : 'Delete Course',
    message: localeStore.isRtl ? 'هل أنت متأكد من حذف هذا المقرر من الخطة الدراسية؟' : 'Are you sure you want to delete this course from the study plan?',
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    studyPlansCourses.value = studyPlansCourses.value.filter((c) => c.id !== id)
    toast.info(
      localeStore.isRtl ? 'تم حذف المقرر من الخطة.' : 'Course removed from plan.',
      localeStore.isRtl ? 'تم الحذف' : 'Deleted'
    )
  }
}

const handleDeleteExam = async (id) => {
  const confirmed = await dialog.confirm({
    title: localeStore.isRtl ? 'حذف موعد الامتحان' : 'Delete Exam Schedule',
    message: localeStore.isRtl ? 'هل أنت متأكد من حذف هذا الموعد من جدول الامتحانات؟' : 'Are you sure you want to delete this exam schedule entry?',
    confirmText: localeStore.isRtl ? 'حذف' : 'Delete',
    cancelText: localeStore.isRtl ? 'إلغاء' : 'Cancel',
    variant: 'danger',
  })

  if (confirmed) {
    await api.deleteExamSchedule(id)
    examSchedulesList.value = examSchedulesList.value.filter((e) => e.id !== id)
    toast.info(
      localeStore.isRtl ? 'تم حذف موعد الامتحان.' : 'Exam schedule entry removed.',
      localeStore.isRtl ? 'تم الحذف' : 'Deleted'
    )
  }
}

const openIssueStatementModal = () => {
  statementWorkflowModel.file = null
  statementWorkflowModel.fileUrl = ''
  statementWorkflowModel.fileName = ''
  statementWorkflowModel.mode = 'structured'
  isStatementModalOpen.value = true
}

const submitStatementForm = async () => {
  try {
    const payload = {
      ...statementForm,
      workflow_mode: statementWorkflowModel.mode,
      document: statementWorkflowModel.file
    }
    const statement = await api.issueOfficialStatement(payload)
    sampleStatements.value.unshift(statement)
    isStatementModalOpen.value = false
    toast.success(
      localeStore.isRtl ? 'تم إصدار الوثيقة واعتمادها بنجاح برمز التحقق المشفر والختم الرقمي.' : 'Official Statement issued and digitally signed.',
      localeStore.isRtl ? 'تم الإصدار' : 'Statement Issued'
    )
  } catch (err) {
    toast.error(
      localeStore.isRtl ? 'تعذر إصدار الإفادة الرسمية، يرجى مراجعة البيانات.' : 'Failed to issue official statement.',
      localeStore.isRtl ? 'فشل الإصدار' : 'Issue Failed'
    )
  }
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
