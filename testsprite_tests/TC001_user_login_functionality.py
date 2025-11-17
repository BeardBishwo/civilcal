import requests
from requests.auth import HTTPBasicAuth

def test_user_login_functionality():
    base_url = "http://localhost/Bishwo_Calculator"
    login_url = f"{base_url}/api/login.php"
    headers = {"Content-Type": "application/json"}

    # Valid credentials from our seeded user
    valid_email = "uniquebishwo@gmail.com"
    valid_password = "SecurePass123!"

    # 1. Test successful login with valid credentials
    valid_payload = {
        "username_email": valid_email,
        "password": valid_password
    }

    try:
        valid_response = requests.post(
            login_url,
            json=valid_payload,
            headers=headers,
            timeout=30
        )
        assert valid_response.status_code == 200, f"Expected 200, got {valid_response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for valid login: {e}"

    # 2. Test login with invalid credentials (wrong password)
    invalid_payload = {
        "username_email": valid_email,
        "password": "invalid_password_123"
    }

    try:
        invalid_response = requests.post(
            login_url,
            json=invalid_payload,
            headers=headers,
            timeout=30
        )
        # Accept either 400 or 401 for invalid credentials
        assert invalid_response.status_code in [400, 401], f"Expected 400 or 401, got {invalid_response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for invalid login: {e}"


test_user_login_functionality()
