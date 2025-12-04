import requests
from requests.auth import HTTPBasicAuth
import time

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {"Accept": "application/json"}
TIMEOUT = 30


def test_calculate_api():
    session = requests.Session()
    session.auth = AUTH
    session.headers.update(HEADERS)

    calculators_with_params = {
        # Example calculator types with example valid params
        "civil": {"length": 10, "width": 5},
        "electrical": {"voltage": 220, "current": 5},
        "hvac": {"temperature": 25, "humidity": 50},
        "plumbing": {"pipeDiameter": 10, "pipeLength": 15},
        "traditional": {"unit1": "inch", "unit2": "cm", "value": 12},
    }

    # Test valid calculations and validate results
    for calc_type, params in calculators_with_params.items():
        try:
            start_time = time.time()
            response = session.post(
                f"{BASE_URL}/api/calculate",
                params={"calculator": calc_type},
                json=params,
                timeout=TIMEOUT,
            )
            elapsed = time.time() - start_time

            assert response.status_code == 200, f"Expected 200 but got {response.status_code} for calculator {calc_type}"
            response_json = response.json()
            assert isinstance(response_json, dict), f"Response not a JSON object for calculator {calc_type}"

            # Basic validation: check if result key exists and is a number type
            assert "result" in response_json, f"'result' key missing in response for calculator {calc_type}"
            result = response_json["result"]
            assert isinstance(result, (int, float)), f"Result not numeric for calculator {calc_type}"

            # Validate response time less than 0.5 seconds
            assert elapsed < 0.5, f"Response time {elapsed}s exceeds 0.5s for calculator {calc_type}"

        except (requests.RequestException, AssertionError) as e:
            raise AssertionError(f"Failed valid calculation test for '{calc_type}': {e}")

    # Test invalid calculator parameter (missing required param or invalid type)
    invalid_test_cases = [
        ({"calculator": "civil"}, {}),  # empty params
        ({"calculator": "electrical"}, {"voltage": "invalid", "current": 5}),  # wrong type voltage
        ({"calculator": "hvac"}, {"temperature": None}),  # missing humidity
        ({"calculator": "plumbing"}, {"pipeDiameter": -5, "pipeLength": 10}),  # negative dimension
        ({"calculator": "traditional"}, {"unit1": "", "unit2": "cm", "value": "NaN"}),  # invalid value
        ({"calculator": ""}, {"value": 10}),  # empty calculator type
        ({}, {"value": 10}),  # missing calculator param
    ]

    for query_params, body in invalid_test_cases:
        try:
            start_time = time.time()
            response = session.post(
                f"{BASE_URL}/api/calculate",
                params=query_params,
                json=body,
                timeout=TIMEOUT,
            )
            elapsed = time.time() - start_time

            # Expect 400 bad request for invalid parameters
            assert response.status_code == 400, f"Expected 400 but got {response.status_code} for invalid params {query_params} body {body}"
            # Validate response time less than 0.5 seconds
            assert elapsed < 0.5, f"Response time {elapsed}s exceeds 0.5s for invalid params {query_params}"

        except (requests.RequestException, AssertionError) as e:
            raise AssertionError(f"Failed invalid parameter test case {query_params} {body}: {e}")

    # Rate limiting test: send many requests rapidly and expect either valid responses or 429 Too Many Requests
    try:
        for i in range(20):
            response = session.post(
                f"{BASE_URL}/api/calculate",
                params={"calculator": "civil"},
                json={"length": 10 + i, "width": 5 + i},
                timeout=TIMEOUT,
            )
            # Allow either 200 or 429 status (rate limiting)
            assert response.status_code in (200, 429), f"Unexpected status {response.status_code} in rate limit test iteration {i}"
            if response.status_code == 429:
                break  # Stop if rate limited
    except requests.RequestException as e:
        raise AssertionError(f"Exception during rate limiting test: {e}")

    # Concurrent requests test: fire multiple requests and verify responses
    import concurrent.futures

    def make_request(i):
        try:
            resp = session.post(
                f"{BASE_URL}/api/calculate",
                params={"calculator": "electrical"},
                json={"voltage": 220 + i, "current": 5 + i},
                timeout=TIMEOUT,
            )
            return resp.status_code, resp.json() if resp.headers.get("Content-Type", "").startswith("application/json") else None
        except Exception as e:
            return e

    with concurrent.futures.ThreadPoolExecutor(max_workers=5) as executor:
        futures = [executor.submit(make_request, i) for i in range(10)]
        for future in concurrent.futures.as_completed(futures):
            result = future.result()
            if isinstance(result, Exception):
                raise AssertionError(f"Exception during concurrent request test: {result}")
            status, data = result
            assert status == 200, f"Concurrent request returned status {status}"
            assert data and "result" in data, "Missing 'result' in concurrent request response"

    # Security validations stub (basic token auth already tested by access)
    # Additional security tests (CSRF, XSS, SQL injection) would normally be here
    # But out of scope for this test, so just confirm auth presence:
    try:
        no_auth_response = requests.post(
            f"{BASE_URL}/api/calculate",
            params={"calculator": "civil"},
            json={"length": 10, "width": 5},
            timeout=TIMEOUT,
        )
        assert no_auth_response.status_code in (401, 403), "Unauthorized request should be rejected"
    except requests.RequestException as e:
        raise AssertionError(f"Exception during unauthenticated request test: {e}")


test_calculate_api()