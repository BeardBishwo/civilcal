<?php
// Define BASE_PATH as it would be in index.php
define('BASE_PATH', __DIR__);

// Require bootstrap
require_once __DIR__ . '/app/bootstrap.php';

echo "Testing helper availability...\n";
if (function_exists('app_base_url')) {
    echo "app_base_url exists.\n";
    echo "URL: " . app_base_url('test') . "\n";
} else {
    echo "ERROR: app_base_url does not exist.\n";
}
