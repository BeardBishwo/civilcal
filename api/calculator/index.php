<?php
/**
 * Calculator API Endpoint Handler
 * Handles dynamic calculator execution routes
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../../app/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parse the request URI to extract module and function
$requestUri = $_SERVER['REQUEST_URI'];

// Try multiple base path patterns
$basePaths = [
    '/' . basename(dirname(__DIR__, 2)) . '/calculator/',
    '/calculator/',
    '/api/calculator/'
];

$path = parse_url($requestUri, PHP_URL_PATH);
foreach ($basePaths as $basePath) {
    if (strpos($path, $basePath) !== false) {
        $path = str_replace($basePath, '', $path);
        break;
    }
}

$parts = explode('/', trim($path, '/'));

if (count($parts) >= 2) {
    $module = $parts[0];
    $function = $parts[1];
    
    $controller = new \App\Controllers\CalculatorController();
    $controller->execute($module, $function);
} else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid calculator endpoint', 'path' => $path, 'parts' => $parts]);
}
