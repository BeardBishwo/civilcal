import requests
import time

BASE_URL = "http://localhost:80/Bishwo_Calculator"
REGISTER_ENDPOINT = f"{BASE_URL}/api/register"
LOGIN_ENDPOINT = f"{BASE_URL}/api/login"

TIMEOUT = 30

def test_user_registration_api():
    headers = {
        "Accept": "application/json"
    }

    # Test valid user registration
    valid_user_data = {
        "username": f"testuser_{int(time.time()*1000)}",
        "email": f"testuser_{int(time.time()*1000)}@example.com",
        "password": "StrongPass123!"
    }
    try:
        start_time = time.time()
        response = requests.post(REGISTER_ENDPOINT, data=valid_user_data, headers=headers, timeout=TIMEOUT)
        elapsed = (time.time() - start_time)*1000  # ms
    except requests.RequestException as e:
        assert False, f"Valid registration request failed with exception: {e}"
    else:
        assert response.status_code == 200, f"Expected 200 OK for valid registration, got {response.status_code}"
        # Response time check per core goals
        assert elapsed < 500, f"API response time too high: {elapsed} ms"


    # Test invalid user registrations - multiple edge cases for validation errors
    invalid_test_cases = [
        {"username": "", "email": "noemail@example.com", "password": "Pass1234"},  # Empty username
        {"username": "user", "email": "invalidemail", "password": "Pass1234"},      # Invalid email format
        {"username": "user", "email": "user@example.com", "password": ""},          # Empty password
        {"username": "a"*256, "email": "user@example.com", "password": "Pass1234"}, # Username too long (assuming limit)
        {"username": "user", "email": "user@@example.com", "password": "Pass1234"}, # Malformed email
        {"username": "user", "email": "user@example.com", "password": "123"},       # Weak password (assuming validation)
    ]

    for idx, invalid_data in enumerate(invalid_test_cases, start=1):
        try:
            start_time = time.time()
            resp = requests.post(REGISTER_ENDPOINT, data=invalid_data, headers=headers, timeout=TIMEOUT)
            elapsed = (time.time()-start_time)*1000
        except requests.RequestException as e:
            assert False, f"Invalid registration request #{idx} failed with exception: {e}"
        else:
            # Check status code 400 validation error
            assert resp.status_code == 400, f"Expected 400 for invalid input test case #{idx}, got {resp.status_code}"
            # Response time check
            assert elapsed < 500, f"API response time too high for invalid test case #{idx}: {elapsed} ms"

    # Rate limiting test: send many requests rapidly
    rate_limit_test_data = {
        "username": f"ratelimituser_{int(time.time()*1000)}",
        "email": f"ratelimituser_{int(time.time()*1000)}@example.com",
        "password": "StrongPass123!"
    }
    limit_exceeded = False
    request_count = 20
    for i in range(request_count):
        try:
            resp = requests.post(REGISTER_ENDPOINT, data=rate_limit_test_data, headers=headers, timeout=TIMEOUT)
        except requests.RequestException:
            pass
        else:
            if resp.status_code == 429:
                limit_exceeded = True
                break
        time.sleep(0.05)  # 50ms gap

    # It is acceptable to either not get 429 or get it after several requests
    # So we don't assert but report
    if limit_exceeded:
        print("Rate limiting is enforced on /api/register (Received 429 Too Many Requests).")

    # Security tests: malformed input including XSS and SQL injection patterns
    security_test_inputs = [
        {"username": "<script>alert(1)</script>", "email": "xss@example.com", "password": "Pass1234!"},
        {"username": "normaluser", "email": "sqlinjection@example.com", "password": "' OR '1'='1"},
        {"username": "'; DROP TABLE users; --", "email": "drop@example.com", "password": "Pass1234"},
    ]

    for idx, sec_data in enumerate(security_test_inputs, start=1):
        try:
            resp = requests.post(REGISTER_ENDPOINT, data=sec_data, headers=headers, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Security test request #{idx} failed with exception: {e}"
        else:
            # Should not succeed with 200 - expect validation or rejection, usually 400 or 422 or 403
            assert resp.status_code in (400, 422, 403), f"Security input test #{idx} should be rejected but got status {resp.status_code}"

    # Concurrent request test for robustness: simulate 10 parallel registrations with valid data
    import concurrent.futures

    def register_user(index):
        user_data = {
            "username": f"concuser_{index}_{int(time.time()*1000)}",
            "email": f"concuser_{index}_{int(time.time()*1000)}@example.com",
            "password": "StrongPass123!"
        }
        try:
            r = requests.post(REGISTER_ENDPOINT, data=user_data, headers=headers, timeout=TIMEOUT)
            return r.status_code
        except Exception:
            return None

    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        futures = [executor.submit(register_user, i) for i in range(10)]
        results = [f.result() for f in futures]

    for i, status_code in enumerate(results):
        assert status_code == 200, f"Concurrent registration #{i} failed with status {status_code}"

test_user_registration_api()
