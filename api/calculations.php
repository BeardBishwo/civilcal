<?php
/**
 * User Calculations API Endpoint
 * Handles retrieval of user calculation history
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
    
    if ($method === 'GET') {
        // Authenticate user (support both session and HTTP Basic Auth)
        $userId = null;
        $user = null;
        
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = \App\Models\User::findByUsername($_SERVER['PHP_AUTH_USER']);
            if ($user) {
                $userArray = is_array($user) ? $user : (array) $user;
                if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                    $userId = $userArray['id'];
                    $user = (object) $userArray;
                }
            }
        } else {
            // Fall back to session auth
            $user = \App\Core\Auth::user();
            $userId = $user ? $user->id : null;
        }
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Authentication required'
            ]);
            exit;
        }
        
        // Get query parameters
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $calculationId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        $calculationService = new \App\Services\CalculationService();
        
        if ($calculationId) {
            // Get specific calculation
            $calculation = $calculationService->getCalculationById($calculationId, $userId);
            
            if ($calculation) {
                echo json_encode([
                    'success' => true,
                    'calculation' => $calculation
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Calculation not found'
                ]);
            }
        } else {
            // Get user calculation history
            $calculations = $calculationService->getUserHistory($userId, $limit, $offset);
            
            echo json_encode([
                'success' => true,
                'calculations' => $calculations,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'total' => count($calculations)
                ]
            ]);
        }
        
    } elseif ($method === 'DELETE') {
        // Delete calculation
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
            $user = \App\Core\Auth::user();
            $userId = $user ? $user->id : null;
        }
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Authentication required'
            ]);
            exit;
        }
        
        $calculationId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$calculationId) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Calculation ID required'
            ]);
            exit;
        }
        
        $calculationService = new \App\Services\CalculationService();
        $result = $calculationService->deleteCalculation($calculationId, $userId);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Calculation deleted successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete calculation'
            ]);
        }
        
    } else {
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use GET or DELETE.'
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