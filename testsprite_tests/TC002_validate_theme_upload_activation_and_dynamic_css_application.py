import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30
HEADERS = {
    "Accept": "application/json"
}

def test_validate_theme_upload_activation_and_dynamic_css_application():
    theme_upload_url = f"{BASE_URL}/admin/themes/upload"
    theme_validate_url = f"{BASE_URL}/admin/themes/validate"
    theme_activate_url = f"{BASE_URL}/admin/themes/activate"
    theme_customize_url = f"{BASE_URL}/admin/themes/customize"
    profile_page_url = f"{BASE_URL}/profile"
    admin_dashboard_url = f"{BASE_URL}/admin"
    help_center_url = f"{BASE_URL}/help"

    # Example minimal theme package payload (mocked as JSON for upload simulation)
    # Assuming the API accepts multipart/form-data with file upload,
    # we will simulate a simple theme zip upload using bytes in-memory.
    theme_file_content = b"PK\x03\x04\x14\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"  # minimal zip header bytes
    files = {
        "theme_package": ("test_theme.zip", theme_file_content, "application/zip")
    }

    # Upload Theme
    try:
        upload_resp = requests.post(theme_upload_url, auth=AUTH, files=files, timeout=TIMEOUT)
        assert upload_resp.status_code == 201, f"Theme upload failed: {upload_resp.status_code} {upload_resp.text}"
        upload_data = upload_resp.json()
        assert "theme_id" in upload_data, "theme_id missing in upload response"
        theme_id = upload_data["theme_id"]

        # Validate Theme
        validate_resp = requests.post(theme_validate_url, auth=AUTH, json={"theme_id": theme_id}, timeout=TIMEOUT)
        assert validate_resp.status_code == 200, f"Theme validation failed: {validate_resp.status_code} {validate_resp.text}"
        validate_data = validate_resp.json()
        assert validate_data.get("valid") is True, f"Theme validation returned invalid: {validate_data}"

        # Activate Theme
        activate_resp = requests.post(theme_activate_url, auth=AUTH, json={"theme_id": theme_id}, timeout=TIMEOUT)
        assert activate_resp.status_code == 200, f"Theme activation failed: {activate_resp.status_code} {activate_resp.text}"
        activate_data = activate_resp.json()
        assert activate_data.get("activated") is True, f"Theme was not activated: {activate_data}"

        # Customize Theme Settings (example customization payload)
        customization_payload = {
            "theme_id": theme_id,
            "settings": {
                "primary_color": "#123456",
                "font_size": "16px",
                "custom_css": ".dynamic-test { color: red; }"
            }
        }
        customize_resp = requests.post(theme_customize_url, auth=AUTH, json=customization_payload, timeout=TIMEOUT)
        assert customize_resp.status_code == 200, f"Theme customization failed: {customize_resp.status_code} {customize_resp.text}"
        customize_data = customize_resp.json()
        assert customize_data.get("customized") is True, f"Theme customization not applied properly: {customize_data}"

        # Verify dynamic CSS application on profile page
        profile_resp = requests.get(profile_page_url, auth=AUTH, timeout=TIMEOUT)
        assert profile_resp.status_code == 200, f"Profile page load failed: {profile_resp.status_code}"
        profile_text = profile_resp.text.lower()
        assert "dynamic-test" in profile_text, "Dynamic CSS class not found on profile page"
        assert "php warning" not in profile_text, "PHP warnings found on profile page"
        assert "missing table" not in profile_text, "Missing table errors found on profile page"

        # Verify no PHP warnings or missing table errors on admin dashboard page
        admin_resp = requests.get(admin_dashboard_url, auth=AUTH, timeout=TIMEOUT)
        assert admin_resp.status_code == 200, f"Admin dashboard load failed: {admin_resp.status_code}"
        admin_text = admin_resp.text.lower()
        assert "php warning" not in admin_text, "PHP warnings found on admin dashboard"
        assert "missing table" not in admin_text, "Missing table errors found on admin dashboard"

        # Verify navigation to help center page works and no errors there
        help_resp = requests.get(help_center_url, auth=AUTH, timeout=TIMEOUT)
        assert help_resp.status_code == 200, f"Help center load failed: {help_resp.status_code}"
        help_text = help_resp.text.lower()
        assert "php warning" not in help_text, "PHP warnings found on help center"
        assert "missing table" not in help_text, "Missing table errors found on help center"

    finally:
        # Cleanup - delete theme if API for deletion exists
        theme_delete_url = f"{BASE_URL}/admin/themes/delete"
        try:
            del_resp = requests.post(theme_delete_url, auth=AUTH, json={"theme_id": theme_id}, timeout=TIMEOUT)
            # No assertion on delete response; just best effort cleanup
        except Exception:
            pass


test_validate_theme_upload_activation_and_dynamic_css_application()