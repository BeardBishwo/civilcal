import requests

BASE_URL = "http://localhost/Bishwo_Calculator"
LOGIN_CREDENTIALS = {
    "username_email": "uniquebishwo@gmail.com",
    "password": "SecurePass123!"
}
HEADERS = {"Content-Type": "application/json"}
TIMEOUT = 30

def test_update_user_profile_information():
    url = f"{BASE_URL}/api/profile.php"
    login_url = f"{BASE_URL}/api/login.php"

    # Create session and login
    session = requests.Session()
    try:
        login_response = session.post(login_url, json=LOGIN_CREDENTIALS, timeout=TIMEOUT)
        assert login_response.status_code == 200, f"Login failed with status {login_response.status_code}"
    except requests.RequestException as e:
        assert False, f"Login request failed: {e}"

    # Valid update payload (only use fields that exist in users table)
    valid_payload = {
        "first_name": "UpdatedFirstName",
        "last_name": "UpdatedLastName",
        "company": "UpdatedCompany",
        "phone": "1234567890"
    }

    # Invalid update payloads examples (empty strings or wrong data types)
    invalid_payloads = [
        {},  # missing all fields - should return 400
        {"first_name": ""},  # empty first name
        {"last_name": 123},  # invalid type for last name
        {"company": None},  # null value
        {"phone": 456},  # invalid type for phone
        {"first_name": "A"*300}  # overly long first name (assuming limit)
    ]

    # Test valid update
    try:
        response = session.put(url, json=valid_payload, headers=HEADERS, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request failed for valid payload: {e}"
    else:
        assert response.status_code == 200, f"Expected 200 OK for valid update, got {response.status_code}"
        resp_json = response.json()
        # Assert returned profile fields match updated data if returned
        for key, val in valid_payload.items():
            assert key in resp_json, f"Key {key} missing in response"
            assert resp_json[key] == val, f"Expected {key}='{val}', got '{resp_json[key]}'"

    # Test invalid updates
    for invalid_payload in invalid_payloads:
        try:
            response = session.put(url, json=invalid_payload, headers=HEADERS, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request failed for invalid payload {invalid_payload}: {e}"
        else:
            # Expecting 400 Bad Request or similar error status
            assert response.status_code == 400, (
                f"Expected 400 Bad Request for payload {invalid_payload}, got {response.status_code}"
            )

test_update_user_profile_information()
