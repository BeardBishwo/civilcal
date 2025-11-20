import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_admin_panel_api_settings_section_endpoint():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    headers = {
        "Accept": "text/html"
    }

    valid_sections = [
        "general",
        "users",
        "security",
        "email",
        "api",
        "performance",
        "advanced"
    ]
    unknown_section = "unknown_section_xyz"

    # Test each valid section returns 200
    for section in valid_sections:
        url = f"{BASE_URL}/admin/settings/{section}"
        try:
            response = requests.get(url, auth=auth, headers=headers, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request to {url} failed with exception: {e}"
        assert response.status_code == 200, f"Expected 200 for section '{section}', got {response.status_code}"

    # Test unknown section returns 404
    url_404 = f"{BASE_URL}/admin/settings/{unknown_section}"
    try:
        response_404 = requests.get(url_404, auth=auth, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to {url_404} failed with exception: {e}"
    assert response_404.status_code == 404, f"Expected 404 for unknown section, got {response_404.status_code}"

    # Test forbidden access: Try without auth or with invalid auth
    url_forbidden = f"{BASE_URL}/admin/settings/{valid_sections[0]}"
    # Without auth
    try:
        response_no_auth = requests.get(url_forbidden, headers=headers, timeout=TIMEOUT, allow_redirects=False)
    except requests.RequestException as e:
        assert False, f"Request to {url_forbidden} without auth failed with exception: {e}"
    # Accept 302 (redirect), 401 (unauthorized), or 403 (forbidden)
    assert response_no_auth.status_code in [302, 401, 403], \
        f"Expected 302, 401, or 403 for no auth, got {response_no_auth.status_code}"

    # With invalid auth
    invalid_auth = HTTPBasicAuth("invaliduser@example.com", "wrongpassword")
    try:
        response_invalid_auth = requests.get(url_forbidden, auth=invalid_auth, headers=headers, timeout=TIMEOUT, allow_redirects=False)
    except requests.RequestException as e:
        assert False, f"Request to {url_forbidden} with invalid auth failed with exception: {e}"
    # Accept 401 (unauthorized) or 403 (forbidden)
    assert response_invalid_auth.status_code in [401, 403], \
        f"Expected 401 or 403 for invalid auth, got {response_invalid_auth.status_code}"

test_admin_panel_api_settings_section_endpoint()
