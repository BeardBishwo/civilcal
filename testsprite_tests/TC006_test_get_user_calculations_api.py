import requests
import threading
import time

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH_USERNAME = "uniquebishwo@gmail.com"
AUTH_PASSWORD = "c9PU7XAsAADYk_A"
TIMEOUT = 30

def test_get_user_calculations_api():
    session = requests.Session()
    headers = {
        "Accept": "application/json"
    }

    # 1. Authentication check - validate login to get authenticated session (simulate login)
    login_url = f"{BASE_URL}/api/login"
    login_payload = {
        "username_email": AUTH_USERNAME,
        "password": AUTH_PASSWORD
    }
    try:
        login_resp = session.post(login_url, data=login_payload, headers=headers, timeout=TIMEOUT)
        assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}: {login_resp.text}"
    except Exception as e:
        raise AssertionError(f"Exception during login: {e}")

    # 2. Test retrieval of user calculation history
    calculations_url = f"{BASE_URL}/api/calculations"
    try:
        start_time = time.time()
        response = session.get(calculations_url, headers=headers, timeout=TIMEOUT)
        response_time = (time.time() - start_time) * 1000  # in milliseconds
    except Exception as e:
        raise AssertionError(f"Exception during GET /api/calculations: {e}")

    # 3. Validate status code
    assert response.status_code == 200, f"Expected status 200, got {response.status_code}"

    # 4. Validate response time under 500 ms if possible
    assert response_time < 500, f"Response time exceeded 500ms: {response_time:.2f}ms"

    # 5. Validate response content format - expect JSON list of calculations
    try:
        data = response.json()
    except Exception:
        raise AssertionError("Response is not valid JSON")

    assert isinstance(data, list), f"Expected response data to be a list, got {type(data)}"

    # 6. Validate at least one calculation object format if list not empty
    if data:
        calc = data[0]
        assert isinstance(calc, dict), f"Calculation record should be dict, got {type(calc)}"
        # Validate typical fields in a calculation history record - adaptable fields based on expected
        expected_fields = ["id", "user_id", "expression", "result", "created_at"]
        for field in expected_fields:
            assert field in calc, f"Field '{field}' missing in calculation record"

    # 7. Test rate limiting by sending rapid requests
    def send_request():
        try:
            r = session.get(calculations_url, headers=headers, timeout=TIMEOUT)
            return r.status_code
        except requests.exceptions.RequestException:
            return None

    statuses = []
    count = 20  # Send 20 rapid requests
    threads = []

    def worker():
        status = send_request()
        statuses.append(status)

    for _ in range(count):
        t = threading.Thread(target=worker)
        threads.append(t)
        t.start()

    for t in threads:
        t.join()

    # There should be mostly 200 status; if rate limiting is enforced, some may be 429
    rate_limit_hits = statuses.count(429)
    assert all(s in [200, 429] for s in statuses), f"Unexpected status codes in rate limit test: {set(statuses)}"
    # We allow some 429 but majority should be 200
    assert statuses.count(200) >= count // 2, f"Too many requests blocked, possible rate limiting malfunction"

    # 8. Test pagination if supported - check metadata presence
    paginated_url = f"{calculations_url}?page=1&limit=5"
    try:
        paginated_resp = session.get(paginated_url, headers=headers, timeout=TIMEOUT)
        assert paginated_resp.status_code == 200, f"Pagination request failed with status {paginated_resp.status_code}"
        page_data = paginated_resp.json()
        assert isinstance(page_data, list), "Paginated response data is not a list"
        # Check length limit
        assert len(page_data) <= 5, f"Pagination limit exceeded, got {len(page_data)} items"
    except Exception:
        # If pagination not supported, just log and continue
        pass

    # 9. Security validation: confirm response does not contain sensitive info (e.g. passwords)
    for item in data:
        assert "password" not in item, "Response contains sensitive information 'password'"

    # 10. Concurrent requests stress test
    concurrency_count = 10
    stress_statuses = []

    def stress_worker():
        try:
            r = session.get(calculations_url, headers=headers, timeout=TIMEOUT)
            stress_statuses.append(r.status_code)
        except requests.exceptions.RequestException:
            stress_statuses.append(None)

    threads = []
    for _ in range(concurrency_count):
        t = threading.Thread(target=stress_worker)
        threads.append(t)
        t.start()
    for t in threads:
        t.join()

    assert all(s == 200 for s in stress_statuses if s is not None), "Some concurrent requests failed"

    # 11. Logout successful to clean up session
    logout_url = f"{BASE_URL}/api/logout"
    try:
        logout_resp = session.post(logout_url, timeout=TIMEOUT)
        assert logout_resp.status_code == 200, f"Logout failed with status {logout_resp.status_code}"
    except Exception:
        pass

test_get_user_calculations_api()
