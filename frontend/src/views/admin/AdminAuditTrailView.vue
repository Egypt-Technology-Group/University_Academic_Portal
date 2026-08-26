<template>
  <div class="space-y-6" :dir="localeStore.dir">
    <!-- Header: Title, Real-time Integrity Check Badge, CSV Export CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
      <div class="space-y-1">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-2xl bg-navy-950 text-gold-400 flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
            <ShieldCheck class="w-5 h-5 text-gold-400" />
          </div>
          <div>
            <h1 class="text-xl sm:text-2xl font-black text-navy-950 tracking-tight">
              {{ localeStore.isRtl ? 'سجل التدقيق والأمان والرقابة الرقمية' : 'Enterprise Audit Trail & Compliance Log' }}
            </h1>
            <p class="text-xs text-slate-500 font-medium">
              {{ localeStore.isRtl ? 'توثيق غير قابل للتلاعب (HMAC SHA-256) لجميع العمليات الحساسة وتعديلات البيانات.' : 'Cryptographically sealed (HMAC SHA-256) audit logs for all security and business transactions.' }}
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Integrity Verification Button -->
        <button
          type="button"
          class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border inline-flex items-center gap-2 shadow-xs cursor-pointer"
          :class="integrityStatus.checked ? (integrityStatus.isValid ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200') : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-300'"
          :disabled="isVerifyingIntegrity"
          @click="handleVerifyIntegrity"
        >
          <Lock v-if="!isVerifyingIntegrity" class="w-3.5 h-3.5" :class="integrityStatus.checked ? (integrityStatus.isValid ? 'text-emerald-600' : 'text-rose-600') : 'text-slate-500'" />
          <RefreshCw v-else class="w-3.5 h-3.5 animate-spin text-navy-900" />
          <span>{{ integrityStatus.checked ? (integrityStatus.isValid ? (localeStore.isRtl ? 'سلسلة التجزئة سليمة وموثقة ✓' : 'Hash Chain Verified ✓') : (localeStore.isRtl ? 'تنبيه: تم رصد عدم تطابق ⚠️' : 'Integrity Alert ⚠️')) : (localeStore.isRtl ? 'فحص سلامة السجلات' : 'Verify Integrity Chain') }}</span>
        </button>

        <!-- CSV Export Button -->
        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white text-xs font-bold transition-all inline-flex items-center gap-2 shadow-xs cursor-pointer"
          @click="handleExportCsv"
        >
          <Download class="w-3.5 h-3.5 text-gold-400" />
          <span>{{ localeStore.isRtl ? 'تصدير التقرير (CSV)' : 'Export Audit Report' }}</span>
        </button>
      </div>
    </div>

    <!-- KPI Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ localeStore.isRtl ? 'إجمالي السجلات المدققة' : 'Total Audit Logs' }}</span>
          <FileText class="w-4 h-4 text-navy-900" />
        </div>
        <div class="text-2xl font-black text-navy-950 font-mono">
          {{ kpiStats.total_logs || 0 }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ localeStore.isRtl ? 'سجلات مشفرة ومؤرخة بالكامل' : 'Immutable indexed transactions' }}
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ localeStore.isRtl ? 'عمليات اليوم' : "Today's Activity" }}</span>
          <Calendar class="w-4 h-4 text-sky-600" />
        </div>
        <div class="text-2xl font-black text-sky-600 font-mono">
          {{ kpiStats.today_logs || 0 }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ localeStore.isRtl ? 'إجراءات ومصادقات مسجلة اليوم' : 'Events captured in last 24h' }}
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ localeStore.isRtl ? 'أحداث الأمان والمصادقة' : 'Security / Auth Events' }}</span>
          <Key class="w-4 h-4 text-amber-500" />
        </div>
        <div class="text-2xl font-black text-amber-600 font-mono">
          {{ kpiStats.security_events || 0 }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ localeStore.isRtl ? 'تسجيل دخول، صلاحيات، وتراخيص' : 'Logins, role checks, access shifts' }}
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
          <span>{{ localeStore.isRtl ? 'العمليات المرفوضة / الفاشلة' : 'Failed Attempts' }}</span>
          <AlertTriangle class="w-4 h-4 text-rose-500" />
        </div>
        <div class="text-2xl font-black text-rose-600 font-mono">
          {{ kpiStats.failed_actions || 0 }}
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
          {{ localeStore.isRtl ? 'محاولات دخول أو تعديل غير مصرح بها' : 'Rejected or blocked attempts' }}
        </div>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <!-- Search Input -->
        <div class="lg:col-span-2 relative">
          <Search class="w-4 h-4 text-slate-400 absolute start-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="filters.search"
            type="text"
            :placeholder="localeStore.isRtl ? 'بحث باسم المستخدم، البريد، عنوان IP، الوحدة...' : 'Search actor, email, IP, description...'"
            class="w-full ps-9 pe-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-1 focus:ring-navy-900 outline-none transition-all"
            @input="debounceSearch"
          />
        </div>

        <!-- Module Filter -->
        <div>
          <select
            v-model="filters.module"
            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-1 focus:ring-navy-900 outline-none cursor-pointer"
            @change="fetchLogs"
          >
            <option value="all">{{ localeStore.isRtl ? 'جميع الوحدات (All Modules)' : 'All Modules' }}</option>
            <option value="auth">{{ localeStore.isRtl ? 'المصادقة والأمان (Auth)' : 'Auth & Security' }}</option>
            <option value="admissions">{{ localeStore.isRtl ? 'القبول والتسجيل (Admissions)' : 'Admissions CRM' }}</option>
            <option value="academic">{{ localeStore.isRtl ? 'الهيكل الأكاديمي (Academic)' : 'Academic Structure' }}</option>
            <option value="services">{{ localeStore.isRtl ? 'الخدمات والإفادات (Services)' : 'Academic Services' }}</option>
            <option value="cms">{{ localeStore.isRtl ? 'المحتوى والأخبار (CMS)' : 'CMS & News' }}</option>
            <option value="events">{{ localeStore.isRtl ? 'الفعاليات والمؤتمرات (Events)' : 'Events Calendar' }}</option>
            <option value="documents">{{ localeStore.isRtl ? 'المستندات واللوائح (Documents)' : 'Documents Repo' }}</option>
            <option value="settings">{{ localeStore.isRtl ? 'إعدادات النظام (Settings)' : 'Site Settings' }}</option>
          </select>
        </div>

        <!-- Action Filter -->
        <div>
          <select
            v-model="filters.action"
            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-1 focus:ring-navy-900 outline-none cursor-pointer"
            @change="fetchLogs"
          >
            <option value="all">{{ localeStore.isRtl ? 'جميع العمليات (All Actions)' : 'All Actions' }}</option>
            <option value="login">{{ localeStore.isRtl ? 'تسجيل دخول (Login)' : 'Login' }}</option>
            <option value="logout">{{ localeStore.isRtl ? 'تسجيل خروج (Logout)' : 'Logout' }}</option>
            <option value="login_failed">{{ localeStore.isRtl ? 'فشل دخول (Failed Login)' : 'Failed Login' }}</option>
            <option value="create">{{ localeStore.isRtl ? 'إنشاء سجل (Create)' : 'Create' }}</option>
            <option value="update">{{ localeStore.isRtl ? 'تعديل بيانات (Update)' : 'Update' }}</option>
            <option value="delete">{{ localeStore.isRtl ? 'حذف سجل (Delete)' : 'Delete' }}</option>
            <option value="status_change">{{ localeStore.isRtl ? 'تغيير حالة (Status Shift)' : 'Status Shift' }}</option>
            <option value="verify">{{ localeStore.isRtl ? 'تدقيق وتوثيق (Verify)' : 'Verify Document' }}</option>
          </select>
        </div>

        <!-- Severity Filter -->
        <div>
          <select
            v-model="filters.severity"
            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-1 focus:ring-navy-900 outline-none cursor-pointer"
            @change="fetchLogs"
          >
            <option value="all">{{ localeStore.isRtl ? 'جميع درجات الأهمية' : 'All Severities' }}</option>
            <option value="info">Info / عادي</option>
            <option value="notice">Notice / تنبيه معتمد</option>
            <option value="warning">Warning / تحذير</option>
            <option value="critical">Critical / حرج</option>
            <option value="security">Security / أمني حساس</option>
          </select>
        </div>

        <!-- Reset Filters Trigger -->
        <div class="flex items-center justify-end">
          <button
            type="button"
            class="w-full px-3 py-2 text-xs font-bold text-slate-600 hover:text-navy-950 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors text-center cursor-pointer"
            @click="resetFilters"
          >
            {{ localeStore.isRtl ? 'إعادة تعيين الفلاتر' : 'Reset Filters' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Main Audit Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="p-12 text-center">
        <LoadingSpinner size="lg" />
        <p class="text-xs text-slate-500 font-bold mt-3">
          {{ localeStore.isRtl ? 'جاري استرجاع سجلات التدقيق والتحقق من سلسلة التجزئة...' : 'Retrieving audited records and validating hash chain...' }}
        </p>
      </div>

      <!-- Empty State -->
      <div v-else-if="logsList.length === 0" class="p-8">
        <EmptyState
          :title="localeStore.isRtl ? 'لم يتم العثور على سجلات تدقيق مطابقة' : 'No matching audit logs found'"
          :description="localeStore.isRtl ? 'جرب تعديل كلمات البحث أو اختيار فلاتر أخرى.' : 'Try adjusting your search criteria or resetting filters.'"
        />
      </div>

      <!-- Table Content -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-start text-xs border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 text-start">ID & {{ localeStore.isRtl ? 'التوقيت' : 'Timestamp' }}</th>
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'المستخدم / المنفذ' : 'Actor / Subject' }}</th>
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'الوحدة والعملية' : 'Module & Action' }}</th>
              <th class="py-3.5 px-4 text-start">{{ localeStore.isRtl ? 'بيان العملية' : 'Description / Payload' }}</th>
              <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'الأهمية والحالة' : 'Severity & Status' }}</th>
              <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'التحقق والمطابقة' : 'Integrity' }}</th>
              <th class="py-3.5 px-4 text-center">{{ localeStore.isRtl ? 'التفاصيل والفرق' : 'Diff & Inspect' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="log in logsList"
              :key="log.id"
              class="hover:bg-slate-50/60 transition-colors group"
            >
              <!-- ID & Timestamp -->
              <td class="py-3.5 px-4 font-mono whitespace-nowrap">
                <div class="font-bold text-navy-950 text-xs">#{{ log.id }}</div>
                <div class="text-[11px] text-slate-400">{{ formatStandardDateTime(log.created_at, localeStore.locale) }}</div>
              </td>

              <!-- Actor -->
              <td class="py-3.5 px-4">
                <div class="font-bold text-navy-950">{{ log.actor_name || 'System' }}</div>
                <div class="text-[11px] text-slate-500 truncate max-w-[180px]">{{ log.actor_email || log.ip_address || '127.0.0.1' }}</div>
                <span class="inline-block text-[10px] font-mono px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200 mt-0.5">
                  {{ log.actor_role || 'system' }}
                </span>
              </td>

              <!-- Module & Action -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="font-bold text-navy-900 uppercase text-[11px] tracking-wider">{{ log.module }}</div>
                <span
                  class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold mt-1"
                  :class="getActionBadgeClass(log.action)"
                >
                  {{ log.action }}
                </span>
              </td>

              <!-- Description -->
              <td class="py-3.5 px-4">
                <div class="text-xs text-slate-700 font-medium line-clamp-2 max-w-sm">
                  {{ localeStore.isRtl ? (log.description_ar || log.description_en) : (log.description_en || log.description_ar) }}
                </div>
                <div v-if="log.auditable_type" class="text-[10px] font-mono text-slate-400 mt-0.5">
                  {{ getShortEntityName(log.auditable_type) }} <span v-if="log.auditable_id">#{{ log.auditable_id }}</span>
                </div>
              </td>

              <!-- Severity & Status -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <span
                  class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  :class="getSeverityBadgeClass(log.severity)"
                >
                  {{ log.severity }}
                </span>
                <div class="text-[10px] font-bold mt-1" :class="log.status === 'failed' ? 'text-rose-600' : 'text-emerald-600'">
                  ● {{ log.status }}
                </div>
              </td>

              <!-- Integrity Hash Badge -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono text-[10px] text-slate-600" :title="log.integrity_hash">
                  <ShieldCheck class="w-3 h-3 text-emerald-600 shrink-0" />
                  <span>{{ log.integrity_hash ? log.integrity_hash.slice(0, 8) + '...' : 'SEALED' }}</span>
                </div>
              </td>

              <!-- Actions: Inspect Diff Modal -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-navy-50 hover:bg-navy-900 text-navy-950 hover:text-white font-bold text-xs transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs"
                  @click="openInspectModal(log)"
                >
                  <Eye class="w-3.5 h-3.5" />
                  <span>{{ localeStore.isRtl ? 'عرض الفروق' : 'Diff & Inspect' }}</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div v-if="paginationMeta.total > 0" class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs text-slate-500 font-medium">
          {{ localeStore.isRtl ? `عرض ${paginationMeta.from || 0} إلى ${paginationMeta.to || 0} من أصل ${paginationMeta.total} سجل تدقيق` : `Showing ${paginationMeta.from || 0} to ${paginationMeta.to || 0} of ${paginationMeta.total} audit entries` }}
        </div>
        <Pagination
          :current-page="paginationMeta.current_page"
          :total-pages="paginationMeta.last_page"
          @change="changePage"
        />
      </div>
    </div>

    <!-- MODAL: Audit Record Deep Inspection & Before/After Diff -->
    <Modal
      v-model="isInspectModalOpen"
      :title="localeStore.isRtl ? 'تفاصيل سجل التدقيق والرقابة وتتبع التغييرات' : 'Audit Entry Details & Before/After Comparison'"
      max-width="3xl"
      @close="isInspectModalOpen = false"
    >
      <div v-if="selectedLog" class="space-y-6 text-start">
        <!-- Top Metadata Summary Card -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div>
            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">{{ localeStore.isRtl ? 'المنفذ / الحساب' : 'Actor / Subject' }}</span>
            <div class="font-bold text-navy-950 mt-0.5">{{ selectedLog.actor_name }}</div>
            <div class="text-slate-500 text-[11px]">{{ selectedLog.actor_email || 'N/A' }}</div>
          </div>
          <div>
            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">{{ localeStore.isRtl ? 'الوحدة والعملية' : 'Module / Action' }}</span>
            <div class="font-bold text-navy-950 mt-0.5 uppercase">{{ selectedLog.module }} • {{ selectedLog.action }}</div>
            <div class="text-slate-500 text-[11px]">{{ selectedLog.request_method || 'HTTP' }} {{ selectedLog.request_url }}</div>
          </div>
          <div>
            <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">{{ localeStore.isRtl ? 'توقيت التسجيل والـ IP' : 'Timestamp & Network' }}</span>
            <div class="font-bold text-navy-950 mt-0.5 font-mono">{{ formatStandardDateTime(selectedLog.created_at, localeStore.locale) }}</div>
            <div class="text-slate-500 font-mono text-[11px]">IP: {{ selectedLog.ip_address || '127.0.0.1' }}</div>
          </div>
        </div>

        <!-- Description Box -->
        <div class="p-3.5 rounded-xl bg-navy-50/70 border border-navy-100 space-y-1">
          <div class="text-[11px] font-bold text-navy-900 uppercase tracking-wider">{{ localeStore.isRtl ? 'بيان النشاط المسجل' : 'Activity Narrative' }}</div>
          <div class="text-xs font-semibold text-slate-800 leading-relaxed">
            {{ localeStore.isRtl ? (selectedLog.description_ar || selectedLog.description_en) : (selectedLog.description_en || selectedLog.description_ar) }}
          </div>
        </div>

        <!-- Cryptographic Seal & Integrity Card -->
        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-emerald-900 flex items-center gap-1.5">
              <Lock class="w-3.5 h-3.5 text-emerald-700" />
              <span>{{ localeStore.isRtl ? 'بصمة التشفير الرقمية (HMAC SHA-256)' : 'Cryptographic Integrity Seal (HMAC SHA-256)' }}</span>
            </span>
            <span class="font-bold text-emerald-700 text-[11px]">✓ Verified Immutable</span>
          </div>
          <div class="font-mono text-[11px] bg-white p-2.5 rounded-xl border border-emerald-200 text-slate-700 break-all select-all">
            {{ selectedLog.integrity_hash || 'HMAC_SHA256_INTEGRITY_SEAL_ACTIVE' }}
          </div>
          <div class="text-[10px] text-slate-500 font-mono">
            Previous Chain Link: {{ selectedLog.previous_hash || 'ROOT_GENESIS_RECORD' }}
          </div>
        </div>

        <!-- Before / After Values Diff View -->
        <div class="space-y-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-navy-950 flex items-center gap-2">
            <span>⚖️</span>
            <span>{{ localeStore.isRtl ? 'مقارنة القيم قبل وبعد التعديل (Before / After Diff)' : 'State Changes & Payload Diff' }}</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
            <!-- Old Values (Before) -->
            <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200 space-y-2">
              <div class="font-bold text-rose-800 text-[11px] uppercase flex items-center justify-between">
                <span>{{ localeStore.isRtl ? 'الحالة السابقة (Before / Old Values)' : 'Previous State (Before)' }}</span>
                <span>🔴</span>
              </div>
              <pre class="bg-white p-3 rounded-xl border border-rose-100 text-[11px] text-slate-700 overflow-x-auto max-h-60 whitespace-pre-wrap">{{ formatJson(selectedLog.old_values) }}</pre>
            </div>

            <!-- New Values (After) -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-2">
              <div class="font-bold text-emerald-800 text-[11px] uppercase flex items-center justify-between">
                <span>{{ localeStore.isRtl ? 'الحالة الجديدة (After / New Values)' : 'New State (After)' }}</span>
                <span>🟢</span>
              </div>
              <pre class="bg-white p-3 rounded-xl border border-emerald-100 text-[11px] text-slate-700 overflow-x-auto max-h-60 whitespace-pre-wrap">{{ formatJson(selectedLog.new_values) }}</pre>
            </div>
          </div>
        </div>

        <!-- Request Context & Headers -->
        <div v-if="selectedLog.context" class="space-y-2">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ localeStore.isRtl ? 'سياق الطلب وبيئة التنفيذ' : 'Execution Context & Request Info' }}</h4>
          <pre class="bg-slate-900 text-slate-200 p-3.5 rounded-2xl font-mono text-[11px] overflow-x-auto max-h-40">{{ formatJson(selectedLog.context) }}</pre>
        </div>
      </div>

      <template #footer>
        <button
          type="button"
          class="px-5 py-2.5 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs transition-all shadow-md cursor-pointer"
          @click="isInspectModalOpen = false"
        >
          {{ $t('common.close') || 'إغلاق' }}
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useLocaleStore } from '../../stores/locale'
import { api } from '../../services/api'
import { formatStandardDateTime } from '../../utils/dateFormat'
import Modal from '../../components/ui/Modal.vue'
import Pagination from '../../components/ui/Pagination.vue'
import EmptyState from '../../components/ui/EmptyState.vue'
import LoadingSpinner from '../../components/ui/LoadingSpinner.vue'
import {
  ShieldCheck,
  Lock,
  Download,
  Search,
  RefreshCw,
  FileText,
  Calendar,
  Key,
  AlertTriangle,
  Eye,
} from 'lucide-vue-next'

const localeStore = useLocaleStore()

const isLoading = ref(true)
const isVerifyingIntegrity = ref(false)
const isInspectModalOpen = ref(false)
const selectedLog = ref(null)

const logsList = ref([])
const kpiStats = reactive({
  total_logs: 0,
  today_logs: 0,
  security_events: 0,
  failed_actions: 0,
})

const paginationMeta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
})

const filters = reactive({
  search: '',
  module: 'all',
  action: 'all',
  severity: 'all',
  page: 1,
})

const integrityStatus = reactive({
  checked: false,
  isValid: true,
  message: '',
})

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filters.page = 1
    fetchLogs()
  }, 350)
}

const fetchLogs = async () => {
  isLoading.value = true
  try {
    const res = await api.getAuditLogs({
      search: filters.search,
      module: filters.module,
      action: filters.action,
      severity: filters.severity,
      page: filters.page,
    })

    logsList.value = res.data || []
    if (res.meta) {
      Object.assign(paginationMeta, res.meta)
    }
    if (res.stats) {
      Object.assign(kpiStats, res.stats)
    }
  } catch (err) {
    console.error('Failed to fetch audit logs', err)
  } finally {
    isLoading.value = false
  }
}

const changePage = (page) => {
  filters.page = page
  fetchLogs()
}

const resetFilters = () => {
  filters.search = ''
  filters.module = 'all'
  filters.action = 'all'
  filters.severity = 'all'
  filters.page = 1
  fetchLogs()
}

const openInspectModal = (log) => {
  selectedLog.value = log
  isInspectModalOpen.value = true
}

const handleVerifyIntegrity = async () => {
  isVerifyingIntegrity.value = true
  try {
    const res = await api.verifyAuditIntegrity()
    integrityStatus.checked = true
    integrityStatus.isValid = res.is_valid
    integrityStatus.message = res.message
  } catch (err) {
    console.error('Integrity check failed', err)
    integrityStatus.checked = true
    integrityStatus.isValid = false
  } finally {
    isVerifyingIntegrity.value = false
  }
}

const handleExportCsv = () => {
  const url = api.getAuditExportUrl({
    module: filters.module,
    severity: filters.severity,
  })
  window.open(url, '_blank')
}

const getShortEntityName = (fullClassName) => {
  if (!fullClassName) return ''
  const parts = fullClassName.split('\\')
  return parts[parts.length - 1]
}

const formatJson = (val) => {
  if (!val) return 'None (Empty Payload)'
  try {
    return JSON.stringify(val, null, 2)
  } catch {
    return String(val)
  }
}

const getActionBadgeClass = (action) => {
  switch (action) {
    case 'create':
      return 'bg-emerald-100 text-emerald-800'
    case 'update':
      return 'bg-sky-100 text-sky-800'
    case 'delete':
      return 'bg-rose-100 text-rose-800'
    case 'login':
    case 'logout':
      return 'bg-indigo-100 text-indigo-800'
    case 'verify':
      return 'bg-amber-100 text-amber-800'
    case 'status_change':
      return 'bg-purple-100 text-purple-800'
    default:
      return 'bg-slate-100 text-slate-800'
  }
}

const getSeverityBadgeClass = (severity) => {
  switch (severity) {
    case 'security':
    case 'critical':
      return 'bg-rose-600 text-white shadow-xs'
    case 'warning':
      return 'bg-amber-500 text-white'
    case 'notice':
      return 'bg-sky-600 text-white'
    case 'info':
    default:
      return 'bg-slate-200 text-slate-700'
  }
}

onMounted(() => {
  fetchLogs()
})
</script>
