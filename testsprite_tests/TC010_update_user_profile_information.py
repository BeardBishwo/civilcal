import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {"Content-Type": "application/json"}
TIMEOUT = 30


def test_update_user_profile_information():
    url = f"{BASE_URL}/profile"

    # Valid payload to update profile
    valid_payload = {
        "first_name": "TestFirstName",
        "last_name": "TestLastName",
        "company": "TestCompany",
        "bio": "This is a test bio."
    }

    # Invalid payloads (examples: wrong field types, missing required fields not specified in schema but test robustness)
    invalid_payloads = [
        {"first_name": 123, "last_name": "Last", "company": "Comp", "bio": "Bio"},   # first_name as number
        {"first_name": "First", "last_name": None, "company": "Comp", "bio": "Bio"}, # last_name is None
        {"first_name": "First", "last_name": "Last", "company": 10, "bio": "Bio"},   # company as number
        {"first_name": "First", "last_name": "Last"}                                # missing company and bio optional but test
    ]

    # Test valid update
    try:
        response = requests.put(url, headers=HEADERS, auth=AUTH, json=valid_payload, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 OK for valid update, got {response.status_code}"
        json_resp = response.json()
        # Optionally verify updated fields are in the response if returned
        for k, v in valid_payload.items():
            assert k in json_resp, f"Response missing updated field '{k}'"
    except requests.RequestException as e:
        assert False, f"Request failed during valid profile update test: {str(e)}"

    # Test invalid updates
    for invalid_payload in invalid_payloads:
        try:
            response = requests.put(url, headers=HEADERS, auth=AUTH, json=invalid_payload, timeout=TIMEOUT)
            assert response.status_code == 400, f"Expected 400 Bad Request for invalid payload {invalid_payload}, got {response.status_code}"
        except requests.RequestException as e:
            assert False, f"Request failed during invalid profile update test: {str(e)}"


test_update_user_profile_information()