<?php
/**
 * Direct Forgot Password API - Bypasses routing issues
 */

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
    
    $email = trim($input['email'] ?? '');
    
    // Validate input
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email address is required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address format']);
        exit;
    }
    
    // Check if user exists
    $userModel = new \App\Models\User();
    $user = $userModel->findByEmail($email);
    
    // Always return success to prevent email enumeration attacks
    // This is a security best practice - don't reveal if email exists
    
    if ($user) {
        // User exists - in production, you would:
        // 1. Generate a secure reset token
        // 2. Store it in database with expiration
        // 3. Send email with reset link
        // 4. Log the attempt
        
        error_log("Password reset requested for existing user: $email (ID: {$user->id})");
        
        // For demo purposes, we'll just log it
        // In production, implement actual email sending here
    } else {
        // User doesn't exist - still log for security monitoring
        error_log("Password reset requested for non-existent email: $email");
    }
    
    // Clean output buffer and return success
    ob_clean();
    
    // Always return the same response to prevent email enumeration
    echo json_encode([
        'success' => true,
        'message' => 'If an account exists with this email address, you will receive password reset instructions shortly.',
        'info' => 'Please check your inbox and spam folder for the reset link.'
    ]);
    
} catch (Exception $e) {
    // Clean output buffer and return error
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to process password reset request. Please try again later.',
        'error' => $e->getMessage()
    ]);
}
?>
