<?php
// Simple test to check APP_BASE value
require_once 'app/Config/config.php';

echo "APP_BASE: [" . (defined('APP_BASE') ? APP_BASE : 'Not defined') . "]\n";
echo "Script name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "\n";

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);

echo "Script dir: [" . $scriptDir . "]\n";

// Remove /public suffix if present
if (substr($scriptDir, -7) === '/public') {
    $scriptDir = substr($scriptDir, 0, -7);
    echo "After removing /public: [" . $scriptDir . "]\n";
}

// Normalize root path to empty string
if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
    $scriptDir = '';
    echo "After normalization: [" . $scriptDir . "]\n";
}

echo "Final script dir: [" . $scriptDir . "]\n";