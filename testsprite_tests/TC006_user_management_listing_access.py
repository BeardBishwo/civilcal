import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_user_management_listing_access():
    url = f"{BASE_URL}/admin/users"
    headers = {
        "Accept": "application/json"
    }
    auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)

    # Test authorized access
    try:
        response = requests.get(url, headers=headers, auth=auth, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 OK for authorized access, got {response.status_code}"
        json_data = response.json()
        assert isinstance(json_data, (list, dict)), "Response payload should be a list or dict representing users listing"
    except requests.RequestException as e:
        assert False, f"Request to {url} failed for authorized access: {e}"

    # Test unauthorized access (no auth)
    try:
        response_unauth = requests.get(url, headers=headers, timeout=TIMEOUT)
        assert response_unauth.status_code in [401, 403], f"Expected 401 or 403 for unauthorized access, got {response_unauth.status_code}"
    except requests.RequestException as e:
        assert False, f"Request to {url} failed for unauthorized access: {e}"

test_user_management_listing_access()
