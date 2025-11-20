import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {"Content-Type": "application/json"}
TIMEOUT = 30

def test_update_user_profile_information():
    url = f"{BASE_URL}/profile"

    # Valid update payload
    valid_payload = {
        "first_name": "TestFirstName",
        "last_name": "TestLastName",
        "company": "TestCompany Inc.",
        "bio": "This is a test bio for profile update."
    }
    
    # Invalid update payload (e.g., numeric first_name which should be string)
    invalid_payload = {
        "first_name": 1234,
        "last_name": "ValidLastName",
        "company": "Company",
        "bio": "Bio text"
    }

    # Test valid profile update
    try:
        response_valid = requests.put(
            url,
            json=valid_payload,
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT
        )
        assert response_valid.status_code == 200, f"Expected 200 for valid update, got {response_valid.status_code}"
        json_resp = response_valid.json()
        # Check that response contains updated fields (optional, depending on API response)
        for key, value in valid_payload.items():
            assert key in json_resp, f"Response missing '{key}'"
            assert json_resp[key] == value, f"Expected {key}='{value}', got '{json_resp[key]}'"
    except requests.RequestException as e:
        assert False, f"Request failed during valid update: {str(e)}"
    
    # Test invalid profile update
    try:
        response_invalid = requests.put(
            url,
            json=invalid_payload,
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT
        )
        assert response_invalid.status_code == 400, f"Expected 400 for invalid update, got {response_invalid.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed during invalid update: {str(e)}"

test_update_user_profile_information()