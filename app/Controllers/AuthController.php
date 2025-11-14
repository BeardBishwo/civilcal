<?php
/**
 * Auth Controller
 * Handles authentication pages
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Auth;
use App\Services\AuditLogger;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        // Generate CSRF token if not exists
        
        $this->view->render('auth/login', [
            'viewHelper' => $this->view
        ]);
    }

    /**
     * Handle login form submission
     */
    public function login()
    {
        header('Content-Type: application/json');
        
        try {
            // Validate CSRF token
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            // Validate input
            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                return;
            }
            
            // Authenticate via central Auth
            $result = Auth::login($email, $password);
            if (!($result['success'] ?? false)) {
                AuditLogger::warning('login_failed', ['email' => $email]);
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials']);
                return;
            }

            $userObj = $result['user'];
            AuditLogger::info('login_success', ['user_id' => $userObj->id ?? null, 'email' => $email]);
            // Backward compatible session variables
            $_SESSION['user_id'] = $userObj->id;
            $_SESSION['user_email'] = $userObj->email ?? '';
            $_SESSION['user_role'] = $userObj->role ?? 'user';
            $_SESSION['user_name'] = trim(($userObj->first_name ?? '') . ' ' . ($userObj->last_name ?? ''));

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $this->view->url('dashboard')
            ]);
            
        } catch (\Exception $e) {
            AuditLogger::error('login_exception', ['message' => $e->getMessage()]);
            error_log('Login error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Show register page
     */
    public function showRegister()
    {
        $this->view->render('auth/register', [
            'viewHelper' => $this->view
        ]);
    }

    /**
     * Handle register form submission
     */
    public function register()
    {
        header('Content-Type: application/json');
        
        try {
            // Validate CSRF token
            
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $profession = trim($_POST['profession'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $termsAccepted = isset($_POST['terms']);
            
            // Validate input
            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                return;
            }
            
            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
                return;
            }
            
            if ($password !== $confirmPassword) {
                echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
                return;
            }
            
            if (!$termsAccepted) {
                echo json_encode(['success' => false, 'message' => 'You must accept the terms and conditions']);
                return;
            }
            
            // Check if user exists
            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);
            
            if ($existingUser) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                return;
            }
            
            // Create user
            $userId = $userModel->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'username' => $username,
                'company' => $company,
                'profession' => $profession,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user'
            ]);
            
            if ($userId) {
                // Centralized login to create DB session + http-only cookie
                $loginResult = Auth::login($email, $password);
                if (!($loginResult['success'] ?? false)) {
                    echo json_encode(['success' => false, 'message' => 'Registration succeeded but auto-login failed']);
                    return;
                }
                // Backward compatible session variables
                $userObj = $loginResult['user'];
                $_SESSION['user_id'] = $userObj->id ?? $userId;
                $_SESSION['user_email'] = $userObj->email ?? $email;
                $_SESSION['user_role'] = $userObj->role ?? 'user';
                $_SESSION['user_name'] = trim(($userObj->first_name ?? $firstName) . ' ' . ($userObj->last_name ?? $lastName));
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful',
                    'redirect' => $this->view->url('dashboard')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
            }
            
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Show forgot password page
     */
    public function showForgotPassword()
    {
        $this->view->render('auth/forgot', [
            'viewHelper' => $this->view
        ]);
    }

    /**
     * Handle forgot password form submission
     */
    public function forgotPassword()
    {
        header('Content-Type: application/json');
        
        try {
            // Get input data (handle both JSON and form data)
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            
            $email = trim($input['email'] ?? '');
            
            // Validate input
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email address is required']);
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                return;
            }
            
            // Check if user exists
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            // Always return success to prevent email enumeration
            // In production, send actual reset email here
            if ($user) {
                // TODO: Generate reset token and send email
                // For now, just log it
                error_log('Password reset requested for: ' . $email);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'If an account exists with this email, a password reset link has been sent.'
            ]);
            
        } catch (\Exception $e) {
            error_log('Forgot password error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        // Store user name for logout message
        $userName = $_SESSION['user_name'] ?? 'User';
        $userId = $_SESSION['user_id'] ?? null;

        // Invalidate DB session and clear cookie
        AuditLogger::info('logout', ['user_id' => $userId]);
        Auth::logout();

        // Start new session for logout page message
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['logout_message'] = 'You have been successfully logged out';
        $_SESSION['logout_user'] = $userName;

        // Render logout page
        $this->view->render('auth/logout', [
            'viewHelper' => $this->view,
            'userName' => $userName
        ]);
    }
}
