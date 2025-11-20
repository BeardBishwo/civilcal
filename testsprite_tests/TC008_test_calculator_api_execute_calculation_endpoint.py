import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30
auth = HTTPBasicAuth(USERNAME, PASSWORD)

def test_calculator_api_execute_calculation_endpoint():
    # Use an example known module and function with valid input_values
    module = "electrical"
    function = "ohms_law"  # changed from 'resistance' to a more likely existing function
    url = f"{BASE_URL}/calculator/{module}/{function}"
    
    # Prepare a valid payload
    valid_payload = {
        "input_values": {
            "voltage": 100,
            "current": 5
        }
    }
    
    headers = {
        "Content-Type": "application/json"
    }

    # 1) Test successful execution (200)
    try:
        response = requests.post(url, json=valid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 OK but got {response.status_code}"
        result_json = response.json()
        # The response must be a JSON object and non-empty
        assert isinstance(result_json, dict), "Response is not JSON object"
        assert len(result_json) > 0, "Result payload is empty"
    except Exception as e:
        raise AssertionError(f"Failed on valid input test: {str(e)}")
    
    # 2) Test validation error (400) with invalid payload (non-number input)
    invalid_payload = {"input_values": {"voltage": "not_a_number", "current": -5}}
    try:
        response = requests.post(url, json=invalid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 Bad Request but got {response.status_code}"
    except Exception as e:
        raise AssertionError(f"Failed on validation error test: {str(e)}")

    # 3) Test unknown calculator (404)
    unknown_module = "unknown_module_xyz"
    unknown_function = "unknown_function_abc"
    url_404 = f"{BASE_URL}/calculator/{unknown_module}/{unknown_function}"
    try:
        response = requests.post(url_404, json=valid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 404, f"Expected 404 Not Found but got {response.status_code}"
    except Exception as e:
        raise AssertionError(f"Failed on unknown calculator test: {str(e)}")

test_calculator_api_execute_calculation_endpoint()
