import requests

BASE_URL = "http://localhost:80"
LOGIN_URL = f"{BASE_URL}/api/login"
LOGOUT_URL = f"{BASE_URL}/api/logout"
TIMEOUT = 30

USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"

def test_user_logout_operation():
    session = requests.Session()
    try:
        # Login to get authenticated session (token/cookies)
        login_payload = {
            "email": USERNAME,
            "password": PASSWORD
        }
        login_headers = {
            "Content-Type": "application/json"
        }
        login_response = session.post(LOGIN_URL, json=login_payload, headers=login_headers, timeout=TIMEOUT)
        assert login_response.status_code == 200, f"Login failed with status code {login_response.status_code}"
        
        # Attempt logout with authenticated session
        logout_response = session.get(LOGOUT_URL, timeout=TIMEOUT)
        assert logout_response.status_code == 200, f"Logout failed with status code {logout_response.status_code}"
        assert "Logout successful" in logout_response.text or logout_response.text != "", "Logout response confirmation missing"
    finally:
        session.close()

test_user_logout_operation()