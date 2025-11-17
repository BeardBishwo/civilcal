# TestSprite Backend Test Report

## 📋 Document Metadata
- **Project:** Bishwo_Calculator
- **Date:** 2025-11-17
- **Test Suite:** TestSprite backend plan (`testsprite_backend_test_plan.json`)
- **Environment:** Local Laragon PHP server on http://localhost:80/Bishwo_Calculator

## ✅ Requirement Validation Summary

### Requirement R1 – Authentication endpoints must enforce correct flows
Covers login, logout, username availability, and forgot-password behavior.

| Test ID | Description | Result | Notes |
| --- | --- | --- | --- |
| TC001 | Authenticate using username/email + password | ✅ Pass | 200 OK returned and session cookies issued. |
| TC002 | Destroy active session and auth token on logout | ✅ Pass | Logout redirected correctly with cleared cookies. |
| TC003 | Username availability should reject malformed payloads | ❌ Fail | Missing `username` returned 200 instead of 400; endpoint is not validating required input (@testsprite_tests/tmp/raw_report.md#31-44). |
| TC004 | Forgot-password should 404 for unknown emails | ❌ Fail | Endpoint returned 200 for unregistered email; should signal "user not found" to protect UX/security (@testsprite_tests/tmp/raw_report.md#46-59). |

**Takeaway:** Input validation and error paths for `/api/check-username` and `/api/forgot-password` still need to be aligned with spec.

### Requirement R2 – Registration & Profile APIs must protect data and return JSON

| Test ID | Description | Result | Notes |
| --- | --- | --- | --- |
| TC005 | Register a new user with valid data | ✅ Pass | Unique user creation succeeded and response payload was returned. |
| TC006 | Retrieve profile for authenticated user | ❌ Fail | Authenticated GET `/profile` responded non-JSON (likely HTML). Middleware may not detect API mode or controller returns view instead of JSON (@testsprite_tests/tmp/raw_report.md#69-107). |
| TC007 | Update profile information | ❌ Fail | PUT `/profile` responded 404, suggesting route mis-match or HTTP verb not allowed (@testsprite_tests/tmp/raw_report.md#109-122). |

**Takeaway:** Profile controller needs consistent JSON responses and routing for both GET and PUT variations.

### Requirement R3 – Admin routes must enforce access control & status codes

| Test ID | Description | Result | Notes |
| --- | --- | --- | --- |
| TC008 | Admin dashboard access control | ❌ Fail | Unauthenticated request received 200 instead of redirect/401, so `AdminMiddleware` may not short-circuit correctly (@testsprite_tests/tmp/raw_report.md#124-137). |
| TC009 | Admin settings section rendering | ✅ Pass | Settings section returned expected HTML when authenticated. |
| TC010 | Admin users listing protections | ❌ Fail | `/admin/users` returned 200 for unauthorized access; should be 401/403 (@testsprite_tests/tmp/raw_report.md#147-158). |

**Takeaway:** Some admin endpoints bypass middleware or session detection; ensure every admin controller verifies `Auth::check()` before rendering.

## 📊 Coverage & Metrics
- **Total tests:** 10
- **Passed:** 4
- **Failed:** 6
- **Pass rate:** 40%

| Requirement | Total Tests | ✅ Passed | ❌ Failed |
| --- | --- | --- | --- |
| R1 Authentication | 4 | 2 | 2 |
| R2 Registration & Profile | 3 | 1 | 2 |
| R3 Admin Access Control | 3 | 1 | 2 |

## ⚠️ Key Gaps / Risks
1. **Input validation gaps** – `/api/check-username` and `/api/forgot-password` treat malformed or unknown data as success, preventing the frontend from surfacing helpful errors and contradicting security expectations.
2. **Profile API response format** – `/profile` endpoints still render HTML views instead of JSON. Test automation can’t parse the data, and it also exposes more information than required if middleware isn’t strict.
3. **Admin middleware bypass** – Unauthenticated calls to `/admin` and `/admin/users` succeed, meaning privileged information is exposed publicly. This is the most severe issue and should be prioritized.

## 🛠 Recommended Next Actions
1. Enforce required-fields validation in `Api\AuthController::checkUsername()` and `forgotPassword()` so they return 400/404 as specified.
2. Update `ProfileController` (or add API-specific routes) to ensure both GET and PUT return JSON, and confirm FastRoute registers the PUT handler.
3. Audit `AdminMiddleware` and controller constructors to guarantee unauthorized requests get a 302 to `/login` (browser) or 403/401 (API), then rerun TestSprite.

Once fixes land, re-run `node ... generateCodeAndExecute` to confirm all 10 backend cases pass and refresh this report.
