import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_user_management_listing_access():
    url = f"{BASE_URL}/admin/users"
    headers = {
        "Accept": "application/json"
    }

    # Test authorized access
    try:
        response = requests.get(url, headers=headers, auth=HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD), timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Authorized request to /admin/users failed due to network error: {e}"
    assert response.status_code == 200, f"Expected 200 OK for authorized admin, got {response.status_code}"
    assert response.content, "Response content is empty for authorized admin access"

    # Test unauthorized access (without auth)
    try:
        response_unauth = requests.get(url, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Unauthorized request to /admin/users failed due to network error: {e}"
    assert response_unauth.status_code == 403, f"Expected 403 Forbidden for unauthorized user, got {response_unauth.status_code}"


test_user_management_listing_access()