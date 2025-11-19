import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
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
    headers = {"Accept": "application/json"}

    # Test valid sections - expect 200
    for section in valid_sections:
        url = f"{BASE_URL}/admin/settings/{section}"
        try:
            response = requests.get(url, auth=AUTH, headers=headers, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request to valid section '{section}' failed with exception: {e}"
        assert response.status_code == 200, (
            f"Expected 200 OK for valid section '{section}', got {response.status_code}. "
            f"Response: {response.text}"
        )
        # Optionally check content structure if known, here we just check JSON response
        try:
            data = response.json()
        except Exception as e:
            assert False, f"Response for section '{section}' is not valid JSON: {e}"
        assert isinstance(data, dict), f"Expected JSON object for section '{section}', got {type(data)}"

    # Test invalid section - expect 404
    invalid_section = "invalidSection12345"
    url_invalid = f"{BASE_URL}/admin/settings/{invalid_section}"
    try:
        response_invalid = requests.get(url_invalid, auth=AUTH, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to invalid section failed with exception: {e}"
    assert response_invalid.status_code == 404, (
        f"Expected 404 Not Found for invalid section, got {response_invalid.status_code}. "
        f"Response: {response_invalid.text}"
    )

    # Test access control by omitting auth - expect 403 or 401
    for section in valid_sections:
        url = f"{BASE_URL}/admin/settings/{section}"
        try:
            response_no_auth = requests.get(url, headers=headers, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request without auth failed with exception: {e}"
        # The system might respond 403 or 401 depending on API design for unauthorized
        assert response_no_auth.status_code in (401, 403), (
            f"Expected 401 or 403 for unauthenticated access to section '{section}', "
            f"got {response_no_auth.status_code}. Response: {response_no_auth.text}"
        )

test_admin_settings_section_retrieval()
