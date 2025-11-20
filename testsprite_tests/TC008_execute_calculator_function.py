import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_execute_calculator_function():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    headers = {"Content-Type": "application/json"}

    # Using a realistic function name based on typical electrical calculators
    valid_module = "electrical"
    valid_function = "ohms_law"
    valid_payload = {
        "input_values": {
            "voltage": 10,
            "resistance": 5
        }
    }

    # POST request with valid inputs - Expect 200 response with calculation result
    valid_url = f"{BASE_URL}/calculator/{valid_module}/{valid_function}"
    valid_response = requests.post(valid_url, auth=auth, json=valid_payload, headers=headers, timeout=TIMEOUT)

    assert valid_response.status_code == 200, f"Expected 200 OK for valid calculation, got {valid_response.status_code}"
    try:
        json_data = valid_response.json()
        assert "output" in json_data, "Response JSON missing expected output field"
    except Exception:
        assert False, "Response not valid JSON"

    # Test with invalid calculator module - Expect 404 response
    invalid_module = "nonexistent_module"
    invalid_function = valid_function
    invalid_url_module = f"{BASE_URL}/calculator/{invalid_module}/{invalid_function}"
    invalid_module_response = requests.post(invalid_url_module, auth=auth, json=valid_payload, headers=headers, timeout=TIMEOUT)

    assert invalid_module_response.status_code == 404, f"Expected 404 Not Found for invalid module, got {invalid_module_response.status_code}"

    # Test with invalid function under valid module - Expect 404 response
    invalid_function_name = "nonexistent_function"
    invalid_url_function = f"{BASE_URL}/calculator/{valid_module}/{invalid_function_name}"
    invalid_function_response = requests.post(invalid_url_function, auth=auth, json=valid_payload, headers=headers, timeout=TIMEOUT)

    assert invalid_function_response.status_code == 404, f"Expected 404 Not Found for invalid function, got {invalid_function_response.status_code}"

test_execute_calculator_function()
