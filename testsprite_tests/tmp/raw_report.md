
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** Bishwo_Calculator
- **Date:** 2025-11-18
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** user login functionality
- **Test Code:** [TC001_user_login_functionality.py](./TC001_user_login_functionality.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 39, in <module>
  File "<string>", line 29, in test_user_login_functionality
AssertionError: Expected 200, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/e9a73325-c46e-4dac-a2c9-dfe613983451
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** user registration process
- **Test Code:** [TC002_user_registration_process.py](./TC002_user_registration_process.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 46, in <module>
  File "<string>", line 26, in test_user_registration_process
AssertionError: Expected 201, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/156a3c1c-8e42-475b-bc21-3ce2b634e586
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** user logout operation
- **Test Code:** [TC003_user_logout_operation.py](./TC003_user_logout_operation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 32, in <module>
  File "<string>", line 23, in test_user_logout_operation
AssertionError: Login failed with status code 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/948f2f5c-0717-4c57-bfdc-3c099e32fdd2
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** admin dashboard access control
- **Test Code:** [TC004_admin_dashboard_access_control.py](./TC004_admin_dashboard_access_control.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 40, in <module>
  File "<string>", line 19, in test_admin_dashboard_access_control
AssertionError: Expected 200 for authorized access, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/8c4068ae-6083-4909-a5f8-96fa1c49dc7f
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** admin settings section retrieval
- **Test Code:** [TC005_admin_settings_section_retrieval.py](./TC005_admin_settings_section_retrieval.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 63, in <module>
  File "<string>", line 27, in test_admin_settings_section_retrieval
AssertionError: Expected 200 OK for valid section 'general', got 500. Response: <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>500 Internal Server Error</title>
</head><body>
<h1>Internal Server Error</h1>
<p>The server encountered an internal error or
misconfiguration and was unable to complete
your request.</p>
<p>Please contact the server administrator at 
 admin@example.com to inform them of the time this error occurred,
 and the actions you performed just before this error.</p>
<p>More information about this error may be available
in the server error log.</p>
</body></html>


- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/8dd4731e-b488-4d85-b8bf-a1a6ceb00349
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC006
- **Test Name:** user management listing access
- **Test Code:** [TC006_user_management_listing_access.py](./TC006_user_management_listing_access.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 32, in <module>
  File "<string>", line 19, in test_user_management_listing_access
AssertionError: Expected 200 OK for authorized access, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/ed33578b-e198-430e-b098-2e17467e60fe
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** list available calculators
- **Test Code:** [TC007_list_available_calculators.py](./TC007_list_available_calculators.py)
- **Test Error:** Traceback (most recent call last):
  File "<string>", line 16, in test_list_available_calculators
  File "/var/task/requests/models.py", line 1024, in raise_for_status
    raise HTTPError(http_error_msg, response=self)
requests.exceptions.HTTPError: 500 Server Error: Internal Server Error for url: http://localhost:80/calculators

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 46, in <module>
  File "<string>", line 18, in test_list_available_calculators
AssertionError: Request failed: 500 Server Error: Internal Server Error for url: http://localhost:80/calculators

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/a5f36575-6dc0-4851-9049-708c0b959991
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** execute calculator function
- **Test Code:** [TC008_execute_calculator_function.py](./TC008_execute_calculator_function.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 56, in <module>
  File "<string>", line 31, in test_execute_calculator_function
AssertionError: Expected status 200 but got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/34eaff00-074c-4e43-8c9d-09af7aae6e0b
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** get user profile data
- **Test Code:** [TC009_get_user_profile_data.py](./TC009_get_user_profile_data.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 46, in <module>
  File "<string>", line 23, in test_get_user_profile_data
AssertionError: Expected 200 OK for authenticated request, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/adbbbc48-4efa-4a96-a245-74210dd4cfc8
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** update user profile information
- **Test Code:** [TC010_update_user_profile_information.py](./TC010_update_user_profile_information.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 49, in <module>
  File "<string>", line 32, in test_update_user_profile_information
AssertionError: Expected 200 OK for valid update, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/c7000806-f16f-44b9-b8d5-701b7bb2b50e/aee17b40-f29e-4c7b-9a30-373d7c9e9b4a
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