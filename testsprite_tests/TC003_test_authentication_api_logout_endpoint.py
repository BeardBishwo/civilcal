import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30


def test_authentication_api_logout_endpoint():
    session = requests.Session()
    login_url = f"{BASE_URL}/api/login"
    logout_url = f"{BASE_URL}/api/logout"

    # Login payload using username and password as per API schema
    login_payload = {
        "username": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }

    # Perform login with basic token authentication: Here token means use basic auth to get session + token
    try:
        # Login to create authenticated session
        login_resp = session.post(
            login_url,
            json=login_payload,
            timeout=TIMEOUT
        )
        # Assert login success first
        assert login_resp.status_code == 200, f"Login failed with status code {login_resp.status_code}"

        # Authenticated logout request
        logout_resp = session.get(logout_url, timeout=TIMEOUT)

        # Assert logout success
        assert logout_resp.status_code == 200, f"Logout failed with status code {logout_resp.status_code}"

        # After logout, session should be destroyed. Try logout again with same session expecting 401
        logout_resp_unauth = session.get(logout_url, timeout=TIMEOUT)
        assert logout_resp_unauth.status_code == 401, (
            f"Expected 401 after logout but got {logout_resp_unauth.status_code}"
        )

        # Also test unauthenticated logout request with a fresh session, expect 401
        fresh_session = requests.Session()
        logout_resp_noauth = fresh_session.get(logout_url, timeout=TIMEOUT)
        assert logout_resp_noauth.status_code == 401, (
            f"Expected 401 for unauthenticated logout but got {logout_resp_noauth.status_code}"
        )

    finally:
        session.close()

test_authentication_api_logout_endpoint()