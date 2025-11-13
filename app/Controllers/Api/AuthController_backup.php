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
            $username = $input['username_email'] ?? $input['username'] ?? $_POST['username_email'] ?? $_POST['username'] ?? '';
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
                        'role' => $user['role'] ?? 'user',
                        'is_admin' => $user['is_admin'] ?? false
                    ],
                    'redirect_url' => $user['is_admin'] ? '/admin/dashboard' : '/profile'
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid username or password']);
            }

        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Login failed due to server error']);
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

            if (empty($username) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['error' => 'All fields are required']);
                return;
            }

            // Create user
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
            error_log('Registration error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed due to server error']);
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
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
}
?>
