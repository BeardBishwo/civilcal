import requests
import time

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = ("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
HEADERS = {
    "Accept": "application/json"
}
TIMEOUT = 30


def test_plugin_upload_toggle_and_manifest_validation():
    session = requests.Session()
    session.auth = AUTH
    session.headers.update(HEADERS)

    plugin_upload_url = f"{BASE_URL}/admin/plugins/upload"
    plugin_toggle_url_template = f"{BASE_URL}/admin/plugins/{{plugin_id}}/toggle"
    plugin_manifest_url_template = f"{BASE_URL}/admin/plugins/{{plugin_id}}/manifest"
    plugin_delete_url_template = f"{BASE_URL}/admin/plugins/{{plugin_id}}/delete"

    profile_url = f"{BASE_URL}/profile"
    admin_url = f"{BASE_URL}/admin"
    help_url = f"{BASE_URL}/help"

    # Prepare a sample plugin file content (as bytes).
    # This is a minimal plugin zip archive or file to simulate upload.
    # Since file content is not specified, use dummy content with proper filename.
    # Assume 'test_plugin.zip' is a valid plugin file for testing.
    from io import BytesIO
    plugin_file_content = BytesIO(b'PK\x03\x04\x14\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00')  # dummy zip header bytes

    # For checking system error strings anywhere in responses.
    error_signatures = ["PHP Warning", "missing table", "error", "Exception"]

    def check_no_system_errors(text):
        lowered = text.lower()
        for sig in error_signatures:
            if sig.lower() in lowered:
                return False
        return True

    plugin_id = None

    try:
        # 1. Confirm navigation to /profile, /admin, and /help works without errors
        for url in [profile_url, admin_url, help_url]:
            resp = session.get(url, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Navigation to {url} failed with status {resp.status_code}"
            assert check_no_system_errors(resp.text), f"System error detected on {url}"

        # 2. Upload a new plugin
        files = {
            'plugin_file': ('test_plugin.zip', plugin_file_content, 'application/zip')
        }
        resp = session.post(plugin_upload_url, files=files, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Plugin upload failed with status {resp.status_code}"
        json_resp = resp.json()
        assert "plugin_id" in json_resp, "Response missing plugin_id after upload"
        plugin_id = json_resp["plugin_id"]

        # 3. Validate plugin manifest for compatibility
        manifest_url = plugin_manifest_url_template.format(plugin_id=plugin_id)
        resp = session.get(manifest_url, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Plugin manifest request failed with status {resp.status_code}"
        manifest = resp.json()
        # Check required manifest keys for compatibility (assuming fields)
        required_keys = ["name", "version", "compatible_versions", "author"]
        for key in required_keys:
            assert key in manifest, f"Manifest missing required key: {key}"
        # Assuming current system version is in manifest['compatible_versions'] list
        system_version = "2025.11.04"
        compatible = any(system_version in v or v in system_version for v in manifest.get("compatible_versions", []))
        assert compatible, "Plugin manifest not compatible with system version"

        # 4. Toggle plugin activation status twice (activate then deactivate)
        toggle_url = plugin_toggle_url_template.format(plugin_id=plugin_id)
        for _ in range(2):  # toggle twice
            resp = session.post(toggle_url, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Plugin toggle failed with status {resp.status_code}"
            toggle_resp = resp.json()
            assert "active" in toggle_resp, "Toggle response missing 'active' field"
            assert isinstance(toggle_resp["active"], bool), "'active' field should be boolean"
            # Also verify no system errors in response content
            assert check_no_system_errors(resp.text), "System error detected during toggle"

        # 5. Check again no PHP warnings or missing table errors on /profile, /admin, /help pages after plugin operations
        for url in [profile_url, admin_url, help_url]:
            resp = session.get(url, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Post-plugin navigation to {url} failed with status {resp.status_code}"
            assert check_no_system_errors(resp.text), f"System error detected on {url} after plugin operations"

    finally:
        # Cleanup plugin by deleting it
        if plugin_id is not None:
            delete_url = plugin_delete_url_template.format(plugin_id=plugin_id)
            try:
                resp = session.post(delete_url, timeout=TIMEOUT)
                assert resp.status_code == 200, f"Plugin deletion failed with status {resp.status_code}"
            except Exception:
                # Fail silently on cleanup to not override test exceptions
                pass


test_plugin_upload_toggle_and_manifest_validation()