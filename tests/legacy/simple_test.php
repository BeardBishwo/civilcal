<?php
// Simple test without any includes
echo "<!DOCTYPE html>";
echo "<html><head><title>Simple Test</title></head><body>";
echo "<h1>PHP Test</h1>";
echo "<p>Server: " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "</p>";
echo "<p>Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'unknown') . "</p>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "</body></html>";
?>


