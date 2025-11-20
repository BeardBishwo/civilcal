import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
AUTH_CREDENTIALS = ("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_admin_panel_api_dashboard_endpoint():
    url = f"{BASE_URL}/admin"

    # Case 1: Authenticated admin user gets 200
    try:
        response = requests.get(url, auth=HTTPBasicAuth(*AUTH_CREDENTIALS), timeout=TIMEOUT, allow_redirects=False)
        assert response.status_code == 200, f"Expected 200 for admin user, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for authenticated admin user: {e}"

    # Case 2: Unauthenticated user gets 302 redirect
    try:
        response = requests.get(url, timeout=TIMEOUT, allow_redirects=False)
        assert response.status_code == 302, f"Expected 302 redirect for unauthenticated user, got {response.status_code}"
        # Optionally check 'Location' header is present for redirect target
        assert 'Location' in response.headers, "Redirect response missing 'Location' header"
    except requests.RequestException as e:
        assert False, f"Request failed for unauthenticated user: {e}"

    # Case 3: Authenticated non-admin user gets 403 Forbidden
    # To simulate non-admin, authenticate with valid user creds but not admin.
    # Since no test credentials provided for non-admin, simulate by registering a non-admin user,
    # log in to get token, then use token. Without token authentication, fallback to basic auth with assumed non-admin.
    # Since no direct API for login token extraction described, we will try with the same password but a different user.
    # Because no alternative user credentials are provided, we simulate failure by logging in with invalid roles.
    # So here, let's create a non-admin user via /api/register and then test access.
    # Wrap creation and deletion in try-finally.
    register_url = f"{BASE_URL}/api/register"
    login_url = f"{BASE_URL}/api/login"
    non_admin_username = "testnonadminuser"
    non_admin_email = "testnonadminuser@example.com"
    non_admin_password = "TestPass123!"
    non_admin_full_name = "Test NonAdmin User"
    non_admin_roles = []  # No engineer_roles or roles, non-admin
    headers = {"Content-Type": "application/json"}

    non_admin_token = None
    user_created = False
    try:
        # Register non-admin user
        reg_payload = {
            "username": non_admin_username,
            "email": non_admin_email,
            "password": non_admin_password,
            "full_name": non_admin_full_name,
            "engineer_roles": non_admin_roles
        }
        reg_resp = requests.post(register_url, json=reg_payload, headers=headers, timeout=TIMEOUT)
        if reg_resp.status_code == 200:
            user_created = True
        else:
            # If user exists or another failure, continue, maybe user exists
            user_created = False

        # Login non-admin user to obtain auth token or use session cookie
        login_payload = {
            "username": non_admin_username,
            "password": non_admin_password
        }
        login_resp = requests.post(login_url, json=login_payload, headers=headers, timeout=TIMEOUT)
        assert login_resp.status_code == 200, f"Login failed for non-admin user with status {login_resp.status_code}"
        # Extract auth cookie or header if any
        session_cookies = login_resp.cookies

        # Access /admin endpoint with non-admin user session cookies
        response = requests.get(url, cookies=session_cookies, timeout=TIMEOUT, allow_redirects=False)
        assert response.status_code == 403, f"Expected 403 Forbidden for non-admin user, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for non-admin user: {e}"
    finally:
        # Cleanup: If user was created, ideally delete the user — no API provided to delete user,
        # so this step is skipped due to lack of API.
        pass

test_admin_panel_api_dashboard_endpoint()