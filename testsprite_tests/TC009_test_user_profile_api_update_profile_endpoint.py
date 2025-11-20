import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_user_profile_api_update_profile_endpoint():
    url = f"{BASE_URL}/profile"
    headers = {"Content-Type": "application/json"}

    # Valid update payload
    valid_payload = {
        "first_name": "TestFirst",
        "last_name": "TestLast",
        "company": "TestCompany",
        "bio": "This is a test bio."
    }

    # Invalid update payload: e.g. first_name as integer instead of string (validation error)
    invalid_payload = {
        "first_name": 123,
        "last_name": "Last",
        "company": "Company",
        "bio": "Bio"
    }

    # 1. Test successful update returns 200
    try:
        resp = requests.put(url, json=valid_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Expected 200 OK for valid update but got {resp.status_code}"
        # Optionally assert response content if detail is provided
        json_resp = resp.json()
        # Check response contains updated fields or confirmation
        for k, v in valid_payload.items():
            assert k in json_resp, f"Response JSON missing expected key '{k}'"
            assert json_resp[k] == v, f"Response JSON field '{k}' expected '{v}' but got '{json_resp[k]}'"
    except requests.RequestException as e:
        assert False, f"Request exception during valid update test: {e}"

    # 2. Test validation error returns 400
    try:
        resp = requests.put(url, json=invalid_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert resp.status_code == 400, f"Expected 400 Bad Request for invalid update but got {resp.status_code}"
    except requests.RequestException as e:
        assert False, f"Request exception during invalid update test: {e}"

    # 3. Test unauthorized access returns 401
    try:
        resp = requests.put(url, json=valid_payload, headers=headers, timeout=TIMEOUT)  # no auth
        assert resp.status_code == 401, f"Expected 401 Unauthorized for no auth but got {resp.status_code}"
    except requests.RequestException as e:
        assert False, f"Request exception during unauthorized update test: {e}"

test_user_profile_api_update_profile_endpoint()