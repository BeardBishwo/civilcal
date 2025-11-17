import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/Bishwo_Calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "SecurePass123!"
TIMEOUT = 30

def test_user_management_listing_access():
    url = f"{BASE_URL}/admin/users"
    
    # Use session-based authentication
    session = requests.Session()
    
    # Login first
    login_response = session.post(
        f"{BASE_URL}/api/login.php",
        json={"username_email": AUTH_USERNAME, "password": AUTH_PASSWORD},
        headers={"Content-Type": "application/json"},
        timeout=TIMEOUT
    )
    assert login_response.status_code == 200, f"Login failed with {login_response.status_code}"

    # Test authorized access (returns HTML page, not JSON)
    try:
        response = session.get(url, timeout=TIMEOUT, allow_redirects=True)
        assert response.status_code == 200, f"Expected 200 for authorized admin, got {response.status_code}"
        # Check for user management page content
        content_lower = response.text.lower()
        assert ("user" in content_lower or "admin" in content_lower), \
            "Expected user management page content"
    except requests.RequestException as e:
        assert False, f"Request to authorized admin /admin/users failed: {str(e)}"

    # Test unauthorized access (no auth) - should redirect to login
    try:
        response_unauth = requests.get(url, timeout=TIMEOUT, allow_redirects=False)
        # Accept 401, 403, or 302 (redirect to login)
        assert response_unauth.status_code in [401, 403, 302], \
            f"Expected 401, 403, or 302 for unauthorized access, got {response_unauth.status_code}"
    except requests.RequestException as e:
        assert False, f"Request to unauthorized /admin/users failed: {str(e)}"

test_user_management_listing_access()