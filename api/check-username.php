<?php
/**
 * Username Availability Checker API
 * Checks if a username is available and provides suggestions
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Prevent any output before JSON
ob_start();

try {
    // Define constant to prevent bootstrap issues
    if (!defined('BISHWO_CALCULATOR')) {
        define('BISHWO_CALCULATOR', true);
    }
    
    // Include necessary files
    require_once __DIR__ . '/../app/bootstrap.php';
    
    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    $username = trim($_GET['username'] ?? '');
    
    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username is required']);
        exit;
    }
    
    // Validate username format
    if (strlen($username) < 3 || strlen($username) > 20) {
        http_response_code(400);
        echo json_encode([
            'available' => false,
            'error' => 'Username must be 3-20 characters long'
        ]);
        exit;
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        http_response_code(400);
        echo json_encode([
            'available' => false,
            'error' => 'Username can only contain letters, numbers, and underscores'
        ]);
        exit;
    }
    
    // Check if username exists
    $userModel = new \App\Models\User();
    $existingUser = $userModel::findByUsername($username);
    
    if ($existingUser) {
        // Username is taken - generate suggestions
        $suggestions = generateUsernameSuggestions($username);
        
        ob_clean();
        echo json_encode([
            'available' => false,
            'message' => 'Username is already taken',
            'suggestions' => $suggestions
        ]);
    } else {
        // Username is available
        ob_clean();
        echo json_encode([
            'available' => true,
            'message' => 'Username is available!'
        ]);
    }
    
} catch (Exception $e) {
    // Clean output buffer and return error
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'available' => false,
        'error' => 'Unable to check username availability',
        'message' => $e->getMessage()
    ]);
}

/**
 * Generate username suggestions based on the requested username
 */
function generateUsernameSuggestions($username) {
    $suggestions = [];
    
    // Add numbers to the end
    for ($i = 1; $i <= 5; $i++) {
        $suggestion = $username . $i;
        if (strlen($suggestion) <= 20) {
            $suggestions[] = $suggestion;
        }
    }
    
    // Add random numbers
    $suggestions[] = $username . rand(10, 99);
    $suggestions[] = $username . rand(100, 999);
    
    // Add year
    $currentYear = date('Y');
    $suggestions[] = $username . substr($currentYear, -2);
    $suggestions[] = $username . $currentYear;
    
    // Add underscore variations
    $suggestions[] = $username . '_user';
    $suggestions[] = $username . '_pro';
    $suggestions[] = 'user_' . $username;
    
    // Add common suffixes
    $suffixes = ['x', 'pro', 'dev', 'eng', 'tech'];
    foreach ($suffixes as $suffix) {
        $suggestion = $username . $suffix;
        if (strlen($suggestion) <= 20) {
            $suggestions[] = $suggestion;
        }
    }
    
    // Remove duplicates and limit to 6 suggestions
    $suggestions = array_unique($suggestions);
    return array_slice($suggestions, 0, 6);
}
?>
