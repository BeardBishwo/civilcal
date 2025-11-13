
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** Bishwo_Calculator
- **Date:** 2025-11-13
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** User calculator navigation basic flow
- **Test Code:** [TC001_User_calculator_navigation_basic_flow.py](./TC001_User_calculator_navigation_basic_flow.py)
- **Test Error:** Testing stopped due to critical PHP fatal error on 'Concrete Volume' calculator page caused by missing backend file. This issue must be fixed before further testing can continue.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/assets/icons/icon-192.png:0:0)
[ERROR] Refused to apply style from 'https://localhost/Bishwo_Calculator/assets/css/civil.css' because its MIME type ('text/html') is not a supported stylesheet MIME type, and strict MIME checking is enabled. (at https://localhost/bishwo_calculator/civil:2076:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/818354df-5c10-4941-b36a-3e4ed92ceed7
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** Theme upload and validation
- **Test Code:** [TC002_Theme_upload_and_validation.py](./TC002_Theme_upload_and_validation.py)
- **Test Error:** The admin login page is currently inaccessible due to a 500 Internal Server Error. This prevents logging in as administrator and testing theme upload functionality. The issue needs to be resolved on the server side before further testing can proceed.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/themes:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/login:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/ea5296cf-91b2-4aec-a534-8ab8d1e370ef
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** Theme activation and dynamic CSS application
- **Test Code:** [TC003_Theme_activation_and_dynamic_CSS_application.py](./TC003_Theme_activation_and_dynamic_CSS_application.py)
- **Test Error:** Cannot proceed with theme activation and verification due to server error on /admin/themes page. Task stopped.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/themes:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/c2414782-f2c2-4237-91b6-45b8101fdf55
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** Theme customization and preview
- **Test Code:** [TC004_Theme_customization_and_preview.py](./TC004_Theme_customization_and_preview.py)
- **Test Error:** The test to verify that administrators can customize theme settings at /admin/themes and preview changes before activation could not be completed due to a persistent connection error preventing admin login. The issue has been reported. Please resolve the server connectivity problem to enable further testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/7c01246a-97ee-4bcb-a160-c20cc139c303
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** Plugin upload and manifest validation
- **Test Code:** [TC005_Plugin_upload_and_manifest_validation.py](./TC005_Plugin_upload_and_manifest_validation.py)
- **Test Error:** Testing stopped due to connection error preventing admin login and access to plugin management interface. Cannot verify plugin upload and manifest validation without admin access.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/assets/icons/icon-192.png:0:0)
[ERROR] Refused to apply style from 'https://localhost/Bishwo_Calculator/assets/css/management.css' because its MIME type ('text/html') is not a supported stylesheet MIME type, and strict MIME checking is enabled. (at https://localhost/bishwo_calculator/management:2076:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/Bishwo_Calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/Bishwo_Calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/d71a77a6-c97f-4238-957e-2e999b83ff71
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC006
- **Test Name:** Plugin activation toggle and deletion
- **Test Code:** [TC006_Plugin_activation_toggle_and_deletion.py](./TC006_Plugin_activation_toggle_and_deletion.py)
- **Test Error:** Testing cannot proceed because the login process fails with a connection error, preventing access to plugin management. The issue must be resolved before plugin activation, deactivation, and deletion can be verified.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/be118d5f-32c0-4178-a265-c8aa6a83490e
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** Backup creation and export
- **Test Code:** [TC007_Backup_creation_and_export.py](./TC007_Backup_creation_and_export.py)
- **Test Error:** Cannot proceed with backup creation test due to 500 Internal Server Error at /admin/help. The server error prevents verifying backup creation and download functionality. Please fix the server error to continue testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/help:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/357d8879-1426-4bf7-90c6-6f47e8bbc02a
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** Restore from backup
- **Test Code:** [TC008_Restore_from_backup.py](./TC008_Restore_from_backup.py)
- **Test Error:** The test to verify that an administrator can restore the system to a previous state using a backup file could not be completed due to a persistent connection error preventing admin login. The connection error was reported as a website issue. Further testing requires resolving the server connectivity problem to enable admin access and restore functionality verification.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/d1b3991e-9a20-4343-a87b-09664e5179c6
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** Audit log viewing and filtering
- **Test Code:** [TC009_Audit_log_viewing_and_filtering.py](./TC009_Audit_log_viewing_and_filtering.py)
- **Test Error:** The /admin/audit-logs page is currently inaccessible due to a 500 Internal Server Error. This blocks verification of audit log access, filtering, and download functionality. Please resolve the server error to continue testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/audit-logs:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/b020d57b-6ec8-422b-8f83-e299db5b1eab
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** Admin POST routes security: rate limiting and CSRF protection
- **Test Code:** [TC010_Admin_POST_routes_security_rate_limiting_and_CSRF_protection.py](./TC010_Admin_POST_routes_security_rate_limiting_and_CSRF_protection.py)
- **Test Error:** Testing cannot proceed because the server is unreachable, causing connection errors on login attempts. Unable to verify rate limiting and CSRF protections on administrative POST routes. Please resolve the server connectivity issue and retry.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/4fbb9691-32a6-47ea-beda-beb2d2156fc6
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC011
- **Test Name:** Structured logging and audit logs generation
- **Test Code:** [TC011_Structured_logging_and_audit_logs_generation.py](./TC011_Structured_logging_and_audit_logs_generation.py)
- **Test Error:** Testing stopped due to persistent server connection error preventing login and audit log verification. Manual and quick login attempts failed. Please resolve server connectivity issues to continue testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/a445c7f4-3f54-4162-a7e6-e3644fe88058
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC012
- **Test Name:** API v1 health endpoint returns 200 OK JSON
- **Test Code:** [TC012_API_v1_health_endpoint_returns_200_OK_JSON.py](./TC012_API_v1_health_endpoint_returns_200_OK_JSON.py)
- **Test Error:** The API endpoint /api/v1/health does not reliably return a 200 OK status and valid JSON. Instead, it returns a 500 Internal Server Error indicating a server-side problem or misconfiguration. The health check endpoint is currently not functional as expected.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/api/v1/health:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/688bd516-51f5-4084-a9ca-3ff3943e7149
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC013
- **Test Name:** API v1 calculators and history endpoints data validation
- **Test Code:** [TC013_API_v1_calculators_and_history_endpoints_data_validation.py](./TC013_API_v1_calculators_and_history_endpoints_data_validation.py)
- **Test Error:** API endpoints /api/v1/calculators and /api/v1/history both returned 500 Internal Server Errors. No valid JSON data could be verified. The server needs to be fixed to proceed with validation of the API responses.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/api/v1/calculators:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/api/v1/history?user=admin@engicalpro.com:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/1ec3f887-df3c-46a4-9ba0-992c6df61128
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC014
- **Test Name:** Edge case: Calculator selection with no available calculators
- **Test Code:** [TC014_Edge_case_Calculator_selection_with_no_available_calculators.py](./TC014_Edge_case_Calculator_selection_with_no_available_calculators.py)
- **Test Error:** Reported the issue about inability to simulate 'no calculators available' state under Electrical module. Stopping further actions as the test cannot proceed without this simulation.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/26f47eee-46ec-461d-bddf-631897e1919d
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC015
- **Test Name:** Theme upload with corrupted or partial files
- **Test Code:** [TC015_Theme_upload_with_corrupted_or_partial_files.py](./TC015_Theme_upload_with_corrupted_or_partial_files.py)
- **Test Error:** Testing cannot proceed due to server connection error on login. Authentication failed and theme upload validation cannot be performed. Please resolve server connectivity issues first.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/api/login.php:0:0)
[ERROR] Login error: SyntaxError: Unexpected token '<', "<h1>404 - "... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/af532a3a-f7b9-479b-a390-326bebd40442
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC016
- **Test Name:** Backup restore with invalid or corrupted backup file
- **Test Code:** [TC016_Backup_restore_with_invalid_or_corrupted_backup_file.py](./TC016_Backup_restore_with_invalid_or_corrupted_backup_file.py)
- **Test Error:** Cannot proceed with the test as the restore interface at /admin/help returns a 500 Internal Server Error. The system does not allow uploading or restoring backups currently. Please fix the server error to continue testing the restore process with corrupted backup files.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/help:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/a59fc7ec-8d98-4c53-b07c-85f0c0e2c3e4/e0f1a36b-7c34-4440-ae3b-864407f0372b
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---


## 3️⃣ Coverage & Matching Metrics

- **0.00** of tests passed

| Requirement        | Total Tests | ✅ Passed | ❌ Failed  |
|--------------------|-------------|-----------|------------|
| ...                | ...         | ...       | ...        |
---


## 4️⃣ Key Gaps / Risks
{AI_GNERATED_KET_GAPS_AND_RISKS}
---