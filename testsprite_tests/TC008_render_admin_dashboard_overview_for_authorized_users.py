import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
ADMIN_ENDPOINT = "/admin"
LOGIN_ENDPOINT = "/api/login"

USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"

TIMEOUT = 30

def test_TC008_render_admin_dashboard_overview_for_authorized_users():
    session = requests.Session()

    try:
        # Authenticate as admin user to get session cookies/auth headers
        login_payload = {
            "username_email": USERNAME,
            "password": PASSWORD
        }
        login_resp = session.post(
            f"{BASE_URL}{LOGIN_ENDPOINT}",
            json=login_payload,
            timeout=TIMEOUT
        )
        assert login_resp.status_code == 200, f"Login failed: {login_resp.text}"

        # 1. Authorized admin user access
        admin_resp = session.get(f"{BASE_URL}{ADMIN_ENDPOINT}", timeout=TIMEOUT)
        assert admin_resp.status_code == 200, f"Admin dashboard not rendered for authorized admin: {admin_resp.status_code}, {admin_resp.text}"
        content_type = admin_resp.headers.get("Content-Type", "")
        assert "text/html" in content_type.lower() or "application/json" in content_type.lower(), f"Unexpected content type for admin dashboard: {content_type}"
        assert len(admin_resp.text) > 0, "Admin dashboard response body is empty"

        # 2. Unauthenticated user access => should redirect (302) to login
        # Use a separate session without authentication
        unauth_session = requests.Session()
        unauth_resp = unauth_session.get(f"{BASE_URL}{ADMIN_ENDPOINT}", allow_redirects=False, timeout=TIMEOUT)
        assert unauth_resp.status_code == 302, f"Expected redirect (302) for unauthenticated user, got: {unauth_resp.status_code}"
        location_header = unauth_resp.headers.get("Location", "")
        assert location_header, "Redirect response missing Location header"
        # Assuming redirect location contains 'login' or similar string
        assert any(s in location_header.lower() for s in ["login", "/api/login"]), f"Redirect location does not point to login: {location_header}"

        # 3. Authenticated non-admin user access => should be forbidden (403)
        # To simulate a non-admin user, register a new user or use a known non-admin user.
        # Here we register a temporary user, then login as that user and test access.
        # Since the test instructions do not provide a non-admin user credential, we create non-admin user temporarily.

        # Register a new non-admin user
        register_endpoint = "/api/register"
        import random, string
        random_str = ''.join(random.choices(string.ascii_lowercase + string.digits, k=8))
        new_username = f"testuser_{random_str}"
        new_email = f"{new_username}@example.com"
        new_password = "TestPass123!"

        register_payload = {
            "username": new_username,
            "email": new_email,
            "password": new_password,
            "first_name": "Test",
            "last_name": "User",
            "terms_agree": True
        }

        reg_resp = session.post(f"{BASE_URL}{register_endpoint}", json=register_payload, timeout=TIMEOUT)
        assert reg_resp.status_code == 200, f"Failed to register non-admin user: {reg_resp.status_code} {reg_resp.text}"
        user_id = None
        try:
            # Login as the non-admin user
            non_admin_session = requests.Session()
            login_payload_non_admin = {
                "username_email": new_username,
                "password": new_password
            }
            login_non_admin_resp = non_admin_session.post(f"{BASE_URL}{LOGIN_ENDPOINT}", json=login_payload_non_admin, timeout=TIMEOUT)
            assert login_non_admin_resp.status_code == 200, f"Login failed for non-admin user: {login_non_admin_resp.text}"

            # Attempt to access admin dashboard as non-admin user
            non_admin_admin_resp = non_admin_session.get(f"{BASE_URL}{ADMIN_ENDPOINT}", timeout=TIMEOUT)
            assert non_admin_admin_resp.status_code == 403, f"Expected 403 Forbidden for non-admin user, got {non_admin_admin_resp.status_code}"
        finally:
            # No endpoint documented for user deletion; skip cleanup to avoid side effects. 
            # Usually, user deletion or disabling endpoints would be needed, but not provided.
            pass

    finally:
        # Logout admin user session if applicable
        logout_endpoint = "/api/logout"
        try:
            session.get(f"{BASE_URL}{logout_endpoint}", timeout=TIMEOUT)
        except Exception:
            pass


test_TC008_render_admin_dashboard_overview_for_authorized_users()