import requests

BASE_URL = "http://localhost:80/bishwo_calculator"
EMAIL = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30


def test_user_login_functionality():
    login_url = f"{BASE_URL}/api/login"
    headers = {"Content-Type": "application/json"}

    # Test successful login with valid credentials
    valid_payload = {
        "email": EMAIL,
        "password": PASSWORD
    }
    try:
        response = requests.post(login_url, json=valid_payload, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request failed during valid login test: {e}"

    assert response.status_code == 200, f"Expected 200 for valid login but got {response.status_code}"

    # Test login failure with invalid credentials
    invalid_payload = {
        "email": EMAIL,
        "password": "invalidpassword123"
    }
    try:
        response_invalid = requests.post(login_url, json=invalid_payload, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request failed during invalid login test: {e}"

    assert response_invalid.status_code == 401, f"Expected 401 for invalid login but got {response_invalid.status_code}"


test_user_login_functionality()
