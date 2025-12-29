<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\GeolocationService;

echo "=== MaxMind Geolocation Test ===\n\n";

// Test 1: Check service status
$geo = new GeolocationService();
$status = $geo->getStatus();

echo "Service Status:\n";
echo "- Enabled: " . ($status['enabled'] ? 'YES' : 'NO') . "\n";
echo "- Database Path: " . $status['city_database_path'] . "\n";
echo "- Database Exists: " . ($status['city_database_exists'] ? 'YES' : 'NO') . "\n";
echo "- MaxMind Reader Available: " . ($status['maxmind_reader_available'] ? 'YES' : 'NO') . "\n\n";

// Test 2: Test with Google DNS (should return US location)
echo "Test with Google DNS (8.8.8.8):\n";
$result1 = $geo->getLocationDetails('8.8.8.8');
echo json_encode($result1, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Test with localhost (should fallback to online service or default)
echo "Test with localhost (127.0.0.1):\n";
$result2 = $geo->getLocationDetails('127.0.0.1');
echo json_encode($result2, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Test with IPv6 localhost
echo "Test with IPv6 localhost (::1):\n";
$result3 = $geo->getLocationDetails('::1');
echo json_encode($result3, JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Test with current IP
echo "Test with current IP:\n";
$result4 = $geo->getLocationDetails();
echo json_encode($result4, JSON_PRETTY_PRINT) . "\n";
