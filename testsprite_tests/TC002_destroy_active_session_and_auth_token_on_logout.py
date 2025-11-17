import requests

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_CREDENTIALS = {
    "username_email": "uniquebishwo@gmail.com",
    "password": "c9PU7XAsAADYk_A"
}
TIMEOUT = 30


def test_destroy_active_session_and_auth_token_on_logout():
    session = requests.Session()
    login_url = f"{BASE_URL}/api/login"
    logout_url = f"{BASE_URL}/api/logout"

    # Step 1: Login to get authenticated session and token
    try:
        login_resp = session.post(
            login_url,
            json=AUTH_CREDENTIALS,
            timeout=TIMEOUT
        )
        assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"
        # Assuming token/session cookie set here for authenticated session
        # Validate a token or session cookie presence
        # This API uses session & cookie authentication so checking cookies
        assert session.cookies, "No cookies set after login"

        # Step 2: Perform authenticated logout
        logout_resp = session.get(logout_url, timeout=TIMEOUT)
        assert logout_resp.status_code == 200, f"Logout failed with status {logout_resp.status_code}"
        # Response text or json can confirm logout success
        # We expect JSON or text confirming logout; accept any 200 as success here
        # Check also that cookies/session cleared or token invalidated
        # Session cookie likely removed or expired after logout
        # We can try to access logout again, expecting unauthorized
        # Or check that cookies cleared or another request fails auth

        # Step 3: Verify access with same session now unauthorized
        logout_resp_2 = session.get(logout_url, timeout=TIMEOUT)
        assert logout_resp_2.status_code == 401, (
            f"Expected 401 Unauthorized after logout but got {logout_resp_2.status_code}"
        )
    finally:
        session.close()

    # Step 4: Test logout endpoint without authentication
    unauth_session = requests.Session()
    try:
        unauth_logout_resp = unauth_session.get(logout_url, timeout=TIMEOUT)
        assert unauth_logout_resp.status_code == 401, (
            f"Expected 401 Unauthorized for unauthenticated logout but got {unauth_logout_resp.status_code}"
        )
    finally:
        unauth_session.close()


test_destroy_active_session_and_auth_token_on_logout()