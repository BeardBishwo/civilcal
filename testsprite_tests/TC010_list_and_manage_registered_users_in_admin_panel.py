import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
ADMIN_USERS_ENDPOINT = "/admin/users"
TIMEOUT = 30

auth_credentials = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")

def test_admin_users_list_and_access_control():
    # 1. Test authorized access with valid basic token credentials
    try:
        response = requests.get(
            BASE_URL + ADMIN_USERS_ENDPOINT,
            auth=auth_credentials,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request for authorized admin user failed: {e}"
    else:
        assert response.status_code == 200, f"Expected 200 OK for authorized admin, got {response.status_code}"
        # The user management table should be present in HTML or JSON - minimal check:
        content_type = response.headers.get("Content-Type", "")
        assert "html" in content_type.lower() or "json" in content_type.lower(), \
            f"Unexpected content type for admin users list: {content_type}"
        # Also basic content check for some keywords indicating user management
        content_lower = response.text.lower()
        assert ("user" in content_lower and "manage" in content_lower) or "table" in content_lower, \
            "Response content does not seem to contain user management table"

    # 2. Test unauthorized access (no credentials)
    try:
        response_unauth = requests.get(
            BASE_URL + ADMIN_USERS_ENDPOINT,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request for unauthorized access failed: {e}"
    else:
        assert response_unauth.status_code in (401, 403), f"Expected 401 or 403 for unauthorized access, got {response_unauth.status_code}"

test_admin_users_list_and_access_control()