import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80"
USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_execute_calculator_function():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    headers = {"Content-Type": "application/json"}

    # Define a valid calculator module, function, and valid input_values
    valid_module = "electrical"
    valid_function = "voltage-drop"
    valid_payload = {
        "input_values": {
            "current": 10,
            "length": 50,
            "resistance": 0.02
        }
    }

    # Test valid calculation execution
    url_valid = f"{BASE_URL}/calculator/{valid_module}/{valid_function}"
    try:
        response_valid = requests.post(url_valid, json=valid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to valid calculator function failed: {e}"

    assert response_valid.status_code == 200, f"Expected status 200 but got {response_valid.status_code}"
    json_valid = response_valid.json()
    assert "result" in json_valid or "output" in json_valid, "Valid calculation response should contain 'result' or 'output' key"

    # Test calculator module not found (use an invalid calculator module)
    invalid_module = "invalid_module_xyz"
    url_invalid_module = f"{BASE_URL}/calculator/{invalid_module}/{valid_function}"
    try:
        response_invalid_module = requests.post(url_invalid_module, json=valid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to invalid calculator module failed: {e}"

    assert response_invalid_module.status_code == 404, f"Expected status 404 for invalid calculator module but got {response_invalid_module.status_code}"

    # Test calculator function not found (use an invalid function)
    invalid_function = "invalid_function_abc"
    url_invalid_function = f"{BASE_URL}/calculator/{valid_module}/{invalid_function}"
    try:
        response_invalid_function = requests.post(url_invalid_function, json=valid_payload, auth=auth, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to invalid calculator function failed: {e}"

    assert response_invalid_function.status_code == 404, f"Expected status 404 for invalid calculator function but got {response_invalid_function.status_code}"


test_execute_calculator_function()