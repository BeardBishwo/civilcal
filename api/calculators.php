<?php
/**
 * Calculators API Endpoint
 * Handles retrieval of available calculators
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
        // Get query parameters
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $tool = isset($_GET['tool']) ? $_GET['tool'] : null;
        
        if ($category && $tool) {
            // Get specific calculator
            $allCalculators = \App\Calculators\CalculatorFactory::getAvailableCalculators();
            $calculator = null;
            
            foreach ($allCalculators as $calc) {
                if (strtolower($calc['category']) === strtolower($category) && 
                    strtolower($calc['slug']) === strtolower($tool)) {
                    $calculator = $calc;
                    break;
                }
            }
            
            if ($calculator) {
                echo json_encode([
                    'success' => true,
                    'calculator' => $calculator
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Calculator not found'
                ]);
            }
        } elseif ($category) {
            // Get calculators by category
            $allCalculators = \App\Calculators\CalculatorFactory::getAvailableCalculators();
            $categoryCalculators = array_filter($allCalculators, function($calc) use ($category) {
                return strtolower($calc['category']) === strtolower($category);
            });
            
            echo json_encode([
                'success' => true,
                'category' => $category,
                'calculators' => array_values($categoryCalculators),
                'count' => count($categoryCalculators)
            ]);
        } else {
            // Get all calculators
            $allCalculators = \App\Calculators\CalculatorFactory::getAvailableCalculators();
            
            // Group by category
            $groupedCalculators = [];
            foreach ($allCalculators as $calculator) {
                $category = $calculator['category'];
                if (!isset($groupedCalculators[$category])) {
                    $groupedCalculators[$category] = [];
                }
                $groupedCalculators[$category][] = $calculator;
            }
            
            echo json_encode([
                'success' => true,
                'calculators' => $groupedCalculators,
                'total' => count($allCalculators),
                'categories' => array_keys($groupedCalculators)
            ]);
        }
        
    } else {
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use GET.'
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