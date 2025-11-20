import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_payment_api_process_payment_endpoint():
    url = f"{BASE_URL}/payment"
    headers = {
        "Content-Type": "application/json"
    }

    # Test valid payment intent (expected 200)
    valid_payload = {
        "amount": 100.50,
        "currency": "USD",
        "payment_method": "credit_card"
    }
    try:
        response = requests.post(url, json=valid_payload, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 for valid payment, got {response.status_code}"
        # Validate response content is as expected - at least JSON and possibly success message or payment id
        try:
            data = response.json()
            assert isinstance(data, dict), "Response JSON should be an object"
            # Could add more validation if response schema known
        except Exception:
            assert False, "Response is not valid JSON on valid payment"
    except requests.RequestException as e:
        assert False, f"Request failed for valid payment intent: {e}"

    # Test payment with missing amount (expected 400)
    invalid_payload_missing_amount = {
        # "amount" missing
        "currency": "USD",
        "payment_method": "credit_card"
    }
    try:
        response = requests.post(url, json=invalid_payload_missing_amount, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 for missing amount, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for missing amount test: {e}"

    # Test payment with missing currency (expected 400)
    invalid_payload_missing_currency = {
        "amount": 50,
        # "currency" missing
        "payment_method": "paypal"
    }
    try:
        response = requests.post(url, json=invalid_payload_missing_currency, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 for missing currency, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for missing currency test: {e}"

    # Test payment with missing payment_method (expected 400)
    invalid_payload_missing_payment_method = {
        "amount": 70,
        "currency": "EUR"
        # "payment_method" missing
    }
    try:
        response = requests.post(url, json=invalid_payload_missing_payment_method, headers=headers, auth=AUTH, timeout=TIMEOUT)
        assert response.status_code == 400, f"Expected 400 for missing payment_method, got {response.status_code}"
    except requests.RequestException as e:
        assert False, f"Request failed for missing payment_method test: {e}"

test_payment_api_process_payment_endpoint()