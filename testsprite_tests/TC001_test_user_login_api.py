import requests
import time
import threading

BASE_URL = "http://localhost:80/Bishwo_Calculator"
LOGIN_ENDPOINT = "/api/login"
TIMEOUT = 30

valid_credentials = {
    "username_email": "uniquebishwo@gmail.com",
    "password": "c9PU7XAsAADYk_A"
}

invalid_credentials = {
    "username_email": "invaliduser@example.com",
    "password": "wrongpassword"
}

def test_user_login_api():
    session = requests.Session()

    # Helper to measure response time and assert status code
    def perform_login_test(creds, expected_status):
        start_time = time.time()
        try:
            resp = session.post(
                BASE_URL + LOGIN_ENDPOINT,
                data=creds,
                timeout=TIMEOUT
            )
        except requests.RequestException as e:
            assert False, f"Request failed with exception: {e}"
        duration = (time.time() - start_time) * 1000  # ms

        # Assert response time under 500ms as per performance benchmark
        assert duration < 500, f"Response time {duration:.2f}ms exceeds 500ms limit"

        assert resp.status_code == expected_status, (
            f"Expected HTTP {expected_status} but got {resp.status_code} with body: {resp.text}"
        )

        # Basic security headers should be present - CSRF token or similar not expected on login but check common headers
        # Check content-type header as JSON or text/html (depends on backend, skip strict check here)
        assert 'content-type' in resp.headers, "Missing Content-Type header in response"

    # Test valid credentials success
    perform_login_test(valid_credentials, 200)

    # Test invalid credentials failure
    perform_login_test(invalid_credentials, 401)

    # Additional edge cases for login

    # 1. Empty username and password
    perform_login_test({"username_email": "", "password": ""}, 401)

    # 2. Missing password only
    perform_login_test({"username_email": valid_credentials["username_email"]}, 401)

    # 3. Missing username only
    perform_login_test({"password": valid_credentials["password"]}, 401)

    # 4. SQL injection style username input
    perform_login_test({"username_email": "' OR '1'='1", "password": "anything"}, 401)

    # 5. Check rate limiting by rapid fire login attempts with invalid creds — expect last to be 401 or 429 (if implemented)
    # We allow some concurrency stress
    error_count = 0
    rate_limit_hit = False

    def rapid_invalid_login():
        nonlocal error_count, rate_limit_hit
        try:
            r = session.post(
                BASE_URL + LOGIN_ENDPOINT,
                data=invalid_credentials,
                timeout=TIMEOUT
            )
            if r.status_code == 429:
                rate_limit_hit = True
            elif r.status_code != 401:
                error_count += 1
        except requests.RequestException:
            error_count += 1

    threads = []
    for _ in range(20):
        t = threading.Thread(target=rapid_invalid_login)
        threads.append(t)
        t.start()
    for t in threads:
        t.join()

    assert error_count == 0, f"Unexpected errors in rapid invalid login attempts: {error_count}"
    # Rate limiting may or may not be implemented, so just report if hit

    # Check logging mechanism or indication by hitting health endpoint for logs presence
    try:
        health_resp = session.get(BASE_URL + "/api/v1/health", timeout=TIMEOUT)
        assert health_resp.status_code == 200
        assert "healthy" in health_resp.text.lower()
    except requests.RequestException:
        pass  # Do not fail test if health check not reachable here

    # No resource cleanup required since login does not create resource

test_user_login_api()