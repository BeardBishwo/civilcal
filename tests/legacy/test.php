<?php
// Simple test file to verify Apache can serve PHP files
echo "Hello from Apache! This file is being served successfully.<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
?>


