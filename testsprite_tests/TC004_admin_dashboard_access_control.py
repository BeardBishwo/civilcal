import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator/"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_admin_dashboard_access_control():
    headers = {
        "Accept": "application/json"
    }

    # Authorized admin user access test
    try:
        response_auth = requests.get(
            BASE_URL + "admin",
            auth=HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD),
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request to admin endpoint with authorized user failed: {e}"

    assert response_auth.status_code == 200, (
        f"Authorized admin user should have access, got status code {response_auth.status_code}"
    )
    # Optionally verify response content type or keys if response JSON expected
    if response_auth.headers.get("Content-Type", "").startswith("application/json"):
        try:
            json_data = response_auth.json()
            assert isinstance(json_data, dict), "Expected JSON object in response"
        except ValueError:
            assert False, "Response is not valid JSON"

    # Unauthorized user access test (no auth)
    try:
        response_unauth = requests.get(
            BASE_URL + "admin",
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request to admin endpoint without auth failed: {e}"

    assert response_unauth.status_code == 403, (
        f"Unauthorized user should receive 403 Forbidden, got status code {response_unauth.status_code}"
    )

    # Unauthorized user access test (wrong auth)
    try:
        response_wrong_auth = requests.get(
            BASE_URL + "admin",
            auth=HTTPBasicAuth("wronguser@example.com", "wrongpassword"),
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Request to admin endpoint with wrong auth failed: {e}"

    assert response_wrong_auth.status_code == 403, (
        f"User with wrong credentials should receive 403 Forbidden, got status code {response_wrong_auth.status_code}"
    )

test_admin_dashboard_access_control()