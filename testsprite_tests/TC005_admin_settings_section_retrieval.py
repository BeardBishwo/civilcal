import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_admin_settings_section_retrieval():
    valid_sections = [
        "general",
        "users",
        "security",
        "email",
        "api",
        "performance",
        "advanced"
    ]

    headers = {
        "Accept": "application/json"
    }

    # Test valid sections: expect 200 OK and JSON response
    for section in valid_sections:
        url = f"{BASE_URL}/admin/settings/{section}"
        try:
            resp = requests.get(url, auth=AUTH, headers=headers, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request to valid section '{section}' failed: {e}"
        assert resp.status_code == 200, (
            f"Expected 200 OK for section '{section}', got {resp.status_code}"
        )
        # The response should be JSON
        try:
            data = resp.json()
        except ValueError:
            assert False, f"Response for section '{section}' is not valid JSON"
        assert isinstance(data, dict), f"Response JSON for section '{section}' is not a dict"

    # Test invalid section: expect 404 Not Found
    invalid_section = "nonexistent_section_12345"
    invalid_url = f"{BASE_URL}/admin/settings/{invalid_section}"
    try:
        resp = requests.get(invalid_url, auth=AUTH, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to invalid section failed: {e}"
    assert resp.status_code == 404, (
        f"Expected 404 Not Found for invalid section, got {resp.status_code}"
    )

    # Test access control by using invalid credentials: expect 403 or 401
    wrong_auth = HTTPBasicAuth("wronguser@example.com", "wrongpassword")
    url = f"{BASE_URL}/admin/settings/general"
    try:
        resp = requests.get(url, auth=wrong_auth, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request with invalid credentials failed: {e}"
    assert resp.status_code in (401, 403), (
        f"Expected 401 or 403 for unauthorized access, got {resp.status_code}"
    )

test_admin_settings_section_retrieval()
