---
noteId: "25566640a19e11f1ba16875512ddc7d8"
tags: []

---

﻿# Vendor-Only Module Management Control Plane Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement an enterprise-grade, cryptographically verified Vendor-Only Module Management Control Plane where module activation and lifecycle management are governed strictly by cryptographically signed vendor subscription entitlements with defense-in-depth policy checks, rate limiting, route isolation, and immutable audit logging.

**Architecture:** 
- **Cryptographic Entitlement Engine (`EntitlementManager`):** Uses asymmetric cryptography (Ed25519 or OpenSSL RSA with Vendor Public Key) / HMAC-SHA256 token verification to validate signed subscription entitlements (`client_id`, `tier`, `licensed_modules`, `valid_until`, `signature`). Direct database tampering or client-side edits cannot enable unauthorized modules.
- **Dual-Gate Module Evaluation (`ModuleManager`):** Module execution requires BOTH administrative enablement AND valid cryptographic vendor entitlement.
- **Dedicated Vendor API Gateways (`/api/v1/vendor/*`):** Isolated endpoints protected by `VendorAuthorizationMiddleware`, rate limiting (`throttle:5,1`), signed payload challenge verification (`X-Vendor-Signature`), and immutable audit logging.
- **Frontend Subscription & License Portal:** `/admin/modules` displays the active verified subscription tier, licensed capabilities, expiration, and a cryptographic license activation workflow for vendor entitlement deployment. Standard client admins cannot toggle switches manually.

**Tech Stack:** PHP 8.2+, Laravel 11, Laravel Sanctum, Spatie Permissions, OpenSSL/Sodium Cryptography, Vue 3, Pinia, Tailwind CSS.

## Global Constraints
- NEVER rely on security-through-obscurity, hidden URLs, or hardcoded passwords.
- Database access or client-side inspection alone MUST NOT grant module activation privileges.
- All module checks at the middleware layer MUST verify cryptographic entitlement validity.
- 100% test coverage with regression tests across cryptographic verification, tamper resistance, route blocking, and UI license inspection.

---

### Task 1: Cryptographic Entitlement Engine & Key Infrastructure

**Files:**
- Create: `backend/app/Core/Security/EntitlementManager.php`
- Create: `backend/app/Core/Security/VendorKeyProvider.php`
- Modify: `backend/config/modules.php`
- Test: `backend/tests/Feature/Core/EntitlementManagerTest.php`

**Interfaces:**
- Produces: `EntitlementManager::verifyEntitlement(string $token): array`
- Produces: `EntitlementManager::applySignedLicense(string $signedPayload): bool`
- Produces: `EntitlementManager::isModuleEntitled(string $moduleKey): bool`
- Produces: `EntitlementManager::getActiveEntitlement(): ?array`

- [ ] **Step 1: Write the failing unit/feature test for EntitlementManager**
- [ ] **Step 2: Run test to verify failure**
- [ ] **Step 3: Implement VendorKeyProvider & EntitlementManager with signature verification and tamper detection**
- [ ] **Step 4: Run test to verify it passes**
- [ ] **Step 5: Commit changes**

---

### Task 2: Integrate Entitlement Verification into ModuleManager & Middleware

**Files:**
- Modify: `backend/app/Core/ModuleManager.php`
- Modify: `backend/app/Http/Middleware/CheckModuleEnabled.php`
- Test: `backend/tests/Feature/Core/EntitledModuleManagerTest.php`

**Interfaces:**
- Consumes: `EntitlementManager`
- Modifies: `ModuleManager::isModuleEnabled(string $moduleKey): bool` (checks cache, database, AND `EntitlementManager::isModuleEntitled`)
- Modifies: `ModuleManager::enable(string $moduleKey)` (rejects if not cryptographically entitled)

- [ ] **Step 1: Write failing test verifying database tampering cannot activate non-entitled modules**
- [ ] **Step 2: Run test to verify failure**
- [ ] **Step 3: Update `ModuleManager` and `CheckModuleEnabled` to enforce dual-gate entitlement check**
- [ ] **Step 4: Run test to verify all pass**
- [ ] **Step 5: Commit changes**

---

### Task 3: Vendor Control Plane API & Isolated Security Middleware

**Files:**
- Create: `backend/app/Http/Middleware/EnsureVendorAuthorized.php`
- Create: `backend/app/Http/Controllers/Api/Vendor/VendorEntitlementController.php`
- Modify: `backend/routes/api.php`
- Test: `backend/tests/Feature/Vendor/VendorControlPlaneApiTest.php`

**Interfaces:**
- Produces: `GET /api/v1/vendor/entitlement/status`
- Produces: `POST /api/v1/vendor/entitlement/apply`
- Produces: `POST /api/v1/vendor/modules/sync`
- Restricts: `/api/v1/admin/modules` (disabled/forbidden for non-vendor callers)

- [ ] **Step 1: Write failing test for Vendor Control Plane API endpoints, signature validation, rate limiting, and 403 on client access**
- [ ] **Step 2: Run test to verify failure**
- [ ] **Step 3: Implement `EnsureVendorAuthorized` middleware and `VendorEntitlementController` with audit logging**
- [ ] **Step 4: Run test to verify it passes**
- [ ] **Step 5: Commit changes**

---

### Task 4: Frontend Subscription & License Activation Portal

**Files:**
- Create: `frontend/src/services/vendorEntitlementApi.js`
- Modify: `frontend/src/views/admin/AdminModulesView.vue`
- Modify: `frontend/src/stores/modules.js`
- Test: `frontend/src/views/admin/__tests__/AdminModulesView.spec.js` (or browser verification)

**Interfaces:**
- Produces: License Inspector UI (displays client ID, subscription tier, valid until, cryptographic fingerprint)
- Produces: Signed License Activation modal for vendor deployments
- Replaces: Direct client toggle switches with verified entitlement indicators

- [ ] **Step 1: Create `vendorEntitlementApi.js` for license status & signed payload application**
- [ ] **Step 2: Update `AdminModulesView.vue` to render enterprise license overview and vendor activation modal**
- [ ] **Step 3: Build frontend assets (`npm run build`)**
- [ ] **Step 4: Verify in live browser via Chrome DevTools MCP**
- [ ] **Step 5: Commit changes**

---

### Task 5: End-to-End Security Verification & Full Suite Audit

**Files:**
- Test: `backend/tests/Feature/Vendor/VendorControlPlaneSecurityTest.php`

- [ ] **Step 1: Write end-to-end security penetration test suite (tamper attacks, replay attacks, expired license rejection, client permission bypass attempts)**
- [ ] **Step 2: Run full backend test suite (`php artisan test`) and verify 100% pass rate**
- [ ] **Step 3: Run full frontend build (`npm run build`)**
- [ ] **Step 4: Verify live browser session**
- [ ] **Step 5: Final commit**
