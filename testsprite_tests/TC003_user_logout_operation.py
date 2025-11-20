import requests

base_url = "http://localhost:80"
username = "uniquebishwo@gmail.com"
password = "c9PU7XAsAADYk_A"

def test_user_logout_operation():
    session = requests.Session()
    try:
        # Login to get authenticated session or token if needed
        login_url = f"{base_url}/api/login"
        login_payload = {
            "email": username,
            "password": password
        }
        login_resp = session.post(login_url, json=login_payload, timeout=30)
        assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"

        # Perform logout operation
        logout_url = f"{base_url}/api/logout"
        logout_resp = session.get(logout_url, timeout=30)
        assert logout_resp.status_code == 200, f"Logout failed with status {logout_resp.status_code}"

        try:
            data = logout_resp.json()
            assert "success" in data or "message" in data or "logout" in str(data).lower()
        except Exception:
            assert logout_resp.text.strip() != "", "Logout response empty"
    finally:
        session.close()

test_user_logout_operation()