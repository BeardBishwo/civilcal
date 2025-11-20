import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
TIMEOUT = 30
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")


def test_authentication_api_forgot_password_endpoint():
    url = f"{BASE_URL}/api/forgot-password"
    headers = {
        "Content-Type": "application/json"
    }

    # Case 1: Valid email provided - Expect 200
    valid_payload = {
        "email": "uniquebishwo@gmail.com"
    }
    try:
        response = requests.post(url, json=valid_payload, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 for valid email, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for valid email case: {e}"

    # Case 2: Missing email - Expect 400
    invalid_payload = {}
    try:
        response = requests.post(url, json=invalid_payload, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 for missing email, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for missing email case: {e}"


test_authentication_api_forgot_password_endpoint()