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
                  <button
                    type="button"
                    class="px-3 py-1.5 rounded-lg bg-navy-900 hover:bg-navy-950 text-white font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1"
                    @click="openReviewRequestModal(req)"
                  >
                    <CheckCircle class="w-3.5 h-3.5 text-gold-400" />
                    <span>{{ localeStore.isRtl ? 'معالجة الطلب' : 'Process' }}</span>
                  </button>
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
            <span class="text-[10px] font-mono bg-gold-100 text-gold-900 px-1.5 py-0.5 rounded font-bold">36 Credits</span>
          </div>
          <ul class="space-y-2 text-xs text-slate-600">
            <li class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
              <div>
                <span class="font-mono font-bold text-navy-900">CS{{ lvl }}01</span>
                <div class="text-[11px] text-slate-500 font-medium">Algorithms & Computation</div>
              </div>
              <span class="font-mono text-[10px] bg-white px-1.5 py-0.5 rounded border border-slate-200">3 Cr</span>
            </li>
            <li class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
              <div>
                <span class="font-mono font-bold text-navy-900">MATH{{ lvl }}02</span>
                <div class="text-[11px] text-slate-500 font-medium">Discrete Mathematics</div>
              </div>
              <span class="font-mono text-[10px] bg-white px-1.5 py-0.5 rounded border border-slate-200">3 Cr</span>
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
        <button type="button" class="px-5 py-2 rounded-xl bg-navy-950 text-white font-bold text-xs shadow-md" @click="submitStatementForm">{{ localeStore.isRtl ? 'إصدار الوثيقة وتوليد QR' : 'Issue & Generate QR' }}</button>
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
  Printer
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

onMounted(() => {
  loadData()
})
</script>
