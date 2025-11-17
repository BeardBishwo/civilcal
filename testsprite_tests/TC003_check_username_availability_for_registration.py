import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json"
}
TIMEOUT = 30

def test_check_username_availability():
    auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)

    # Valid username availability check
    payload_valid = {"username": "testuser123"}
    try:
        response = requests.post(
            f"{BASE_URL}/api/check-username",
            json=payload_valid,
            headers=HEADERS,
            auth=auth,
            timeout=TIMEOUT
        )
        assert response.status_code == 200, f"Expected 200 OK, got {response.status_code}"
        data = response.json()
        assert "available" in data or "suggestions" in data, "Response missing expected keys"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

    # Invalid input: missing username field
    payload_invalid_empty = {}
    try:
        response = requests.post(
            f"{BASE_URL}/api/check-username",
            json=payload_invalid_empty,
            headers=HEADERS,
            auth=auth,
            timeout=TIMEOUT
        )
        assert response.status_code == 400, f"Expected 400 Bad Request for missing username, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

    # Invalid input: username is None
    payload_invalid_none = {"username": None}
    try:
        response = requests.post(
            f"{BASE_URL}/api/check-username",
            json=payload_invalid_none,
            headers=HEADERS,
            auth=auth,
            timeout=TIMEOUT
        )
        assert response.status_code == 400, f"Expected 400 Bad Request for null username, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

    # Invalid input: username as an integer
    payload_invalid_type = {"username": 123456}
    try:
        response = requests.post(
            f"{BASE_URL}/api/check-username",
            json=payload_invalid_type,
            headers=HEADERS,
            auth=auth,
            timeout=TIMEOUT
        )
        # Assuming server rejects invalid types with 400
        assert response.status_code == 400, f"Expected 400 Bad Request for non-string username, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

test_check_username_availability()