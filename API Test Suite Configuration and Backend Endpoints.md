# API Test Suite Configuration and Backend Endpoints

## Overview

This document explains the existing API test suite configuration (as defined in `package.json`) and the backend endpoints those tests are expected to cover. It highlights current gaps (missing test files), details endpoint flows (authentication, calculator operations, health checks, admin dashboard stats), and notes environment configuration dependencies.

## 1. Test Suite Configuration (package.json)

### 1.1 Scripts Overview
- **test**: `node tests/index.js` (entrypoint missing).
- **test:local**: `TEST_ENV=local node tests/index.js`.
- **test:staging**: `TEST_ENV=staging node tests/index.js`.
- **test:prod**: `TEST_ENV=production node tests/index.js`.
- **test:report**: `npx playwright show-report test-results/api-tests`.
- **test:debug**: `DEBUG=* npx playwright test tests/Api/ --trace on`.
- **test:auth**: `npx playwright test tests/Api/auth.spec.js`.
- **test:calculator**: `npx playwright test tests/Api/calculator.spec.js`.
- **test:admin**: `npx playwright test tests/Api/admin-dashboard.spec.js tests/Api/security.spec.js tests/Api/health-check.spec.js`.
- **test:security**: `npx playwright test tests/Api/security.spec.js`.
- **test:health**: `npx playwright test tests/Api/health-check.spec.js`.
- **test:ci**: `TEST_ENV=staging npx playwright test tests/Api/ --reporter=json,junit --output=test-results/api-tests`.
- **install:browsers**: `npx playwright install`.
- **clean:results**: `rm -rf test-results/*` (Unix-specific; Windows users need alt command).
- **validate:config**: `node -e "console.log('Configuration valid:', require('./tests/config.json'))"` (config file missing).

### 1.2 Dependencies
- `@playwright/test`: ^1.40.0
- `playwright`: ^1.40.0

### 1.3 Missing Artifacts (to be created)
- `tests/index.js` (runner/orchestrator).
- `tests/config.json` (environment config: baseUrl, credentials, etc.).
- Playwright specs: `tests/Api/auth.spec.js`, `calculator.spec.js`, `admin-dashboard.spec.js`, `security.spec.js`, `health-check.spec.js` (all referenced but absent).

## 2. Environment Configuration

### 2.1 .env
- `APP_ENV=development`, `APP_URL=http://localhost/Bishwo_Calculator`, `APP_BASE=/Bishwo_Calculator`.
- DB connection vars: `DB_HOST`, `DB_NAME`, `DB_USER` (note: config expects DB_DATABASE/DB_USERNAME keys; align naming in loader).

### 2.2 config/database.php
- Loads .env via `loadConfigEnv()`, parses key=value lines into `$_ENV`.
- Returns DB config array using `$_ENV['DB_HOST']`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (with fallbacks to `getenv`).

### 2.3 config/app.php
- Builds app URL and environment metadata (env, debug, timezone).

### 2.4 Test Config (expected)
- `tests/config.json` should define `baseUrl`, credentials, environment overrides (TEST_ENV-local/staging/production), and perhaps tokens.

## 3. Backend Endpoints (Targets for Tests)

### 3.1 Authentication API (`/api/login.php`)
- **Flow**:
  1. Bootstrap/session init.
  2. Instantiate `AuthController` -> `login()`.
  3. Parse JSON body (username/email, password).
  4. Lookup user (`User::findByUsername`).
  5. Verify password hash.
  6. On success: set session vars, generate session token (random 32-byte hex), persist to `user_sessions`, set cookie, regenerate session ID.
  7. Return JSON `{ success, message, redirect_url?, user }`; else 401 with error.
- **Test ideas**: valid login, invalid password, non-existent user, missing fields, session token persistence, cookie presence, rate/lockout behavior (if present).

### 3.2 Calculator API (`/api/calculate.php`)
- **Flow**:
  1. POST JSON body with `category`, `tool`, `data`.
  2. Optional HTTP Basic Auth: resolve user for history saves.
  3. Instantiate `CalculationService`.
  4. Factory creates calculator by type/slug; validate input.
  5. Execute calculator logic; return result.
  6. If authenticated, save to `calculation_history`.
- **Test ideas**: success path for a known calculator, validation failures (missing fields, bad types), unauthorized vs authorized (history saved), boundary inputs, unknown calculator slug returns error.

### 3.3 Health Check API (`/api/health-check.php`)
- **Flow**:
  1. GET handler builds health object (status default healthy, timestamp).
  2. Database check: `SELECT 1` via singleton; pass/fail status.
  3. Filesystem check: storage directory existence & writability.
  4. Memory check: usage vs memory_limit.
  5. PHP extensions check: pdo, pdo_mysql, json, mbstring.
  6. Set HTTP status 200 or 503; return JSON with checks.
- **Test ideas**: healthy response shape, missing extension simulation (mock or env), storage unwritable scenario, HTTP status toggling based on failures.

### 3.4 Admin Dashboard Stats API (`/api/admin/dashboard-stats.php`)
- **Flow**:
  1. GET with HTTP Basic Auth.
  2. Authenticate user via username/password; ensure admin role.
  3. Acquire DB connection.
  4. Queries: total users, active users (30d), new users today, total calculations, today’s calculations, popular calculators (30d), system metrics (memory, disk, PHP version), recent activity.
  5. Returns JSON `{ success, stats }` or 401 on auth/role failure.
- **Test ideas**: admin auth success, non-admin rejected, stats keys present, data types/numeric checks, empty-data handling.

### 3.5 Security/Other Endpoints (if expanding tests)
- **IP restrictions, CSRF, rate limiting**: covered by middleware on admin/API routes; ensure tokens and headers are present in test requests.
- **Uploads**: media endpoints (if API-level tests added) must include CSRF and auth; consider using fixtures and cleanup.

## 4. Test Suite Design Recommendations

1. **Create Missing Scaffolding**:
   - `tests/index.js`: load config, set baseUrl per TEST_ENV, orchestrate Playwright runs or supertest-style HTTP calls.
   - `tests/config.json`: include environments (local/staging/prod) with `baseUrl`, credentials (admin, user), feature flags.
   - Add Playwright specs under `tests/Api/` for auth, calculator, admin-dashboard, health, security.

2. **Environment Handling**:
   - Respect `TEST_ENV` env var; default to local.
   - Map env to `baseUrl`, credentials, and optional headers (basic auth for admin endpoints).

3. **Fixtures & Data Setup**:
   - Seed test users (admin + standard) and known calculator payloads.
   - Provide reusable request builders (e.g., `loginAndGetToken`, `basicAuthHeader`).

4. **Assertions**:
   - Verify HTTP status, response shape, and critical fields (tokens, IDs, counts).
   - For calculator API, assert deterministic outputs for fixture inputs.
   - For admin stats, assert numeric types and non-negative counts.

5. **Error Cases**:
   - Invalid credentials, missing fields, malformed JSON.
   - Unauthorized access to admin endpoints.
   - Health-check forced failures (simulate DB down or storage unwritable if feasible in a staging env).

6. **Reporting & CI**:
   - Ensure `test:ci` uses reporters outputting JSON/JUnit to `test-results/api-tests`.
   - Add GitHub/CI pipeline step to run `npm run test:ci` after installing Playwright browsers.

7. **Platform Notes**:
   - `clean:results` uses `rm -rf`; replace with cross-platform alternative for Windows (e.g., `rimraf`).

## 5. Quick Endpoint Reference

| Endpoint | Method | Auth | Purpose |
| --- | --- | --- | --- |
| `/api/login.php` | POST | Session (sets cookie) | Authenticate user, create session token |
| `/api/calculate.php` | POST | Optional Basic Auth | Execute calculator logic; save history if authenticated |
| `/api/health-check.php` | GET | None | Report system health (DB, FS, memory, extensions) |
| `/api/admin/dashboard-stats.php` | GET | Basic Auth (admin) | Return user/calculation stats and system metrics |

## 6. Gaps & Next Steps

- **Missing test files**: create `tests/index.js`, `tests/config.json`, and all referenced Playwright specs.
- **Config alignment**: ensure `.env` keys match `config/database.php` expectations (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
- **Test data**: prepare seed users and calculator payloads for deterministic assertions.
- **Cross-platform**: adjust cleanup command for Windows environments.

## Conclusion

The test suite is partially configured in `package.json` but lacks implementation files. The key API endpoints are ready for coverage: authentication, calculator operations, health checks, and admin dashboard stats. Implementing the missing test scaffolding, aligning environment configuration, and seeding deterministic data will enable reliable automated API validation across environments.
