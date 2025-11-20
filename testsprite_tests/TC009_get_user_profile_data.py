import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30


def test_get_user_profile():
    profile_url = f"{BASE_URL}/profile"
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    headers = {
        "Accept": "application/json",
    }

    # Authenticated request
    try:
        auth_response = requests.get(profile_url, headers=headers, auth=auth, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Authenticated request failed with exception: {e}"

    assert auth_response.status_code == 200, (
        f"Expected status code 200 for authenticated request but got {auth_response.status_code}. "
        f"Response: {auth_response.text}"
    )
    try:
        profile_data = auth_response.json()
    except ValueError:
        assert False, "Authenticated response is not valid JSON"
    assert isinstance(profile_data, dict), "Profile data should be a JSON object"

    # Unauthenticated request (no auth header)
    try:
        unauth_response = requests.get(profile_url, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Unauthenticated request failed with exception: {e}"

    assert unauth_response.status_code == 401, (
        f"Expected status code 401 for unauthenticated request but got {unauth_response.status_code}. "
        f"Response: {unauth_response.text}"
    )


test_get_user_profile()