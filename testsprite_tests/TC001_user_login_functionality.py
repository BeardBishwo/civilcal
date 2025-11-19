import requests

BASE_URL = "http://localhost:80"
LOGIN_ENDPOINT = "/api/login"

AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_user_login_functionality():
    url = BASE_URL + LOGIN_ENDPOINT
    headers = {"Content-Type": "application/json"}

    # Valid credentials payload
    valid_payload = {
        "email": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }

    # Invalid credentials payload
    invalid_payload = {
        "email": AUTH_USERNAME,
        "password": "wrong_password_123"
    }

    try:
        # Test successful login with valid credentials
        response = requests.post(url, json=valid_payload, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200, got {response.status_code}"

        # Test login failure with invalid credentials
        response_invalid = requests.post(url, json=invalid_payload, headers=headers, timeout=TIMEOUT)
        assert response_invalid.status_code == 401, f"Expected 401, got {response_invalid.status_code}"

    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"


test_user_login_functionality()
