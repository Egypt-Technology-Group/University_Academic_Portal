---
noteId: "972ed1909d9211f1b3a56b6322f75d63"
tags: []
name: "ramssko-architecture-guardrails"
description: "Use this skill whenever creating, renaming, or restructuring ANY file in the Ramssko frontend (Vue 3 + TypeScript + Inertia.js + Laravel) — new pages/surfaces, new Pinia stores, new design-system components, new layouts, new composables, or changes to navigation, theming, the data grid, or dialogs. Also use when reviewing whether a proposed change is safe. Trigger this proactively even if the user just says \"add a page for X\" or \"create a new component\" without mentioning architecture — the goal is to make sure new code follows the project's established structure, naming conventions, and protected contracts instead of inventing a new pattern. Do NOT use this for one-off styling tweaks with no structural implications."

---

# Ramssko Frontend Architecture Guardrails

Ramssko is a Vue 3 + TypeScript + Inertia.js + Laravel enterprise SaaS. This skill exists so that new code added by an agent follows the *existing* architecture rather than introducing a competing pattern. Read this before creating or restructuring any frontend file.

## Protected invariants — never change these without an explicit, deliberate migration

| ID | Contract | Why |
|----|----------|-----|
| INV-01 | `PlatformLayout → MasterShell → slot` nesting | All platform pages depend on this exact hierarchy |
| INV-02 | `TenantLayout → MasterShell → slot` nesting | All tenant pages depend on this exact hierarchy |
| INV-03 | `useThemeStore()` is the **single** source of theme truth | Everything reads theme from here; don't add a second theme source |
| INV-04 | `setupInertiaStateBridge()` is the **only** Inertia→Pinia bridge | Store hydration depends on this one function |
| INV-05 | `EnterpriseDataGrid` prop contract: `columns`, `data`, `total`, pagination events | Every page using the grid depends on this exact shape |
| INV-06 | `EnterpriseDialog`: `open` prop + `close` emit | All dialog instances depend on this contract |
| INV-07 | `useNavigation()` returns `{ groups, rawGroups }` | Sidebar and CommandPalette both consume this exact shape |
| INV-08 | `NavigationStore.groups` is `NavigationGroup[]` | Sidebar, CommandPalette, useNavigation all read this structure |
| INV-09 | `ThemeEngine.applyTheme()` / `ThemeEngine.setMode()` static API | ThemeStore calls these directly; don't add parallel theme-application paths |
| INV-10 | Pinia store ids: `'theme'`, `'sidebar'`, `'navigation'`, `'user'`, `'tenant'`, `'surface'` | Used for hydration + DevTools; don't rename or duplicate |
| INV-11 | `surfaces/` folder structure + page naming | `app.ts`'s page resolver depends on these exact paths |
| INV-12 | `design-system/components/` component names | Imported throughout every surface/module — renaming breaks imports silently at build time only |

**Before adding a new store, layout, or shared component:** check this table first. If what you need looks like it overlaps with an existing invariant (e.g. "I need theme-like global config" → that's INV-03, extend `useThemeStore`, don't create a second store), extend the existing contract instead of creating a parallel one.

## Where new code goes

- **New page** → `resources/js/surfaces/<surface>/<Module>/<Page>.vue`, following the exact naming/nesting convention of sibling pages already in that surface. Check an existing page in the same surface before creating a new one — mirror its layout declaration style, its use of `EnterpriseDataGrid`/`EnterpriseFormEngine`, and its import ordering.
- **New shared/reusable UI element** → `resources/js/design-system/components/`. If it's CRUD-table-related, it belongs alongside `EnterpriseDataGrid`/`EnterpriseRowActions` under `design-system/components/crud/`.
- **New cross-cutting reactive logic** (a "how do I get X" hook usable from multiple components) → `resources/js/composables/`, following the `useX` naming convention (`useNavigation`, `useTheme`, `useUsers`, `useGlobalClick`, `useGlobalKeydown`, `useBodyScrollLock`, etc.).
- **New global app state** → a Pinia store under the existing store id list (INV-10). Do not introduce ad-hoc global reactive state (e.g. a bare exported `ref()` singleton) as an alternative to Pinia — it fragments state management and won't hydrate via `setupInertiaStateBridge`.
- **New pure logic/algorithms** (filtering, resolving, computing) not tied to Vue reactivity → a plain TS class/module under `resources/js/core/`, mirroring `navigation-resolver.ts` / `theme-engine.ts`: static or pure functions, no direct DOM/store coupling, called *from* composables/stores rather than reaching into them.

## Conventions to replicate in new code

1. **Navigation** — any new nav-triggering element (link, button, menu item) must go through Inertia's `<Link>`/`router.visit()` (see the `ramssko-performance-patterns` skill for the full rule). `EnterpriseHeader.vue`'s use of `Link as InertiaLink` is the reference pattern to copy for aliasing if needed.
2. **Data tables** — any new list/table view should reuse `EnterpriseDataGrid` with its existing prop contract (INV-05) rather than hand-rolling a table. Row-level actions go through `EnterpriseRowActions`, whose "click outside" behavior must be registered via the shared `useGlobalClick` composable (never a direct listener — see performance-patterns skill rule #2).
3. **Dialogs/modals** — reuse `EnterpriseDialog` with the `open`/`close` contract (INV-06). If the new dialog needs to coexist with others (e.g. a module with multiple CRUD dialogs like Users has create/edit/view/delete), it must use the shared `useBodyScrollLock` composable, not direct `document.body.style.overflow` manipulation.
4. **Forms** — build on `EnterpriseFormEngine` for multi-section forms rather than a bespoke form component, so validation/section patterns stay consistent.
5. **Theming** — any new component that needs theme-derived values should consume `useTheme()` (which itself is backed by `useThemeStore()`/`storeToRefs`), never read `ThemeEngine` or CSS variables directly, and never introduce a second theme composable.
6. **Store hydration** — any new page-load-driven state must be wired through the existing `setupInertiaStateBridge()` (INV-04) — either by adding a new field it maps into an existing/new Pinia store, not by having the page component read `usePage().props` directly and manually push into a store in `onMounted`.
7. **Permissions/licensing on navigation** — any new nav item with a `permission` or `licenseModule` guard must flow through `NavigationResolver` (`resources/js/core/navigation-resolver.ts`); that resolver must remain a **pure** function (return new objects, never mutate `item.children` — see performance-patterns rule #3), since navigation state is store-backed (INV-08).
8. **TypeScript on reusable components** — when extending `EnterpriseDataGrid`/`ColumnDef` usage to a new page, use the generic type parameter (`ColumnDef<T>`) rather than falling back to `any`, once generics are activated on the component (REM-17 pattern).

## When a request seems to require breaking an invariant

If a task genuinely seems to require, e.g., a second theme source, a different dialog contract, or a competing navigation store — stop and flag this explicitly to the user rather than silently introducing a parallel pattern. State which invariant (by ID) the request conflicts with and ask whether they want to extend the existing contract or deliberately branch from it.

## Quick checklist before finishing any structural change

- [ ] File placed in the correct directory per the "where new code goes" section above
- [ ] No protected invariant (INV-01 … INV-12) altered without explicit confirmation
- [ ] Reused existing shared components (`EnterpriseDataGrid`, `EnterpriseDialog`, `EnterpriseFormEngine`, `EnterpriseSidebar`/`EnterpriseCommandPalette` navigation patterns) instead of duplicating their behavior
- [ ] New composables/stores follow existing naming (`useX`, store ids from INV-10)
- [ ] Cross-checked against `ramssko-performance-patterns` skill for any navigation, listener, store-mutation, or global-resource code
