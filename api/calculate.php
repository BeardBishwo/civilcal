<?php
/**
 * Calculator API Endpoint
 * Handles calculation requests for all calculator types
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../app/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        // Handle calculation request
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid JSON input'
            ]);
            exit;
        }
        
        // Validate required fields
        if (!isset($input['category']) || !isset($input['tool']) || !isset($input['data'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing required parameters: category, tool, data'
            ]);
            exit;
        }
        
        // Authenticate user (support both session and HTTP Basic Auth)
        $userId = null;
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = \App\Models\User::findByUsername($_SERVER['PHP_AUTH_USER']);
            if ($user) {
                $userArray = is_array($user) ? $user : (array) $user;
                if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                    $userId = $userArray['id'];
                }
            }
        } else {
            // Fall back to session auth
            $user = \App\Core\Auth::user();
            $userId = $user ? $user->id : null;
        }
        
        // Perform calculation
        $calculationService = new \App\Services\CalculationService();
        $result = $calculationService->performCalculation(
            $input['category'],
            $input['tool'],
            $input['data'],
            $userId
        );
        
        // Return result
        if (isset($result['success']) && $result['success']) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
        
    } else {
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use POST.'
        ]);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}