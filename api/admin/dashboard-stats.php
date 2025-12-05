<?php
/**
 * Admin Dashboard Stats API Endpoint
 * Handles retrieval of admin dashboard statistics
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../../app/bootstrap.php';

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
        // Authenticate admin user (support both session and HTTP Basic Auth)
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
        
        if (!$userId || !$user || (isset($user->role) && $user->role !== 'admin')) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Admin authentication required'
            ]);
            exit;
        }
        
        // Get database instance
        $db = \App\Core\Database::getInstance();
        
        // Get user statistics
        $totalUsers = 0;
        $activeUsers = 0;
        $newUsersToday = 0;
        
        try {
            $stmt = $db->query("SELECT COUNT(*) as total FROM users");
            $totalUsers = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $activeUsers = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
            $newUsersToday = $stmt->fetch()['total'];
        } catch (\Exception $e) {
            // If users table doesn't exist or has different structure, use defaults
        }
        
        // Get calculation statistics
        $totalCalculations = 0;
        $calculationsToday = 0;
        $popularCalculators = [];
        
        try {
            $stmt = $db->query("SELECT COUNT(*) as total FROM calculation_history");
            $totalCalculations = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM calculation_history WHERE DATE(created_at) = CURDATE()");
            $calculationsToday = $stmt->fetch()['total'];
            
            $stmt = $db->query("
                SELECT calculator_type, calculator_slug, COUNT(*) as usage_count 
                FROM calculation_history 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY calculator_type, calculator_slug 
                ORDER BY usage_count DESC 
                LIMIT 5
            ");
            $popularCalculators = $stmt->fetchAll();
        } catch (\Exception $e) {
            // If calculation_history table doesn't exist, use defaults
        }
        
        // Get system statistics
        $systemStats = [
            'uptime' => 'Unknown',
            'memory_usage' => memory_get_usage(true),
            'disk_space' => function_exists('disk_free_space') ? disk_free_space('.') : 0,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ];
        
        // Get recent activity
        $recentActivity = [];
        try {
            $stmt = $db->query("
                SELECT 'calculation' as type, calculator_type as details, created_at 
                FROM calculation_history 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $recentActivity = $stmt->fetchAll();
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
        
        // Prepare response
        $stats = [
            'users' => [
                'total' => (int)$totalUsers,
                'active' => (int)$activeUsers,
                'new_today' => (int)$newUsersToday
            ],
            'calculations' => [
                'total' => (int)$totalCalculations,
                'today' => (int)$calculationsToday,
                'popular' => $popularCalculators
            ],
            'system' => $systemStats,
            'activity' => $recentActivity,
            'generated_at' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
        
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