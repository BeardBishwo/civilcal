import requests
from requests.auth import HTTPBasicAuth

base_url = "http://localhost:80/bishwo_calculator"
auth = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
headers = {"Content-Type": "application/json"}
timeout = 30

def test_user_registration_process():
    register_url = f"{base_url}/api/register"
    
    # Valid user registration data
    valid_payload = {
        "username": "testuser123",
        "email": "testuser123@example.com",
        "password": "StrongPass!123",
        "first_name": "Test",
        "last_name": "User"
    }
    
    # Invalid user registration data (missing password)
    invalid_payload = {
        "username": "testuser456",
        "email": "testuser456@example.com",
        "first_name": "Test",
        "last_name": "User"
    }
    
    # --- Test registration success with valid data ---
    try:
        response = requests.post(register_url, auth=auth, headers=headers, json=valid_payload, timeout=timeout)
        assert response.status_code == 201, f"Expected 201 for valid registration, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for valid registration: {e}"
    
    # --- Test registration failure with invalid data ---
    try:
        response = requests.post(register_url, auth=auth, headers=headers, json=invalid_payload, timeout=timeout)
        assert response.status_code == 400, f"Expected 400 for invalid registration, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for invalid registration: {e}"


test_user_registration_process()