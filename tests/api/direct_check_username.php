<?php
/**
 * Direct Username Availability Checker - Bypasses routing issues
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Support both JSON POST and query string access
    $rawBody = file_get_contents('php://input');
    $jsonInput = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonInput = [];
    }

    $username = trim(
        $jsonInput['username']
            ?? $_POST['username']
            ?? $_GET['username']
            ?? ''
    );

    if (empty($username)) {
        ob_clean();
        echo json_encode([
            'available' => false,
            'error' => 'Username is required',
            'message' => 'Please enter a username'
        ]);
        exit;
    }
    
    // Validate username format
    if (strlen($username) < 3) {
        ob_clean();
        echo json_encode([
            'available' => false,
            'error' => 'Username too short',
            'message' => 'Username must be at least 3 characters long'
        ]);
        exit;
    }
    
    if (strlen($username) > 20) {
        ob_clean();
        echo json_encode([
            'available' => false,
            'error' => 'Username too long',
            'message' => 'Username must be 20 characters or less'
        ]);
        exit;
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        ob_clean();
        echo json_encode([
            'available' => false,
            'error' => 'Invalid characters',
            'message' => 'Username can only contain letters, numbers, and underscores'
        ]);
        exit;
    }
    
    // Check if username exists
    $userModel = new \App\Models\User();
    $existingUser = $userModel::findByUsername($username);
    
    if ($existingUser) {
        // Username is taken - generate suggestions
        $suggestions = generateUsernameSuggestions($username, $userModel);
        
        ob_clean();
        echo json_encode([
            'available' => false,
            'message' => 'Username is already taken',
            'suggestions' => $suggestions,
            'status' => 'taken'
        ]);
    } else {
        // Username is available
        ob_clean();
        echo json_encode([
            'available' => true,
            'message' => 'Username is available!',
            'status' => 'available'
        ]);
    }
    
} catch (Exception $e) {
    // Clean output buffer and return error
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'available' => false,
        'error' => 'Server error',
        'message' => 'Unable to check username availability: ' . $e->getMessage()
    ]);
}

/**
 * Generate username suggestions and check their availability
 */
function generateUsernameSuggestions($username, $userModel) {
    $baseSuggestions = [];
    
    // Add numbers to the end
    for ($i = 1; $i <= 3; $i++) {
        $suggestion = $username . $i;
        if (strlen($suggestion) <= 20) {
            $baseSuggestions[] = $suggestion;
        }
    }
    
    // Add random numbers
    $baseSuggestions[] = $username . rand(10, 99);
    
    // Add year
    $currentYear = date('Y');
    $baseSuggestions[] = $username . substr($currentYear, -2);
    
    // Add underscore variations
    $baseSuggestions[] = $username . '_user';
    $baseSuggestions[] = $username . '_pro';
    
    // Add common suffixes
    $suffixes = ['x', 'pro', 'dev'];
    foreach ($suffixes as $suffix) {
        $suggestion = $username . $suffix;
        if (strlen($suggestion) <= 20) {
            $baseSuggestions[] = $suggestion;
        }
    }
    
    // Check availability of suggestions and return only available ones
    $availableSuggestions = [];
    foreach ($baseSuggestions as $suggestion) {
        if (count($availableSuggestions) >= 4) break; // Limit to 4 suggestions
        
        $existingUser = $userModel::findByUsername($suggestion);
        if (!$existingUser) {
            $availableSuggestions[] = $suggestion;
        }
    }
    
    return $availableSuggestions;
}
?>
