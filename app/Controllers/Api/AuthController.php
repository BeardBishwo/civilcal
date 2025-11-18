<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Database;
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
            // Log the request for debugging
            error_log('API Login request received - Method: ' . $_SERVER['REQUEST_METHOD']);
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log('API Login error: Invalid method ' . $_SERVER['REQUEST_METHOD']);
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            $rawInput = file_get_contents('php://input');
            error_log('API Login raw input: ' . $rawInput);
            
            $input = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('API Login JSON decode error: ' . json_last_error_msg());
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON input']);
                return;
            }
            
            $username = $input['username_email'] ?? $input['username'] ?? $_POST['username_email'] ?? $_POST['username'] ?? '';
            $password = $input['password'] ?? $_POST['password'] ?? '';
            $rememberMe = $input['remember_me'] ?? $_POST['remember_me'] ?? false;
            
            error_log('API Login attempt for username: ' . $username);

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
                // Session is already started in bootstrap.php
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user'] = $user;
                $_SESSION['is_admin'] = $user['is_admin'] ?? false;

                // Also create a database-backed session and auth_token cookie
                // so that session management tests and Auth::check() can rely
                // on the same user_sessions infrastructure.
                try {
                    $db = Database::getInstance();
                    $pdo = $db->getPdo();

                    $sessionToken = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

                    $stmt = $pdo->prepare("\n                        INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)\n                        VALUES (?, ?, ?, ?, ?)\n                    ");
                    $stmt->execute([
                        $user['id'],
                        $sessionToken,
                        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        $_SERVER['HTTP_USER_AGENT'] ?? '',
                        $expiresAt,
                    ]);

                    // Regenerate PHP session ID for security, if active
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_regenerate_id(true);
                    }

                    // Set auth_token cookie used by Auth::check()
                    setcookie('auth_token', $sessionToken, [
                        'expires' => time() + (30 * 24 * 60 * 60),
                        'path' => '/',
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Strict',
                    ]);
                } catch (Exception $e) {
                    // Do not fail the login on persistence issues, but log them
                    error_log('API Login session persistence error: ' . $e->getMessage());
                }

                // Handle "Remember Me" functionality
                if ($rememberMe) {
                    // Generate secure remember token
                    $rememberToken = bin2hex(random_bytes(32));
                    
                    // Set secure cookie for 30 days
                    $expire = time() + (30 * 24 * 60 * 60); // 30 days
                    setcookie('remember_token', $rememberToken, [
                        'expires' => $expire,
                        'path' => '/',
                        'domain' => '',
                        'secure' => isset($_SERVER['HTTPS']), // Only over HTTPS if available
                        'httponly' => true, // Prevent JavaScript access
                        'samesite' => 'Strict' // CSRF protection
                    ]);
                    
                    // Store token hash in database (you'd need to add this column)
                    // For now, just log it for demo purposes
                    error_log("Remember token set for user {$user['username']}: expires " . date('Y-m-d H:i:s', $expire));
                }

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
                        'role' => $user['role'] ?? 'user',
                        'is_admin' => $user['is_admin'] ?? false
                    ],
                    'redirect_url' => ($user['is_admin'] ?? false) ? app_base_url('/admin/dashboard') : app_base_url('/')
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid username or password']);
            }

        } catch (Exception $e) {
            error_log('API Login exception: ' . $e->getMessage());
            error_log('API Login exception trace: ' . $e->getTraceAsString());
            error_log('API Login exception file: ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'error' => 'Login failed due to server error',
                'debug' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }
    }

    /**
     * Handle API registration requests
     */
    public function register()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? '';
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            
            // Support both formats: full_name OR (first_name + last_name)
            $fullName = $input['full_name'] ?? '';
            $firstName = $input['first_name'] ?? '';
            $lastName = $input['last_name'] ?? '';
            
            // If full_name provided, parse it
            if (!empty($fullName) && (empty($firstName) || empty($lastName))) {
                $nameParts = explode(' ', trim($fullName), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';
            }
            
            $phoneNumber = $input['phone_number'] ?? $input['phone'] ?? '';
            $engineerRoles = $input['engineer_roles'] ?? [];
            $termsAgree = $input['terms_agree'] ?? false;
            $marketingAgree = $input['marketing_agree'] ?? false;

            // Validate required fields
            if (empty($username) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['error' => 'Username, email, and password are required']);
                return;
            }

            // Validate name (either full_name or first_name must be provided)
            if (empty($firstName)) {
                http_response_code(400);
                echo json_encode(['error' => 'First name is required']);
                return;
            }

            // Validate that at least one engineering specialty is selected (only if provided)
            if (!empty($engineerRoles) && (!is_array($engineerRoles) || count($engineerRoles) == 0)) {
                http_response_code(400);
                echo json_encode(['error' => 'Please select at least one engineering specialty']);
                return;
            }

            // Validate terms agreement (only if provided)
            if (isset($input['terms_agree']) && !$termsAgree) {
                http_response_code(400);
                echo json_encode(['error' => 'You must agree to the Terms of Service and Privacy Policy to register']);
                return;
            }

            // Create user with agreement preferences
            $userModel = new User();
            $result = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phoneNumber,
                'terms_agreed' => $termsAgree ? 1 : 0,
                'marketing_emails' => $marketingAgree ? 1 : 0
            ]);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $result,
                    'username' => $username
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Registration failed - username or email may already exist']);
            }

        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed due to server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle API logout requests
     */
    public function logout()
    {
        header('Content-Type: application/json');
        
        try {
            // Session is already started in bootstrap.php
            
            // Get user_id before destroying session to delete DB session
            $userId = $_SESSION['user_id'] ?? null;
            
            // Delete database session if exists
            if ($userId) {
                try {
                    $db = Database::getInstance();
                    $pdo = $db->getPdo();
                    
                    // Delete user sessions from database
                    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                    $stmt->execute([$userId]);
                } catch (Exception $e) {
                    error_log('Error deleting user session: ' . $e->getMessage());
                }
            }
            
            // Clear auth_token cookie
            if (isset($_COOKIE['auth_token'])) {
                setcookie('auth_token', '', [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
            
            // Clear remember me cookie if it exists
            $this->clearRememberToken();
            
            // Destroy session
            session_destroy();
            
            echo json_encode([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (Exception $e) {
            error_log('Logout error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Logout failed']);
        }
    }

    /**
     * Handle API forgot password requests
     */
    public function forgotPassword()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'] ?? $_POST['email'] ?? '';

            if (empty($email)) {
                http_response_code(400);
                echo json_encode(['error' => 'Email is required']);
                return;
            }

            // For demo purposes, just return success
            echo json_encode([
                'success' => true,
                'message' => 'Password reset email sent'
            ]);

        } catch (Exception $e) {
            error_log('forgotPassword error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error processing password reset request']);
        }
    }

    /**
     * Get current user status
     */
    public function userStatus()
    {
        header('Content-Type: application/json');
        
        try {
            // Session is already started in bootstrap.php
            
            if (isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => true,
                    'logged_in' => true,
                    'user' => $_SESSION['user'] ?? null
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'logged_in' => false
                ]);
            }
        } catch (Exception $e) {
            error_log('userStatus error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error getting user status']);
        }
    }

    /**
     * Check username availability
     */
    public function checkUsername()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = trim($input['username'] ?? '');

            if (empty($username)) {
                echo json_encode(['error' => 'Username is required']);
                return;
            }

            $userModel = new User();
            $existingUser = $userModel->findByUsername($username);
            
            if (!$existingUser) {
                echo json_encode(['available' => true, 'username' => $username]);
                return;
            }

            // Generate suggestions
            $suggestions = [];
            for ($i = 1; $i <= 3; $i++) {
                $suggestion = $username . $i;
                if (!$userModel->findByUsername($suggestion)) {
                    $suggestions[] = $suggestion;
                }
            }
            
            echo json_encode([
                'available' => false,
                'username' => $username,
                'suggestions' => $suggestions
            ]);

        } catch (Exception $e) {
            error_log('checkUsername error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error checking username availability']);
        }
    }

    /**
     * Check if user has valid remember me token and auto-login
     */
    public function checkRememberToken()
    {
        header('Content-Type: application/json');
        
        try {
            // Check if remember token exists in cookies
            if (!isset($_COOKIE['remember_token'])) {
                echo json_encode(['success' => false, 'message' => 'No remember token']);
                return;
            }
            
            $token = $_COOKIE['remember_token'];
            
            // For production, you'd validate this token against database
            // For now, we'll just check if it's a valid format and not expired
            if (strlen($token) === 64) { // 32 bytes = 64 hex chars
                // In a real app, you'd:
                // 1. Hash the token and look it up in database
                // 2. Get associated user_id
                // 3. Validate token hasn't expired
                // 4. Auto-login the user
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Remember token valid',
                    'auto_login' => true
                ]);
            } else {
                // Invalid token format
                $this->clearRememberToken();
                echo json_encode(['success' => false, 'message' => 'Invalid token format']);
            }
            
        } catch (Exception $e) {
            error_log('checkRememberToken error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Token validation failed']);
        }
    }
    
    /**
     * Clear remember me token/cookie
     */
    private function clearRememberToken()
    {
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', [
                'expires' => time() - 3600, // Expire in the past
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
    }
}
?>
