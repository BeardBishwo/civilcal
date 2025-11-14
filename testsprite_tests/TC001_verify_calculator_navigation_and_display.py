import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:80/bishwo_calculator"
AUTH = HTTPBasicAuth("uniquebishwo@gmail.com", "c9PU7XAsAADYk_A")
TIMEOUT = 30

def test_verify_calculator_navigation_and_display():
    session = requests.Session()
    session.auth = AUTH

    endpoints = [
        "/profile",
        "/admin",
        "/help"
    ]

    php_warning_indicators = [
        "Warning: ",
        "Deprecated:",
        "Notice:",
        "Fatal error",
        "Call to undefined function",
        "undefined index",
        "missing table",
        "mysqli_sql_exception",
        "PDOException"
    ]

    for endpoint in endpoints:
        url = BASE_URL + endpoint
        try:
            response = session.get(url, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request to {url} failed with exception: {e}"

        assert response.status_code == 200, f"Unexpected status code {response.status_code} for {url}"
        content = response.text

        # Check that there are no PHP warnings or missing table errors in the page content
        for indicator in php_warning_indicators:
            assert indicator not in content, f"Found PHP warning or error indicator '{indicator}' in response from {url}"

    # Additional check: simulate dynamic dropdown on home page to get list of calculators
    home_url = BASE_URL + "/"
    try:
        home_response = session.get(home_url, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to {home_url} failed with exception: {e}"

    assert home_response.status_code == 200, f"Unexpected status code {home_response.status_code} for home page"

    home_content = home_response.text.lower()

    # Basic check that some calculator-related terms or dropdown options exist
    calculator_keywords = ["dropdown", "calculator", "select", "option"]
    assert any(keyword in home_content for keyword in calculator_keywords), "Home page does not contain calculator navigation elements"

test_verify_calculator_navigation_and_display()