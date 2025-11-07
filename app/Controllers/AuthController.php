<?php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Show login form
     */
    public function login() {
        $this->setCategory('auth');
        $this->setTitle('Login - Bishwo Calculator');
        $this->setDescription('Login to your Bishwo Calculator account');
        
        $data = [
            'title' => 'Sign In to Your Account',
            'subtitle' => 'Access your engineering calculation tools',
            'description' => 'Enter your credentials to access your personalized engineering calculator dashboard'
        ];
        
        $this->authView('auth/login', $data);
    }
    
    /**
     * Handle login form submission
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            // Basic validation
            if (empty($email) || empty($password)) {
                $this->json(['error' => 'Email and password are required'], 400);
                return;
            }
            
            // Placeholder authentication logic
            // TODO: Implement proper authentication with database
            if ($email === 'admin@bishwo.com' && $password === 'admin123') {
                // Set session variables
                $_SESSION['user'] = [
                    'id' => 1,
                    'email' => $email,
                    'name' => 'Admin User',
                    'role' => 'admin'
                ];
                $_SESSION['user_logged_in'] = true;
                
                $this->json(['success' => true, 'redirect' => '/admin/dashboard']);
            } else {
                $this->json(['error' => 'Invalid credentials'], 401);
            }
        } else {
            $this->json(['error' => 'Invalid request method'], 405);
        }
    }
    
    /**
     * Show registration form
     */
    public function register() {
        $this->setCategory('auth');
        $this->setTitle('Register - Bishwo Calculator');
        $this->setDescription('Create your Bishwo Calculator account');
        
        $data = [
            'title' => 'Create Your Account',
            'subtitle' => 'Join the engineering community',
            'description' => 'Register to access advanced engineering calculation tools and features'
        ];
        
        $this->authView('auth/register', $data);
    }
    
    /**
     * Handle registration form submission
     */
    public function createAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Basic validation
            if (empty($name) || empty($email) || empty($password)) {
                $this->json(['error' => 'All fields are required'], 400);
                return;
            }
            
            if ($password !== $confirmPassword) {
                $this->json(['error' => 'Passwords do not match'], 400);
                return;
            }
            
            if (strlen($password) < 6) {
                $this->json(['error' => 'Password must be at least 6 characters'], 400);
                return;
            }
            
            // Placeholder registration logic
            // TODO: Implement proper registration with database
            $this->json(['success' => true, 'message' => 'Account created successfully. Please login.']);
        } else {
            $this->json(['error' => 'Invalid request method'], 405);
        }
    }
    
    /**
     * Show forgot password form
     */
    public function forgotPassword() {
        $this->setCategory('auth');
        $this->setTitle('Reset Password - Bishwo Calculator');
        $this->setDescription('Reset your Bishwo Calculator password');
        
        $data = [
            'title' => 'Reset Your Password',
            'subtitle' => 'Enter your email to receive reset instructions',
            'description' => 'We\'ll send you a link to reset your password'
        ];
        
        $this->authView('auth/forgot-password', $data);
    }
    
    /**
     * Handle password reset request
     */
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            
            if (empty($email)) {
                $this->json(['error' => 'Email is required'], 400);
                return;
            }
            
            // Placeholder password reset logic
            // TODO: Implement proper password reset functionality
            $this->json(['success' => true, 'message' => 'Password reset link sent to your email']);
        } else {
            $this->json(['error' => 'Invalid request method'], 405);
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Clear session data
        unset($_SESSION['user'], $_SESSION['user_logged_in']);
        
        // Redirect to home page
        $this->redirect('/');
    }
}
?>
