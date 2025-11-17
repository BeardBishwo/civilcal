import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_return_requested_admin_settings_section_ui():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    valid_sections = ["general", "users", "security", "email", "advanced"]
    headers = {
        "Accept": "text/html"
    }

    # Test valid sections - expect 200 and some content
    for section in valid_sections:
        try:
            url = f"{BASE_URL}/admin/settings/{section}"
            response = requests.get(url, auth=auth, headers=headers, timeout=TIMEOUT)
            assert response.status_code == 200, f"Expected 200 for section '{section}', got {response.status_code}"
            # Validate response looks like HTML containing the section name (basic check)
            content = response.text.lower()
            assert section in content or len(content) > 0, f"Response content for section '{section}' seems invalid"
        except requests.exceptions.RequestException as e:
            assert False, f"Request failed for section '{section}': {e}"

    # Test invalid section - expect 404
    invalid_section = "invalidsection"
    try:
        url = f"{BASE_URL}/admin/settings/{invalid_section}"
        response = requests.get(url, auth=auth, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 404, f"Expected 404 for invalid section, got {response.status_code}"
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed for invalid section test: {e}"

test_return_requested_admin_settings_section_ui()