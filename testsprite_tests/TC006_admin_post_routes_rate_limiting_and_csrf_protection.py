import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def get_csrf_token(session, url):
    # Get the CSRF token by visiting the page and parsing the token from cookies or page (assuming cookie named csrf_token)
    try:
        resp = session.get(url, auth=AUTH, timeout=TIMEOUT)
        resp.raise_for_status()
        # Try to get CSRF token from cookies (common approach)
        token = resp.cookies.get('csrf_token')
        if not token:
            # fallback: look for meta tag or hidden input - not possible here, so None
            token = None
        return token
    except requests.RequestException:
        return None

def test_admin_post_routes_rate_limiting_and_csrf_protection():
    session = requests.Session()
    session.auth = AUTH

    post_endpoints = [
        "/admin/themes",
        "/admin/help",
        "/admin/plugins"
    ]

    # We'll test POST to these URLs: base + endpoint.
    # Since we don't know exact POST route detail, send minimal valid data for testing.
    # For CSRF token, get from GET request cookie or assume it's required in header 'X-CSRF-Token'.
    # Test sequence:
    # 1. POST without CSRF token -> expect 403 or error about CSRF
    # 2. POST with invalid CSRF token -> expect 403 or error
    # 3. POST with valid CSRF token -> expect success or rate limiting
    # 4. Rapid POSTs to check rate limiting (send multiple valid requests quickly)
    
    for endpoint in post_endpoints:
        url = f"{BASE_URL}{endpoint}"
        csrf_token = get_csrf_token(session, url)
        headers = {}
        if csrf_token:
            headers['X-CSRF-Token'] = csrf_token

        # 1. POST without CSRF token
        try:
            resp = session.post(url, headers={}, timeout=TIMEOUT, data={})
            # Expect 403 Forbidden or 400 Bad Request indicating missing CSRF token
            # Accept any client error status that indicate rejection due to missing CSRF
            assert resp.status_code in (400, 403), f"POST {endpoint} without CSRF should be rejected, got {resp.status_code}"
            # Check body or text to not contain php warnings or table errors
            assert "Warning" not in resp.text, f"PHP warning found in response body of POST {endpoint} without CSRF"
            assert "Table" not in resp.text or "not found" not in resp.text.lower(), f"DB table error found in POST {endpoint} response"
        except requests.RequestException as e:
            assert False, f"RequestException on POST {endpoint} without CSRF: {e}"

        # 2. POST with invalid CSRF token
        try:
            invalid_headers = {'X-CSRF-Token': 'invalid_token'}
            resp = session.post(url, headers=invalid_headers, timeout=TIMEOUT, data={})
            assert resp.status_code in (400, 403), f"POST {endpoint} with invalid CSRF token should be rejected, got {resp.status_code}"
            assert "Warning" not in resp.text, f"PHP warning found in response body of POST {endpoint} with invalid CSRF"
            assert "Table" not in resp.text or "not found" not in resp.text.lower(), f"DB table error found in POST {endpoint} response"
        except requests.RequestException as e:
            assert False, f"RequestException on POST {endpoint} with invalid CSRF: {e}"

        # 3. POST with valid CSRF token - attempt once and check success or rate limit
        try:
            resp = session.post(url, headers=headers, timeout=TIMEOUT, data={})
            # Accept 200 OK, 201 Created, or 429 Too Many Requests (rate limiting)
            assert resp.status_code in (200, 201, 429), f"POST {endpoint} with valid CSRF should succeed or be rate-limited, got {resp.status_code}"
            assert "Warning" not in resp.text, f"PHP warning found in response body of POST {endpoint} with valid CSRF"
            assert "Table" not in resp.text or "not found" not in resp.text.lower(), f"DB table error found in POST {endpoint} response"
        except requests.RequestException as e:
            assert False, f"RequestException on POST {endpoint} with valid CSRF: {e}"

        # 4. Rate limiting - send multiple POSTs quickly with valid CSRF token and check if rate limiting kicks in
        try:
            rate_limit_triggered = False
            for _ in range(5):  # Send 5 quick requests
                resp = session.post(url, headers=headers, timeout=TIMEOUT, data={})
                if resp.status_code == 429:
                    rate_limit_triggered = True
                    break
            assert rate_limit_triggered, f"Rate limiting not enforced on POST {endpoint} after rapid requests"
            assert "Warning" not in resp.text, f"PHP warning found in response body during rate limit test POST {endpoint}"
            assert "Table" not in resp.text or "not found" not in resp.text.lower(), f"DB table error found in rate limit POST {endpoint} response"
        except requests.RequestException as e:
            assert False, f"RequestException during rate limiting test POST {endpoint}: {e}"

    session.close()

test_admin_post_routes_rate_limiting_and_csrf_protection()
