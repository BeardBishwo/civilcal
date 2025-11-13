<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    /**
     * Handle API login requests
     */
    public function login()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? $_POST['username'] ?? '';
            $password = $input['password'] ?? $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(['error' => 'Username and password are required']);
                return;
            }

            // Find user by username or email
            $user = User::findByUsername($username);

            if ($user && password_verify($password, $user->password)) {
                // Convert object to array
                $user = (array) $user;
                // Start session and set user data
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user'] = $user;
                $_SESSION['is_admin'] = $user['is_admin'] ?? false;

                // Check for installer auto-deletion on first admin login
                if (($user['role'] === 'admin' || $user['role'] === 'super_admin') && 
                    class_exists('\App\Services\InstallerService')) {
                    
                    $installerService = \App\Services\InstallerService::class;
                    
                    if ($installerService::shouldAutoDelete() && 
                        !$installerService::isInstallerProcessed() &&
                        $installerService::isFirstAdminLogin($user['id'])) {
                        
                        if ($installerService::deleteInstaller()) {
                            $installerService::markInstallerProcessed();
                            error_log("Installer automatically deleted after first admin login: {$user['email']}");
                        }
                    }
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'] ?? '',
                        'is_admin' => $user['is_admin'] ?? false
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Handle API registration requests
     */
    public function register()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? $_POST['username'] ?? '';
            $email = $input['email'] ?? $_POST['email'] ?? '';
            $password = $input['password'] ?? $_POST['password'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['error' => 'Username, email, and password are required']);
                return;
            }

            // Initialize User model and create user
            $userModel = new User();
            $result = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Registration failed']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Handle API logout requests
     */
    public function logout()
    {
        header('Content-Type: application/json');
        
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            session_destroy();
            
            echo json_encode([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Handle API forgot password requests
     */
    public function forgotPassword()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'] ?? $_POST['email'] ?? '';

            if (empty($email)) {
                http_response_code(400);
                echo json_encode(['error' => 'Email is required']);
                return;
            }

            // For now, just return success (implement email sending later)
            echo json_encode([
                'success' => true,
                'message' => 'Password reset instructions sent to your email'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Get current user status
     */
    public function userStatus()
    {
        header('Content-Type: application/json');
        
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $logged_in = !empty($_SESSION['user_id']);
            
            echo json_encode([
                'logged_in' => $logged_in,
                'user' => $logged_in ? [
                    'id' => $_SESSION['user_id'] ?? null,
                    'username' => $_SESSION['username'] ?? null,
                    'is_admin' => $_SESSION['is_admin'] ?? false
                ] : null
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Check username availability and provide suggestions
     */
    public function checkUsername()
    {
        // Set headers first
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Log the request for debugging
            error_log('Username check request received. GET params: ' . print_r($_GET, true));
            
            $username = $_GET['username'] ?? '';
            
            if (empty($username)) {
                echo json_encode(['error' => 'Username is required', 'debug' => 'No username provided']);
                return;
            }

            // Validate username format
            if (strlen($username) < 3 || strlen($username) > 20) {
                echo json_encode(['error' => 'Username must be 3-20 characters', 'debug' => 'Invalid length: ' . strlen($username)]);
                return;
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                echo json_encode(['error' => 'Username can only contain letters, numbers, and underscore', 'debug' => 'Invalid format']);
                return;
            }

            // Try to create User model
            try {
                $userModel = new User();
                error_log('User model created successfully');
            } catch (Exception $e) {
                error_log('Failed to create User model: ' . $e->getMessage());
                echo json_encode(['error' => 'Database connection error', 'debug' => 'User model failed']);
                return;
            }
            
            // Check if username exists
            try {
                $existingUser = $userModel->findByUsername($username);
                error_log('Username check completed for: ' . $username . ' - exists: ' . ($existingUser ? 'yes' : 'no'));
            } catch (Exception $e) {
                error_log('Database query failed: ' . $e->getMessage());
                echo json_encode(['error' => 'Database query error', 'debug' => 'Query failed']);
                return;
            }
            
            if (!$existingUser) {
                echo json_encode(['available' => true, 'username' => $username, 'debug' => 'Available']);
                return;
            }

            // Username taken, generate suggestions
            $suggestions = [];
            for ($i = 1; $i <= 3; $i++) {
                $suggestion = $username . $i;
                try {
                    if (!$userModel->findByUsername($suggestion)) {
                        $suggestions[] = $suggestion;
                    }
                } catch (Exception $e) {
                    error_log('Suggestion check failed: ' . $e->getMessage());
                    break; // Stop generating suggestions if there's an error
                }
            }
            
            echo json_encode([
                'available' => false,
                'username' => $username,
                'suggestions' => $suggestions,
                'debug' => 'Taken'
            ]);

        } catch (Exception $e) {
            error_log('checkUsername error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Error checking username availability', 
                'debug' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Resend email verification
     */
    public function resendVerification()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode(['error' => 'Email is required']);
                return;
            }

            // Find user by email
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if (!$user) {
                echo json_encode(['error' => 'User not found']);
                return;
            }

            // For demo purposes, just return success
            // In a real application, you would send an email here
            echo json_encode([
                'success' => true,
                'message' => 'Verification email sent successfully'
            ]);

        } catch (Exception $e) {
            error_log('resendVerification error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error resending verification email']);
        }
    }

/**
 * Handle API logout requests
 */
public function logout()
{
    header('Content-Type: application/json');
    
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        
        echo json_encode([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

/**
 * Handle API forgot password requests
 */
public function forgotPassword()
{
    header('Content-Type: application/json');
    
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? $_POST['email'] ?? '';

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email is required']);
            return;
        }

        // For now, just return success (implement email sending later)
        echo json_encode([
            'success' => true,
            'message' => 'Password reset instructions sent to your email'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

/**
 * Get current user status
 */
public function userStatus()
{
    header('Content-Type: application/json');
    
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $logged_in = !empty($_SESSION['user_id']);
        
        echo json_encode([
            'logged_in' => $logged_in,
            'user' => $logged_in ? [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'is_admin' => $_SESSION['is_admin'] ?? false
            ] : null
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

/**
 * Check username availability and provide suggestions
 */
public function checkUsername()
{
    // Set headers first
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');
    
    try {
        // Log the request for debugging
        error_log('Username check request received. GET params: ' . print_r($_GET, true));
        
        $username = $_GET['username'] ?? '';
        
        if (empty($username)) {
            echo json_encode(['error' => 'Username is required', 'debug' => 'No username provided']);
            return;
        }

        // Validate username format
        if (strlen($username) < 3 || strlen($username) > 20) {
            echo json_encode(['error' => 'Username must be 3-20 characters', 'debug' => 'Invalid length: ' . strlen($username)]);
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            echo json_encode(['error' => 'Username can only contain letters, numbers, and underscore', 'debug' => 'Invalid format']);
            return;
        }

        // Try to create User model
        try {
            $userModel = new User();
            error_log('User model created successfully');

            // For demo purposes, just return success
            // In a real application, you would send an email here
            echo json_encode([
                'success' => true,
                'message' => 'Verification email sent successfully'
            ]);

        } catch (Exception $e) {
            error_log('resendVerification error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error resending verification email']);
        }
    }
}
