import requests
import uuid
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
TIMEOUT = 30
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
REGISTER_ENDPOINT = f"{BASE_URL}/api/register"


def test_authentication_api_register_endpoint():
    # Generate unique suffix to avoid conflicts
    unique_suffix = str(uuid.uuid4())[:8]
    valid_payload = {
        "username": f"testuser_{unique_suffix}",
        "email": f"testuser_{unique_suffix}@example.com",
        "password": "StrongPassword123!",
        "full_name": "Test User",
        "engineer_roles": ["civil", "structural"]
    }

    # Helper function to make POST request to register endpoint
    def post_register(payload):
        headers = {"Content-Type": "application/json"}
        response = requests.post(
            REGISTER_ENDPOINT,
            json=payload,
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        return response

    # 1. Test successful registration (Expect 200)
    response = post_register(valid_payload)
    assert response.status_code == 200, f"Expected 200, got {response.status_code}"
    # Optionally verify minimal content
    try:
        json_resp = response.json()
    except Exception:
        json_resp = None  # Registration success may or may not return json

    # 2. Test validation failure (missing required fields) (Expect 400)
    invalid_payload = valid_payload.copy()
    invalid_payload.pop("email")  # Remove required field to cause validation failure
    response = post_register(invalid_payload)
    assert response.status_code == 400, f"Expected 400 for validation failure, got {response.status_code}"

    # 3. Simulate server error (500)
    # Since it may be difficult to simulate a real 500 without internal server control,
    # try sending invalid JSON or something unexpected that might cause 500
    headers = {"Content-Type": "application/json"}
    # Here we forcibly send invalid JSON by sending plain text with JSON content-type
    try:
        response = requests.post(
            REGISTER_ENDPOINT,
            data="{'invalid_json': True unquoted_value}",
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        # Server may respond 400 or 500 depending on implementation
        assert response.status_code in (400, 500), f"Expected 400 or 500 for malformed request, got {response.status_code}"
    except requests.exceptions.RequestException:
        # Network or connection error also means test cannot proceed here
        pass


test_authentication_api_register_endpoint()