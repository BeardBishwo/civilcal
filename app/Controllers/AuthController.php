<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Services\SecurityNotificationService;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->view->render('auth/login');
    }

    public function showRegister()
    {
        $this->view->render('auth/register');
    }

    public function showForgotPassword()
    {
        $this->view->render('auth/forgot');
    }
    
    public function logout()
    {
        // Clear session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Clear cookies
        if (isset($_COOKIE['auth_token'])) {
            setcookie('auth_token', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Redirect to login
        header('Location: /login');
        exit;
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        try {
            if (empty($username) || empty($password)) {
                throw new Exception('Please provide both username and password.');
            }

            // Find user by username or email
            $user = User::findByUsername($username);

            if ($user && password_verify($password, $user->password)) {
                $user = (array) $user;
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user'] = $user;
                $_SESSION['is_admin'] = $user['is_admin'] ?? false;
                
                // Security Notification
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $securityService = new SecurityNotificationService();
                $securityService->checkAndNotifyNewLogin($user['id'], $ipAddress);

                // Database Session & Auth Token Cookie (copied from API Auth)
                $db = Database::getInstance();
                $pdo = $db->getPdo();
                $sessionToken = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

                $stmt = $pdo->prepare("
                    INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $user['id'],
                    $sessionToken,
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $expiresAt
                ]);
                
                 // Set auth_token cookie
                setcookie('auth_token', $sessionToken, [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]);

                // Remember Me
                if ($rememberMe) {
                    $rememberToken = bin2hex(random_bytes(32));
                    setcookie('remember_token', $rememberToken, [
                        'expires' => time() + (30 * 24 * 60 * 60),
                        'path' => '/',
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                    // In a real app, store hash in DB
                }

                // Redirect based on role
                if (!empty($user['is_admin'])) {
                    header('Location: /admin/dashboard');
                } else {
                    header('Location: /dashboard'); // Or home /
                }
                exit;

            } else {
                throw new Exception('Invalid credentials.');
            }

        } catch (Exception $e) {
            $this->view->render('auth/login', ['error' => $e->getMessage(), 'username' => $username]);
        }
    }

    public function register()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        
        try {
            // Basic validation
            if (empty($username) || empty($email) || empty($password)) {
                throw new Exception('All fields are required.');
            }

            $userModel = new User();
            
            // Check existence
            if ($userModel->findByUsername($username)) {
                throw new Exception('Username already taken.');
            }
            // Add email check if method exists, usually findByUsername handles email too or separate method

            $userId = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'role' => 'user'
            ]);

            if ($userId) {
                // Login immediately or redirect to login
                header('Location: /login?registered=1');
                exit;
            } else {
                throw new Exception('Registration failed.');
            }

        } catch (Exception $e) {
             $this->view->render('auth/register', [
                 'error' => $e->getMessage(),
                 'username' => $username,
                 'email' => $email,
                 'first_name' => $firstName,
                 'last_name' => $lastName
             ]);
        }
    }
    
    public function forgotPassword()
    {
         $this->view->render('auth/forgot');
    }
}
