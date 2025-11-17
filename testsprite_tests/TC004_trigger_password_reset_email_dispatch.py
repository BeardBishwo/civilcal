import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_trigger_password_reset_email_dispatch():
    headers = {
        "Content-Type": "application/json"
    }

    # Test valid registered email triggers reset email queued (200)
    valid_email = "uniquebishwo@gmail.com"
    valid_payload = {"email": valid_email}
    try:
        response_valid = requests.post(
            f"{BASE_URL}/api/forgot-password",
            json=valid_payload,
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_valid.status_code == 200, f"Expected 200 for valid email, got {response_valid.status_code}"
        # Optional: check response content if needed
    except requests.RequestException as e:
        assert False, f"Request failed for valid email: {e}"

    # Test non-registered email returns 404 Account not found
    invalid_email = "nonexistent_email_for_test_999@example.com"
    invalid_payload = {"email": invalid_email}
    try:
        response_invalid = requests.post(
            f"{BASE_URL}/api/forgot-password",
            json=invalid_payload,
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_invalid.status_code == 404, f"Expected 404 for unknown email, got {response_invalid.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for invalid email: {e}"

test_trigger_password_reset_email_dispatch()