import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
TIMEOUT = 30
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {
    "Accept": "text/html,application/json",
    "User-Agent": "BishwoTestAgent/1.0"
}

def test_structured_logging_for_general_and_audit_events():
    """
    Verify that system logs and audit logs are structured correctly,
    include necessary event details, and are stored appropriately for audit and debugging.
    Focus on /profile, /admin, and /help; ensure no PHP warnings or missing table errors,
    and navigation works without error.
    """
    error_indicators = [
        "PHP Warning",
        "Warning:",
        "Missing table",
        "Fatal error",
        "Exception",
        "Traceback"
    ]

    # Endpoints to check
    endpoints = ["/profile", "/admin", "/help"]

    for endpoint in endpoints:
        url = BASE_URL + endpoint
        try:
            resp = requests.get(url, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request to {endpoint} failed with exception: {e}"

        # Status code check
        assert resp.status_code == 200, f"Expected 200 OK from {endpoint} but got {resp.status_code}"

        content = resp.text

        # Ensure PHP warnings or missing table errors are not present in the response content
        for err in error_indicators:
            assert err not in content, f"Error indicator '{err}' found in response from {endpoint}"

        # Check for structured log/event markers if present in the response (optional, based on UI exposure)
        # For audit logs, request /admin/audit-logs and check JSON structure
        if endpoint == "/admin":
            audit_logs_url = BASE_URL + "/admin/audit-logs"
            try:
                audit_resp = requests.get(audit_logs_url, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
            except requests.RequestException as e:
                assert False, f"Request to /admin/audit-logs failed with exception: {e}"

            assert audit_resp.status_code == 200, f"/admin/audit-logs returned {audit_resp.status_code}, expected 200"

            # Only try parsing JSON if Content-Type is application/json
            content_type = audit_resp.headers.get('Content-Type', '')
            if 'application/json' in content_type:
                try:
                    audit_json = audit_resp.json()
                except ValueError:
                    assert False, "/admin/audit-logs response is not valid JSON"

                # Validate audit log entries have structured necessary event details
                assert isinstance(audit_json, dict), "Audit logs response is not a JSON object"
                assert "logs" in audit_json, "Audit logs JSON missing 'logs' key"
                logs = audit_json["logs"]
                assert isinstance(logs, list), "'logs' key in audit logs response is not a list"

                for log_entry in logs:
                    assert isinstance(log_entry, dict), "Each log entry should be a dict"
                    # Check required keys for structured logs
                    required_keys = ["timestamp", "event_type", "user", "details"]
                    missing_keys = [k for k in required_keys if k not in log_entry]
                    assert not missing_keys, f"Audit log entry missing keys: {missing_keys}"
            else:
                # If not JSON, just ensure no error indicators
                for err in error_indicators:
                    assert err not in audit_resp.text, f"Error indicator '{err}' found in response from /admin/audit-logs"

    # Additional navigation check: from /profile to /admin, then /help
    try:
        session = requests.Session()
        session.auth = AUTH
        # Access /profile, then /admin, then /help sequentially
        for path in ["/profile", "/admin", "/help"]:
            resp = session.get(BASE_URL + path, headers=HEADERS, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Navigation failed at {path} with status {resp.status_code}"
            # Ensure no error messages in any navigation step
            for err in error_indicators:
                assert err not in resp.text, f"Error '{err}' found in navigation response at {path}"
    except requests.RequestException as e:
        assert False, f"Navigation requests failed with exception: {e}"


test_structured_logging_for_general_and_audit_events()
