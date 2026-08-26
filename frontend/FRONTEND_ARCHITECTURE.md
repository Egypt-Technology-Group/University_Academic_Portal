---
noteId: "5a452510a0e211f19e622d09c95e7ea1"
tags: []

---

# University Academic Portal — Frontend Architecture & Component Standards
**Architecture Version:** 2.0 (Self-Describing & Enforced)  
**Applies To:** All Frontend Engineers & AI Agents

This specification is the single source of truth for the frontend architecture of the University Academic Portal (EgyiTech). Every agent or developer must consult and follow this document prior to creating or modifying frontend components, stores, utilities, or views.

---

## 1. Core Architectural Invariants (Protected Rules)

The following invariants MUST NOT be violated or duplicated under any circumstances:

| Invariant ID | Architecture Rule | Mandate & Rational |
| :--- | :--- | :--- |
| **INV-01: UI Primitives Single Source** | All reusable UI elements must reside exclusively in `frontend/src/components/ui/`. | Never recreate local ad-hoc buttons, modals, badges, inputs, or empty states inside views. Always reuse and extend the central primitives. |
| **INV-02: Date & Time Localization** | All date, time, range, and relative timestamp displays MUST use `frontend/src/utils/dateFormat.js`. | Never display raw ISO timestamps (`2026-09-09T...`) or hand-roll inline date formatters. |
| **INV-03: Locale & RTL Invariant** | Dynamic language support is powered by `useLocaleStore()` (`ar` / `en`). | All bilingual text attributes must be translated via `getTranslated(entity.field, localeStore.locale)` or `$t(...)`. Layout directionality (`rtl`/`ltr`) is managed automatically. |
| **INV-04: Form Controls Invariant** | Form fields and dynamic schemas must be built using `EnterpriseFormField.vue` and `EnterpriseFormEngine.vue`. | Do not write custom repetitive `<input>` / `<select>` boilerplate with inline error divs. |
| **INV-05: Modal Contract** | All dialogs must use `Modal.vue` (`v-model="isOpen"`, `:title`, optional `#footer` slot). | Preserves backdrop focus, keyboard escape listeners, and backdrop click-outside safety. |
| **INV-06: Empty State Invariant** | All tables, search lists, and data grids without records must render `<EmptyState />`. | Never hand-roll ad-hoc SVG icon + warning text divs. |

---

## 2. Directory Organization Standards

```
frontend/src/
├── assets/                  # Static styles, global fonts, tailwind tokens
├── components/
│   ├── layout/              # Shell layouts (AdminLayout.vue, Navbar.vue, Footer.vue)
│   └── ui/                  # REUSABLE UI PRIMITIVES (Single source of truth)
│       ├── EnterpriseFormField.vue   # Atomic multi-type field with preview & errors
│       ├── EnterpriseFormEngine.vue  # Declarative schema-driven form composer
│       ├── StatusFilterTabs.vue      # Filter tabs with KPI count badges
│       ├── AuditTimeline.vue         # Event timeline & audit logs
│       ├── EmptyState.vue            # Accessible, slot-extensible empty fallback
│       ├── Modal.vue                 # Accessible dialog & modal wrapper
│       ├── Button.vue                # Standardized action buttons
│       ├── Badge.vue                 # Status and category badges
│       ├── Card.vue                  # Elevation container
│       ├── Breadcrumbs.vue           # Navigation trails
│       ├── LoadingSpinner.vue        # Centered loading states
│       └── Pagination.vue            # Table pagination controls
├── router/                  # Vue Router configuration
├── services/
│   └── api.js               # Centralized Axios API client & translation helpers
├── stores/
│   ├── auth.js              # Pinia authentication & token store
│   └── locale.js            # Pinia locale & RTL direction store
├── utils/
│   └── dateFormat.js        # Standardized locale-aware date/time formatting
└── views/
    ├── admin/               # Administrative consoles and management dashboards
    └── *.vue                # Public-facing views & student service portals
```

---

## 3. Catalog of Reusable Components & Usage Guide

### A. `EnterpriseFormField.vue`
**When to use:** In any modal, form, or filter panel requiring input fields.
**Features:** Supports `text`, `number`, `email`, `password`, `tel`, `url`, `date`, `time`, `textarea`, `select`, `checkbox`, and `image`/`file` upload with inline image preview.

```vue
<EnterpriseFormField
  v-model="form.title_ar"
  type="text"
  :label="$t('admin.labelTitleAr')"
  required
  col-span="6"
  placeholder="العنوان بالعربية..."
  :error-message="errors.title_ar"
/>

<!-- Image Upload with Instant Preview -->
<EnterpriseFormField
  type="image"
  label="صورة الغلاف"
  col-span="12"
  :preview-url="imagePreview || form.cover_url"
  button-text="اختيار صورة"
  @file-selected="handleFileSelect"
/>
```

---

### B. `EnterpriseFormEngine.vue`
**When to use:** Complex, multi-section, or dynamic forms configured via a declarative JSON schema.
**Features:** Automatic section grid generation, conditional field visibility (`showIf`/`vIf`/`dependsOn`), slot overrides (`#field-[key]`), and direct error binding.

```vue
<EnterpriseFormEngine
  v-model="formData"
  :schema="formSchema"
  :errors="backendErrors"
  :is-submitting="isSaving"
  show-actions
  @submit="handleSave"
  @cancel="closeModal"
/>
```

---

### C. `EmptyState.vue`
**When to use:** Whenever a table, query filter, search result, or list returns zero records.

```vue
<EmptyState
  v-if="items.length === 0"
  :title="$t('common.noData')"
  :description="$t('common.noDataDesc')"
>
  <template #action>
    <Button @click="createNew">{{ $t('common.addNew') }}</Button>
  </template>
</EmptyState>
```

---

### D. `StatusFilterTabs.vue`
**When to use:** Multi-status filtering headers (e.g. Applications Queue, Service Requests, Documents).

```vue
<StatusFilterTabs
  v-model="activeFilter"
  :status-tabs="[
    { key: 'all', label: 'الكل' },
    { key: 'pending', label: 'قيد الانتظار' },
    { key: 'approved', label: 'مقبول' }
  ]"
  :status-counts="countsObject"
/>
```

---

### E. `AuditTimeline.vue`
**When to use:** Application decision logs, request tracking histories, and audit records.

```vue
<AuditTimeline :timeline="record.timeline" />
```

---

### F. `dateFormat.js` Utilities
**When to use:** All date and time renderings.

```javascript
import { 
  formatStandardDate, 
  formatStandardDateTime, 
  formatStandardTime, 
  formatTimeRange,
  getLocalizedMonth,
  getLocalizedDay 
} from '@/utils/dateFormat'

// Examples:
formatStandardDate('2026-09-09', 'ar')          // "٩ سبتمبر ٢٠٢٦"
formatStandardDateTime('2026-09-09 10:30', 'en') // "09 Sep 2026, 10:30 AM"
formatTimeRange('09:00', '12:00', 'ar')         // "٠٩:٠٠ ص - ١٢:٠٠ م"
```

---

## 4. Agent Implementation Protocol & Workflow

Before writing any new code:
1. **Search Existing Components:** Check `frontend/src/components/ui/` first.
2. **Never Duplicate Common UI Elements:** Always use `EnterpriseFormField`, `EmptyState`, `StatusFilterTabs`, `AuditTimeline`, `Modal`, `Button`, or `Badge`.
3. **Verify Build:** Always run `npm run build` from `frontend/` to confirm 0 compilation errors before marking any task as complete.
