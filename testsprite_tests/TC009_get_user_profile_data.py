import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_get_user_profile_data():
    headers = {"Accept": "application/json"}

    # Authenticated request
    try:
        resp_auth = requests.get(
            f"{BASE_URL}/profile",
            headers=headers,
            auth=HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD),
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Authenticated request failed with exception: {e}"

    assert resp_auth.status_code == 200, f"Expected 200 OK for authenticated request, got {resp_auth.status_code}"
    try:
        data = resp_auth.json()
    except ValueError:
        assert False, "Response is not valid JSON for authenticated request"
    # Basic checks for expected profile fields (based on typical user profile info)
    assert isinstance(data, dict), "Authenticated response JSON is not an object"
    # The profile data should typically contain email or username or similar
    assert ("email" in data or "username" in data or "first_name" in data or "last_name" in data), "Profile data missing expected fields"

    # Unauthenticated request (no auth header)
    try:
        resp_unauth = requests.get(
            f"{BASE_URL}/profile",
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Unauthenticated request failed with exception: {e}"

    assert resp_unauth.status_code == 401 or resp_unauth.status_code == 403, \
        f"Expected 401 or 403 for unauthenticated request, got {resp_unauth.status_code}"

test_get_user_profile_data()
