<?php
/**
 * Direct Login API - Bypasses routing issues
 * This file provides a working login endpoint when the main API has routing problems
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

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
    
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $username = $input['username_email'] ?? $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $rememberMe = $input['remember_me'] ?? false;
    
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        exit;
    }
    
    // Find user
    $userModel = new \App\Models\User();
    $user = $userModel::findByUsername($username);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    // Convert array to object if needed
    if (is_array($user)) {
        $user = (object) $user;
    }
    
    // Verify password
    if (!password_verify($password, $user->password)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
    
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['user'] = (array) $user;
    $_SESSION['is_admin'] = $user->is_admin ?? false;
    $_SESSION['full_name'] = ($user->first_name ?? '') . ' ' . ($user->last_name ?? '');
    
    // Handle "Remember Me" functionality
    if ($rememberMe) {
        $rememberToken = bin2hex(random_bytes(32));
        $expire = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_token', $rememberToken, [
            'expires' => $expire,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        error_log("Remember token set for user {$user->username}: expires " . date('Y-m-d H:i:s', $expire));
    }
    
    // Clean output buffer and return success
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $_SESSION['full_name'],
            'is_admin' => $_SESSION['is_admin']
        ],
        'redirect' => app_base_url('dashboard')
    ]);
    
} catch (Exception $e) {
    // Clean output buffer and return error
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Login failed',
        'message' => $e->getMessage()
    ]);
}
?>
