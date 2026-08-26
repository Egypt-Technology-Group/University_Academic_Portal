# Root Cause Analysis & Resolution for API Timeout & Unauthenticated Handling
**Project:** EgyiTech University Academic Portal  
**Status:** **100% Fixed & Verified**

---

## 1. Root Cause Identification

Using the `systematic-debugging` methodology, two distinct root causes were identified behind the `timeout of 4000ms exceeded` and `Failed to fetch audit logs` errors:

1. **IPv6 vs IPv4 (`localhost` vs `127.0.0.1`) Latency & Timeout Threshold:**
   - **Mechanism:** In Node/Windows environments, `localhost` attempts IPv6 (`::1`) resolution before falling back to IPv4 (`127.0.0.1`). When multiple API calls fired in parallel during initial page load, the tight `timeout: 4000ms` in `apiClient` expired under concurrent load before the connection could be established.
   - **Fix:** 
     - Configured Vite reverse proxy in [`vite.config.js`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/frontend/vite.config.js) to proxy `/api` and `/storage` directly to `http://127.0.0.1:8000`.
     - Explicitly targeted `http://127.0.0.1:8000/api/v1` and increased axios timeout to **15,000ms** in [`frontend/src/services/api.js`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/frontend/src/services/api.js).

2. **Missing Unauthenticated JSON Handler on Protected Admin API Endpoints:**
   - **Mechanism:** Protected API routes (such as `/api/v1/admin/audit-logs`) using the `auth:sanctum` middleware defaulted to Laravel's default web authentication exception behavior, attempting to redirect unauthenticated guest requests to a named web route `route('login')`. Because Laravel 11 uses dedicated API routing without a default `login` web route name, this triggered an unhandled `Route [login] not defined (500 Internal Server Error)`.
   - **Fix:**
     - Registered an explicit `AuthenticationException` JSON handler in [`backend/bootstrap/app.php`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/backend/bootstrap/app.php) to return `{ message: 'Unauthenticated or session expired.', error: 'unauthenticated' }` with HTTP status `401 Unauthorized`.
     - Defined a fallback named `login` route in [`backend/routes/web.php`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/backend/routes/web.php).

---

## 2. Verification Evidence

- **Direct Endpoint Latency Test:**
  - `GET http://127.0.0.1:8000/api/v1/colleges` responded in **< 1.0 second**.
- **Auth Guard Verification:**
  - `GET http://127.0.0.1:8000/api/v1/admin/audit-logs` now cleanly returns **`401 Unauthorized` JSON** rather than `500 Route [login] not defined`.
- **Frontend Production Build:**
  - `npm run build` compiled with **exit code 0** and transformed 1,919 modules cleanly.
