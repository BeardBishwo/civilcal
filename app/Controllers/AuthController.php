<?php
/**
 * Auth Controller
 * Handles authentication pages
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
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
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                return;
            }
            
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
            
            // Find user
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                return;
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                return;
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            
            // Update last login
            $userModel->updateLastLogin($user['id']);
            
            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'redirect' => $this->view->url('dashboard')
            ]);
            
        } catch (\Exception $e) {
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
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                return;
            }
            
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
                // Auto-login after registration
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                $_SESSION['user_name'] = trim($firstName . ' ' . $lastName);
                
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
        $this->view->render('auth/forgot-password', [
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
            // Validate CSRF token
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                return;
            }
            
            $email = trim($_POST['email'] ?? '');
            
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
        session_destroy();
        header('Location: /');
        exit;
    }
}
