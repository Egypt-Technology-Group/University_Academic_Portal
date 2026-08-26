# Modular Plugin-Style Architecture Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the monolithic Laravel + Vue architecture of the University Academic Portal into a production-grade, decoupled, plug-in style modular ecosystem where every domain (Admissions, Academic Structure, Academic Services, CMS, Events, Regulations & Documents, Results, Audit Trail, Settings) is an autonomous module that can be independently enabled/disabled, configured, versioned, migrated, routed, and tested without breaking the host application or neighboring modules.

**Architecture:** 
- **Backend (Laravel Module Engine)**: Hexagonal/Modular monolith architecture leveraging a centralized `ModuleManager` service provider, dynamic module registries, isolated database schemas/prefixing, isolated routes (`/api/v1/modules/{module}`), dynamic event contracts, and policy-driven feature-flag evaluation.
- **Frontend (Vue 3 Micro-Module Engine)**: Centralized `ModuleRegistry` with asynchronous route lazy-loading, dynamic navigation sidebar injection, scoped Pinia stores, isolated module configuration views, feature-flag guards, and unified fallback boundaries.

**Tech Stack:**
- Backend: Laravel 11.x, PHP 8.2+, MySQL / SQLite (isolated table prefixes / schema partitions), Laravel Event-Driven Contracts.
- Frontend: Vue 3 (Composition API, `<script setup>`), Pinia, Vue Router 4 (Dynamic `router.addRoute()`), Vite (Code Splitting / Dynamic Chunking), Tailwind CSS.

## Global Constraints

- Zero coupling between sibling modules: Modules MUST communicate exclusively through contracts, domain events, or the core `ModuleManager` service.
- Disabling or removing any module MUST leave the rest of the application fully functional, with its navigation links, routes, API endpoints, and database queries dynamically suspended.
- Core Host System provides shared primitives only: Authentication & RBAC, Internationalization (i18n), Flash Toast Hub, Dialog Modal Provider, Theme & CSS Variables, Base Layouts, and Audit Logging Infrastructure.
- Full RTL/LTR and Arabic/English language integrity preserved across all module registration manifests.

---

## Existing System Audit & Domain Decomposition

### Identified Independent Modules:
1. **Core / Host Platform** (`core`): Auth, User management, Roles & Permissions, Dynamic Site Branding & Settings, Audit Logging, Dialog/Toast infrastructure.
2. **Admissions & Applications** (`admissions`): Admission cycles, Multi-step application submission, application review workflow, missing document request dispatch, applicant portal timeline.
3. **Academic Structure** (`academic-structure`): Colleges/institutes, scientific departments, degree programs, faculty profiles, curricula matrices.
4. **Academic & Student Services** (`academic-services`): Student service requests (enrollment certificates, transcripts), study plans, exam schedules, digitally signed official statements.
5. **Content Management System (CMS)** (`cms`): News articles, media categories, urgent announcements, public notifications.
6. **Campus Events & Calendar** (`events`): Campus events, venue coordination, online seat reservation, registration tracking.
7. **Document Repository & Bylaws** (`documents`): Academic regulations, student bylaws, downloadable PDF matrices, download counter analytics.
8. **Student Academic Results** (`results`): Student transcripts, course grade breakdown, term GPA / Cumulative GPA calculations.

---

## Implementation Tasks

### Task 1: Backend Modular Core Engine & Module Manager
**Files:**
- Create: `backend/app/Core/Contracts/ModuleInterface.php`
- Create: `backend/app/Core/ModuleManager.php`
- Create: `backend/app/Core/Providers/ModuleServiceProvider.php`
- Create: `backend/config/modules.php`
- Test: `backend/tests/Feature/Core/ModuleManagerTest.php`

**Interfaces:**
- Consumes: Laravel Service Container, DB Facade.
- Produces: `ModuleManager::register()`, `ModuleManager::isEnabled($name)`, `ModuleManager::getRegisteredModules()`, `ModuleManager::getNavItems()`.

- [ ] **Step 1: Write test for ModuleManager registry and enable/disable toggling**
- [ ] **Step 2: Create `ModuleInterface` contract defining manifests, permissions, routes, and migrations**
- [ ] **Step 3: Implement `ModuleManager` with caching and dynamic module discovery**
- [ ] **Step 4: Register `ModuleServiceProvider` in Laravel config**
- [ ] **Step 5: Run tests and verify core module registry functions**

---

### Task 2: Backend Feature Flag & Module Lifecycle Policy
**Files:**
- Create: `backend/app/Core/Middleware/EnsureModuleEnabled.php`
- Create: `backend/app/Http/Controllers/Api/ModuleManagementController.php`
- Create: `backend/routes/core_api.php`
- Test: `backend/tests/Feature/Core/ModuleMiddlewareTest.php`

**Interfaces:**
- Consumes: `ModuleManager`
- Produces: Middleware `module.enabled:{module_slug}`, API endpoints `GET /api/v1/modules`, `PATCH /api/v1/modules/{module}/toggle`.

- [ ] **Step 1: Write tests for disabled module route blocking (404/503 response)**
- [ ] **Step 2: Implement `EnsureModuleEnabled` route middleware**
- [ ] **Step 3: Create `ModuleManagementController` allowing superadmins to toggle modules dynamically**
- [ ] **Step 4: Mount module endpoints in API pipeline**
- [ ] **Step 5: Verify tests pass**

---

### Task 3: Backend Domain Modularization (Phase 1: Admissions & Academic Structure)
**Files:**
- Create: `backend/app/Modules/Admissions/AdmissionsModule.php`
- Create: `backend/app/Modules/Admissions/Routes/api.php`
- Create: `backend/app/Modules/AcademicStructure/AcademicStructureModule.php`
- Create: `backend/app/Modules/AcademicStructure/Routes/api.php`
- Modify: `backend/routes/api.php`
- Test: `backend/tests/Feature/Modules/AdmissionsModuleTest.php`

**Interfaces:**
- Consumes: `ModuleInterface`, `EnsureModuleEnabled`
- Produces: Domain-isolated route groups and event contracts.

- [ ] **Step 1: Move Admissions controllers, models, and migrations into `app/Modules/Admissions` namespace**
- [ ] **Step 2: Define `AdmissionsModule` class implementing `ModuleInterface`**
- [ ] **Step 3: Move Academic Structure controllers and models into `app/Modules/AcademicStructure` namespace**
- [ ] **Step 4: Define `AcademicStructureModule` class implementing `ModuleInterface`**
- [ ] **Step 5: Run PHPUnit tests to verify route isolation and integrity**

---

### Task 4: Backend Domain Modularization (Phase 2: Academic Services, CMS, Events, Documents, Results)
**Files:**
- Create: `backend/app/Modules/AcademicServices/AcademicServicesModule.php`
- Create: `backend/app/Modules/Cms/CmsModule.php`
- Create: `backend/app/Modules/Events/EventsModule.php`
- Create: `backend/app/Modules/Documents/DocumentsModule.php`
- Create: `backend/app/Modules/Results/ResultsModule.php`
- Modify: `backend/routes/api.php`
- Test: `backend/tests/Feature/Modules/AllModulesTest.php`

**Interfaces:**
- Consumes: `ModuleManager`
- Produces: Cleanly separated domain packages.

- [ ] **Step 1: Modularize Academic Services & Exam Schedules into `app/Modules/AcademicServices`**
- [ ] **Step 2: Modularize CMS (News & Announcements) into `app/Modules/Cms`**
- [ ] **Step 3: Modularize Events & Registration into `app/Modules/Events`**
- [ ] **Step 4: Modularize Document Repository & Bylaws into `app/Modules/Documents`**
- [ ] **Step 5: Modularize Student Results & Transcript Engine into `app/Modules/Results`**
- [ ] **Step 6: Run full backend test suite to ensure 100% regression-free operation**

---

### Task 5: Frontend Micro-Module Registry & Dynamic Routing
**Files:**
- Create: `frontend/src/core/modules/moduleRegistry.js`
- Create: `frontend/src/core/modules/types.js`
- Create: `frontend/src/stores/modules.js`
- Modify: `frontend/src/router/index.js`
- Test: `frontend/tests/moduleRegistry.test.js`

**Interfaces:**
- Consumes: Vue Router, Pinia
- Produces: `moduleRegistry.register()`, `moduleRegistry.getEnabledRoutes()`, `moduleRegistry.getNavigationMenuItems()`.

- [ ] **Step 1: Write tests for client module registry and dynamic route insertion**
- [ ] **Step 2: Build `moduleRegistry.js` with lifecycle hooks (`install`, `onEnable`, `onDisable`)**
- [ ] **Step 3: Create `useModulesStore` in Pinia to sync enabled modules with backend manifest**
- [ ] **Step 4: Update `router/index.js` to dynamically add active module routes via `router.addRoute()`**
- [ ] **Step 5: Verify routing and fallback when a module route is accessed while disabled**

---

### Task 6: Frontend Module Packaging & Encapsulation
**Files:**
- Create: `frontend/src/modules/admissions/index.js`
- Create: `frontend/src/modules/academic-structure/index.js`
- Create: `frontend/src/modules/academic-services/index.js`
- Create: `frontend/src/modules/cms/index.js`
- Create: `frontend/src/modules/events/index.js`
- Create: `frontend/src/modules/documents/index.js`
- Create: `frontend/src/modules/results/index.js`
- Modify: `frontend/src/components/layout/AdminLayout.vue`
- Modify: `frontend/src/components/layout/Navbar.vue`

**Interfaces:**
- Consumes: `moduleRegistry`
- Produces: Decoupled plug-and-play frontend modules.

- [ ] **Step 1: Package Admissions module with routes, navigation item descriptor, and permissions**
- [ ] **Step 2: Package Academic Structure module**
- [ ] **Step 3: Package Academic Services module**
- [ ] **Step 4: Package CMS, Events, Documents, and Results modules**
- [ ] **Step 5: Refactor `AdminLayout.vue` and `Navbar.vue` to compute nav menus dynamically from `moduleRegistry`**
- [ ] **Step 6: Run `npm run build` and verify bundle chunks are split per module**

---

### Task 7: Admin Module Center UI & System Orchestration
**Files:**
- Create: `frontend/src/views/admin/AdminModulesView.vue`
- Modify: `frontend/src/components/layout/AdminLayout.vue`
- Test: `frontend/tests/AdminModulesView.test.js`

**Interfaces:**
- Consumes: `useModulesStore`, `useDialog`, `useToast`
- Produces: Administrative management panel for live module enabling, disabling, health metrics, and configuration.

- [ ] **Step 1: Build `AdminModulesView.vue` with toggle switches, status badges, and description cards**
- [ ] **Step 2: Connect toggle actions with confirmation dialogs and backend API**
- [ ] **Step 3: Verify dynamic sidebar updates and route protection upon toggling a module**
- [ ] **Step 4: Execute end-to-end build and smoke test all active modules**

---

## Phased Migration Strategy & Risk Mitigation

1. **Phase 1 (Foundational Infrastructure)**: Establish Backend `ModuleManager` and Frontend `ModuleRegistry` in non-breaking mode.
2. **Phase 2 (Parallel Routing & Dual Registration)**: Route existing views through both legacy paths and module registry.
3. **Phase 3 (Encapsulation & Namespace Migration)**: Migrate domain models, controllers, and views into respective `/modules/*` namespaces.
4. **Phase 4 (Admin Live Control & Final Verification)**: Activate the live Module Management Center and verify isolated disabling across all 8 modules.

## Acceptance Criteria
- [ ] Any module can be turned OFF from the Admin Module Center; its navigation items immediately vanish, and visiting its URLs triggers an authorized graceful fallback.
- [ ] All 8 domain modules (`admissions`, `academic-structure`, `academic-services`, `cms`, `events`, `documents`, `results`, `audit-trail`) operate with zero direct cross-imports.
- [ ] Build passes cleanly with granular Vite code-split chunks for each module.
- [ ] Full RTL/LTR and bilingual internationalization maintained throughout.
