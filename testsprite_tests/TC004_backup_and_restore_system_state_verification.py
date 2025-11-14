import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth('uniquebishwo@gmail.com', 'c9PU7XAsAADYk_A')
TIMEOUT = 30

def test_backup_and_restore_system_state_verification():
    session = requests.Session()
    session.auth = AUTH
    session.headers.update({'Accept': 'application/json'})
    backup_id = None
    
    def check_page_no_errors(path):
        # Check page loads without PHP warnings or missing table errors
        resp = session.get(f"{BASE_URL}{path}", timeout=TIMEOUT)
        assert resp.status_code == 200, f"Failed to load {path}"
        content = resp.text.lower()
        assert 'warning' not in content, f"PHP warnings found in {path}"
        assert 'error' not in content, f"Errors found in {path}"
        assert 'missing table' not in content, f"Missing table error in {path}"

    try:
        # Step 1: Navigate profile, admin dashboard, help center to verify navigation works and no errors
        for path in ["/profile", "/admin", "/help"]:
            check_page_no_errors(path)

        # Step 2: Create a backup (POST to /admin/help/backup)
        backup_create_resp = session.post(f"{BASE_URL}/admin/help/backup", timeout=TIMEOUT)
        assert backup_create_resp.status_code == 201 or backup_create_resp.status_code == 200,\
            f"Backup creation failed with status {backup_create_resp.status_code}"
        backup_data = backup_create_resp.json()
        assert 'backup_id' in backup_data, "Backup ID missing in response"
        backup_id = backup_data['backup_id']

        # Step 3: Verify that backup includes expected items by downloading artifact (assumed endpoint)
        backup_download_resp = session.get(f"{BASE_URL}/admin/help/backup/{backup_id}/download", timeout=TIMEOUT)
        assert backup_download_resp.status_code == 200, "Failed to download backup file"
        content_disposition = backup_download_resp.headers.get('Content-Disposition', '')
        assert 'attachment' in content_disposition.lower(), "Backup download missing attachment disposition"
        # Basic content checks (we expect some binary data, so we just check size)
        assert len(backup_download_resp.content) > 1000, "Backup file size too small, likely incomplete"

        # Step 4: Restore from backup (POST to /admin/help/restore with backup_id)
        restore_resp = session.post(f"{BASE_URL}/admin/help/restore", json={"backup_id": backup_id}, timeout=TIMEOUT)
        assert restore_resp.status_code == 200, f"Restore failed with status {restore_resp.status_code}"
        restore_result = restore_resp.json()
        assert restore_result.get('success') is True, "Restore response did not indicate success"

        # Step 5: After restore, re-check profile, admin, and help pages to confirm state restored and error free
        for path in ["/profile", "/admin", "/help"]:
            check_page_no_errors(path)

    finally:
        # Clean-up: if backup created, attempt to delete it (DELETE /admin/help/backup/{backup_id})
        if backup_id:
            try:
                del_resp = session.delete(f"{BASE_URL}/admin/help/backup/{backup_id}", timeout=TIMEOUT)
                # Deletion may return 200 or 204
                assert del_resp.status_code in (200, 204), "Failed to delete backup in cleanup"
            except Exception:
                pass

test_backup_and_restore_system_state_verification()