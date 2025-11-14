import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {
    "Accept": "application/json",
}
TIMEOUT = 30

def test_audit_log_filtering_and_download_functionality():
    session = requests.Session()
    session.auth = AUTH
    session.headers.update(HEADERS)

    try:
        # Step 1: Access the audit log viewer page (/admin/audit-logs) to check for PHP warnings/missing tables
        audit_logs_url = f"{BASE_URL}/admin/audit-logs"
        resp = session.get(audit_logs_url, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Expected 200 OK for audit log page, got {resp.status_code}"
        content_text = resp.text.lower()
        assert "warning" not in content_text, "PHP warning found in audit logs page HTML"
        assert "missing table" not in content_text, "Missing table error found in audit logs page HTML"

        # Step 2: Apply various filters to audit logs
        # Since no exact schema for filters provided, try common filters as query params:
        # Example filters: date_from, date_to, user, action
        filters = {
            "date_from": "2025-01-01",
            "date_to": "2025-12-31",
            "user": "admin",
            "action": "login",
            "format": "json"  # Assume JSON response on filter with format=json
        }
        filtered_logs_resp = session.get(audit_logs_url, params=filters, timeout=TIMEOUT)
        assert filtered_logs_resp.status_code == 200, f"Filtering audit logs failed: {filtered_logs_resp.status_code}"
        filtered_data = filtered_logs_resp.json()
        assert isinstance(filtered_data, dict) or isinstance(filtered_data, list), "Filtered logs response not JSON format"

        # Basic validation: filtered logs should have entries or at least be valid JSON structure
        assert filtered_data, "Filtered audit logs are empty"

        # Step 3: Download selected audit log files
        # Since no direct info on download links, simulate download by invoking a known endpoint:
        # Assuming a parameter ?download=true or /download endpoint exists for audit logs
        # Let's try /admin/audit-logs/download?date_from=...&date_to=...
        download_url = f"{BASE_URL}/admin/audit-logs/download"
        download_params = {
            "date_from": filters["date_from"],
            "date_to": filters["date_to"],
            "user": filters["user"],
            "action": filters["action"]
        }
        download_resp = session.get(download_url, params=download_params, timeout=TIMEOUT)
        assert download_resp.status_code == 200, f"Audit log download failed with status: {download_resp.status_code}"

        content_type = download_resp.headers.get("Content-Type", "").lower()
        assert "application" in content_type or "octet-stream" in content_type or "zip" in content_type, \
            f"Unexpected Content-Type for download: {content_type}"

        content_disposition = download_resp.headers.get("Content-Disposition", "").lower()
        assert "attachment" in content_disposition or "filename" in content_disposition, "No attachment disposition header"

        # Step 4: Verify integrity and correctness of downloaded data
        # Check that content is not empty and appears to be a valid log or archive
        content_bytes = download_resp.content
        assert len(content_bytes) > 0, "Downloaded audit log file is empty"

        # Additional minimal integrity check - for text logs check for expected text headers/log signature, for zip check header bytes
        if "zip" in content_type or download_resp.content[:2] == b'PK':
            # Zip archive detected - minimal check by ZIP signature "PK"
            assert content_bytes.startswith(b'PK'), "Downloaded file does not start with PK signature (zip)"
        else:
            # Assume text log - check UTF-8 decode and presence of log keywords
            text = content_bytes.decode('utf-8', errors='ignore').lower()
            assert "audit" in text or "log" in text or "event" in text, "Downloaded log content does not contain expected keywords"

        # Step 5: Additional navigation checks to profile (/profile), admin dashboard (/admin), and help center (/help)
        for path in ["/profile", "/admin", "/help"]:
            nav_resp = session.get(f"{BASE_URL}{path}", timeout=TIMEOUT)
            assert nav_resp.status_code == 200, f"Navigation failed for {path} with status {nav_resp.status_code}"
            nav_content = nav_resp.text.lower()
            assert "warning" not in nav_content, f"PHP warning found on page {path}"
            assert "missing table" not in nav_content, f"Missing table error found on page {path}"

    finally:
        session.close()

test_audit_log_filtering_and_download_functionality()