<?php
// CLI wrapper to simulate a GET request for api/check-username.php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['username'] = $argv[1] ?? 'testuser123';

require __DIR__ . '/api/check-username.php';
