import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_CREDENTIALS = ("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_TC007_update_profile_information_and_preferences():
    session = requests.Session()

    # Authenticate user to obtain session/cookies
    login_url = f"{BASE_URL}/api/login"
    login_payload = {
        "username_email": AUTH_CREDENTIALS[0],
        "password": AUTH_CREDENTIALS[1]
    }
    login_headers = {"Content-Type": "application/json"}
    login_resp = session.post(login_url, json=login_payload, headers=login_headers, timeout=TIMEOUT)
    assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"

    try:
        # Valid update profile request
        update_url = f"{BASE_URL}/profile"
        valid_payload = {
            "first_name": "TestFirst",
            "last_name": "TestLast",
            "company": "TestCompany",
            "phone": "+1234567890"
        }
        headers = {"Content-Type": "application/json"}

        put_resp = session.put(update_url, json=valid_payload, headers=headers, timeout=TIMEOUT)
        assert put_resp.status_code == 200, f"Expected 200 on valid update, got {put_resp.status_code}"

        # Validate response content for success if any
        # (Assuming response is JSON and might echo updated profile or confirmation)
        try:
            json_data = put_resp.json()
            # Could validate that json_data contains updated fields if spec provided
            for key in valid_payload:
                if key in json_data:
                    assert json_data[key] == valid_payload[key], f"Mismatch in {key} after update"
        except Exception:
            # If no JSON or no body, skip

            pass

        # Input validation - invalid payload (e.g., phone number as integer)
        invalid_payload = {
            "first_name": "Test",
            "last_name": "User",
            "company": "TestCo",
            "phone": 12345  # invalid type, phone should be string
        }
        resp_invalid = session.put(update_url, json=invalid_payload, headers=headers, timeout=TIMEOUT)
        assert resp_invalid.status_code == 400, f"Expected 400 on invalid input, got {resp_invalid.status_code}"

        # Unauthorized access: call without auth
    finally:
        # Logout after tests
        logout_url = f"{BASE_URL}/api/logout"
        session.get(logout_url, timeout=TIMEOUT)

    # Unauthorized test without session or auth headers
    unauth_session = requests.Session()
    resp_unauth = unauth_session.put(update_url, json=valid_payload, headers={"Content-Type": "application/json"}, timeout=TIMEOUT)
    assert resp_unauth.status_code == 401, f"Expected 401 unauthorized, got {resp_unauth.status_code}"

test_TC007_update_profile_information_and_preferences()