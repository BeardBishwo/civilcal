import requests
from requests.auth import HTTPBasicAuth
import threading

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_admin_dashboard_stats_api():
    session = requests.Session()
    session.auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)
    headers = {
        "Accept": "application/json"
    }

    # Helper function to perform GET request and validate response
    def get_dashboard_stats():
        try:
            resp = session.get(f"{BASE_URL}/api/admin/dashboard/stats", headers=headers, timeout=TIMEOUT)
        except requests.exceptions.RequestException as e:
            assert False, f"Request failed: {e}"
        else:
            # Check status code
            assert resp.status_code == 200, f"Expected status 200, got {resp.status_code}"
            # Validate response is JSON and is a dictionary/object
            try:
                data = resp.json()
            except ValueError:
                assert False, "Response is not valid JSON"

            assert isinstance(data, dict), "Response JSON is not an object"

            return resp.elapsed.total_seconds()

    # Test normal single request and measure response time
    response_time = get_dashboard_stats()
    assert response_time <= 0.5, f"Response time exceeded 500ms: {response_time}s"

    # Test with multiple concurrent requests to check for race conditions / concurrency handling
    thread_count = 10
    times = []
    exceptions = []

    def concurrent_request():
        try:
            elapsed = get_dashboard_stats()
            times.append(elapsed)
        except AssertionError as e:
            exceptions.append(str(e))

    threads = []
    for _ in range(thread_count):
        t = threading.Thread(target=concurrent_request)
        threads.append(t)
        t.start()
    for t in threads:
        t.join()

    assert not exceptions, f"Errors during concurrent requests: {exceptions}"
    assert all(t <= 0.5 for t in times), f"One or more concurrent requests exceeded 500ms: {times}"

    # Test rate limiting by firing rapid consecutive requests beyond typical thresholds
    rate_limit_violations = 0
    for i in range(50):
        try:
            resp = session.get(f"{BASE_URL}/api/admin/dashboard/stats", headers=headers, timeout=TIMEOUT)
        except requests.exceptions.RequestException as e:
            assert False, f"Request failed during rate limiting test: {e}"
        else:
            if resp.status_code == 429:
                rate_limit_violations += 1
            elif resp.status_code != 200:
                assert False, f"Unexpected status code {resp.status_code} during rate limiting test"

    if rate_limit_violations > 0:
        print(f"Rate limiting detected: {rate_limit_violations} requests returned 429")

    # Security testing: ensure unauthorized access is forbidden
    try:
        resp_unauth = requests.get(f"{BASE_URL}/api/admin/dashboard/stats", headers=headers, timeout=TIMEOUT)
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed during unauthorized access test: {e}"
    else:
        # Using no auth to test unauthorized access (simulate by new session without auth)
        session_no_auth = requests.Session()
        try:
            resp_no_auth = session_no_auth.get(f"{BASE_URL}/api/admin/dashboard/stats", timeout=TIMEOUT)
        except requests.exceptions.RequestException as e:
            assert False, f"Request failed for no-auth test: {e}"
        else:
            # Expected 401 or 403 for unauthorized access
            assert resp_no_auth.status_code in [401, 403], f"Unauthorized access not prevented, status code: {resp_no_auth.status_code}"

test_admin_dashboard_stats_api()
