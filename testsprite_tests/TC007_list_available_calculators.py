import requests
from requests.auth import HTTPBasicAuth

def test_list_available_calculators():
    base_url = "http://localhost:80/bishwo_calculator"
    endpoint = "/calculators"
    url = base_url + endpoint
    auth = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
    headers = {
        "Accept": "application/json"
    }
    timeout = 30

    try:
        response = requests.get(url, auth=auth, headers=headers, timeout=timeout)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request to list calculators failed: {e}"

    # Confirm response status code is 200 OK
    assert response.status_code == 200, f"Expected status code 200, got {response.status_code}"
    
    # Confirm response is JSON and parse it
    try:
        calculators_list = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    # The response should be a list containing calculator entries; check type
    assert isinstance(calculators_list, list), f"Expected response to be a list, got {type(calculators_list)}"

    # Check the completeness and accuracy - at least 250 calculators expected as per product overview
    assert len(calculators_list) >= 250, f"Expected at least 250 calculators, got {len(calculators_list)}"

    # Optional: Check if entries look valid (each entry is a dict with expected keys)
    for calculator in calculators_list:
        assert isinstance(calculator, dict), f"Calculator entry is not a dict: {calculator}"
        assert "name" in calculator or "id" in calculator or "module" in calculator, "Calculator entry missing expected identifiers"


test_list_available_calculators()
