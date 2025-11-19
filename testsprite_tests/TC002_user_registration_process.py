import requests
import uuid

BASE_URL = "http://localhost:80"
REGISTER_ENDPOINT = f"{BASE_URL}/api/register"
HEADERS = {
    "Content-Type": "application/json",
    "Authorization": "Basic dW5pcXVlYmlzaHdvQGdtYWlsLmNvbTplOVBVN1hBc0FBRFlrX0E="
}  # base64 for 'uniquebishwo@gmail.com:c9PU7XAsAADYk_A'

def test_user_registration_process():
    timeout = 30

    # Generate unique email to avoid conflict
    unique_email = f"testuser_{uuid.uuid4().hex[:8]}@example.com"
    valid_payload = {
        "username": "testuser",
        "email": unique_email,
        "password": "ValidPass123!",
        "first_name": "Test",
        "last_name": "User"
    }

    # Test successful registration
    response = requests.post(REGISTER_ENDPOINT, json=valid_payload, headers=HEADERS, timeout=timeout)
    assert response.status_code == 201, f"Expected 201, got {response.status_code}"
    try:
        json_resp = response.json()
    except Exception:
        json_resp = None
    assert json_resp is not None, "Response is not valid JSON"

    # Test invalid inputs with expected 400 response
    invalid_payloads = [
        {},  # empty
        {"username": "", "email": "", "password": "", "first_name": "", "last_name": ""},  # all empty strings
        {"username": "a"*256, "email": "invalidemail", "password": "short", "first_name": "T"*300, "last_name": "U"*300},  # overly long and malformed email and password
        {"username": "user", "email": "missingat.com", "password": "ValidPass123!", "first_name": "Test", "last_name": "User"},  # invalid email format
        {"username": "user", "email": unique_email, "password": "", "first_name": "Test", "last_name": "User"},  # missing password
    ]

    for payload in invalid_payloads:
        resp = requests.post(REGISTER_ENDPOINT, json=payload, headers=HEADERS, timeout=timeout)
        assert resp.status_code == 400, f"Expected 400 for payload {payload}, got {resp.status_code}"

test_user_registration_process()