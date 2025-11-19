import requests
from requests.auth import HTTPBasicAuth

def test_list_available_calculators():
    base_url = "http://localhost:80"
    endpoint = "/calculators"
    url = base_url + endpoint

    auth = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
    headers = {
        "Accept": "application/json"
    }

    try:
        response = requests.get(url, auth=auth, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"

    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"

    try:
        calculators = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    # Assert the response is a list or dict (depending on the API design)
    assert isinstance(calculators, (list, dict)), "Response JSON should be a list or dict"

    # If it's a list, check length is >= 250 (per PRD: 250+ calculators)
    if isinstance(calculators, list):
        assert len(calculators) >= 250, f"Expected at least 250 calculators, got {len(calculators)}"

    # If it's a dict, try finding a key that holds the calculators list
    if isinstance(calculators, dict):
        # Attempt common keys like 'calculators', 'data', or similar
        candidates = ['calculators', 'data', 'items', 'results']
        found_list = False
        for key in candidates:
            if key in calculators and isinstance(calculators[key], list):
                assert len(calculators[key]) >= 250, f"Expected at least 250 calculators in '{key}', got {len(calculators[key])}"
                found_list = True
                break
        assert found_list, f"No list of calculators found in response keys {list(calculators.keys())}"

test_list_available_calculators()