import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/Bishwo_Calculator"
ADMIN_ENDPOINT = "/admin"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "SecurePass123!"
TIMEOUT = 30


def test_admin_dashboard_access_control():
    # Test authorized access using session-based authentication
    session = requests.Session()
    
    try:
        # First login as admin
        login_response = session.post(
            f"{BASE_URL}/api/login.php",
            json={"username_email": USERNAME, "password": PASSWORD},
            headers={"Content-Type": "application/json"},
            timeout=TIMEOUT
        )
        assert login_response.status_code == 200, f"Login failed with {login_response.status_code}"
        
        # Now access admin dashboard with session
        response = session.get(
            BASE_URL + ADMIN_ENDPOINT,
            timeout=TIMEOUT,
            allow_redirects=True
        )
        assert response.status_code == 200, f"Expected 200 for authorized admin, got {response.status_code}"
        # Check that we got the admin dashboard, not login page
        assert "dashboard" in response.text.lower() or "admin" in response.text.lower(), "Admin dashboard content not found in response."
    except requests.RequestException as e:
        assert False, f"Request to admin endpoint failed for authorized user: {e}"

    # Test unauthorized access - no auth provided (should redirect to login)
    try:
        response_unauth = requests.get(
            BASE_URL + ADMIN_ENDPOINT,
            timeout=TIMEOUT,
            allow_redirects=False
        )
        # Accept 401, 403, or 302 (redirect to login)
        assert response_unauth.status_code in [401, 403, 302], \
            f"Expected 403, 401, or 302 for unauthorized user, got {response_unauth.status_code}"
    except requests.RequestException as e:
        assert False, f"Request to admin endpoint failed for unauthorized user: {e}"


test_admin_dashboard_access_control()