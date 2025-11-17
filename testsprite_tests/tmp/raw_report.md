
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** Bishwo_Calculator
- **Date:** 2025-11-17
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** user login functionality
- **Test Code:** [TC001_user_login_functionality.py](./TC001_user_login_functionality.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 48, in <module>
  File "<string>", line 26, in test_user_login_functionality
AssertionError: Expected 200, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/e7338bb6-cde2-435a-92c1-2b1e0e04430e
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** user registration process
- **Test Code:** [TC002_user_registration_process.py](./TC002_user_registration_process.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 63, in <module>
  File "<string>", line 30, in test_user_registration_process
AssertionError: Expected 201, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/97239c49-3b8a-41a3-a14e-35c3c921da4c
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** user logout operation
- **Test Code:** [TC003_user_logout_operation.py](./TC003_user_logout_operation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 56, in <module>
  File "<string>", line 17, in test_user_logout_operation
AssertionError: Login failed with status code 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/9d591908-cdcc-4bd0-8739-1f61f8e2dd71
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** admin dashboard access control
- **Test Code:** [TC004_admin_dashboard_access_control.py](./TC004_admin_dashboard_access_control.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 37, in <module>
  File "<string>", line 19, in test_admin_dashboard_access_control
AssertionError: Expected 200 for authorized admin, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/d384f297-ee5b-45c0-8593-6a28c1823eca
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** admin settings section retrieval
- **Test Code:** [TC005_admin_settings_section_retrieval.py](./TC005_admin_settings_section_retrieval.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 48, in <module>
  File "<string>", line 27, in test_admin_settings_section_retrieval
AssertionError: Expected 200 OK for section 'general', got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/6dae4f18-0238-4456-8d15-2279fa9f6b54
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
AssertionError: Expected 200 for authorized admin, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/05533e80-7254-4b2f-a125-e7eaec71c512
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** list available calculators
- **Test Code:** [TC007_list_available_calculators.py](./TC007_list_available_calculators.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 22, in <module>
  File "<string>", line 11, in test_list_available_calculators
AssertionError: Expected 200 OK but got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/62b9a3e9-1cc3-4936-afab-7506b1d3a70d
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** execute calculator function
- **Test Code:** [TC008_execute_calculator_function.py](./TC008_execute_calculator_function.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 55, in <module>
  File "<string>", line 26, in test_execute_calculator_function
AssertionError: Expected 200 OK for valid calculation, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/a7c80e9b-ffdd-4f06-b992-734a823cfbb2
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** get user profile data
- **Test Code:** [TC009_get_user_profile_data.py](./TC009_get_user_profile_data.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 36, in <module>
  File "<string>", line 17, in test_get_user_profile_data
AssertionError: Expected 200 OK for authenticated request but got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/5895fdb9-8ed6-481d-aa1c-feb456c2030d
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** update user profile information
- **Test Code:** [TC010_update_user_profile_information.py](./TC010_update_user_profile_information.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 55, in <module>
  File "<string>", line 36, in test_update_user_profile_information
AssertionError: Expected 200 OK for valid update, got 500

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/57a9732d-f941-41fe-812e-5498ec9764ba/8eeda329-db99-49c7-bd80-76685e6ebcab
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