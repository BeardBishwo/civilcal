import requests
import time
from concurrent.futures import ThreadPoolExecutor, as_completed

BASE_URL = "http://localhost:80/Bishwo_Calculator"
LOGIN_ENDPOINT = f"{BASE_URL}/api/login"
LOGOUT_ENDPOINT = f"{BASE_URL}/api/logout"
HEALTH_ENDPOINT = f"{BASE_URL}/api/v1/health"
TIMEOUT = 30

USERNAME = "uniquebishwo@gmail.com"
PASSWORD = "c9PU7XAsAADYk_A"

def test_user_logout_api():
    session = requests.Session()
    try:
        # Step 1: Health Check - Ensure API is healthy before test
        health_response = session.get(HEALTH_ENDPOINT, timeout=TIMEOUT)
        assert health_response.status_code == 200, f"Health check failed with status {health_response.status_code}"

        # Step 2: Login to get authenticated session/cookies or token if any
        login_payload = {
            "username_email": USERNAME,
            "password": PASSWORD
        }
        login_response = session.post(LOGIN_ENDPOINT, data=login_payload, timeout=TIMEOUT)
        assert login_response.status_code == 200, f"Login failed with status {login_response.status_code}"

        # Step 3: Stress Test Logout endpoint with concurrency
        def logout_call():
            try:
                r = session.post(LOGOUT_ENDPOINT, timeout=TIMEOUT)
                return r.status_code, r.elapsed.total_seconds()
            except requests.RequestException as e:
                return f"Exception: {e}", None

        concurrency_level = 10
        with ThreadPoolExecutor(max_workers=concurrency_level) as executor:
            futures = [executor.submit(logout_call) for _ in range(concurrency_level)]
            results = []
            for future in as_completed(futures):
                results.append(future.result())

        # Validate all responses from stress test
        for idx, (status, resp_time) in enumerate(results):
            assert status == 200, f"Logout request {idx+1} failed with status: {status}"
            assert resp_time is not None and resp_time <= 0.5, f"Logout request {idx+1} took too long: {resp_time}s"

        # Step 4: Error Handling - Attempt logout without authentication
        session_no_auth = requests.Session()
        try:
            resp = session_no_auth.post(LOGOUT_ENDPOINT, timeout=TIMEOUT)
            # Depending on implementation, logout might require no auth or might redirect or accept anonymous logout
            # We accept 200 or 401 (if system rejects unauthenticated logout)
            assert resp.status_code in (200, 401), f"Unauthenticated logout returned unexpected status {resp.status_code}"
        except requests.RequestException as e:
            assert False, f"Exception during unauthenticated logout: {e}"

        # Step 5: Rate Limiting Check - Rapidly hit logout endpoint beyond typical limit
        rapid_requests = 20
        rate_limit_results = []
        for _ in range(rapid_requests):
            try:
                r = session.post(LOGOUT_ENDPOINT, timeout=TIMEOUT)
                rate_limit_results.append(r.status_code)
            except requests.RequestException as e:
                rate_limit_results.append(f"Exception: {e}")

        # Count rate limiting responses if any (commonly 429)
        rate_limited = sum(1 for r in rate_limit_results if r == 429)
        # It's acceptable if some requests are rate limited, fail if all are errors/exceptions
        assert any(isinstance(r, int) and r == 200 for r in rate_limit_results), "No successful logout responses in rapid requests"
        # Proceed despite rate limits, just report if rate limiting detected
        if rate_limited:
            print(f"Notice: Detected {rate_limited} rate limited logout responses out of {rapid_requests} requests.")

    finally:
        session.close()

test_user_logout_api()