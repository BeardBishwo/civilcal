import requests
import string
import random

BASE_URL = "http://localhost:80/Bishwo_Calculator"
REGISTER_ENDPOINT = "/api/register"
DELETE_ENDPOINT_TEMPLATE = "/api/users/{user_id}"

HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json"
}

def generate_random_username_email():
    suffix = ''.join(random.choices(string.ascii_lowercase + string.digits, k=8))
    username = f"testuser_{suffix}"
    email = f"{username}@example.com"
    return username, email

def test_register_new_user_with_valid_data():
    username, email = generate_random_username_email()
    payload = {
        "username": username,
        "email": email,
        "password": "ValidPass123!",
        "first_name": "Test",
        "last_name": "User",
        "terms_agree": True
    }
    user_id = None

    try:
        resp = requests.post(
            f"{BASE_URL}{REGISTER_ENDPOINT}",
            json=payload,
            headers=HEADERS,
            timeout=30
        )
        # Validate success or expected error if duplicate
        if resp.status_code == 200:
            data = resp.json()
            assert 'id' in data, "Response JSON must include 'id'"
            user_id = data['id']
        elif resp.status_code == 400:
            # Validation failure - should not happen with valid data
            raise AssertionError(f"Validation failure when using valid data: {resp.text}")
        elif resp.status_code == 409:
            # Duplicate user detected
            raise AssertionError("User with same username or email already exists.")
        else:
            resp.raise_for_status()

        # Additionally, test error handling for duplicate username/email, by sending again
        resp_dup = requests.post(
            f"{BASE_URL}{REGISTER_ENDPOINT}",
            json=payload,
            headers=HEADERS,
            timeout=30
        )
        assert resp_dup.status_code == 409, "Duplicate registration must return 409 Conflict"

        # Test validation failure by missing required 'password'
        invalid_payload = payload.copy()
        invalid_payload.pop("password")
        resp_invalid = requests.post(
            f"{BASE_URL}{REGISTER_ENDPOINT}",
            json=invalid_payload,
            headers=HEADERS,
            timeout=30
        )
        assert resp_invalid.status_code == 400, "Missing password should return 400 validation error"

    finally:
        # Cleanup: delete created user if possible
        if user_id is not None:
            try:
                delete_resp = requests.delete(
                    f"{BASE_URL}{DELETE_ENDPOINT_TEMPLATE.format(user_id=user_id)}",
                    headers=HEADERS,
                    timeout=30
                )
                # It's okay if delete_resp is 200 or 204; ignore others but do not raise here
            except Exception:
                pass

test_register_new_user_with_valid_data()
