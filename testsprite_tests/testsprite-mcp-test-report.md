# TestSprite AI Testing Report (Backend)

## 1. Document Metadata

- **Project:** Bishwo_Calculator
- **Date:** 2025-11-17
- **Environment:** Local Laragon (port 80) proxied via TestSprite tunnel
- **Prepared by:** Cascade via TestSprite MCP

---

## 2. Requirement Validation Summary

### Requirement A — Authentication API Stability (TC001–TC003)

| Test ID | Description | Result | Findings |
| --- | --- | --- | --- |
| TC001 | User login functionality | ❌ Failed | `/api/login` returned HTTP 500 for valid credentials. Response body indicates unhandled exception before auth can complete. Likely DB seed/migrations missing required users or env misconfig (e.g., `.env` DB creds). |
| TC002 | User registration process | ❌ Failed | `/api/register` responded 500 instead of 201. Input payload satisified API contract, so failure likely due to DB layer (missing `users` table columns such as `terms_agree` / `engineer_roles`) or validation exception not caught. |
| TC003 | User logout operation | ❌ Failed | Test could not even reach logout because prerequisite login failed with 500. Indicates login must succeed before downstream auth flows can be verified. |

**Requirement verdict:** Not met. All auth endpoints crash. Root cause appears to be missing DB migrations/seed data or misconfigured `.env`, causing PDO exceptions that bubble up as 500s.

---

### Requirement B — Admin Access Control (TC004–TC006)

| Test ID | Description | Result | Findings |
| --- | --- | --- | --- |
| TC004 | Admin dashboard access control | ❌ Failed | GET `/admin` returned 500 even with admin creds. Since login already fails, middleware never receives authenticated session, leading to fatal error (likely undefined session/auth object). |
| TC005 | Admin settings section retrieval | ❌ Failed | GET `/admin/settings/general` returned 500. Same upstream login failure, compounded by possible missing view/template dependencies. |
| TC006 | User management listing access | ❌ Failed | `/admin/users` returned 500. Again blocked by global auth failure. |

**Requirement verdict:** Not met. Need to stabilize authentication layer first, then verify admin middleware + templates. Confirm that required tables (`user_sessions`, `settings`, etc.) exist.

---

### Requirement C — Calculator Engine Availability (TC007–TC008)

| Test ID | Description | Result | Findings |
| --- | --- | --- | --- |
| TC007 | List available calculators | ❌ Failed | `/calculators` responded 500. Stack trace implies global exception handler triggered before controller response—likely because middleware expects logged-in user (session missing) or modules reference nonexistent database records. |
| TC008 | Execute calculator function | ❌ Failed | `/calculator/electrical/load` returned 500. Without authenticated context and seeded module definitions, execution path throws (e.g., missing module class file include or DB row). |

**Requirement verdict:** Not met. Need to ensure calculator modules autoloaded and any prerequisite tables/config (e.g., `calculator_modules`) are migrated.

---

### Requirement D — User Profile Management (TC009–TC010)

| Test ID | Description | Result | Findings |
| --- | --- | --- | --- |
| TC009 | Get user profile data | ❌ Failed | `/profile` GET returned 500 because login prerequisite failed, so middleware likely throws when `$_SESSION['user_id']` absent. |
| TC010 | Update user profile information | ❌ Failed | `/profile` PUT returned 500. Same upstream issue plus possible CSRF/token requirement not satisfied by API test. |

**Requirement verdict:** Not met. Need successful session establishment and CSRF handling for API clients.

---

## 3. Coverage & Metrics

| Requirement | Total Tests | ✅ Passed | ❌ Failed |
| --- | --- | --- | --- |
| Requirement A — Authentication API Stability | 3 | 0 | 3 |
| Requirement B — Admin Access Control | 3 | 0 | 3 |
| Requirement C — Calculator Engine Availability | 2 | 0 | 2 |
| Requirement D — User Profile Management | 2 | 0 | 2 |

- **Overall Pass Rate:** 0% (0 / 10 passing)
- **Blocking Severity:** Critical — no backend endpoint returned success.

---

## 4. Key Gaps / Risks

1. **Database not initialized:** All endpoints crash with HTTP 500, consistent with missing migrations or invalid DB credentials. Run `php database/migrate.php`, ensure `.env` points to live MySQL DB, and seed required admin user.
2. **Authentication prerequisites unmet:** Since login fails, every downstream test (logout, admin pages, profile) also fails. Fixing login will unblock majority of suite.
3. **Error handling lacks graceful fallback:** Controllers bubble raw exceptions causing 500 instead of JSON errors. Add try/catch with structured error responses to improve resiliency and observability.

---

## 5. Recommended Next Actions

1. Initialize DB: `php database/migrate.php` then verify with `php tests/database/check_db.php`.
2. Seed admin + sample user credentials referenced by tests (e.g., `uniquebishwo@gmail.com`).
3. Re-run local smoke tests (`tests/api/*`) to confirm 200 responses before rerunning TestSprite suite.
4. Add logging around `/api/login` and `/api/register` to capture DB error details for future regressions.

---

*Report auto-generated from TestSprite raw output located at `testsprite_tests/tmp/raw_report.md`.*
