---
noteId: "9730f4709d9211f1b3a56b6322f75d63"
tags: []
name: "ramssko-performance-patterns"
description: "Use this skill whenever writing, editing, or reviewing ANY frontend code in a Vue 3 + TypeScript + Inertia.js + Pinia stack (this applies to the Ramssko project specifically, but the patterns generalize to any Inertia+Vue+Pinia SaaS app). Trigger this proactively for tasks like adding a new page, component, navigation link, dialog/modal, dropdown menu, data table/grid, form, pagination UI, theme/dark-mode logic, or any global event listener (click/keydown/scroll) — even if the user does not mention \"performance\" explicitly. This skill encodes the recurring performance anti-patterns found in this stack (full-page reloads instead of SPA navigation, N global listeners in v-for loops, mutating reactive store data, redundant computed refs, unbatched CSS writes) and the correct idiomatic fix for each, so new code is written correctly the first time instead of needing a later remediation pass."

---

# Vue 3 + Inertia.js + Pinia Performance Patterns

Purpose: prevent the reintroduction of known performance anti-patterns in this stack. Apply these rules **while writing new code**, not just when auditing. If you are about to write code that matches an "Anti-pattern" section below, use the "Always do instead" rule unprompted.

## 1. Navigation must always use Inertia, never the browser

**Anti-pattern:** `<a href="...">` for internal routes, or `window.location.href = url`, or `location.assign(...)`.

**Always do instead:**
- In templates: `<Link href="...">` from `@inertiajs/vue3` for any clickable internal navigation (sidebar items, breadcrumbs, cards, menu items).
- In script/programmatic navigation (command palettes, "go to" actions, after form submit, redirects triggered by code): `import { router } from '@inertiajs/vue3'; router.visit(url)`.
- `<Link>` renders as a real `<a>` tag, so accessibility and existing CSS classes still apply — there is never a reason to fall back to a plain `<a>` for an internal route.
- Only use a plain `<a>`/`<a target="_blank">` for genuinely external URLs or downloads.

**Why it matters:** a native `<a>`/`window.location` causes a full document reload — the entire Vue app, all Pinia store state, and the whole DOM are torn down and rebuilt. This silently defeats the reason Inertia.js is in the stack at all.

## 2. Never register a global listener (`window`/`document`) per component instance

**Anti-pattern:** any component that will be rendered in a `v-for` (row actions, cards, list items, per-item dropdowns/menus) calling `window.addEventListener('click', ...)`, `addEventListener('keydown', ...)`, or similar directly in `onMounted`.

**Always do instead:** route all such listeners through a shared singleton composable that keeps exactly one real listener on `window`/`document` and fans out to registered callbacks:

```ts
// composables/useGlobalClick.ts
const handlers = new Set<(e: MouseEvent) => void>()
let attached = false

function ensureAttached() {
  if (attached) return
  window.addEventListener('click', (e) => handlers.forEach(h => h(e)))
  attached = true
}

export function useGlobalClick(handler: (e: MouseEvent) => void) {
  ensureAttached()
  handlers.add(handler)
  onUnmounted(() => handlers.delete(handler))
}
```

Same pattern for `useGlobalKeydown`. Any "click outside to close" or global shortcut behavior in a per-row/per-item component MUST go through a composable like this, never a direct `window.addEventListener` in that component.

**Why it matters:** N rendered instances = N listeners, and every single click/keypress anywhere in the app then fires N handler invocations. This scales linearly with data size and becomes a measurable bottleneck at realistic enterprise row counts (50–200+).

## 3. Never mutate data that came from a Pinia store or from props

**Anti-pattern:** any filter/transform/resolver function that does `item.children = filtered` or otherwise reassigns a property on an object that was passed in by reference (from a store, from props, or from a shared constant).

**Always do instead:** filters/transforms must be pure — return new objects:

```ts
// ❌ mutates the original object
function filterItems(items) {
  return items.filter(passes).map(item => {
    item.children = filterItems(item.children) // mutation!
    return item
  })
}

// ✅ pure — returns new objects
function filterItems(items) {
  return items.filter(passes).map(item => ({
    ...item,
    children: item.children ? filterItems(item.children) : item.children,
  }))
}
```

Before writing any function that filters/transforms/decorates data sourced from a Pinia store, a Pinia `storeToRefs()` result, Inertia page props, or a module-level constant, check: does this function return new objects, or does it write back onto the input? If in doubt, spread (`{ ...obj }`) rather than reassign a property.

**Why it matters:** JS objects are references. Mutating them corrupts the store's source of truth silently — the bug doesn't show up on the first render, only after the second read/filter pass, making it very hard to trace later.

## 4. Don't wrap store state in redundant `computed()` at every call site

**Anti-pattern:** a composable (`useX()`) that is called from multiple components, and on every call creates fresh `computed(() => store.something)` wrappers around the same underlying store ref.

**Always do instead:**
- Use `storeToRefs(store)` to extract reactive refs from a Pinia store — this returns the *same* underlying ref/computed identity to every caller instead of creating a new computed per call site.
- If you need a derived value (e.g. `colors` derived from `theme`), define that as a `computed` **inside the store itself** (Pinia setup-store), not inside the consuming composable. The store's computed is created once and shared; a composable-level computed is recreated per call site.

**Why it matters:** with C call sites and K derived values per call, you get C×K live computed watchers all tracking the same source — all invalidated together on every state change, for no benefit over 1×K shared computeds.

## 5. Never `v-for` over a range proportional to unbounded data (pagination, long lists)

**Anti-pattern:** `v-for="p in totalPages"` where `totalPages` can be large (hundreds), with a `v-if` inside to decide whether each iteration actually renders anything.

**Always do instead:** compute the small, bounded array of what should actually display (e.g. a "windowed" pagination: first, last, current ± N, ellipsis markers) in a `computed`, and `v-for` over that small array only. The template loop must be O(visible items), never O(total items/pages).

## 6. Batch CSS custom-property / DOM style writes; don't do a call chain that doubles them

**Anti-pattern:** a "set theme" function that internally calls another function that itself writes the same properties again (e.g. `applyTheme()` internally calling `setMode()`, and the caller *also* calling both) — and/or writing each CSS var with a separate synchronous `style.setProperty()` call outside of a batching mechanism.

**Always do instead:**
- Each theming function should do its own distinct set of writes exactly once; the caller decides the sequence explicitly rather than functions silently calling each other.
- Wrap grouped `setProperty` calls in a single `requestAnimationFrame` callback (or build a `:root { ... }` string and swap a single `<style>` element's content) so the browser only recalculates styles once per theme change, not once per property.

## 7. Global singleton resources (body scroll lock, overlays) need reference counting, not boolean toggling

**Anti-pattern:** any component that can appear multiple times simultaneously (dialogs/modals) directly setting `document.body.style.overflow = 'hidden'` on open and `''` on close.

**Always do instead:** a shared composable with a module-level counter:

```ts
let lockCount = 0
export function useBodyScrollLock() {
  function lock() { if (++lockCount === 1) document.body.style.overflow = 'hidden' }
  function unlock() { if (--lockCount <= 0) { lockCount = 0; document.body.style.overflow = '' } }
  return { lock, unlock }
}
```
If a component that manages a global on/off DOM resource could ever have a sibling instance open at the same time, it must use reference counting, never a plain flag.

## 8. Prefer `Set`/`Map` lookups over `Array.includes`/`.find` in per-render, per-item checks

**Anti-pattern:** `openIds.value.includes(id)` or `isActive(item)` doing a `String.startsWith`/array scan, called once per rendered item, per render.

**Always do instead:** derive a `computed` `Set`/`Map` once (e.g. `computed(() => new Set(openIds.value))`), and have the template do `set.has(id)` lookups. Only recompute when the underlying source actually changes (e.g. keyed off `page.url`, not on every render).

## 9. Cache expensive per-item transforms keyed by object identity, not by re-deriving every time

**Anti-pattern:** a `computed` that maps a full array into richer view-model objects (e.g. parsing dates, deriving labels) on every reactive re-evaluation, even when the underlying array reference/individual items haven't changed.

**Always do instead:** use a module-level `WeakMap<RawItem, ViewModel>` cache keyed by the raw object reference; check cache before re-deriving. Since Inertia/Pinia typically replace whole arrays with new references on real data changes, stale cache entries are naturally garbage collected — no manual invalidation needed.

## 10. New page components should use Inertia persistent layouts when the project has adopted that pattern

If this codebase already uses Inertia's persistent-layout mechanism (layout declared via `defineOptions({ layout: ... })` rather than template-wrapping), follow that same pattern for any new page — do not wrap new pages in `<Layout>...</Layout>` in the template, as that causes the whole layout (sidebar, header, command palette) to remount on every navigation, undoing the benefit of SPA navigation from rule #1. Check an existing recent page in `resources/js/surfaces/` for the current convention before writing a new one.

## 11. Type generics on reusable components — activate them, don't default to `any`

When building or extending a generic reusable component (data grids, form builders, list components), prefer an explicit generic type parameter (`<script setup lang="ts" generic="T">`) with `data: T[]` and column/field defs typed against `T`, rather than typing props as `any[]`. This is zero runtime cost and prevents silent shape mismatches at every call site.

## Self-check before finishing any frontend change

Before considering a change complete, check it against this list:
- [ ] Any new internal navigation uses `<Link>` or `router.visit()`, never `<a href>`/`window.location`
- [ ] Any new listener added inside a `v-for`-rendered component goes through a shared singleton composable
- [ ] Any filter/transform function returns new objects rather than mutating inputs (store data, props, constants)
- [ ] Any new composable wrapping store state uses `storeToRefs`/store-level computeds, not per-call-site `computed()`
- [ ] Any new unbounded list rendering (pagination, long menus) computes a bounded "visible" subset first
- [ ] Any new global on/off DOM resource (scroll lock, overlay backdrop) is reference-counted if multiple instances can coexist
- [ ] Any new per-item active/open/selected check uses a precomputed `Set`/`Map`, not an array scan or string comparison repeated per item
