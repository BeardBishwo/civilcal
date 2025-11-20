import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_authentication_api_check_username_endpoint():
    url = f"{BASE_URL}/api/check-username"
    headers = {
        "Content-Type": "application/json",
        "Accept": "application/json"
    }

    # Test case: valid username availability check (expected 200)
    valid_payload = {
        "username": "uniqueusername12345"
    }
    try:
        response = requests.post(
            url,
            json=valid_payload,
            headers=headers,
            auth=HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD),
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request failed for valid payload: {e}"

    assert response.status_code == 200, f"Expected 200, got {response.status_code}"
    json_data = response.json()
    assert "available" in json_data or "suggestions" in json_data, "Response missing expected keys"

    # Test case: bad request check (expected 400)
    # Send empty payload or missing required fields to provoke 400
    bad_payload = {}
    try:
        response_bad = requests.post(
            url,
            json=bad_payload,
            headers=headers,
            auth=HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD),
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request failed for bad payload: {e}"

    assert response_bad.status_code == 400, f"Expected 400, got {response_bad.status_code}"

test_authentication_api_check_username_endpoint()