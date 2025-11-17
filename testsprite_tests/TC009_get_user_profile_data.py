import requests

BASE_URL = "http://localhost/Bishwo_Calculator"
LOGIN_CREDENTIALS = {
    "username_email": "uniquebishwo@gmail.com",
    "password": "SecurePass123!"
}

def test_get_user_profile_data():
    profile_url = f"{BASE_URL}/api/profile.php"
    login_url = f"{BASE_URL}/api/login.php"
    timeout = 30

    # Create session for authenticated requests
    session = requests.Session()

    # Login first
    try:
        login_response = session.post(login_url, json=LOGIN_CREDENTIALS, timeout=timeout)
        assert login_response.status_code == 200, f"Login failed with status {login_response.status_code}"
    except requests.RequestException as e:
        assert False, f"Login request failed with exception: {e}"

    # Authenticated request
    try:
        auth_response = session.get(profile_url, timeout=timeout)
    except requests.RequestException as e:
        assert False, f"Authenticated request failed with exception: {e}"
    else:
        assert auth_response.status_code == 200, f"Expected 200 OK for authenticated request but got {auth_response.status_code}"
        try:
            profile_data = auth_response.json()
        except ValueError:
            assert False, "Response from authenticated request is not valid JSON"
        else:
            # Validate expected fields in profile data (at least it should be a dict)
            assert isinstance(profile_data, dict), "Profile data should be a JSON object"
            # Can add more validations if schema known, e.g. keys "first_name", "last_name", "email"
            assert 'email' in profile_data or 'username' in profile_data or len(profile_data) > 0, "Profile data seems empty or missing expected keys"

    # Unauthenticated request (new session without login)
    try:
        unauth_response = requests.get(profile_url, timeout=timeout)
    except requests.RequestException as e:
        assert False, f"Unauthenticated request failed with exception: {e}"
    else:
        assert unauth_response.status_code == 401, f"Expected 401 Unauthorized for unauthenticated request but got {unauth_response.status_code}"

test_get_user_profile_data()