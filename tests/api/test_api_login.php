<?php
// Test the API login endpoint
echo "Testing API Login Endpoint\n";
echo "========================\n\n";

// Test data
$testEmail = 'uniquebishwo@gmail.com';
$testPassword = 'testpassword123';

// Prepare the request data
$data = [
    'username_email' => $testEmail,
    'password' => $testPassword,
    'remember_me' => false
];

// Convert to JSON
$jsonData = json_encode($data);

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, 'http://localhost:80/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

echo "Sending login request...\n";
echo "Email: $testEmail\n";
echo "Password: $testPassword\n\n";

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Close cURL
curl_close($ch);

// Check for errors
if ($error) {
    echo "❌ CURL Error: $error\n";
    exit(1);
}

echo "HTTP Response Code: $httpCode\n";
echo "Raw Response: $response\n\n";

// Parse the response
$responseData = json_decode($response, true);

if ($responseData === null) {
    echo "❌ Failed to parse JSON response\n";
    exit(1);
}

// Check the response
if ($httpCode == 200 && isset($responseData['success']) && $responseData['success']) {
    echo "✅ LOGIN SUCCESSFUL!\n";
    echo "User ID: " . $responseData['user']['id'] . "\n";
    echo "Username: " . $responseData['user']['username'] . "\n";
    echo "Email: " . $responseData['user']['email'] . "\n";
    echo "Role: " . $responseData['user']['role'] . "\n";
    echo "Is Admin: " . $responseData['user']['is_admin'] . "\n";
    echo "Redirect URL: " . $responseData['redirect_url'] . "\n";
} else {
    echo "❌ LOGIN FAILED!\n";
    if (isset($responseData['error'])) {
        echo "Error: " . $responseData['error'] . "\n";
    }
    if (isset($responseData['debug'])) {
        echo "Debug Info:\n";
        print_r($responseData['debug']);
    }
}

echo "\nTest completed.\n";
?>
