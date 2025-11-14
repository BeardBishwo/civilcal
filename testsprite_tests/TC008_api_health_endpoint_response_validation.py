import requests
from requests.auth import HTTPBasicAuth

def test_api_health_endpoint_response_validation():
    base_url = "http://localhost:80/bishwo_calculator"
    endpoint = "/api/v1/health"
    url = base_url + endpoint
    auth = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
    headers = {
        "Accept": "application/json"
    }
    try:
        response = requests.get(url, headers=headers, auth=auth, timeout=30)
    except requests.RequestException as e:
        assert False, f"HTTP request failed: {e}"

    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"

    try:
        json_data = response.json()
    except ValueError:
        assert False, "Response is not a valid JSON"

    # Validate typical health response keys likely indicating system availability
    # Since schema detail not provided, verify keys like 'status' or 'healthy'
    valid_keys = {'status', 'healthy', 'message', 'uptime'}
    assert any(key in json_data for key in valid_keys), \
        f"Response JSON does not contain expected health keys. Keys found: {list(json_data.keys())}"

test_api_health_endpoint_response_validation()