---
noteId: "9730cd609d9211f1b3a56b6322f75d63"
tags: []
name: "ramssko-enterprise-component-enhancer"
description: "Use this skill whenever building, enhancing, or customizing reusable UI primitives (Data Grids, Forms, Dialogs, Filter Bars, Data Visualization cards) in the Ramssko frontend stack. Provides production-grade patterns for adding high-level professional features (slot-based custom rendering, dynamic schemas, accessible keyboard states, sophisticated empty/skeleton states, and advanced interactive UX) while strictly adhering to Ramssko Architecture Guardrails (INV-05, INV-06, etc.) and Performance Patterns. Trigger this proactively when asked to \"improve table UI\", \"add dynamic form capabilities\", \"build an advanced dialog\", or \"make UI components more flexible and customizable\"."

---

# Ramssko Enterprise Component Enhancer & Customization Standard

This skill defines the engineering rules for enhancing frontend components (Data Grids, Form Engines, Dialogs, Cards, and Filters) in the Ramssko platform. The core strategy is **Enhancement via Extension & Slot Composition**, ensuring 100% compliance with existing invariants (`INV-05`, `INV-06`, `INV-12`), Pinia store contracts (`INV-03`, `INV-10`), and zero performance regressions.

---

## 1. Fundamental Component Philosophy

1. **Extend, Never Fork (`INV-12`):** Never duplicate or replace `EnterpriseDataGrid` or `EnterpriseFormEngine` with parallel implementations. Enhance the central components in `resources/js/design-system/components/` by adding optional props, slots, and type-safe config builders.
2. **Preserve Contracts (`INV-05`, `INV-06`):** Any added capability MUST be purely additive. Existing code relying on mandatory props (e.g., `columns`, `data`, `total` on `EnterpriseDataGrid`) must continue working without modifications.
3. **Mechanics vs. Semantics Separation:** 
   - *Mechanics:* Base layout, sorting handlers, scroll-lock, event emissions, A11y.
   - *Semantics (Customization):* Slot templates (`#cell(key)`, `#field(key)`, `#action(name)`), cell render formatters (Badge, Currency, Date, Status), and design-token-driven styles.
4. **Zero-Mutation Reactive Pure Operations:** All internal transformations (e.g. column reordering, custom cell transformations) must return new objects/arrays instead of mutating props or Pinia-sourced objects (Performance Pattern #3).

---

## 2. Data Grid Enhancement Guidelines (`EnterpriseDataGrid`)

When adding advanced capabilities to lists and data tables, follow these enhancement patterns:

### A. Slot-Driven Cell & Header Customization
Provide Granular Slot Injections with strict fallback defaults:
```vue
<!-- Standard pattern for EnterpriseDataGrid cell customization -->
<template v-for="col in columns" :key="col.key" #[`cell-${col.key}`]="slotProps">
  <slot :name="`cell-${col.key}`" v-bind="slotProps">
    <!-- Default Semantic Formatter fallback -->
    <component 
      :is="resolveCellFormatter(col.type)" 
      :value="slotProps.value" 
      :config="col.formatterConfig" 
    />
  </slot>
</template>

```

### B. Standardized Semantic Cell Formatters

Implement light, reusable cell semantics inside `resources/js/design-system/components/crud/formatters/`:

* `BadgeCell.vue`: Semantic status tags (Success, Warning, Danger, Neutral) matching design tokens.
* `CurrencyCell.vue`: Locale-aware currency formatting with RTL alignment support.
* `DateCell.vue`: Relative/Absolute humanized date formatting.
* `BooleanCell.vue`: Accessible status icon indicators (Check/Cross) with dark mode contrast.

### C. Advanced State Handling (Loading, Empty, Error)

Every enhanced data grid MUST support three explicit non-happy path states:

1. **Skeleton Loading:** Render O(visible items) structural skeleton rows during initial fetch (never layout-shifting spinners for full tables).
2. **Context-Aware Empty State:** Differentiate between "No data created yet" (with primary action slot) and "No results matching filters" (with clear filters trigger).
3. **Inline Error State:** Render standard non-disruptive inline error banners with retry handlers for failed refetches.

### D. Typed Column Schema Builders (TypeScript Generics)

Enforce type safety for custom columns (Performance Pattern #11):

```ts
// Use generic helper to generate type-safe grid column definitions
export function createColumnHelper<T Record<string, any extends>>() {
  return {
    column: <K T extends keyof>(config: ColumnDef<T, K>): ColumnDef<T, K> => config,
  }
}

```

---

## 3. Form Engine Enhancement Guidelines (`EnterpriseFormEngine`)

When scaling forms for complex multi-section or dynamic schema workflows:

### A. Schema-Driven Section & Field Composition

Forms should accept a structured configuration array while allowing full slot overrides per field:

```ts
export interface FormFieldDef {
  key: string
  label: string
  component: 'text' | 'select' | 'async-select' | 'checkbox' | 'rich-text' | 'file-upload'
  placeholder?: string
  rules?: any
  colSpan?: 1 | 2 | 3 | 4 | 12 // Grid layout alignment
}

```

### B. Defensive Form State & Validation Sync

* **Backend Sync:** Direct pass-through of Laravel validation errors from props to field error messages.
* **Unsaved Changes (Dirty Tracking):** Expose a `isDirty` reactive state from form composables to trigger navigation guards.
* **Async Select Placeholders:** Always provide an explicit "Select..." option with a `null`/`empty` value to prevent silent accidental first-option selection.

### C. Custom Field Slot Injection

Allow consuming pages to seamlessly override specific inputs without rewriting the surrounding label, grid wrapping, or error display:

```vue
<template v-for="field in section.fields" :key="field.key">
  <div :class="`col-span-${field.colSpan || 12}`">
    <label>{{ field.label }}</label>
    <slot :name="`field-${field.key}`" :field="field" :model-value="form[field.key]">
      <!-- Default Component Input -->
      <EnterpriseInput :error="errors[field.key]" v-model="form[field.key]"/>
    </slot>
    <span v-if="errors[field.key]" class="field-error">{{ errors[field.key] }}</span>
  </div>
</template>

```

---

## 4. Dialog & Modal Customization Rules (`EnterpriseDialog`)

### A. Strict Contract Alignment (`INV-06`)

All modal enhancements MUST preserve the `:open` prop and `@close` event binding.

### B. Enhanced Modal Features Strategy

* **Focus Trapping & Focus Return:** Ensure keyboard focus moves inside the dialog on mount and returns to the triggering element on unmount (A11y rule).
* **Body Scroll Lock Integration:** Must use the shared `useBodyScrollLock()` reference-counting composable (Performance Pattern #7) to prevent breaking background scroll when nesting dialogs.
* **Destructive Action Wrappers:** Use a specialized `EnterpriseConfirmDialog.vue` (built on top of `EnterpriseDialog`) for delete/deactivate confirmations, featuring non-dismissible backdrop modes for safety.

---

## 5. Performance & Architectural Safety Matrix for Enhancements

| Enhancement Intent | Anti-Pattern to AVOID | Required Standard Implementation |
| --- | --- | --- |
| **Clicking a Row Action Menu** | Attaching `window.addEventListener('click')` inside row component | Register listener via `useGlobalClick()` composable |
| **Grid Active/Selected Rows Check** | Calling `selectedArray.includes(id)` on every render loop | Pre-compute a `Set` in a `computed()` ref (`selectedSet.has(id)`) |
| **Theme-aware Cell Styles** | Reading hardcoded CSS variables or writing inline DOM styles | Use design token CSS variables (`var(--color-surface)`) & `useTheme()` |
| **Navigation from Grid Row** | Using `<a href="/target">` or `window.location.href` | Use `<Link href="...">` or `router.visit()` via Inertia |
| **Filter Modification** | Mutating input parameter properties (`filter.active = true`) | Return a new object (`{ ...filter, active: true }`) |

---

## 6. Pre-Flight Checklist for Component Enhancements

Before completing any component enhancement task, verify:

* [ ] **Invariant Check:** Does `EnterpriseDataGrid` still satisfy `INV-05`? Does `EnterpriseDialog` satisfy `INV-06`?
* [ ] **Directory Placement:** Are shared formatters and component extensions placed in `resources/js/design-system/components/`?
* [ ] **Slot Naming:** Are slot names intuitive and scoped (`#cell-key`, `#field-key`, `#header-key`)?
* [ ] **A11y & Directionality:** Does the enhanced component mirror correctly in RTL mode, and are all interactive controls keyboard-operable?
* [ ] **Theme Uniformity:** Does the component automatically respect light/dark modes using standard design tokens (`tokens.css`) via `useTheme()`?
* [ ] **Performance Pass:** Are zero direct `window` listeners attached per row, and are all data transformations pure?