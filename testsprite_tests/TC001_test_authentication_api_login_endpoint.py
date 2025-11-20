import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
LOGIN_ENDPOINT = f"{BASE_URL}/api/login"
TIMEOUT = 30

AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"

def test_authentication_api_login_endpoint():
    headers = {
        "Content-Type": "application/json"
    }

    # 1. Test successful login with valid username and password
    payload_valid_username = {
        "username": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }
    try:
        response = requests.post(LOGIN_ENDPOINT, json=payload_valid_username, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 for valid username login, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during valid username login: {e}"

    # 2. Test successful login with valid email and password
    payload_valid_email = {
        "email": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }
    try:
        response = requests.post(LOGIN_ENDPOINT, json=payload_valid_email, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 for valid email login, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during valid email login: {e}"

    # 3. Test missing credentials (empty payload)
    payload_missing = {}
    try:
        response = requests.post(LOGIN_ENDPOINT, json=payload_missing, headers=headers, timeout=TIMEOUT)
        # Expecting 400 Bad Request for missing credentials
        assert response.status_code == 400, f"Expected 400 for missing credentials, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during missing credentials test: {e}"

    # 4. Test invalid credentials (wrong password)
    payload_invalid_password = {
        "username": AUTH_USERNAME,
        "password": "invalidpassword123"
    }
    try:
        response = requests.post(LOGIN_ENDPOINT, json=payload_invalid_password, headers=headers, timeout=TIMEOUT)
        # Expecting 401 Unauthorized for invalid credentials
        assert response.status_code == 401, f"Expected 401 for invalid password, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during invalid password test: {e}"

    # 5. Test invalid credentials (wrong username)
    payload_invalid_username = {
        "username": "nonexistentuser@example.com",
        "password": AUTH_PASSWORD
    }
    try:
        response = requests.post(LOGIN_ENDPOINT, json=payload_invalid_username, headers=headers, timeout=TIMEOUT)
        # Expecting 401 Unauthorized for invalid username
        assert response.status_code == 401, f"Expected 401 for invalid username, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during invalid username test: {e}"

test_authentication_api_login_endpoint()