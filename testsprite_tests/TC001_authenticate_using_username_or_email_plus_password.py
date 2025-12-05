import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_authenticate_using_username_or_email_plus_password():
    headers = {
        "Content-Type": "application/json"
    }
    login_url = f"{BASE_URL}/api/login"

    # Successful login with username_email and password (without remember_me)
    payload_valid = {
        "username_email": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }
    try:
        response = requests.post(login_url, json=payload_valid, headers=headers, timeout=TIMEOUT)
        print(f"Response Status Code: {response.status_code}")
        print(f"Response Body: {response.text}")
        assert response.status_code == 200, f"Expected 200 OK, got {response.status_code}"
        json_resp = response.json()
        assert isinstance(json_resp, dict) and len(json_resp) > 0, "Response JSON is empty or invalid"
    except requests.RequestException as e:
        assert False, f"RequestException during valid login test: {e}"

    # Successful login with remember_me True
    payload_remember_me = {
        "username_email": AUTH_USERNAME,
        "password": AUTH_PASSWORD,
        "remember_me": True
    }
    try:
        response = requests.post(login_url, json=payload_remember_me, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 OK with remember_me, got {response.status_code}"
        json_resp = response.json()
        assert isinstance(json_resp, dict) and len(json_resp) > 0, "Response JSON is empty or invalid with remember_me"
    except requests.RequestException as e:
        assert False, f"RequestException during login with remember_me test: {e}"

    # Error case: missing username_email
    payload_missing_username = {
        "password": AUTH_PASSWORD
    }
    try:
        response = requests.post(login_url, json=payload_missing_username, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 Bad Request for missing username_email, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"RequestException during missing username_email test: {e}"

    # Error case: missing password
    payload_missing_password = {
        "username_email": AUTH_USERNAME
    }
    try:
        response = requests.post(login_url, json=payload_missing_password, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 Bad Request for missing password, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"RequestException during missing password test: {e}"

    # Error case: invalid username_email and password
    payload_invalid_credentials = {
        "username_email": "invalid_user@example.com",
        "password": "wrongpassword"
    }
    try:
        response = requests.post(login_url, json=payload_invalid_credentials, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 401, f"Expected 401 Unauthorized for invalid login, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"RequestException during invalid login test: {e}"

test_authenticate_using_username_or_email_plus_password()