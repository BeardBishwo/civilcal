import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_admin_dashboard_access_control():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    admin_url = f"{BASE_URL}/admin"

    # Test authorized access
    try:
        response = requests.get(admin_url, auth=auth, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to admin dashboard failed: {e}"
    else:
        assert response.status_code == 200, f"Expected 200 for authorized access, got {response.status_code}"
        # Optionally check content to confirm it's the admin dashboard view
        assert "dashboard" in response.text.lower() or "admin" in response.text.lower()

    # Test unauthorized access with invalid credentials
    invalid_auth = HTTPBasicAuth("invaliduser@example.com", "wrongpassword")
    try:
        response = requests.get(admin_url, auth=invalid_auth, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to admin dashboard with invalid auth failed: {e}"
    else:
        assert response.status_code in (401, 403), f"Expected 401 or 403 for unauthorized access, got {response.status_code}"

    # Test unauthorized access with no credentials
    try:
        response = requests.get(admin_url, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to admin dashboard without auth failed: {e}"
    else:
        assert response.status_code in (401, 403), f"Expected 401 or 403 for no auth access, got {response.status_code}"

test_admin_dashboard_access_control()
