
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** Bishwo_Calculator
- **Date:** 2025-11-13
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** User Calculator Navigation and Opening
- **Test Code:** [TC001_User_Calculator_Navigation_and_Opening.py](./TC001_User_Calculator_Navigation_and_Opening.py)
- **Test Error:** Testing stopped due to fatal error on calculator page 'Concrete Volume'. Missing required file caused the page to fail loading. Further testing cannot proceed until this is resolved.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/199b1b45-cfb9-4c8b-a4ba-74c2e55655a3
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** Theme Upload and Validation
- **Test Code:** [TC002_Theme_Upload_and_Validation.py](./TC002_Theme_Upload_and_Validation.py)
- **Test Error:** The /admin/themes page is currently inaccessible due to a 500 Internal Server Error. Theme upload and validation testing cannot proceed until the server issue is resolved.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/themes:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/f5fdd306-908b-4efb-83b4-70aefcb948fc
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** Theme Activation and Dynamic CSS Application
- **Test Code:** [TC003_Theme_Activation_and_Dynamic_CSS_Application.py](./TC003_Theme_Activation_and_Dynamic_CSS_Application.py)
- **Test Error:** The task to validate dynamic theme activation and CSS application could not be completed due to a persistent server connection error preventing admin login and theme upload. The issue has been reported as a blocker. No further testing could be performed.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/c10795a4-fbbd-41f4-a241-be1e91e602d6
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** Theme Customization and Settings Persistence
- **Test Code:** [TC004_Theme_Customization_and_Settings_Persistence.py](./TC004_Theme_Customization_and_Settings_Persistence.py)
- **Test Error:** The theme customization interface at /admin/themes is not accessible due to a 500 Internal Server Error. Therefore, I could not perform the customization, save settings, or verify changes. The task cannot be completed as intended.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/themes:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/e6abb4ee-2f8a-443a-b1c5-80a0ec60a16c
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** Plugin Upload and Manifest Validation
- **Test Code:** [TC005_Plugin_Upload_and_Manifest_Validation.py](./TC005_Plugin_Upload_and_Manifest_Validation.py)
- **Test Error:** Testing cannot proceed due to persistent connection error preventing login and access to plugin management interface. Please resolve server connectivity issues first.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/95188746-660b-4165-8694-ce3d6c0701e9
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC006
- **Test Name:** Plugin Activation Toggle and Deletion
- **Test Code:** [TC006_Plugin_Activation_Toggle_and_Deletion.py](./TC006_Plugin_Activation_Toggle_and_Deletion.py)
- **Test Error:** Testing stopped due to persistent connection error preventing login and access to admin panel. Unable to perform plugin activation and deletion tests as required.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/bfd9bbcf-c750-4706-af38-ba719398f6d8
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** Backup Creation and Download
- **Test Code:** [TC007_Backup_Creation_and_Download.py](./TC007_Backup_Creation_and_Download.py)
- **Test Error:** The backup system verification task cannot proceed because the admin panel login is blocked by a persistent server connection error. Multiple login attempts including demo quick login failed. Please resolve the server connectivity issue to enable backup testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/b5e3651c-4588-4aea-b59f-711a1edcb683
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** Backup Restore Functionality
- **Test Code:** [TC008_Backup_Restore_Functionality.py](./TC008_Backup_Restore_Functionality.py)
- **Test Error:** The testing task to verify system backup and restore functionality could not be completed due to a critical server connection error preventing login to the admin panel. The issue was reported as requested. No further testing was possible without resolving this connectivity problem.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/71198000-805b-4ec1-8706-6a1464e6c06a
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** Audit Log Filtering and Download
- **Test Code:** [TC009_Audit_Log_Filtering_and_Download.py](./TC009_Audit_Log_Filtering_and_Download.py)
- **Test Error:** The audit logs admin page is currently inaccessible due to a server error (500 Internal Server Error). Testing of filtering and downloading audit logs cannot proceed until this issue is resolved.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/admin/audit-logs:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/d468b5fb-9913-40ee-a067-96b02f383bf0
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** Rate Limiting and CSRF Protection on Admin POST Routes
- **Test Code:** [TC010_Rate_Limiting_and_CSRF_Protection_on_Admin_POST_Routes.py](./TC010_Rate_Limiting_and_CSRF_Protection_on_Admin_POST_Routes.py)
- **Test Error:** Testing stopped due to server connection error preventing login and access to admin POST routes. Unable to verify rate limiting and CSRF protections without backend connectivity.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/Bishwo_Calculator/api/login:0:0)
[ERROR] Login error: SyntaxError: Unexpected token 'E', "Exception:"... is not valid JSON (at https://localhost/bishwo_calculator/login:743:16)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/1587d666-1c0c-4934-8bfd-f2bb96a8a3b1
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC011
- **Test Name:** Structured Logging and Audit Logs Integrity
- **Test Code:** [TC011_Structured_Logging_and_Audit_Logs_Integrity.py](./TC011_Structured_Logging_and_Audit_Logs_Integrity.py)
- **Test Error:** The task to confirm that general and audit logs are structured properly, contain required fields, and record critical events cannot be completed because the admin panel or log viewer interface is not accessible. The page /Bishwo_Calculator/modules/index.php returns a 404 error. System events and user actions were triggered successfully on the frontend to generate logs, but verification of these logs is not possible without access to the log viewer. Please resolve the missing admin panel or provide access to the logs for further testing.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/bishwo_calculator/assets/icons/icon-192.png:0:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/assets/icons/icon-192.png:0:0)
[ERROR] Refused to apply style from 'https://localhost/Bishwo_Calculator/assets/css/site.css' because its MIME type ('text/html') is not a supported stylesheet MIME type, and strict MIME checking is enabled. (at https://localhost/bishwo_calculator/site:2076:0)
[ERROR] Failed to load resource: the server responded with a status of 404 (Not Found) (at https://localhost/Bishwo_Calculator/modules/index.php:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/fa4ce864-4746-47e6-bfea-343319083632
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC012
- **Test Name:** API Health Endpoint Response
- **Test Code:** [TC012_API_Health_Endpoint_Response.py](./TC012_API_Health_Endpoint_Response.py)
- **Test Error:** The health endpoint /api/v1/health is currently returning a 500 Internal Server Error, preventing validation of its status and JSON content. Further testing cannot proceed until this server error is resolved.
Browser Console Logs:
[ERROR] Failed to load resource: the server responded with a status of 400 (Bad Request) (at http://localhost/bishwo_calculator:0:0)
[ERROR] Failed to load resource: the server responded with a status of 500 (Internal Server Error) (at https://localhost/api/v1/health:0:0)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/012568fa-5490-4d12-a0bc-518e6a49a9c1/0dd5b5ed-d098-4a55-a78a-1cd809c8eee1
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