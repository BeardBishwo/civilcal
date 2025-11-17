
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** Bishwo_Calculator
- **Date:** 2025-11-17
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** authenticate using username or email plus password
- **Test Code:** [TC001_authenticate_using_username_or_email_plus_password.py](./TC001_authenticate_using_username_or_email_plus_password.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/d071a0ba-7612-4002-bf57-85778d57fb0e
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** destroy active session and auth token on logout
- **Test Code:** [TC002_destroy_active_session_and_auth_token_on_logout.py](./TC002_destroy_active_session_and_auth_token_on_logout.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 58, in <module>
  File "<string>", line 41, in test_destroy_active_session_and_auth_token_on_logout
AssertionError: Expected 401 Unauthorized after logout but got 200

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/3fab8f73-0f0f-426d-a778-40892221b697
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** check username availability for registration
- **Test Code:** [TC003_check_username_availability_for_registration.py](./TC003_check_username_availability_for_registration.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/e0940e51-f7a9-4c29-bcb1-6d152b1815ff
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** trigger password reset email dispatch
- **Test Code:** [TC004_trigger_password_reset_email_dispatch.py](./TC004_trigger_password_reset_email_dispatch.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/dcc9b4a7-e616-4567-aa96-2dd10fbd9d10
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** register a new user with valid data
- **Test Code:** [TC005_register_a_new_user_with_valid_data.py](./TC005_register_a_new_user_with_valid_data.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 86, in <module>
  File "<string>", line 42, in test_register_new_user_with_valid_data
AssertionError: Response JSON must include 'id'

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/706f8dca-e85a-4ed0-9790-776db7eddbfb
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC006
- **Test Name:** retrieve profile for authenticated user
- **Test Code:** [TC006_retrieve_profile_for_authenticated_user.py](./TC006_retrieve_profile_for_authenticated_user.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/b0fa2ff8-49da-4c20-b079-47a348c17df6
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** update profile information and preferences
- **Test Code:** [TC007_update_profile_information_and_preferences.py](./TC007_update_profile_information_and_preferences.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/2d9e9ea1-32a4-400b-a3f8-96616c20ac1b
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** render admin dashboard overview for authorized users
- **Test Code:** [TC008_render_admin_dashboard_overview_for_authorized_users.py](./TC008_render_admin_dashboard_overview_for_authorized_users.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 98, in <module>
  File "<string>", line 40, in test_TC008_render_admin_dashboard_overview_for_authorized_users
AssertionError: Expected redirect (302) for unauthenticated user, got: 200

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/5ba146b0-fe7f-4828-93dd-fe3b9e638cbc
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** return requested admin settings section ui
- **Test Code:** [TC009_return_requested_admin_settings_section_ui.py](./TC009_return_requested_admin_settings_section_ui.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/95185782-5e6b-4761-8a77-fb503dd40bb3
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** list and manage registered users in admin panel
- **Test Code:** [TC010_list_and_manage_registered_users_in_admin_panel.py](./TC010_list_and_manage_registered_users_in_admin_panel.py)
- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/274b56cb-b8d6-4463-af7a-0860bb9edf25/26a48994-2480-4fc8-ab13-266919d66d0e
- **Status:** ✅ Passed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---


## 3️⃣ Coverage & Matching Metrics

- **70.00** of tests passed

| Requirement        | Total Tests | ✅ Passed | ❌ Failed  |
|--------------------|-------------|-----------|------------|
| ...                | ...         | ...       | ...        |
---


## 4️⃣ Key Gaps / Risks
{AI_GNERATED_KET_GAPS_AND_RISKS}
---