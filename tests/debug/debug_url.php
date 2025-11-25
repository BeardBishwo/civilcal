<?php
// Load bootstrap to get env vars
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Helpers/functions.php';

echo "<h1>Debug URL Generation</h1>";
echo "<pre>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "APP_BASE (Constant): " . (defined('APP_BASE') ? APP_BASE : 'NOT DEFINED') . "\n";
echo "APP_URL (Constant): " . (defined('APP_URL') ? APP_URL : 'NOT DEFINED') . "\n";
echo "getenv('APP_BASE'): " . getenv('APP_BASE') . "\n";
echo "\n";
echo "Generated URL for 'admin/activity': " . app_base_url('admin/activity') . "\n";
echo "</pre>";
