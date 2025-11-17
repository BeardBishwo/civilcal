import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_retrieve_profile_authenticated_user():
    headers = {"Accept": "application/json"}

    # Test unauthorized access - no auth headers
    unauthorized_response = requests.get(f"{BASE_URL}/profile", headers=headers, timeout=TIMEOUT)
    assert unauthorized_response.status_code == 401, f"Expected 401 Unauthorized, got {unauthorized_response.status_code}"

    # Test authorized access - basic token auth (HTTP Basic Auth)
    try:
        auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)
        auth_response = requests.get(f"{BASE_URL}/profile", headers=headers, auth=auth, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

    assert auth_response.status_code == 200, f"Expected 200 OK, got {auth_response.status_code}"

    try:
        profile_data = auth_response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    # Basic validation of profile structure
    assert isinstance(profile_data, dict), "Profile response is not a JSON object"
    # The profile must include at least one identifying field like 'email' or 'username'
    assert any(key in profile_data for key in ("email", "username", "first_name", "last_name")), \
        "Profile JSON does not contain expected user fields"

test_retrieve_profile_authenticated_user()