import requests
import uuid

BASE_URL = "http://localhost/Bishwo_Calculator"
TIMEOUT = 30

def test_user_registration_process():
    headers = {
        "Content-Type": "application/json"
    }

    # Generate unique user data for valid registration
    unique_suffix = str(uuid.uuid4()).replace("-", "")[:8]
    valid_user = {
        "username": f"testuser_{unique_suffix}",
        "email": f"testuser_{unique_suffix}@example.com",
        "password": "ValidPassw0rd!",
        "first_name": "Test",
        "last_name": "User"
    }

    # Testing successful registration
    try:
        response = requests.post(
            f"{BASE_URL}/api/register.php",
            json=valid_user,
            headers=headers,
            timeout=TIMEOUT
        )
        assert response.status_code == 200, f"Expected 200, got {response.status_code}"
        json_resp = response.json()
        assert isinstance(json_resp, dict), "Response is not a JSON object"
        # Check for at least 'username' or 'id' in the response
        assert 'username' in json_resp or 'id' in json_resp, "Response missing expected keys"

    finally:
        # Clean up: No user deletion API
        pass

    # Testing registration failure cases
    invalid_cases = [
        ({"username_email": "no_username@example.com", "password": "pass"}, 400),
        ({"username": "user1", "username_email": "invalid-email", "password": "pass"}, 400),
        ({"username": "user2", "username_email": "user2@example.com", "password": "123"}, 400),
        ({"username": "user3", "username_email": "user3@example.com"}, 400),
        ({"username": "", "username_email": "user4@example.com", "password": "password123"}, 400),
    ]

    for payload, expected_status in invalid_cases:
        try:
            resp = requests.post(
                f"{BASE_URL}/api/register.php",
                json=payload,
                headers=headers,
                timeout=TIMEOUT
            )
            assert resp.status_code == expected_status, (
                f"Expected {expected_status} for payload {payload}, got {resp.status_code}"
            )
        except requests.RequestException as e:
            assert False, f"RequestException occurred: {e}"

test_user_registration_process()
