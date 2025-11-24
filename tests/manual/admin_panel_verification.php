<?php
/**
 * Manual Admin Panel Verification
 * Tests admin dashboard, settings, user management endpoints for conflicts/errors
 */

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Database;
use App\Models\User;

header('Content-Type: application/json');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => [],
    'summary' => [
        'total' => 0,
        'passed' => 0,
        'failed' => 0,
        'errors' => []
    ]
];

// Test 1: Admin Dashboard Access
$test1 = [
    'id' => 'TC_ADMIN_001',
    'name' => 'Admin Dashboard Access',
    'status' => 'PENDING',
    'details' => []
];

try {
    // Simulate admin session
    $_SESSION['user_id'] = 1;
    $_SESSION['is_admin'] = true;
    
    // Check if DashboardController exists
    if (class_exists('\App\Controllers\Admin\DashboardController')) {
        $test1['details'][] = '✓ DashboardController class exists';
        
        $controller = new \App\Controllers\Admin\DashboardController();
        
        // Check if index method exists
        if (method_exists($controller, 'index')) {
            $test1['details'][] = '✓ Dashboard index() method exists';
            $test1['status'] = 'PASSED';
        } else {
            $test1['details'][] = '✗ Dashboard index() method NOT found';
            $test1['status'] = 'FAILED';
        }
    } else {
        $test1['details'][] = '✗ DashboardController class NOT found';
        $test1['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test1['status'] = 'ERROR';
    $test1['error'] = $e->getMessage();
    $test1['file'] = $e->getFile();
    $test1['line'] = $e->getLine();
}

$results['tests'][] = $test1;
$results['summary']['total']++;
if ($test1['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test1['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test1['name'];

// Test 2: Admin Settings Access
$test2 = [
    'id' => 'TC_ADMIN_002',
    'name' => 'Admin Settings Access',
    'status' => 'PENDING',
    'details' => []
];

try {
    if (class_exists('\App\Controllers\Admin\SettingsController')) {
        $test2['details'][] = '✓ SettingsController class exists';
        
        $controller = new \App\Controllers\Admin\SettingsController();
        
        if (method_exists($controller, 'index')) {
            $test2['details'][] = '✓ Settings index() method exists';
            $test2['status'] = 'PASSED';
        } else {
            $test2['details'][] = '✗ Settings index() method NOT found';
            $test2['status'] = 'FAILED';
        }
    } else {
        $test2['details'][] = '✗ SettingsController class NOT found';
        $test2['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test2['status'] = 'ERROR';
    $test2['error'] = $e->getMessage();
}

$results['tests'][] = $test2;
$results['summary']['total']++;
if ($test2['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test2['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test2['name'];

// Test 3: User Management Access
$test3 = [
    'id' => 'TC_ADMIN_003',
    'name' => 'User Management Access',
    'status' => 'PENDING',
    'details' => []
];

try {
    if (class_exists('\App\Controllers\Admin\UserController')) {
        $test3['details'][] = '✓ Admin UserController class exists';
        
        $controller = new \App\Controllers\Admin\UserController();
        
        if (method_exists($controller, 'index')) {
            $test3['details'][] = '✓ User index() method exists';
            $test3['status'] = 'PASSED';
        } else {
            $test3['details'][] = '✗ User index() method NOT found';
            $test3['status'] = 'FAILED';
        }
    } else {
        $test3['details'][] = '✗ Admin UserController class NOT found';
        $test3['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test3['status'] = 'ERROR';
    $test3['error'] = $e->getMessage();
}

$results['tests'][] = $test3;
$results['summary']['total']++;
if ($test3['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test3['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test3['name'];

// Test 4: Authentication API
$test4 = [
    'id' => 'TC_AUTH_001',
    'name' => 'Authentication API',
    'status' => 'PENDING',
    'details' => []
];

try {
    if (file_exists(__DIR__ . '/../../api/login.php')) {
        $test4['details'][] = '✓ API login.php file exists';
        
        if (class_exists('\App\Controllers\Api\AuthController')) {
            $test4['details'][] = '✓ AuthController class exists';
            
            $controller = new \App\Controllers\Api\AuthController();
            if (method_exists($controller, 'login')) {
                $test4['details'][] = '✓ login() method exists';
                $test4['status'] = 'PASSED';
            } else {
                $test4['details'][] = '✗ login() method NOT found';
                $test4['status'] = 'FAILED';
            }
        } else {
            $test4['details'][] = '✗ AuthController class NOT found';
            $test4['status'] = 'FAILED';
        }
    } else {
        $test4['details'][] = '✗ API login.php file NOT found';
        $test4['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test4['status'] = 'ERROR';
    $test4['error'] = $e->getMessage();
}

$results['tests'][] = $test4;
$results['summary']['total']++;
if ($test4['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test4['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test4['name'];

// Test 5: Profile API
$test5 = [
    'id' => 'TC_PROFILE_001',
    'name' => 'Profile API',
    'status' => 'PENDING',
    'details' => []
];

try {
    if (file_exists(__DIR__ . '/../../api/profile.php')) {
        $test5['details'][] = '✓ API profile.php file exists';
        $test5['status'] = 'PASSED';
    } else {
        $test5['details'][] = '✗ API profile.php file NOT found';
        $test5['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test5['status'] = 'ERROR';
    $test5['error'] = $e->getMessage();
}

$results['tests'][] = $test5;
$results['summary']['total']++;
if ($test5['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test5['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test5['name'];

// Test 6: Database Tables
$test6 = [
    'id' => 'TC_DB_001',
    'name' => 'Database Tables Verification',
    'status' => 'PENDING',
    'details' => []
];

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    $requiredTables = ['users', 'calculation_history', 'user_sessions', 'settings'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            $test6['details'][] = "✓ Table '$table' exists";
        } else {
            $test6['details'][] = "✗ Table '$table' MISSING";
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        $test6['status'] = 'PASSED';
    } else {
        $test6['status'] = 'FAILED';
        $test6['missing_tables'] = $missingTables;
    }
} catch (Exception $e) {
    $test6['status'] = 'ERROR';
    $test6['error'] = $e->getMessage();
}

$results['tests'][] = $test6;
$results['summary']['total']++;
if ($test6['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test6['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test6['name'];

// Test 7: User Model
$test7 = [
    'id' => 'TC_MODEL_001',
    'name' => 'User Model Verification',
    'status' => 'PENDING',
    'details' => []
];

try {
    if (class_exists('\App\Models\User')) {
        $test7['details'][] = '✓ User model class exists';
        
        $userModel = new User();
        
        $requiredMethods = ['find', 'findByUsername', 'create', 'update'];
        $missingMethods = [];
        
        foreach ($requiredMethods as $method) {
            if (method_exists($userModel, $method)) {
                $test7['details'][] = "✓ Method '$method' exists";
            } else {
                $test7['details'][] = "✗ Method '$method' MISSING";
                $missingMethods[] = $method;
            }
        }
        
        if (empty($missingMethods)) {
            $test7['status'] = 'PASSED';
        } else {
            $test7['status'] = 'FAILED';
            $test7['missing_methods'] = $missingMethods;
        }
    } else {
        $test7['details'][] = '✗ User model class NOT found';
        $test7['status'] = 'FAILED';
    }
} catch (Exception $e) {
    $test7['status'] = 'ERROR';
    $test7['error'] = $e->getMessage();
}

$results['tests'][] = $test7;
$results['summary']['total']++;
if ($test7['status'] === 'PASSED') $results['summary']['passed']++;
else if ($test7['status'] === 'FAILED') $results['summary']['failed']++;
else $results['summary']['errors'][] = $test7['name'];

// Calculate pass rate
$results['summary']['pass_rate'] = $results['summary']['total'] > 0 
    ? round(($results['summary']['passed'] / $results['summary']['total']) * 100, 2) . '%'
    : '0%';

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
