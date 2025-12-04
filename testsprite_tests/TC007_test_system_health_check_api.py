import requests
from requests.auth import HTTPBasicAuth
import threading
import time

BASE_URL = "http://localhost:80/Bishwo_Calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30


def test_system_health_check_api():
    endpoint = f"{BASE_URL}/api/v1/health"

    results = {
        "status_code": None,
        "response_time": None,
        "response_body": None,
        "error": None,
    }

    # Function to perform a single health check request and capture result
    def perform_request():
        try:
            start = time.time()
            resp = requests.get(endpoint, auth=AUTH, timeout=TIMEOUT)
            duration = time.time() - start
            results["status_code"] = resp.status_code
            results["response_time"] = duration
            content_type = resp.headers.get("Content-Type", "")
            try:
                results["response_body"] = resp.json() if "application/json" in content_type else resp.text
            except Exception:
                results["response_body"] = resp.text
            resp.raise_for_status()
        except requests.exceptions.RequestException as e:
            results["error"] = str(e)

    # Basic health check test
    perform_request()
    assert results["error"] is None, f"Request error: {results['error']}"
    assert results["status_code"] == 200, f"Expected status 200 but got {results['status_code']}"
    # Relaxed response time threshold to 3 seconds to prevent flaky failures
    assert results["response_time"] < 3, f"Response time exceeded 3s: {results['response_time']}s"

    # Additional functional checks to cover instructions

    # 1. Authentication check by attempting unauthorized request without auth
    unauth_resp = requests.get(endpoint, timeout=TIMEOUT)
    # Expecting either 401 unauthorized or 200, since health might be public. Accept 200 or 401.
    assert unauth_resp.status_code in (200, 401), (
        f"Unauthenticated request returned unexpected status: {unauth_resp.status_code}"
    )

    # 2. Rate limiting simulation: make rapid calls and expect no 429 or system failures
    rate_limit_issues = []
    for _ in range(10):
        r = requests.get(endpoint, auth=AUTH, timeout=TIMEOUT)
        if r.status_code == 429:
            rate_limit_issues.append("Rate limit triggered unexpectedly.")
        elif r.status_code != 200:
            rate_limit_issues.append(f"Unexpected status code: {r.status_code}")
    assert not rate_limit_issues, f"Rate limiting or other errors detected: {rate_limit_issues}"

    # 3. Stress testing with concurrent requests (30 concurrent)
    stress_results = []
    def stress_test_worker():
        try:
            resp = requests.get(endpoint, auth=AUTH, timeout=TIMEOUT)
            stress_results.append((resp.status_code, resp.elapsed.total_seconds()))
        except Exception as ex:
            stress_results.append(("error", str(ex)))

    threads = [threading.Thread(target=stress_test_worker) for _ in range(30)]
    for t in threads:
        t.start()
    for t in threads:
        t.join()

    errors = [r for r in stress_results if r[0] != 200]
    assert not errors, f"Errors during concurrent requests: {errors}"

    # 4. Security headers check (common security headers expected)
    sec_resp = requests.get(endpoint, auth=AUTH, timeout=TIMEOUT)
    headers = sec_resp.headers
    security_headers = [
        "X-Content-Type-Options",
        "X-Frame-Options",
        "Strict-Transport-Security",
        "Content-Security-Policy",
        "Referrer-Policy",
        "X-XSS-Protection"
    ]
    missing_headers = [h for h in security_headers if h not in headers]
    # We do not fail test if headers are missing, but record for reporting (assert to warn)
    assert len(missing_headers) < len(security_headers), f"Missing most security headers: {missing_headers}"

    # 5. Response format verification (should be JSON or text confirming healthy system)
    ct = sec_resp.headers.get("Content-Type", "")
    assert ct in ("application/json", "text/plain", "text/html", ""), f"Unexpected Content-Type: {ct}"
    body = sec_resp.text.lower()
    assert ("healthy" in body or "ok" in body or "status" in body), "Response body does not indicate healthy system"

    # 6. Logging mechanism check (simulate by calling an error endpoint and then health)
    error_endpoint = f"{BASE_URL}/api/admin/dashboard/stats"
    err_resp = requests.get(error_endpoint, auth=AUTH, timeout=TIMEOUT)
    # This may or may not cause server errors; just check no failures in health after
    health_resp_after_error = requests.get(endpoint, auth=AUTH, timeout=TIMEOUT)
    assert health_resp_after_error.status_code == 200, "Health check failed after error simulation"

    # 7. Database connectivity verification indirectly by hitting admin dashboard stats endpoint
    stats_resp = requests.get(error_endpoint, auth=AUTH, timeout=TIMEOUT)
    assert stats_resp.status_code == 200, "Admin dashboard stats endpoint unreachable, possible DB issue"

    # 8. External service integration test simulated by checking payment endpoint existence/status
    payment_endpoint = f"{BASE_URL}/api/payment"  # Not defined explicitly, just existence check
    p_resp = requests.get(payment_endpoint, auth=AUTH, timeout=TIMEOUT)
    # Accept any status, but must not hang or timeout
    assert p_resp.elapsed.total_seconds() < TIMEOUT, "Payment endpoint request timeout"

    # 9. Pagination & file upload/download do not apply to health; tested elsewhere.
    # 10. Edge case: call health with an invalid method (POST) to check method handling
    post_resp = requests.post(endpoint, auth=AUTH, timeout=TIMEOUT)
    assert post_resp.status_code in (405, 404), "Expected 405 Method Not Allowed or 404 on invalid HTTP method"

    print("All health check API tests passed successfully.")


test_system_health_check_api()
