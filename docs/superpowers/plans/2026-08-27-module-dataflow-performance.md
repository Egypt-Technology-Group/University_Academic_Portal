---
noteId: "98685550a1ab11f1ba16875512ddc7d8"
tags: []

---

# Module Dataflow Performance Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Resolve module state once per request and once per browser session, then load enabled public module content in parallel without coupling it to admin control-plane requests.

**Architecture:** The backend exposes one entitlement-filtered module snapshot reused by all checks and serializers. The frontend keeps a shared manifest readiness promise and a TTL/in-flight API cache; public content waits only for that manifest, while admin metadata and vendor entitlement remain lazy.

**Tech Stack:** Laravel/PHP, PHPUnit, Vue 3, Pinia, Axios, Vite.

**Spec:** Approved in chat on 2026-08-27.

## Global Constraints

- Preserve cryptographic license verification and `module.enabled` request enforcement.
- Public pages must not request admin settings, entitlement status, or full module-management metadata.
- No public module content request may be serialized behind another enabled module content request.

### Task 1: Backend entitlement/module snapshot

**Files:**
- Modify: `backend/app/Core/Security/EntitlementManager.php`
- Modify: `backend/app/Core/ModuleManager.php`
- Modify: `backend/app/Http/Controllers/Api/ModuleManagementController.php`
- Test: `backend/tests/Feature/Core/ModuleManagerTest.php`

- [ ] Add regression assertions for entitled enabled IDs remaining secure and stable across repeated checks.
- [ ] Implement normalized entitled-ID lookup once per request-scoped manager.
- [ ] Reuse it in manager checks and management serializers.
- [ ] Run focused PHPUnit tests.

### Task 2: Public manifest/content loading

**Files:**
- Modify: `frontend/src/services/apiCache.js`
- Modify: `frontend/src/stores/modules.js`
- Modify: `frontend/src/views/HomeView.vue`

- [ ] Add the public cache/readiness behavior.
- [ ] Route Home loading through the shared cache after manifest readiness.
- [ ] Keep failures isolated per dataset.
- [ ] Run the production build.

### Task 3: Admin isolation and verification

**Files:**
- Modify: `frontend/src/stores/modules.js`
- Modify: `frontend/src/views/admin/AdminModulesView.vue`

- [ ] Add the missing full-management fetch action and avoid unnecessary post-toggle full refreshes.
- [ ] Invalidate public manifest/content cache after authoritative mutations.
- [ ] Run backend tests and frontend build.
